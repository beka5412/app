<?php

use Backend\Models\Balance;
use Backend\Models\EmailMessage;
use Backend\Models\EmailQueue;
use Backend\Models\EmailTemplate;
use Backend\Models\Language;
use Backend\Models\Smtp;
use Backend\Models\User;
use Backend\Models\Customer;
use Backend\Models\Administrator;
use Backend\Models\OrderMeta;
use Backend\Models\Setting;
use Backend\Models\BankAccount;
use Backend\Models\Bank;
use Backend\Models\CheckoutTheme;
use Backend\Models\ProductLink;
use Backend\Models\Product;
use Pdp\Rules;
use Pdp\Domain;
use JSMin\JSMin;
use Backend\Enums\EmailTemplate\EEmailTemplateType;
use Backend\Enums\Currency\ECurrencySymbol;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Models\IuguBank;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

if (!function_exists('dir_files'))
{
    function dir_files($dir, $filter = '', &$results = array())
    {
        if (!is_dir($dir)) return;
        $files = scandir($dir);

        foreach($files as $key => $value)
        {
            // $path = $dir;
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value); 

            if(!is_dir($path))
            {
                if(empty($filter) || preg_match($filter, $path)) $results[] = $path;
            }
            
            elseif ($value != "." && $value != "..")
            {
                dir_files($path, $filter, $results);
            }
        }

        return $results;
    }
}

if (!function_exists('import'))
{
    function import($file, $data=[])
    {
        if (!empty($data)) extract($data);
        if (file_exists($file))
        {
            ob_start();
            require $file; 
            return ob_get_clean();
        }
        return '';
    }
}

if (!function_exists('array_accumulator'))
{
    /**
     * Reduce
     *
     * @param array $array
     * @param Closure(mixed, mixed...): mixed $callback
     * @param mixed $initial_value
     * @return mixed
     */
    function array_accumulator(array $array, Closure $callback, $initial_value = 0)
    {
        $total = $initial_value;
        
        foreach ($array as $key => $value)
            $total = is_numeric($key) ? $callback($total, $value) : $callback($total, $key, $value);

        return $total;
    }
}

if (!function_exists('array_every'))
{
    function array_every(Array $array, Closure $callback)
    {
        // se algum retornar false, a funcao retornar false
        foreach ($array as $key => $value) if ( !(is_numeric($key) ? $callback($value) : $callback($key, $value)) ) return false;

        // se nenhum item era false, retorna true
        return true;
    }
}

if (!function_exists('array_some'))
{
    function array_some(Array $array, Closure $callback)
    {
        // se algum retornar true, a funcao retornar true
        foreach ($array as $key => $value) if ( (is_numeric($key) ? $callback($value) : $callback($key, $value)) ) return true;

        // se nenhum item era true, retorna false
        return false;
    }
}

if (!function_exists('base_path'))
{
    function base_path($path)
    {
        return "../../".$path;
    }
}

if (!function_exists('abs_path'))
{
    function abs_path($path)
    {
        return $_SERVER['DOCUMENT_ROOT']."/../../".$path;
    }
}

if (!function_exists('route_is'))
{
    function route_is($test)
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        
        if (gettype($test) == "string")
        {
            $aux = explode("?", $uri);
            $route = $aux[0];
            return $route == $test;
        }

        else if (gettype($test) == "array")
        {
            return array_some($test, function($test) use ($uri) {
                $aux = explode("?", $uri);
                $route = $aux[0];
                return $route == $test;
            });
        }
    }
}

if (!function_exists('route_starts_with'))
{
    function route_starts_with($input)
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $aux = explode("?", $uri);
        $route = $aux[0];

        if (gettype($input) == 'array')
        {
            foreach ($input as $input_)
            {
                $start = substr($route, 0, strlen($input_));
                if ($start == $input_) return true;
            }
            return false;

        }

        if (gettype($input) == 'string')
        {
            $start = substr($route, 0, strlen($input));
            return $start == $input;
        }
    }
}

if (!function_exists('get_current_route'))
{
    function get_current_route()
    {
        $url = site_url() . $_SERVER['REQUEST_URI'] ?? '';
      
        $aux = explode("?", $url);
        $route = $aux[0];
        return $route;
    }
}

if (!function_exists('content_type'))
{
    function content_type()
    {
        return $_SERVER['CONTENT_TYPE'] ?? '';
    }
}

if (!function_exists('client_name'))
{
    function client_name()
    {
        return (
            getallheaders()['Client-Name'] ?? ''
        );
    }
}

if (!function_exists('get_header'))
{
    function get_header($key)
    {
        return getallheaders()[$key] ?? '';
    }
}

if (!function_exists('aes256_encode'))
{
    function aes256_encode($message, $password)
    {
        $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($message, $cipher, $password, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $password, $as_binary=true);
        $ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);
        return $ciphertext;
    }
}

