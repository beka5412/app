<?php

namespace Backend\Controllers\Queue;

use Backend\App;
use Backend\Http\Response;
use Backend\Models\EmailMessage;
use Backend\Models\EmailQueue;
use Backend\Models\EmailTemplate;
use Backend\Notifiers\Email\Mailer as Email;
use Exception;

class EmailController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function handle()
    {
        header("Content-Type: application/json");

        $email_queue = EmailQueue::where('status', 'waiting')->where('scheduled_at', '<=', today())->orderBy('created_at')->first();
        if (empty($email_queue)) return Response::json(['status' => 'error', 'message' => 'Nothing to run now.']);
        
        $email_queue->status = 'executed';
        $email_queue->save();

        $email_message = EmailMessage::find($email_queue->email_message_id);
        if (empty($email_message)) return Response::json(['status' => 'error', 'message' => 'Email message not found.'], '400 Bad Request');

        // $email_template = EmailTemplate::find($email_message->email_template_id);
        // if (empty($email_template)) return Response::json(['status' => 'error', 'message' => 'Email template not found.'], '400 Bad Request');


        // $name = 'Ezequiel';
        // $html = Email::view($email_message->email_template_path, compact('name'));
        // ['title' => $title, 'subject' => $subject, 'content' => $body] = Email::readTemplate($html);

        // Email::template($html, $data);

        // $title = Email::template($title, $data);
        // $subject = Email::template($subject, $data);
        // $body = Email::template($body, $data);

        $compact = [
            'locale' => $email_message->lang,
        ];

        ['title' => $title, 'subject' => $subject, 'content' => $body] = get_object_vars(
            Email::readTemplate(
                Email::view($email_message->email_template_path, $compact)
            )
        );

        $data = json_decode($email_message->data);
        
        $title = Email::template($title, $data);
        $subject = Email::template($subject, $data);
        $body = Email::template($body, $data);

        try 
        {
            Email::to($email_queue->email)
                ->title($title)
                ->subject($subject)
                ->body($body)
                ->send($email_queue->smtp_id);

            $email_queue->status = 'sent';
        }

        catch (Exception $ex)
        {
            $email_queue->status = 'error';
            $email_queue->response = $ex->getMessage();
            
            // habilitar quando tiver com pelo menos 2 smtps cadastrados no banco de dados
            // retry_email($email_queue);

            // TODO: retentativas no outro dia com os mesmos SMTPs
            // $oneday = date("Y-m-d H:i:s", strtotime(today() . " + 1 day"));
        }

        $email_queue->save();

        if ($email_queue->status === 'sent') 
            return Response::json(['status' => 'success', 'message' => 'Email sent.'], '200 OK');
        return Response::json(['status' => 'error', 'message' => 'Email not sent.'], '400 Bad Request');
    }
}
