<?php 

namespace Backend\Controllers\Cronjobs;
use Backend\Http\Response;

class CurrencyController
{
    public function client($from, $to)
    {
        header("Content-Type: application/json");
        $url = "https://min-api.cryptocompare.com/data/price?api_key=e5fc33229506a4b529dbed88a3aa5b0a814b706da5350d324a7f0099cd7f9b17&fsym=$from&tsyms=$to";
        $json = json_decode(file_get_contents($url));
        $brl = floatval($json->BRL ?? 0);
        return $brl;

        // $stripe_brl = (float) number_format($brl - 0.13553, 2); // ajusta para o dolar da stripe

        // update_setting('usd_brl', $stripe_brl);
        // return Response::json(['stripe_brl' => $stripe_brl, 'brl' => $brl]);
    }

    public function update()
    {
        $usd_brl = $this->client('USD', 'BRL');
        $usd_brl = (float) number_format($usd_brl - 0.13553, 2);
        if ($usd_brl > 0) update_setting('usd_brl', $usd_brl);
        
        $eur_brl = $this->client('EUR', 'BRL');
        if ($eur_brl > 0) update_setting('eur_brl', $eur_brl);
    }
}