if (!function_exists('aes256_decode'))
{
    function aes256_decode($hash, $password)
    {
        $c = base64_decode($hash);
        $ivlen = openssl_cipher_iv_length($cipher="AES-256-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $password, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $password, $as_binary=true);
        return $hmac && $calcmac && hash_equals($hmac, $calcmac) ? $original_plaintext : "";
    }
}

/**
 * @deprecated
 * @version 1.0
 */
// if (!function_exists('subdomain'))
// {
//     function subdomain()
//     {
//         $host = $_SERVER['HTTP_HOST'] ?? '';
//         $aux = explode('.'.env('HOST'), $host);
//         $subdomain = sizeof($aux) > 1 ? $aux[0] : '';
//         return $subdomain;
//     }
// }

/**
 * @version 1.1
 */
if (!function_exists('subdomain'))
{
    function subdomain()
    {
        return extract_subdomain($_SERVER["HTTP_HOST"]);
    }
}

/**
 * @version 1.1
 */
if (!function_exists('subdomain_translated'))
{
    function subdomain_translated()
    {
        $subdomain = strtolower(
            array_keys(
                env_find_by_value(subdomain()) ?? []
            )[0] ?? ''
        );
        $subdomain = str_replace("subdomain_", "", $subdomain);
        return $subdomain;
    }
}

if (!function_exists('env_find_by_value'))
{
    function env_find_by_value($search)
    {
        $search = trim($search);
        $env = file(base_path('.env'));
        $result = null;
        foreach ($env as $line)
        {
            $key = explode("=", $line)[0];
            $has_value = !empty(explode("=", $line)[1] ?? false);
            if (!$has_value) continue;
            $value = trim(substr($line, strlen($key) + 1, strlen($line)));
            
            if ($search == $value)
            {
                $result = [$key => $value];
                break;
            }
        }
    
        return $result;
    }
}

if (!function_exists('env_subdomains'))
{
    function env_subdomains()
    {
        $env = file(base_path('.env'));
        $result = [];
        foreach ($env as $line)
        {
            $key = explode("=", $line)[0];
            $has_value = !empty(explode("=", $line)[1] ?? false);
            if (!$has_value) continue;
            $value = trim(substr($line, strlen($key) + 1, strlen($line)));
            
            if (str_starts_with($key, "SUBDOMAIN_"))
                $result[] = [$key => $value];
        }
    
        return $result;
    }
}

// function extract_domain($host)
// {
//     $host = strtolower(trim($host)); // converte para minusculo e tira os espacos do comeco e fim
//     $host = str_replace("http://", "", str_replace("https://", "", $host)); // retira os protocolos
//     $host = ltrim($host, "www."); // retira o subdominio www
//     $count = substr_count($host, '.'); // conta quantos pontos tem na string
    
//     // se nao tem ponto (localhost)
//     if ($count === 0)
//     {
        
//     }

//     // se tem apenas 1 ponto (site.com)
//     else if ($count === 1)
//     {

//     }

//     // se tem 2 pontos (web[.]site[.]com)
//     else if ($count === 2)
//     {
//         // se o segundo item da lista separada por ponto tiver o comprimento de caracteres maior que 3.
//         // if (strlen(explode('.', $host)[1]) > 3)
//         // {
//         //     $host = explode('.', $host, 2)[1]; // splita em 2 parte pelo ponto e pega a parte da direita
//         // }
//     }

//     // // se tem mais de 2 pontos (sub2[.]sub1[.]site[.]com, sub3[.]sub2[.]sub1[.]site[.]com)
//     else if ($count > 2)
//     {
//         // splita em 2 partes pelo ponto e fica com o lado da direita, o dominio
//         $host = explode('.', $host, 2)[1];

//         // repete a execucao dessa funcao ate limpar todos os pontos ate ficar com 2 pontos
//         $host = extract_domain($host);
//     }

//     return $host;

//     // $host = explode('/', $host);
//     // return $host[0];
// }

/**
 * @deprecated
 * @version 1.0
 */ 
// if (!function_exists('site_url'))
// {
//     function site_url()
//     {
//         return env('URL');
//     }
// }

/**
 * url do site principal no dominio da rocketpays ou no dominio mapeado do usuario
 * https://rocketpays.app
 * https://userdomain1.com
 * https://userdomain1.com.br
 * 
 * @version 1.1
 */
if (!function_exists('site_url'))
{
    function site_url()
    {
        $protocol = "http://";
        $host = "$_SERVER[HTTP_HOST]";
        $url = "$protocol$host";
        $subdomain_index = env('SUBDOMAIN_INDEX');
        $domain = extract_domain($url);
        $return = $subdomain_index ? $url : "$protocol$domain";
        return $return;
        // return "$protocol$domain";
    }
}

if (!function_exists('site_url_base'))
{
    function site_url_base()
    {
        $protocol = "$_SERVER[PROTOCOL]://";
        $host = "$_SERVER[HTTP_HOST]";
        $url = "$protocol$host";
        $subdomain_index = env('SUBDOMAIN_INDEX');
        $domain = extract_domain($url);
        $subdomain = str_replace($domain, "", $host);
        $real_host = str_replace($subdomain, $subdomain_index ? "$subdomain_index." : "", $host);
        $return = $protocol.$real_host;
        return $return;
    }
}

if (!function_exists('site_name'))
{
    function site_name()
    {
        return env('APP_NAME');
    }
}

/**
 * using jeremykendall/php-domain-parser
 * 
 * @param string $initial_domain
 * @return string
 */
function extract_domain($initial_domain)
{
    $initial_domain = str_replace("http://", "", $initial_domain);
    $initial_domain = str_replace("https://", "", $initial_domain);
    $aux = explode(":", $initial_domain);
    $initial_domain = $aux[0] ?? '';
    $port = $aux[1] ?? '';
    $portstr = ($port?":$port":"");
    $publicSuffixList = $GLOBALS['domainPublicSuffixList'];
    $end = array_reverse(explode(".", $initial_domain))[0] ?? '';
    if ($end == "localhost") return $end.$portstr;
    $domain = Domain::fromIDNA2008($initial_domain);
    $result = $publicSuffixList->resolve($domain);
    return $result->registrableDomain()->toString().$portstr;
}

/**
 * using jeremykendall/php-domain-parser
 * 
 * @param string $initial_domain
 * @return string
 */
function extract_subdomain($initial_domain)
{
    $initial_domain = str_replace("http://", "", $initial_domain);
    $initial_domain = str_replace("https://", "", $initial_domain);
    $aux = explode(":", $initial_domain);
    $initial_domain = $aux[0] ?? '';
    $port = $aux[1] ?? '';
    $portstr = ($port?":$port":"");
    $publicSuffixList = $GLOBALS['domainPublicSuffixList'];
    $end = array_reverse(explode(".", $initial_domain))[0] ?? '';
    if ($end == "localhost") return rtrim(str_replace($end, "", $initial_domain), ".");
    $domain = Domain::fromIDNA2008($initial_domain);
    $result = $publicSuffixList->resolve($domain);
    $main_domain = $result->registrableDomain()->toString();
    return rtrim(str_replace($main_domain, "", $initial_domain), ".");
}

/**
 * Esse host considera a porta
 * ex.: site.com:5012
 */
if (!function_exists('site_host'))
{
    function site_host()
    {
        $port = $_SERVER["SERVER_PORT"];
        $portstr = $port <> "80" && $port <> "443" ? ":$port" : "";
        return extract_domain("$_SERVER[HTTP_HOST]$portstr");
    }
}

if (!function_exists('site_protocol'))
{
    function site_protocol()
    {
        return env("PROTOCOL");
    }
}

if (!function_exists('current_url'))
{
    function current_url()
    {
        return "$_SERVER[PROTOCOL]://".site_host();
    }
}

if (!function_exists('full_url'))
{
    function full_url()
    {
        return "$_SERVER[PROTOCOL]://".site_host().$_SERVER['REQUEST_URI'];
    }
}

if (!function_exists('translate_subdomain_name'))
{
    function translate_subdomain_name($subdomain_name)
    {
        return env('SUBDOMAIN_'.strtoupper($subdomain_name)) ?: $subdomain_name;
    }
}

if (!function_exists('get_subdomain_serialized'))
{
    function get_subdomain_serialized($subdomain)
    {
        $subdomain = translate_subdomain_name($subdomain);
        return "$_SERVER[PROTOCOL]://$subdomain.".site_host();
    }
}

if (!function_exists('pascal_case'))
{
    function pascal_case($string)
    {
        $words = preg_split("/[^a-zA-Z0-9]/", $string);
        return join("", array_map(fn($word) => ucwords($word), $words));
    }
}

function in_debug()
{
    return env('DEBUG') == 'true';
}

if (!function_exists('authenticated'))
{
    function authenticated()
    {
        return !empty(user());
    }
}

if (!function_exists('user'))
{
    function user()
    {
        return User::where('access_token', access_token())->with('bank_account')->first();
    }
}

if (!function_exists('customer'))
{
    function customer()
    {
        return Customer::where('access_token', c_access_token())->first();
    }
}

if (!function_exists('admin'))
{
    function admin()
    {
        return Administrator::where('access_token', a_access_token())->first();
    }
}

if (!function_exists('access_token'))
{
    function access_token()
    {
        return $_SESSION[env('USER_AUTH_KEY')] ?? '';
    }
}

if (!function_exists('c_access_token'))
{
    function c_access_token()
    {
        return $_SESSION[env('CUSTOMER_AUTH_KEY')] ?? '';
    }
}

if (!function_exists('a_access_token'))
{
    function a_access_token()
    {
        return $_SESSION[env('ADMIN_AUTH_KEY')] ?? '';
    }
}

if (!function_exists('session_customer'))
{
    function session_customer()
    {
        return $_SESSION['customer'] ?? '';
    }
}

if (!function_exists('session_user'))
{
    function session_user()
    {
        return $_SESSION['user'] ?? '';
    }
}

if (!function_exists('array_to_header'))
{
    function array_to_header($headers)
    {
        return array_map(fn ($name, $value) => "{$name}: {$value}", array_keys($headers), array_values($headers));
    }
}

if (!function_exists('hash_make'))
{
    function hash_make($password)
    {
        $salt = "w4cgXiFAICAgICA)*{}gIC0+ICVhI/DB4M@#DAwMDAwMGNmI.DAwMDE%!@#¨&*gMDAxMCAw_-MDExIDAx    MDAgMDEwMSAwMTEwIDAxMTEgw6cgYHshQ !$".
        "(_A== 0x57686174206973204c6f72656d20497073756d3f0d0a4c6f72656d20497073756d2069732073696d706c792064756d6d792074657874206f6620746865".
        "207072696e74696e6720616e64207479706573657474696e6720696e6475737472792e204c6f72656d20497073756d20686173206265656e2074686520696e6475".
        "737472792773207374616e646172642064756d6d79207465787420657665722073696e6365207468652031353030732c207768656e20616e20756e6b6e6f776e20".
        "7072696e74657220746f6f6b20612067616c6c6579206f66207479706520616e6420736372616d626c656420697420746f206d616b652061207479706520737065".
        "63696d656e20626f6f6b2e20497420686173207375727669766564206e6f74206f6e6c7920666976652063656e7475726965732c2062757420616c736f20746865".
        "206c65617020696e746f20656c656374726f6e6963207479706573657474696e672c2072656d61696e696e6720657373656e7469616c6c7920756e6368616e6765".
        "636f6e7461696e696e67204c6f72656d20497073756d2070617373616765732c20616e64206d6f726520726563656e746c792077697468206465736b746f702070".
        "75626c697368696e6720736f667477617265206c696b6520416c64757320506167654d616b657220696e636c7564696e672076657273696f6e73206f66204c6f72".
        "656d20497073756d2e0d0a0d0a57687920646f207765207573652069743f0d0a49742069732061206c6f6e672065737461626c6973686564206661637420746861".
        "742061207265616465722077696c6c206265206469737472616374656420627920746865207265616461626c6520636f6e74656e74206f66206120706167652077".
        "68656e206c6f6f6b696e6720617420697473206c61796f75742e2054686520706f696e74206f66207573696e67204c6f72656d20497073756d2069732074686174".
        "206974206861732061206d6f72652d6f722d6c657373206e6f726d616c20646973747269627574696f6e206f66206c6574746572732c206173206f70706f736564".
        "0746f207573696e672027436f6e74656e7420686572652c20636f6e74656e742068657265272c206d616b696e67206974206c6f6f6b206c696b652072656164616".
        "6c6520456e676c6973682e204d616e79206465736b746f70207075626c697368696e67207061636b6167657320616e6420776562207061676520656469746f7273";
        return hash('haval256,4', sha1(md5($salt.$password)));
    }
}

if (!function_exists('hash_uniq'))
{
    function hash_uniq()
    {
        return hash_make(rand(0,99999999999).uniqid().microtime().base64_decode('w4cgXiFAICAgICAgIC0+ICVhIDB4MDAwMDAwMGNmIDAwMDEgMDAxMCAwMDExIDAxMDAgMDEwMSAwMTEwIDAxMTEgw6cgYHshQA=='));
    }
}

/**
 * Length: 124
 */
if (!function_exists('ghash'))
{
    function ghash()
    {
        return base64_encode(uniqid().sha1(rand(0,99999).microtime().md5(time()*2.129887*pi())).sha1(rand(57778,135858).'^$@#$ 03aa32$f234'));
    }
}

/**
* Encrypt value to a cryptojs compatiable json encoding string
*
* @param mixed $passphrase
* @param mixed $value
* @return string
*/
function cryptoJsAesEncrypt($passphrase, $value){
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx.$passphrase.$salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32,16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}

function aes_encode($password, $data)
{
    return base64_encode(cryptoJsAesEncrypt($password, $data));
}

function aes_encode_db($data)
{
    return aes_encode(env('AES_DB'), $data);
}

/**
* Decrypt data from a CryptoJS json encoding string
*
* @param mixed $passphrase
* @param mixed $jsonString
* @return mixed
*/
function cryptoJsAesDecrypt($passphrase, $jsonString){
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}

function aes_decode($password, $base64_hash)
{
    return cryptoJsAesDecrypt($password, base64_decode($base64_hash));
}

function aes_decode_db($data)
{
    return aes_decode(env('AES_DB'), $data);
}

if (!function_exists('get_host'))
{
    function get_host()
    {
        return env('HOST2') ?: env('HOST');
    }
}

if (!function_exists('ajax_url'))
{
    function ajax_url()
    {
        $host = get_host();
        $protocol = env('PROTOCOL');
        return "$protocol://$host";
    }
}

/**
 * Autentica o usuário
 */
if (!function_exists('authenticate'))
{
    function authenticate($access_token)
    {
        $_SESSION[env('USER_AUTH_KEY')] = $access_token;
        $_SESSION['user'] = User::where('access_token', $access_token)->first();
    }
}

/**
 * Autentica o cliente no subdominio purchase
 */
if (!function_exists('c_authenticate'))
{
    function c_authenticate($access_token)
    {
        $_SESSION[env('CUSTOMER_AUTH_KEY')] = $access_token;
        $_SESSION['customer'] = Customer::where('access_token', $access_token)->first();
    }
}

/**
 * Desloga cliente
 */
if (!function_exists('c_logout'))
{
    function c_logout()
    {
        unset($_SESSION[env('CUSTOMER_AUTH_KEY')]);
        unset($_SESSION['customer']);
    }
}

/**
 * Desloga usuario
 */
if (!function_exists('logout'))
{
    function logout()
    {
        unset($_SESSION[env('USER_AUTH_KEY')]);
        unset($_SESSION['user']);
    }
}

/**
 * Desloga usuario
 */
if (!function_exists('a_logout'))
{
    function a_logout()
    {
        unset($_SESSION[env('ADMIN_AUTH_KEY')]);
        unset($_SESSION['admin']);
    }
}

/**
 * Autenticar customer na URL principal (sem subdominio)
 */
if (!function_exists('auth_customer'))
{
    function auth_customer($access_token)
    {
        $site = get_subdomain_serialized("checkout");
        $payload = compact('access_token');
        $url = "$site/ajax/auth/customer";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);        
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response ?: '{}');
    }
}

