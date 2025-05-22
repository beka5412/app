<?php

namespace Backend\Controllers\User\Kyc;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Http\Link;
use Backend\Template\View;
use Backend\Exceptions\User\{InvalidImageException, UserNotFoundException};
use Backend\Enums\Kyc\{EKycType, EKycStatus};
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Exceptions\BankAccount\EmptyDocException;
use Backend\Exceptions\Kyc\{
    EmptyTypeException,
    EmptyNameException,
    EmptyEmailException,
    DocBackNotFoundException,
    DocBackImageNotFoundException,
    EmptyBirthdateException,
    EmptyPhoneException,
    ThereIsAlreadyKycException,
    EmptyFirstNameException,
    EmptyLastNameException,
    EmptyDocFrontException,
    EmptyDocBackException,
    EmptyResponsibleNameException,
    EmptyResponsibleDocException,
    EmptyFantasyNameException,
    EmptyStreetException,
    EmptyAddressNoException,
    EmptyCityException,
    EmptyStateException,
    EmptyNationalityException,
    EmptyZipcodeException,
    EmptyNeighborhoodException
};
use Backend\Models\FeedEmail;
use Backend\Models\User;
use Backend\Models\Kyc;
use Backend\Models\FilePermission;
use Backend\Models\IuguBank;
use Backend\Models\IuguSeller;
use Backend\Services\Iugu\IuguRest;
use Ezeksoft\PHPWriteLog\Log;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Edit Kyc';
        $this->context = 'dashboard';
        // $this->indexFile = 'frontend/view/user/kyc/editView.php';
        $this->user = user();
    }

    public function update(Request $request)
    {
        $user = $this->user;

        $response = [];
        $body = $request->json();
        $doc_front = safe_filename($body?->doc_front ?? '');
        $doc_back = safe_filename($body?->doc_back ?? '');
        $type = $body?->type ?? '';
        $first_name = trim($body?->first_name ?? '');
        $last_name = trim($body?->last_name ?? '');
        $phone = $body?->phone ?? '';
        $birthdate = $body?->birthdate ?? '';
        // $email = $body?->email ?? '';
        $street = $body?->street ?? '';
        $address_no = $body?->address_no ?? '';
        $city = $body?->city ?? '';
        $state = $body?->state ?? '';
        $nationality = $body?->nationality ?? '';
        $zipcode = $body?->zipcode ?? '';
        $neighborhood = $body?->neighborhood ?? '';
        $doc = $body?->doc ?? '';
        $responsible_name = $body?->responsible_name ?? '';
        $responsible_doc = $body?->responsible_doc ?? '';
        $fantasy_name = $body?->fantasy_name ?? '';
        $bankaccount_bank = $body?->bankaccount_bank ?? '';
        $bankaccount_type = $body?->bankaccount_type ?? '';
        $bankaccount_agency = $body?->bankaccount_agency ?? '';
        $bankaccount_account = $body?->bankaccount_account ?? '';

        $bank_name = '';
        if ($bankaccount_bank)
        {
            $iugu_bank = IuguBank::find($bankaccount_bank);
            $bank_name = $iugu_bank->name ?? '';
        }

        $bank_acc_type = $bankaccount_type == 'current' ? 'Corrente' : 'Poupança';

        $response = [];
        $first_kyc_attempt = false;

        try
        {
            $kyc = Kyc::where('user_id', $user->id)->first();

            if (!empty($kyc) && $kyc->status <> EKycStatus::REJECTED->value) throw new ThereIsAlreadyKycException;

            if (empty($kyc)) 
            {
                $kyc = new Kyc;
                $kyc->user_id = $user->id;
                $first_kyc_attempt = true;
            }

            if (!$type) throw new EmptyTypeException;
            if (!$doc) throw new EmptyDocException;

            $is_cpf = strlen($doc) <= 14;

            // is cpf
            if ($is_cpf) 
            {
                if (!$first_name) throw new EmptyFirstNameException;
                if (!$last_name) throw new EmptyLastNameException;
            }

            // is cnpj
            else
            {
                if (!$responsible_name) throw new EmptyResponsibleNameException;
                if (!$responsible_doc) throw new EmptyResponsibleDocException;
                if (!$fantasy_name) throw new EmptyFantasyNameException;
            }
            
            if (!$phone) throw new EmptyPhoneException;
            if (!$birthdate) throw new EmptyBirthdateException;
            if (!$street) throw new EmptyStreetException;
            if (!$address_no) throw new EmptyAddressNoException;
            if (!$city) throw new EmptyCityException;
            if (!$state) throw new EmptyStateException;
            if (!$nationality) throw new EmptyNationalityException;
            if (!$zipcode) throw new EmptyZipcodeException;
            if (!$neighborhood) throw new EmptyNeighborhoodException;

            if ($type == EKycType::PASSPORT->value || $type == EKycType::COMPANY->value)
            {
                if (!$doc_front) throw new EmptyDocFrontException;
            }

            if ($type == EKycType::ID->value || $type == EKycType::DRIVING_LICENSE->value)
            {
                if (!$doc_front) throw new EmptyDocFrontException;
                if (!$doc_back) throw new EmptyDocBackException;
            }

            $kyc->doc = $doc;
            $kyc->doc_front = $doc_front;
            $kyc->doc_back = $doc_back;
            $kyc->name = "$first_name $last_name";
            $kyc->type = $type;
            $kyc->phone = $phone;
            $kyc->street = $street;
            $kyc->address_no = $address_no;
            $kyc->city = $city;
            $kyc->state = $state;
            $kyc->nationality = $nationality;
            $kyc->zipcode = $zipcode;
            $kyc->neighborhood = $neighborhood;
            $birthdate = explode('/', $birthdate);
            $kyc->birthdate = "$birthdate[2]-$birthdate[0]-$birthdate[1]";
            $kyc->responsible_name = $responsible_name;
            $kyc->responsible_doc = $responsible_doc;
            $kyc->fantasy_name = $fantasy_name;
            $kyc->status = EKycStatus::PENDING->value;
            $kyc->save();

            // enviar um email para confirmar o email e marcar no kyc que foi enviado 
            // $user->email = $email;
            // $user->save();
            
            $feed_emails = FeedEmail::all();
            foreach ($feed_emails as $feed_email)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name,
                ];
    
                send_email($feed_email->email, $email_data, EEmailTemplatePath::REQUESTED_KYC, 'pt_BR');
            }


            /**
             * Iugu
             */

            if ($first_kyc_attempt)
            {
                $iugu_payload_create_account = [
                    "name" => $kyc->name
                ];

                $headers = ['Content-Type' => 'application/json'];

                $iugu_create_account_response = IuguRest::request(
                    verb: 'POST',
                    url: "/marketplace/create_account?api_token=".env('IUGU_API_TOKEN'),
                    headers: $headers,
                    body: json_encode($iugu_payload_create_account),
                    timeout: 10
                );

                $iugu_create_account_json = $iugu_create_account_response->json;

                $iugu_seller = new IuguSeller;
                $iugu_seller->user_id = $user->id;
                $iugu_seller->account_id = $iugu_create_account_json->account_id ?? '';
                $iugu_seller->live_api_token = $iugu_create_account_json->live_api_token ?? '';
                $iugu_seller->test_api_token = $iugu_create_account_json->test_api_token ?? '';
                $iugu_seller->user_token = $iugu_create_account_json->user_token ?? '';
                $iugu_seller->response_create = $iugu_create_account_response->body;


                $iugu_payload_verification = [
                    'data' => [
                        'price_range' => 'Mais que R$ 10,00',
                        'physical_products' => 'false',
                        'business_type' => 'Ebooks',
                        'person_type' => $is_cpf ? 'Pessoa Física' : 'Pessoa Jurídica',
                        'automatic_transfer' => false,
                        'address' => "$street $address_no",
                        'cep' => preg_replace('/(\d{5})(\d{3})/', '$1-$2', $zipcode),
                        'city' => $city,
                        'district' => $neighborhood,
                        'state' => $state,
                        'telephone' => preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '$1-$2-$3', $kyc->phone),
                        'bank' => $bank_name,
                        'bank_ag' => $bankaccount_agency,
                        'account_type' => $bank_acc_type,
                        'bank_cc' => $bankaccount_account
                    ]
                ];
                
                if ($is_cpf) 
                {
                    $iugu_payload_verification['data']['cpf'] = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
                    $iugu_payload_verification['data']['name'] = $kyc->name;
                }

                else
                {
                    $iugu_payload_verification['data']['cnpj'] = $doc;
                    $iugu_payload_verification['data']['company_name'] = $fantasy_name;
                    $iugu_payload_verification['data']['resp_name'] = $responsible_name;
                    $iugu_payload_verification['data']['resp_cpf'] = preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $responsible_doc);
                }

                $iugu_verification_response = IuguRest::request(
                    verb: 'POST',
                    url: "/accounts/$iugu_seller->account_id/request_verification?api_token=".$iugu_seller->live_api_token,
                    headers: $headers,
                    body: json_encode($iugu_payload_verification),
                    timeout: 10
                );

                $iugu_verification_json = $iugu_verification_response->json;

                $iugu_seller->response_verification = $iugu_verification_response->body;
                $iugu_seller->save();

                $iugu_payload_account_configuration = [
                    'credit_card' => [
                        'active' => true,
                        'soft_descriptor' => env('IUGU_DESCRIPTOR'),
                        'installments' => true,
                        'max_installments' => 12,
                        'max_installments_without_interest' => 0,
                        'two_step_transaction' => false,
                        'installments_pass_interest' => true
                    ],
                    'commission_percent' => '6',
                    'commissions' => [
                        'cents' => 250,
                        'percent' => 6,
                        'credit_card_cents' => 250,
                        'credit_card_percent' => 6,
                        'bank_slip_cents' => 250,
                        'bank_slip_percent' => 6,
                        'pix_cents' => 250,
                        'pix_percent' => 6,
                        'permit_aggregated' => true
                    ],
                    'auto_advance_option' => 1,
                    'splits' => [
                        [
                            'recipient_account_id' => env('IUGU_ACCOUNT_ID'),
                            'percent' => 6,
                            'cents' => 250,
                            'bank_slip_percent' => 6,
                            'bank_slip_cents' => 250,
                            'credit_card_cents' => 250,
                            'credit_card_percent' => 6,
                            'pix_percent' => 6,
                            'pix_cents' => 250,
                            'permit_aggregated' => true
                        ]
                    ]
                ];

                $iugu_account_configuration = IuguRest::request(
                    verb: 'POST',
                    url: "/accounts/configuration?api_token=".$iugu_seller->live_api_token,
                    headers: $headers,
                    body: json_encode($iugu_payload_account_configuration),
                    timeout: 10
                );

                (new Log)->write(
                    base_path('logs/iugu_accounts_configuration.log'), 
                    "\n===============\n[" . date('Y-m-d H:i:s') . ']' . $iugu_account_configuration->body . "\n"
                );
            }

            $data = compact('first_kyc_attempt');
            $data['response_create'] = $iugu_seller->response_create;
            $data['response_verification'] = $iugu_seller->response_verification;
            $data['payload_verification'] = $iugu_payload_verification;

            $response = ["status" => "success", "message" => "Kyc atualizado com sucesso.", "data" => $data];
        }

        catch(EmptyNameException)
        {
            $response = ["status" => "error", "message" => "O nome não pode estar em branco."];
        }

        catch(EmptyDocException)
        {
            $response = ["status" => "error", "message" => "Os números da sua identidade/empresa não podem estar em branco."];
        }

        catch(EmptyBirthdateException)
        {
            $response = ["status" => "error", "message" => "A data de nascimento/criação não pode estar em branco."];
        }

        catch(EmptyPhoneException)
        {
            $response = ["status" => "error", "message" => "O telefone não pode estar em branco."];
        }

        catch(ThereIsAlreadyKycException)
        {
            $response = ["status" => "error", "message" => "Você não pode enviar o KYC mais de uma vez."];
        }

        // catch(EmptyEmailException)
        // {
        //     $response = ["status" => "error", "message" => "O e-mail não pode estar em branco."];
        // }

        catch(EmptyTypeException)
        {
            $response = ["status" => "error", "message" => "O tipo de documento não pode estar vazio."];
        }

        catch(EmptyFirstNameException)
        {
            $response = ["status" => "error", "message" => "O seu nome não pode estar vazio."];
        }

        catch(EmptyLastNameException)
        {
            $response = ["status" => "error", "message" => "O seu sobrenome não pode estar vazio."];
        }

        catch(EmptyResponsibleNameException)
        {
            $response = ["status" => "error", "message" => "O nome do responsável não pode estar vazio."];
        }

        catch(EmptyResponsibleDocException)
        {
            $response = ["status" => "error", "message" => "O CPF do responsável não pode estar vazio."];
        }

        catch(EmptyFantasyNameException)
        {
            $response = ["status" => "error", "message" => "O nome fantasia não pode estar vazio."];
        }

        catch(EmptyDocFrontException)
        {
            $response = ["status" => "error", "message" => "A foto de frente do documento não foi carregada."];
        }

        catch(EmptyDocBackException)
        {
            $response = ["status" => "error", "message" => "A foto do verso do documento não foi carregada."];
        }

        catch (EmptyStreetException)
        {
            $response = ["status" => "error", "message" => "A rua não pode estar em branco."];
        }
        
        catch (EmptyAddressNoException)
        {
            $response = ["status" => "error", "message" => "O número do endereço não pode estar em branco."];
        }
        
        catch (EmptyCityException)
        {
            $response = ["status" => "error", "message" => "A cidade não pode estar vazia."];
        }
        
        catch (EmptyStateException)
        {
            $response = ["status" => "error", "message" => "O Estado não pode estar vazio."];
        }
        
        catch (EmptyNationalityException)
        {
            $response = ["status" => "error", "message" => "A nacionalidade não pode estar vazia."];
        }
        
        catch (EmptyZipcodeException)
        {
            $response = ["status" => "error", "message" => "O CEP não pode estar em branco."];
        }
        
        catch (EmptyNeighborhoodException)
        {
            $response = ["status" => "error", "message" => "O bairro não pode estar em branco."];
        }

        catch (\Exception $ex)
        {
            $logContent = [
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
                'user_id' => $user->id ?? 'N/A',
                'request' => $request->all() ?? 'N/A',
            ];

            (new Log)->write(base_path('logs/errors.log'), json_encode($logContent, JSON_PRETTY_PRINT));
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Upload product image
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @throws \Backend\Exceptions\User\InvalidImageException
     * @throws \Backend\Exceptions\User\UserNotFoundException
     */
    public function uploadImage(Request $request)
    {
        $user = $this->user;
        $response = [];

        $filename = safe_filename(uniqid() . ".png");

        $source = $_FILES['image']['tmp_name'] ?? '';
        $dir = 'safe_upload/kyc';
        $path = base_path($dir);
        $destination = $path . "/" . $filename;

        try
        {
            $check = getimagesize($source);
            $mime = $check['mime'] ?? '';
            $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
            if (!$is_image) throw new InvalidImageException;

            $user = User::find($user->id);
            if (empty($user)) throw new UserNotFoundException;

            $uploaded_image = move_uploaded_file($source, $destination);

            $fileperm = new FilePermission;
            $fileperm->path = "$dir/";
            $fileperm->name = $filename;
            $fileperm->perm_read = 1;
            $fileperm->user_id = $user->id;
            $fileperm->save();

            $response = ['status' => 'success', 'message' => "Imagem carregada com sucesso.", "data" => ["image" => $filename]];
        }

        catch (InvalidImageException $ex)
        {
            $response = ['status' => 'error', 'message' => "Este arquivo não é uma imagem válida."];
        }

        catch (UserNotFoundException $ex)
        {
            $response = ['status' => 'error', 'message' => "Usuário não encontrado."];
        }

        catch (\Exception $ex)
        {
            $logContent = [
                'timestamp' => date('Y-m-d H:i:s'),
                'error' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
                'user_id' => $user->id ?? 'N/A',
                'request' => $request->all() ?? 'N/A',
            ];

            (new Log)->write(base_path('logs/errors.log'), json_encode($logContent, JSON_PRETTY_PRINT));

            $not_found = new NotFoundController($this->application);
            client_name() == 'Pager' ? $not_found->element($request) : $not_found->view($request);
        }

        finally
        {
            Response::json($response);
        }
    }

    public function front_png(Request $request, $id)
    {
        $user = $this->user;

        try
        {
            $kyc = Kyc::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($kyc)) throw new ModelNotFoundException;

            if (empty($kyc->doc_front)) throw new DocBackNotFoundException;
            
            $filename = safe_filename($kyc->doc_front);
            $file = base_path("safe_upload/kyc/$filename");

            if (!is_file($file)) throw new DocBackImageNotFoundException;
            
            header('Content-Type: image/png');
            readfile($file);
        }

        catch (ModelNotFoundException|DocBackNotFoundException|DocBackImageNotFoundException $ex)
        {
            header('Content-Type: image/png');
            readfile(base_path("frontend/public/images/default.png"));
        }

        catch (\Exception $ex)
        {
            $not_found = new NotFoundController($this->application);
            client_name() == 'Pager' ? $not_found->element($request) : $not_found->view($request);
        }
    }

    public function back_png(Request $request, $id)
    {
        $user = $this->user;

        try
        {
            $kyc = Kyc::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($kyc)) throw new ModelNotFoundException;

            if (empty($kyc->doc_back)) throw new DocBackNotFoundException;
            
            $filename = safe_filename($kyc->doc_back);
            $file = base_path("safe_upload/kyc/$filename");

            if (!is_file($file)) throw new DocBackImageNotFoundException;
            
            header('Content-Type: image/png');
            readfile($file);
        }

        catch (ModelNotFoundException|DocBackNotFoundException|DocBackImageNotFoundException $ex)
        {
            header('Content-Type: image/png');
            readfile(base_path("frontend/public/images/default.png"));
        }

        catch (\Exception $ex)
        {
            $not_found = new NotFoundController($this->application);
            client_name() == 'Pager' ? $not_found->element($request) : $not_found->view($request);
        }
    }

    public function get_image(Request $request, $name)
    {
        $user = $this->user;

        try
        {
            $fileperm = FilePermission::where('name', $name)->where('user_id', $user->id)->first();
            if (empty($fileperm)) throw new ModelNotFoundException;
            
            $filename = safe_filename($fileperm->name);
            $file = base_path("$fileperm->path$filename");

            if (!is_file($file)) throw new DocBackImageNotFoundException;
            
            header('Content-Type: image/png');
            readfile($file);
        }

        catch (ModelNotFoundException|DocBackImageNotFoundException $ex)
        {
            header('Content-Type: image/png');
            readfile(base_path("frontend/public/images/default.png"));
        }

        catch (\Exception $ex)
        {
            $not_found = new NotFoundController($this->application);
            client_name() == 'Pager' ? $not_found->element($request) : $not_found->view($request);
        }
    }
}