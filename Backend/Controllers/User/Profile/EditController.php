<?php

namespace Backend\Controllers\User\Profile;

use Backend\Http\Request;
use Backend\Template\View;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Exceptions\Profile\InvalidImageException;
use Backend\Http\Response;
use Backend\Models\UserAddress;
use Backend\Models\Kyc;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;
use Backend\Types\User\Address;
use Backend\Types\User\Profile;
use Exception;
use Ezeksoft\PHPWriteLog\Log;

class EditController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Profile';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/profile/editView.php';

    public function view(string $view_method, Request $request, array $params=[])
    {
        extract((array) $this);
        extract($params);

        $kyc = Kyc::where('user_id', $user->id)->first();

        $profile = new Profile([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $kyc->phone ?? '',
            'document' => $kyc->doc ?? ''
        ]);

        $address = new Address([
            'zipcode' => $kyc->zipcode ?? '',
            'street' => $kyc->street ?? '',
            'number' => $kyc->address_no ?? '',
            'neighborhood' => $kyc->neighborhood ?? '',
            'city' => $kyc->city ?? '',
            'state' => $kyc->state ?? '',
        ]);
        
        return View::$view_method($this->indexFile, compact('title', 'context', 'user', 'address', 'profile'));
    }
    
    public function uploadImage(Request $request)
    {
        $user = $this->user;

        $filename = uniqid() . ".png";

        $source = $_FILES['image']['tmp_name'] ?? '';
        $destination = base_path('frontend/public/upload') . "/" . $filename;

        try
        {
            $check = getimagesize($source);
            $mime = $check['mime'] ?? '';
            $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
            if (!$is_image) throw new InvalidImageException;

            move_uploaded_file($source, $destination);

            $user->photo = "/upload/$filename";
            $user->save();

            $response_data = new ResponseData([
                'status' => EResponseDataStatus::SUCCESS, 
                'message' => 'Imagem carregada com sucesso.', 
                'data' => ['image' => $user->photo]
            ]);
            $response_status = new ResponseStatus('200 OK');
        }

        catch (Exception|InvalidImageException)
        {
            $response_data = new ResponseData([
                'status' => EResponseDataStatus::ERROR, 
                'message' => 'Este arquivo não é uma imagem válida.'
            ]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status, true);
    }

    public function editProfile(Request $request)
    {
        $user = $this->user;
        $body = $request->all();
        //(new Log)->write(base_path('logs/request.log'), json_encode($body, JSON_PRETTY_PRINT));

        try {
            $user->name = $body->name ?? $user->name;
            $user->email = $body->email ?? $user->email;
            $user->save();

            $userAddress = UserAddress::firstOrNew(['user_id' => $user->id]);
            $userAddress->street = $body->street ?? $userAddress->street;
            $userAddress->number = $body->number ?? $userAddress->number;
            $userAddress->neighborhood = $body->neighborhood ?? $userAddress->neighborhood;
            $userAddress->city = $body->city ?? $userAddress->city;
            $userAddress->state = $body->state ?? $userAddress->state;
            $userAddress->zipcode = $body->zipcode ?? $userAddress->zipcode;
            $userAddress->save();

            $kyc = Kyc::firstOrNew(['user_id' => $user->id]);

            $kyc->phone = $body->phone ?? $kyc->phone;
            $kyc->doc = $body->document ?? $kyc->doc;
            $kyc->zipcode = $body->zipcode ?? $kyc->zipcode;
            $kyc->street = $body->street ?? $kyc->street;
            $kyc->address_no = $body->number ?? $kyc->address_no;
            $kyc->neighborhood = $body->neighborhood ?? $kyc->neighborhood;
            $kyc->city = $body->city ?? $kyc->city;
            $kyc->state = $body->state ?? $kyc->state;
            $kyc->save();

            $response_data = new ResponseData([
                'status' => EResponseDataStatus::SUCCESS,
                'message' => 'Perfil atualizado com sucesso.',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'address' => [
                        'phone' => $kyc->phone,
                        'document' => $kyc->doc,
                        'zipcode' => $kyc->zipcode,
                        'street' => $kyc->street,
                        'number' => $kyc->address_no,
                        'neighborhood' => $kyc->neighborhood,
                        'city' => $kyc->city,
                        'state' => $kyc->state
                    ]
                ]
            ]);
            $response_status = new ResponseStatus('200 OK');
        }
        catch (Exception $e) {
            $response_data = new ResponseData([
                'status' => EResponseDataStatus::ERROR,
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ]);
            $response_status = new ResponseStatus('400 Bad Request');
        }

        return Response::json($response_data, $response_status, true);
    }
}