/**
 * Customer Authentication Global
 */
if (!function_exists('c_authenticate_g'))
{
    function c_authenticate_g($access_token)
    {
        $response = auth_customer($access_token);
        // print_r($response);

        // $c_access_token = $response->session->c_access_token ?? '';
        // $customer = $response->session->customer ?? '';
        // $c_session = compact('c_access_token', 'customer');
        // $_SESSION = !empty($_SESSION) ? $_SESSION + $c_session : $c_session;

        // return $c_session;
    }
}

if (!function_exists('a_authenticate'))
{
    function a_authenticate($access_token)
    {
        $_SESSION[env('ADMIN_AUTH_KEY')] = $access_token;
        $_SESSION['admin'] = Administrator::where('access_token', $access_token)->first();
    }
}

if (!function_exists('remove_html_comments'))
{
    function remove_html_comments($content_html)
    {
        $content_html = preg_replace("~<!--(.*?)-->~s", '', $content_html);
        $content_html = preg_replace("~\/\*(.*?)\*\/~s", '', $content_html);
        return $content_html;
    }
}

if (!function_exists('get_ordermeta'))
{
    function get_ordermeta($order_id, $property)
    {
        return OrderMeta::where('order_id', $order_id)->where('name', $property)?->first()?->value ?? '';
    }
}

