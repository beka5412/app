<?php

namespace Backend\Notifiers\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Backend\Models\Smtp as SmtpDB;

class SDK
{
    public $recipients;
    public $title;
    public $subject;
    public $body;
    public $debug_mode = false;

    public function to($email) 
    {
        $this->recipients[] = $email;
        return $this;
    }

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    public function view($name, $data=[])
    {
        return import(base_path("frontend/email/{$name}Email.php"), $data);
    }

    public function template($html, $data=[])
    {
        foreach ($data as $key => $value)
            $html = str_replace("[$key]", $value, $html);
        return $html;
    }

   
    public function readTemplate($html)
    {
        $lines = explode("\n", $html);
        $line1 = $lines[0] ?? '';
        $line2 = $lines[1] ?? '';

        $title = "";
        $subject = "";

        if (str_contains($line1, "TITLE="))
        {
            $title = ($pos = strpos($line1, "=")) !== false ? substr($line1, $pos + 1, strlen($line1)) : '';
            array_shift($lines);
        }

        if (str_contains($line2, "SUBJECT="))
        {
            $subject = ($pos = strpos($line2, "=")) !== false ? substr($line2, $pos + 1, strlen($line2)) : '';
            array_shift($lines);
        }

        $content = join("\n", $lines);

        return (object) compact('title', 'subject', 'content');
    }

    public function debug($debug_mode)
    {
        $this->debug_mode = $debug_mode;
        return $this;
    }

    public function send($smtp_id=null)
    {
        $mail = new PHPMailer(true); // enabled exceptions

        if ($this->debug_mode) $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->isSMTP();

        $smtp = SmtpDB::find($smtp_id);
        if ($smtp)
        {
            $mail->Host = $smtp->host;
            $mail->Username = $smtp->username;
            $mail->Password = $smtp->password;
            $mail->Port = $smtp->port;
            $from = $smtp->from;
        }
        else
        {
            $mail->Host = env('STMP_HOST');
            $mail->Username = env('STMP_USERNAME');
            $mail->Password = env('STMP_PASSWORD');
            $mail->Port = env('STMP_PORT');
            $from = env('SMTP_FROM');
        }

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $mail->Port == 587 ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;

        $mail->setFrom($from, $this->title);

        foreach ($this->recipients as $recipient) $mail->addAddress($recipient);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $this->subject;
        $mail->Body    = $this->body;
        $mail->AltBody = strip_tags($mail->Body);

        $mail->send();
    }
}



