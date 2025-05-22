<?php 

namespace Backend\Serializers;

class Url
{
    public function __construct($url, $request_uri=null)
    {
        if (empty($request_uri)) $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $uri = explode('?', $request_uri)[0];
        $uri_split = explode('/', $uri);
        $url_split = explode('/', $url);
        $request_url = [];
        $values = [];
        $param_keys = [];
        $n = 0;
        foreach ($uri_split as $item)
        {
            $item = $url_split[$n] ?? ''; 
            $p_aux = explode("}", $item);
            // quando localizar uma variavel
            if (sizeof($p_aux) > 1)
            {
                $p_name = $p_aux[0];
                $p = trim($p_name);
                $p = str_replace("{", "", $p);
                $param_keys[] = $p;
                $values[] = $uri_split[$n] ?? '';
                $request_url[] = $url_split[$n] ?? '';
            }
            else $request_url[] = $uri_split[$n] ?? '';
            $n++;
        }
        $this->param_keys = $param_keys;
        $this->params = $values;
        $this->request_url = join("/", $request_url);
        $this->match = $this->request_url == $url || $this->request_url == $url.'/';
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParamKeys()
    {
        return $this->param_keys;
    }

    public function getRequestUrl()
    {
        return $this->request_url;
    }

    public function match()
    {
        return $this->match;
    }
}