if (!function_exists('add_ordermeta'))
{
    function add_ordermeta($order_id, $name, $value)
    {   
        $ordermeta = OrderMeta::where('order_id', $order_id)->where('name', $name)->first();        

        if (empty($ordermeta))
            $ordermeta = new OrderMeta;

        $ordermeta->order_id = $order_id;
        $ordermeta->name = $name;
        $ordermeta->value = $value;
        $ordermeta->save();
    }
}

if (!function_exists('today'))
{
    function today()
    {
        return date('Y-m-d H:i:s');
    }
}

if (!function_exists('date_br'))
{
    function date_br($datetime)
    {
        if (!$datetime) $datetime = "0000-00-00 00:00:00";
        $aux = explode(" ", $datetime);
        $date = $aux[0];
        $time = $aux[1];        
        $date = join("/", array_reverse(explode("-", $date)));
        $aux2 = explode(":", $time);
        $hour = $aux2[0];
        $min = $aux2[1];
        return "$date às $hour:{$min}h";
    }
}

/**
 * Pega porcentagem de parcelamento do banco de dados e converte porcentagem de 0 a 100 para porcentagem de 0 a 1
 * 
 * @param float $qty 
 * @return float
 */
if (!function_exists('insx'))
{
    function insx(Float $qty) : Float
    {
        return ($percent = Setting::where('name', "installment_{$qty}x")?->first()?->value) ? $percent /= 100 : 0;
    }
}

