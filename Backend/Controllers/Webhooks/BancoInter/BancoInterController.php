<?php 

namespace Backend\Controllers\Webhooks\BancoInter;

use Backend\App;
use Backend\Http\Request;
use Ezeksoft\PHPWriteLog\Log;
use Backend\Models\BancoInterRequest;
use Backend\Services\OneSignal\OneSignal;
use Backend\Models\Withdrawal;
use Backend\Models\User;

class BancoInterController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
    }
    
    public function pix(Request $request)
    {
        (new Log)->write(base_path("logs/bancointer.log"), [
            "\$_REQUEST" => $_REQUEST,
            "RAW:" => $request->raw(),
        ]);

        $w_fee = doubleval(get_setting('withdrawal_fee'));

        $json = $request->json();
        $transaction_id = $json[0]->codigoSolicitacao ?? '';

        $bi_request = BancoInterRequest::where('external_transaction_id', $transaction_id)->whereNotNull('external_transaction_id')->first();

        $withdrawal = Withdrawal::find($bi_request->withdrawal_id);
        if (!empty($withdrawal))
        {
            $withdrawal->status = 'approved';
            $withdrawal->save();

            $user = User::find($withdrawal->user_id);
            if (!empty($user))
            {
                /**
                 * Notificação push com OneSignal
                 */    
                $onesignal = new OneSignal;
                $onesignal->setTitle("Saque Realizado!");
                $onesignal->setDescription("Valor: R$ ".currency($withdrawal->amount - $w_fee));
                $onesignal->addExternalUserID($user->email);
                $onesignal->pushNotification();
            }
        }
    }
}