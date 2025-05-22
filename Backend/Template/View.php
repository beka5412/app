<?php

namespace Backend\Template;

use JSMin\JSMin;
use Backend\Http\Link;
use Ezeksoft\PHPWriteLog\Log;

class View
{
    public static function template($path, ?array $data = [])
    {
        $html = self::layout(import(base_path($path), $data), $data);
        echo remove_html_comments($html);
    }

    public static function response($path, ?array $data = [])
    {
        // $list_blocks = include base_path("elements.config.php");
        $context_ = $data['context'] ?? '';
        // $first_block = array_keys($list_blocks)[0];
        $context_selected = $context_ ? $context_ : '';
        $folder = $context_selected;
        $subdomain = subdomain();
        if ($subdomain && $subdomain <> env('SUBDOMAIN_INDEX')) $folder = "subdomains/$subdomain/$context_selected";

        if ($path && $context_selected)
        {
            $html = '';

            $css = '';
            $css .= "<style id=\"_templateCss\">";
            $d = dir_files(base_path("frontend/design/$folder"), '/\.css$/');
            if (!empty($d)) foreach ($d as $filename) $css .= "\n" . import($filename, $data);
            $d = null;
            $css .= "</style>";
            $data['css'] = $css;

            $html .= "<div>";
            $html .= $css;

            $html .= self::layout(import(base_path($path), $data), $data);
            $html .= "</div>";

            // $aux_s = explode("<title>", $html)[0] ?? '';
            // $aux_e = explode("</title>", $html)[1] ?? '';
            // echo $aux_s . $aux_e;
            echo remove_html_comments($html);
        }

        return new View;
    }

    public static function render($path = "", ?array $data = [], ?array $config = [])
    {
        if (!empty($config)) $config = (object) $config;
        $list_blocks = include base_path("elements.config.php");
        $context_ = $data['context'] ?? '';
        $context_selected = $context_ ? $context_ : '';
        if (empty($context_selected)) return;

        $packages = json_decode(array_accumulator(file(base_path("import.json")), fn ($total, $row) => $total .= $row, ""));
        $folder = $context_selected;

        $subdomain = subdomain_translated();

        if (subdomain() && subdomain() <> env('SUBDOMAIN_INDEX')) $folder = "subdomains/$subdomain/$context_selected";

        $html = "";

        $css = "<style id=\"importCss\">";
        foreach ($packages?->dependencies ?? [] as $dependency) foreach ($dependency->files as $file) if (substr($file, -4) == '.css')
            $css .= "\n" . str_replace("    ", "", str_replace("\t", "", str_replace("\n", "", import(base_path($file), $data))));
        $css .= "</style>";
        // $import_css = $css;

        $css .= "<style id=\"defaultCss\">";
        $css .= import(base_path("frontend/design/default.css"), $data);
        $css .= "</style>";
        $css .= "<style id=\"templateCss\">";
        $d = dir_files(base_path("frontend/design/$folder"), '/\.css$/');
        if (!empty($d)) foreach ($d as $filename) $css .= "\n" . import($filename, $data);
        $d = null;
        $css .= "</style>";
        $data['css'] = $css;
        
        $app_html = self::layout(import(base_path("frontend/view/layouts/$folder/_appView.php"), $data), $data);

        // echo base_path("frontend/view/layouts/$folder/_appView.php");

        // // extract($data);
        // // echo "frontend/view/layouts/$folder/_appView.php";
        // // require (base_path("frontend/view/layouts/$folder/_appView.php"));
        // die();


        // echo self::layout(import(base_path("frontend/view/layouts/$folder/_sHtmlView.php"), $data), $data);

        $content_html = '<div id="app" class="app">';
        if ($path && file_exists(base_path($path))) $content_html .= self::layout(import(base_path($path), $data), $data);
        $content_html .= "</div>";

        // // --
        // $title = '';
        // $doc = new \DOMDocument();
        // @$doc->loadHTML($content_html);
        // $el_title = $doc->getElementsByTagName('title')->item(0);
        // try
        // {
        //     $title = $el_title->textContent;
        //     $el_title->parentNode->removeChild($el_title);
        //     $content_html = $doc->saveHTML();
        // }
        // catch(\Exception $ex){}
        // $el_title = '';
        // $doc = '';

        $content_html = remove_html_comments($content_html);
        $arr_s = explode("<title>", $content_html);
        $aux_s = $arr_s[0] ?? '';
        $arr_e = explode("</title>", $content_html);
        $aux_e = $arr_e[1] ?? '';
        $title = explode("</title>", $arr_s[1] ?? '')[0] ?? '';
        if ($title) $content_html = $aux_s . $aux_e; // remote a tag <title>

        // $app_html = $aux_s . "<title>$title</title>" . $aux_e;
        $app_html = remove_html_comments($app_html);
        if ($title)
        {

            $aux_s = explode("<title>", $app_html)[0] ?? '';
            $aux_e = explode("</title>", $app_html)[1] ?? '';
            if (!$aux_s && !$aux_e)
            {
                $aux_h_s = explode("<head>", $app_html)[0] ?? '';
                $head_s = $aux_h_s[0] ?? '';
                $head_e = $aux_h_s[1] ?? '';
                $app_html = $head_s . $title . $head_e; // adicionar tag
            }
            else
                $app_html = $aux_s . "<title>$title</title>" . $aux_e;
        }

        // echo self::layout(import(base_path("frontend/view/layouts/$folder/_eHtmlView.php"), $data), $data);

        $js = "";
        $js .= "<script id=\"coreJs\">\n";

        foreach ($packages?->dependencies ?? [] as $dependency) foreach ($dependency->files as $file) if (substr($file, -3) == '.js') $js .= "\n" . import(base_path($file), $data);

        $defines = [
            'frontend/dom/namespaces.js' // define namespaces
        ];

        foreach ($defines as $file) $js .= "\n" . import(base_path($file), $data);

        $js .= "(()=>{";
        foreach (dir_files(base_path('frontend/dom/app/'), '/\.js$/') as $filename) $js .= "\n" . import($filename, $data);

        $files = [
            'frontend/dom/bootstrap.js', // carrega tudo necessario
            'frontend/dom/routes.js', // define rotas
            'frontend/dom/events.js', // define rotas
            'frontend/dom/index.js', // app
            'frontend/dom/end.js', // app
        ];

        foreach ($files as $file)
            $js .= "\n" . import(base_path($file), $data);

        $js .= "let coreJs = document.getElementById('coreJs');";
        $js .= "coreJs.parentNode.removeChild(coreJs);";
        $js .= "})();";
        $js .= "</script>";

        if ($config->no_js ?? false) $js = '';

        $html = $content_html . $js;
        $html = str_replace("<Content />", $html, $app_html);

        echo $html;

        // echo JSMin::minify($js);
        // Link::changeUrl(site_url(), $_SERVER['REQUEST_URI'] ?? '/');
        if ($config->no_js ?? false) Link::changeUrl(current_url(), $_SERVER['REQUEST_URI'] ?? '/');

        return new View;
    }

