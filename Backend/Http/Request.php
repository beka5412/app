<?php

namespace Backend\Http;

class Request
{
    public function query($param)
    {
        return $_REQUEST[$param] ?? '';
    }

    public function queryString()
    {
        $aux = explode("?", $this->uri());
        return $aux[1] ?? '';
    }

    public function json()
    {
        return json_decode(file_get_contents('php://input'));
    }

    public function raw()
    {
        return file_get_contents('php://input');
    }

    public function pageParams()
    {
        $params_str = $_REQUEST['params'] ?? '';
        $params = json_decode(urldecode($params_str));
        return $params;
    }

    public function uri()
    {
        return $_SERVER['REQUEST_URI'] ?? '';
    }

    public function route()
    {
        $aux = explode("?", $this->uri());
        return $aux[0] ?? '';
    }

    public function header($key)
    {
        return get_header($key);
    }

    public function all()
    {
        return json_decode(json_encode($_REQUEST));
    }
}