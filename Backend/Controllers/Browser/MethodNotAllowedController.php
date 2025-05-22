<?php

namespace Backend\Controllers\Browser;

use Backend\Template\View;
use Backend\Exceptions\FileNotFoundException;
use Backend\Models\User;

class MethodNotAllowedController
{
    public function __construct()
    {
        $this->title = "Method Not Allowed";
        $this->user = user();
    }

    public function view()
    {
        $title = $this->title;
        $user = $this->user;

        $content_type = $_SERVER['CONTENT_TYPE'] ?: 'text/html';
        header("Content-Type: $content_type");
        header("HTTP/1.1 405 Method Not Allowed");
        
        if ($content_type == 'text/html')
        {
            if (authenticated())
                $context = "dashboard";

            else
                $context = "public";
            
            $filepath = "frontend/view/browser/$context/405MethodNotAllowedView.php";
            
            try 
            {
                if (!file_exists(base_path($filepath))) throw new FileNotFoundException;
                View::render($filepath, compact('title', 'context', 'user'));
            }

            catch (FileNotFoundException $ex)
            {
                echo "<h1>405 Method Not Allowed</h1>";
            }
        }

        else if ($content_type == 'application/json')
            echo json_encode(["code" => 405, "message" => "Method Not Allowed"]);

        else echo "405 Method Not Allowed";
    }
}