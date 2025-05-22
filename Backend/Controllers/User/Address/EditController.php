<?php

namespace Backend\Controllers\User\Address;

use Backend\Controllers\Browser\Dashboard\NotFoundController;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Http\Request;
use Backend\Models\UserAddress;
use Backend\Template\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'User Address';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/address/editView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);
        
        $url = site_url().'/profile/address';

        $user_address = null; //  UserAddress::where('user_id', $user->id)->first();
        $view = View::$view_method($this->indexFile, compact('title', 'context', 'user', 'user_address', 'url'));

        return $view;
    }
}