if (!function_exists('get_setting'))
{
    function get_setting($name)
    {
        return Setting::where('name', $name)->first()->value ?? '';
    }
}

if (!function_exists('set_setting'))
{
    function set_setting($name, $value=null)
    {
        $setting = new Setting;
        $setting->name = $name;
        $setting->value = $value;
        $setting->save();
        return $setting;
    }
}

if (!function_exists('update_setting'))
{
    function update_setting($name, $value=null)
    {
        $setting = Setting::where('name', $name)->first();

        if (empty($setting))
        {
            $setting = new Setting;
            $setting->name = $name;
        }
        
        $setting->value = $value;
        $setting->save();

        return $setting;
    }
}

if (!function_exists('balance'))
{
    function balance($user_id=null)
    {
        if (!$user_id) $user_id = user()->id;
        return Balance::where('user_id', $user_id)->first();
    }
}

if (!function_exists('bank_account'))
{
    function bank_account($user_id=null)
    {
        if (!$user_id) $user_id = user()->id;
        return BankAccount::with('bank')->with('user')->where('user_id', $user_id)->first();
    }
}

if (!function_exists('banks'))
{
    function banks()
    {
        return Bank::all();
    }
}

if (!function_exists('bank'))
{
    function bank($id)
    {
        return Bank::find($id);
    }
}

if (!function_exists('iugu_banks'))
{
    function iugu_banks()
    {
        return IuguBank::all();
    }
}

if (!function_exists('themes'))
{
    function themes()
    {
        return CheckoutTheme::all();
    }
}

if (!function_exists('get_session'))
{
    function get_session($entity="", $property="")
    {
        if (empty($_SESSION[$entity])) $_SESSION[$entity] = '{}';
        if (empty($property)) return $_SESSION[$entity];
        $object = json_decode($_SESSION[$entity] ?? '{}');
        return $object?->$property ?? '';
    }
}

if (!function_exists('put_session'))
{
    function put_session($entity="", $property="", $value="")
    {
        if (empty($_SESSION[$entity])) $_SESSION[$entity] = '{}';
        $object = json_decode($_SESSION[$entity]);
        $object->$property = $value;
        $_SESSION[$entity] = json_encode($object);
        return $_SESSION[$entity];
    }
}

