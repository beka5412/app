<?php

namespace Backend\Controllers\Controller;

abstract class AFrontendController
{
    use TUserController;
    public string $title = '';
    public string $context = '';
    public string $indexFile = '';
}