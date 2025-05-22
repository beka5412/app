<?php

namespace Backend\Controllers;
use Backend\Http\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Backend\Models\Customer;
use Backend\Models\EmailMessage;
use Backend\Models\EmailQueue;
use Backend\Notifiers\Email\Mailer as Email;
use Exception;

class Script
{
    public function _customers_password_hash()
    {
        $customers = Customer::whereRaw(DB::raw("LENGTH(password)")." <= 12")->get();
        foreach ($customers as $customer)
        {
            echo "$customer->id ".strlen($customer->password)."<br />";
            $customer->password = hash_make($customer->password);
            $customer->save();
        }
    }

    public function resend_all_purchases()
    {
        set_time_limit(0);

        $email_queue = EmailQueue::where('status', 'sent')->whereNull('resent')->orderBy('id', 'DESC')->first();
        if (empty($email_queue)) die('1');
        $email_queue->resent = 1;
        $email_queue->save();


        $email_message = EmailMessage::find($email_queue->email_message_id);
        if (empty($email_message)) die('2');

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

        $result_message = 'Success';

        try 
        {
            echo "$email_queue->email | $title | $subject | $email_queue->smtp_id";

            Email::to($email_queue->email)
                ->title($title)
                ->subject($subject)
                ->body($body)
                ->send($email_queue->smtp_id);
        }

        catch (Exception $ex)
        {
            $result_message = $ex->getMessage();
        }

        echo "$email_queue->id | $email_queue->email | $result_message <br>";
    }
}