if (!function_exists('delete_session'))
{
    function delete_session($entity="", $property="")
    {
        if (empty($_SESSION[$entity])) $_SESSION[$entity] = '{}';
        $object = json_decode($_SESSION[$entity]);
        if (!is_null($object?->$property)) $object->$property = '';
        $_SESSION[$entity] = json_encode($object);
        return $_SESSION[$entity];
    }
}

if (!function_exists('drop_session'))
{
    function drop_session($entity)
    {
        unset($_SESSION[$entity]);
    }
}

if (!function_exists('currency'))
{
    function currency($number)
    {
        $n = is_numeric($number)
            ? $number
            : 0;
        return number_format(doubleval($n), 2, ',', '.');
    }
}

if (!function_exists('currencyk'))
{
    function currencyk($number)
    {
        if ($number >= 1000)
        {
            $rounded = round($number);
            $arr = explode(',', number_format($rounded));
            $letters = array('k', 'M', 'B', 'T');
            $parts = count($arr) - 1;
    
            return $arr[0] . ((int) $arr[1][0] !== 0 ? ',' . $arr[1][0] : '') . $letters[$parts - 1];
        }
    
        return $number;
    }
}

if (!function_exists('number_to_currency_by_symbol'))
{
    function number_to_currency_by_symbol($number, $symbol='usd')
    {
        $number = (float) $number;

        if (in_array($symbol, ['brl']))
            return number_format($number, 2, ',', '.');
        return number_format($number, 2);
    }
}

if (!function_exists('slugify'))
{
    function slugify($text="", $divider='_')
    {
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, $divider);
        $text = preg_replace('~-+~', $divider, $text);
        $text = strtolower($text);
        $text = str_replace('/', '', $text);
        $text = str_replace('\\', '', $text);
        $text = str_replace(':', '', $text);
        return $text;
    }
}

if (!function_exists('safe_filename'))
{
    function safe_filename($name="")
    {
        $aux = explode(".", $name);
        $str = '';

        if (sizeof($aux) > 1)
        {
            $ext = end($aux);
            $filename = substr($name, 0, strpos($name, $ext));
            $filename = slugify($filename);
            $str = "$filename.$ext";
        }

        else 
        {
            $str = $name;
        }

        return $str;
    }
}

if (!function_exists('request_method'))
{
    function request_method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? '';
    }
}

if (!function_exists('__'))
{
    function __($text, $vars=[])
    {
        return strtr($GLOBALS['translate']->$text ?? $text, $vars);
    }
}

if (!function_exists('lang'))
{
    function lang($lang, $text, $vars=[])
    {
        if (!$lang) $lang = 'en_US'; 
        $json = json_decode(join("\n", file(base_path("lang/".$lang.".json"))));
        return strtr($json->$text ?? $text, $vars);
    }
}