    public static function basic($path, ?array $data = [])
    {
        echo self::layout(import(base_path($path), $data), $data);
    }

    public static function layout($content, array $data = [])
    {
        $list = include base_path("elements.config.php");

        $context = $data['context'] ?? null;
        if (empty($context)) return $content;

        $blocks = ($subdomain = subdomain()) ? ($list[$subdomain][$context] ?? null) : ($list["."][$context] ?? null);
        if (empty($blocks)) return $content;

        foreach ($blocks as $tag => $item)
        {
            $method = $tag;
            $method = str_replace("<", "", $method);
            $method = str_replace("/", "", $method);
            $method = str_replace(">", "", $method);
            $method = trim($method);

            // repetir na quantidade de vezes que o elemento aparece
            $qty_s = sizeof(explode("<$method", $content));
            for ($i = 0; $i < $qty_s; $i++)
            {
                $end1 = '';
                $aux1 = '';
                $start1 = '';
                $end1 = '';
                $aux5 = '';
                $params = '';
                $end = '';

                if (strpos($tag, "<$method") !== false)
                {
                    $var = "<$method";
                    $aux1 = explode($var, $content);
                    $start1 = $aux1[0] ?? '';
                    $end1 = ($aux1[1] ?? false) ? substr($content, strlen($start1) + strlen($var), strlen($content)) : '';


                    // if ($method == 'Pagination') goto end_tag;

                    if (!$end1) goto end_tag;
                    $end_str = ">";

                    $aux5 = explode($end_str, $end1);
                    $params = $aux5[0];
                    $end = substr($end1, strpos($end1, $end_str) + strlen($end_str));

                    // (new Log)->write(base_path('logs/view.txt'), "\n============================================".$item['path']);

                    if ($item['path'] ?? false)
                    {
                        $path = str_replace('{key}', $context, $item['path']);
                        $path = str_replace('{subdomain}', $subdomain, $path);
                        $content = $start1 . import(base_path($path), $data) . $end;
                    }
                }

                $end1 = '';
                $aux1 = '';
                $start1 = '';
                $end1 = '';
                $aux5 = '';
                $params = '';
                $end = '';
            }
            $i = 0;

            end_tag:
            if (strpos($tag, "</$method>") !== false)
            {
                // repetir na quantide de vezes que o elemento aparece
                $qty_e = sizeof(explode("</$method>", $content));
                for ($i = 0; $i < $qty_e; $i++)
                {
                    $var = "</$method>";
                    $aux1 = explode($var, $content);
                    $start1 = $aux1[0] ?? '';
                    $end = ($aux1[1] ?? false) ? substr($content, strlen($start1) + strlen($var), strlen($content)) : '';

                    // if (!$end) goto loop1;

                    if ($item['path'] ?? false)
                        $content = $start1 . import(base_path(str_replace('{key}', $context, $item['path'])), $data) . $end;

                    $var = null;
                    $aux1 = null;
                    $start1 = null;
                    $end = null;
                }
            }

            loop1:
        }

        return $content;
    }

    public static function js($path, ?array $data = [])
    {
        echo import(base_path($path), $data);
    }

    public static function jsmin($path, ?array $data = [])
    {
        $js = import(base_path($path), $data);
        echo JSMin::minify($js);
    }
    public static function cssmin($path, ?array $data = [])
    {
        $css = import(base_path($path), $data);
        echo minify_css($css);
    }
}
