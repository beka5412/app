<?php

namespace Backend\Controllers\Public;

use Backend\App;
use JSMin\JSMin;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;

class UpsellController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function js(Request $request)
    {
        header("Content-Type: text/javascript");

        View::jsmin('frontend/pjs/public/upsell/upsell.js.php');
    }
}