if (!function_exists('uuid'))
{
    function uuid($data=null)
    {
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

/**
 * Retira a tipagem do objeto
 */
if (!function_exists('typeoff'))
{
    function typeoff($object)
    {
        return json_decode(json_encode($object));
    }
}

if (!function_exists('cur2int'))
{
    /**
     * @param float $price  Decimal de 2 digitos
     * @return void
     */
    function cur2int(float $price)
    {
        $price = strval($price);
        $aux = explode(".", $price);
        $left = $aux[0] ?? '';
        $right = $aux[1] ?? '00';
        $right = substr($right, 0, 2);
        if (strlen($right) == 0) $right = "00";
        else if (strlen($right) == 1) $right = $right."0";
        $price = "$left$right";
        $price = preg_replace("/\D/", "", $price);
        return $price;
    }
}

if (!function_exists('text_etc'))
{
    function text_etc(string $words, int $limit = 35, string $etc = '...'): string
    {
        return trim(substr($words, 0, $limit)) . (strlen($words) > $limit ? $etc : '');
    }
}

if (!function_exists('debug_html'))
{
    function debug_html($content, $die=true)
    {
        echo '<pre>';
        print_r($content);
        if ($die) die();
        echo '</pre>';
    }
}

function sanitize($string)
{
    $string = trim($string);
    $string = str_replace('\'', '\\\'', $string);
    return $string;
}

if (!function_exists('cmp_both_valid'))
{

    /**
     * Compara os dois valores (apenas se ambos existirem)
     * 
     * Ex.:
     * ```php
     * <?php echo cmp_both_valid(5, '<=', 6) ? 'verdadeiro' : 'falso';
     * ```
     */
    function cmp_both_valid($arg1, $cmp, $arg2)
    {
        if (!empty($arg1) && !empty($arg2))
        {
            switch ($cmp)
            {
                case '=':
                case '==':
                    return $arg1 == $arg2;

                case '===':
                    return $arg1 === $arg2;
                
                case '<>':
                case '!=':
                    return $arg1 <> $arg2;

                case '!==':
                    return $arg1 !== $arg2;
                    
                case '<':
                    return $arg1 < $arg2;
                    
                case '>':
                    return $arg1 > $arg2;
                    
                case '<=':
                    return $arg1 <= $arg2;
                    
                case '>=':
                    return $arg1 >= $arg2;
                
                // ...
            }
        }

        return false;
    }
}

if (!function_exists('checkout_slug_exists'))
{
    /**
     * Checa se o slug do plano nao coincide com outros tipos de slug de um checkout.
     * 
     * Ex.: http://checkout.site.com/ABCDEF123456/slug-aqui
     * 
     * O primeiro path param eh o sku do checkout ou produto e o segundo representa alguma variacao no checkout.
     *
     * @param int $product_id
     * @param mixed $slug
     * @return bool
     */
    function checkout_slug_exists(int $product_id, mixed $slug) : bool
    {
        return ProductLink::where('slug', $slug)->where('product_id', $product_id)->exists();
    }
}

if (!function_exists('purchased_products'))
{
    function purchased_products($order)
    {
        $meta_products = OrderMeta::where('order_id', $order->id)->where('name', 'product_id')->get();            // padrao
        $meta_orderbumps = OrderMeta::where('order_id', $order->id)->where('name', 'orderbump_items')->first();   // extra

        $product_names = [];    // nome de todos os produtos comprados
        $products = [];         // todos os produtos comprados
        $products_base = [];    // produto comprado (sem contar os extras adicionados no carrinho)
        foreach ($meta_products as $meta_product)
        {
            $product = Product::find($meta_product->value);
            if (!empty($product)) 
            {
                $product_names[] = $product->name;
                $products[] = $product;
                $products_base[] = $product;
            }
        }

        $meta_orderbumps = json_decode($meta_orderbumps->value ?? '[]');
        foreach ($meta_orderbumps as $orderbump)
        {
            $product = Product::find($orderbump->product_id);
            if (!empty($product)) 
            {
                $product_names[] = $product->name;
                $products[] = $product;
            }
        }

        return compact('product_names', 'products', 'products_base');
    }
}

if (!function_exists('objval'))
{
    function objval(array $array)
    {
        return (object) $array;
    }
}

if (!function_exists('query_string'))
{
    function query_string($url, $s)
    {
        $result = "";
        $aux = explode("?", $url);
        if (isset($aux[1]))
        {
            $query_string = $aux[1];
            $params = explode("&", $query_string);
            foreach ($params as $item)
            {
                $aux1 = explode("=", $item);
                $key = $aux1[0];
                $value = substr($item, strlen($key) + 1);
                if ($key == $s)
                {
                    $result = $value;
                    break;
                }
            }
        }
        return $result;
    }
}

if (!function_exists('query_string_replace'))
{
    /**
     * Substitui um parâmetro na query string
     *
     * @param string $url
     * @param mixed $s
     * @param mixed $v
     * @return string
     */
    function query_string_replace(string $url, $s, $v)
    {
        // replace query string
        $result = $url;
        $aux = explode("?", $url);
        if (isset($aux[1]))
        {
            $query_string = $aux[1];
            $params = explode("&", $query_string);
            $p = "";
            $n = 0;
            foreach ($params as $item)
            {
                $aux1 = explode("=", $item);
                $key = $aux1[0];
                $value = substr($item, strlen($key) + 1);
                if ($key == $s)
                {
                    $value = $v;
                }
                if ($n > 0) $p .= "&";
                $p .= "$key=$value";
                $n++;
            }
            $result = $aux[0]."?".$p;
        }
        return $result;
    }
}

if (!function_exists('mask'))
{
    /**
     * Cria uma mascara
     * 
     * ```php
     * <?php
     * echo mask('12345678909', '###.###.###-##'); // CPF
     * // 123.456.789-09
     * echo mask('12345678909123', '##.###.###/####-##'); // CNPJ
     * // '12.345.678/9091-23'
     * ```
     *
     * @param string $val
     * @param string $mask
     * @return string
     */
    function mask(string $val, string $mask): string
    {
        $maskared = '';
        $k = 0;

        for($i = 0; $i <= strlen($mask) - 1; $i++)
        {
            if($mask[$i] == '#')
            {
                if(isset($val[$k])) $maskared .= $val[$k++];
            }
            
            else
            {
                if(isset($mask[$i])) $maskared .= $mask[$i];
            }
        }

        return $maskared;
    }
}


if (!function_exists('minify_css'))
{
    function minify_css($css)
    {
        $css = str_replace("  ", "", $css);
        $css = preg_replace("/\n|\r|\t/", "", $css);
        return $css;
    }
}

if (!function_exists('minify_js'))
{
    function minify_js($js)
    {
        return JSMin::minify($js);
    }
}

if (!function_exists('send_email'))
{
    /**
     * Envia e-mail utilizando um template
     *
     * @param string $email Destino
     * @param array $data Dados para preencher as variaveis
     * @param EEmailTemplatePath $template Identificador do template
     * @param string|null $lang
     * @return void
     */
    function send_email(string $email, array $data, EEmailTemplatePath $template, ?string $lang='pt_BR')
    {
        if (!$email) return;

        // $email_template = EmailTemplate::where('type', $template)->first();
        // if (!$email_template) return;

        $email_message = new EmailMessage;
        $email_message->data = json_encode($data);
        // $email_message->email_template_id = $email_template->id;
        $email_message->email_template_path = $template;
        $email_message->lang = $lang;
        $email_message->save();

        $email_queue = new EmailQueue;
        $email_queue->email_message_id = $email_message->id;
        $email_queue->email = $email;
        $email_queue->current_attempt = 1;
        $email_queue->smtp_id = Smtp::first()->id;
        $email_queue->save();
    }
}

if (!function_exists('next_smtp'))
{
    function next_smtp(int $smtp_id, bool $infinity=false): ?int
    {
        $current = Smtp::where('id', $smtp_id)->first();
        if (!$current) return Smtp::first()->id ?? null;
        $next = Smtp::where('id', '>', $current->id)->first();
        if (!$next && $infinity) return next_smtp(0);
        return $next->id ?? null;
    }
}

if (!function_exists('retry_email'))
{
    /**
     * Replaces the email in the sending queue to try to send
     * with another SMTP server.
     *
     * @param EmailQueue $eq Failed send object
     * @param ?string $scheduled_at Date of appointment
     * @return EmailQueue Object of the new send
     */
    function retry_email(EmailQueue $eq, $scheduled_at=null): ?EmailQueue
    {
        $limit = Smtp::count();
        if ($eq->current_attempt >= $limit) return null;

        $email_queue = new EmailQueue;
        $email_queue->email_message_id = $eq->email_message_id;
        $email_queue->email = $eq->email;
        $email_queue->current_attempt = $eq->current_attempt + 1;
        $email_queue->smtp_id = next_smtp($eq->smtp_id);
        $email_queue->scheduled_at = $scheduled_at ?: today();
        $email_queue->reference = $eq->id;
        $email_queue->save();
        return $email_queue;
    }
}

if (!function_exists('currency_code_to_symbol'))
{
    /**
     * Convert currency code in symbol.
     * 
     * Eg.: USD to $.
     *
     * @param ?string $code
     * @return ECurrencySymbol
     */
    function currency_code_to_symbol(?string $code): ECurrencySymbol
    {
        if (!$code) return ECurrencySymbol::USD;
        return ECurrencySymbol::cases()[
            array_search(strtoupper($code), array_column(ECurrencySymbol::cases(), "name"))
        ];
    }
}

if (!function_exists('languages'))
{
    function languages($only_enabled)
    {
        if ($only_enabled) return Language::where('enabled', 1)->get();
        return Language::all();
    }
}

if (!function_exists('check_testmode_key'))
{
    function check_testmode_key($testmode_key)
    {
        return strlen($testmode_key) > 0 && strlen(env('STRIPE_TESTMODE_KEY')) > 0 && $testmode_key == env('STRIPE_TESTMODE_KEY');
    }
}

if (!function_exists('stripe_secret'))
{
    function stripe_secret($testmode_key='')
    {
        return env(check_testmode_key($testmode_key) ? 'STRIPE_SECRET_TEST' : 'STRIPE_SECRET');
    }
}

if (!function_exists('stripe_public'))
{
    function stripe_public($testmode_key)
    {
        return env(check_testmode_key($testmode_key) ? 'STRIPE_PUBKEY_TEST' : 'STRIPE_PUBKEY');
    }
}

if (!function_exists('get_recurrence_interval'))
{
    function get_recurrence_interval($recurrence_interval, $recurrence_interval_count)
    {
        if ($recurrence_interval == 'day' && $recurrence_interval_count == 1) return 'day';
        if ($recurrence_interval == 'week' && $recurrence_interval_count == 1) return 'week';
        if ($recurrence_interval == 'week' && $recurrence_interval_count == 2) return 'fortnight';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 1) return 'month';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 2) return 'bimester';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 3) return 'quarter';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 6) return 'semester';
        elseif ($recurrence_interval == 'year' && $recurrence_interval_count == 1) return 'year';
        return '';
    }
}

