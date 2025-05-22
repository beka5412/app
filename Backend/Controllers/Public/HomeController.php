<?php

namespace Backend\Controllers\Public;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\User;
use Backend\Http\Link;
use Setono\MetaConversionsApi\Event\Event;
use Setono\MetaConversionsApi\Pixel\Pixel;
use Setono\MetaConversionsApi\Client\Client;

class HomeController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }

    public function index(Request $request)
    {
        $login = new LoginController($this->application);
        $login->index($request);
        Link::changeUrl(site_url(), '/login');
    }

    public function conversion_api(Request $request)
    {
        $event = new Event(Event::EVENT_VIEW_CONTENT);
        $event->eventSourceUrl = 'https://example.com/products/blue-jeans';
        $event->userData->clientUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
        $event->userData->email[] = 'johndoe@example.com';
        $event->pixels[] = new Pixel('3448203008801055', 'EAAvsFUhC8psBAMIHUQPw1Eq8EFtDSaoo3w88AYXwnrwpt8LdFgyF8wOwbmUgiik6yihT40BvepYrCzCvcMO6irkr4KDOVqPhrgSWZArGaw7NnSwhGEfzIZAhE639sOlmHzJ5ZCiGRnfWJ7NtZCZC351Nq60PkDfqEjLZABA1GT6UhLrS7J20tV');
        // $event->testEventCode = 'test event code'; // uncomment this if you want to send a test event

        $client = new Client();
        $client->sendEvent($event);
    }
}