if (!function_exists('get_recurrence_interval_ly'))
{
    function get_recurrence_interval_ly($recurrence_interval, $recurrence_interval_count)
    {
        if ($recurrence_interval == 'day' && $recurrence_interval_count == 1) return 'daily';
        if ($recurrence_interval == 'week' && $recurrence_interval_count == 1) return 'weekly';
        if ($recurrence_interval == 'week' && $recurrence_interval_count == 2) return 'fortnightly';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 1) return 'monthly';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 2) return 'twomonthly';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 3) return 'quarterly';
        elseif ($recurrence_interval == 'month' && $recurrence_interval_count == 6) return 'semmiannualy';
        elseif ($recurrence_interval == 'year' && $recurrence_interval_count == 1) return 'annualy';
        return '';
    }
}

if (!function_exists('empty_paged_collection'))
{
    function empty_paged_collection(LengthAwarePaginator $collection): bool
    {
        return (json_decode(json_encode($collection))->total ?? 0) == 0;
    }
}


if (!function_exists('bincheck'))
{
    function bincheck(string $number=''): string
    {
        $flag_regexps = [
            'electron' => '/^(4026|417500|4405|4508|4844|4913|4917)\d+$/',
            'maestro' => '/^(5018|5020|5038|5612|5893|6304|6759|6761|6762|6763|0604|6390)\d+$/',
            'dankort' => '/^(5019)\d+$/',
            'interpayment' => '/^(636)\d+$/',
            'unionpay' => '/^(62|88)\d+$/',
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/'
        ];
    
        foreach ($flag_regexps as $flag => $reg)
            if (preg_match($reg, $number))
                return $flag;

        return '';
    }
}