<?php

namespace Backend\Controllers\Admin\Kyc;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Controllers\Browser\NotFoundController;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Enums\Kyc\EKycStatus;
use Backend\Exceptions\Kyc\{
    KycNotFoundException,
    DocBackNotFoundException,
    DocBackImageNotFoundException
};
use Backend\Http\Link;
use Backend\Models\Administrator;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Backend\Models\Kyc;
use Backend\Models\User;

class EditController
{
    public App $application;

    public string $title = 'Editar kyc';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/admin/kyc/editView.php';
    public ?Administrator $admin;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->admin = admin();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        try
        {
            $kyc = Kyc::where('id', $id)->first();
            if (empty($kyc)) throw new ModelNotFoundException;

            $full_name = $kyc?->name ?? '';
            $aux_name = explode(' ', $full_name);
            $first_name = $aux_name[0] ?? '';
            $last_name = substr($full_name, strlen($first_name), strlen($full_name));
            $edit_enabled = empty($kyc) || $kyc?->status == EKycStatus::REJECTED->value;
            $is_cpf = strlen($kyc?->doc ?? '') <= 14;
            $is_cnpj = strlen($kyc?->doc ?? '') > 14;

            View::render($this->indexFile, compact(
                'title',
                'context',
                'admin',
                'kyc',
                'full_name',
                'aux_name',
                'first_name',
                'last_name',
                'edit_enabled',
                'is_cpf',
                'is_cnpj'
            ));
        }

        catch (ModelNotFoundException $ex)
        {
            $link = new Link($this->application);
            $link->to(site_url(), '/admin/kyc');
            Link::changeUrl(site_url(), '/admin/kyc');
        }
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $body = $request->pageParams();
        $id = $body?->id;

        try
        {
            $kyc = Kyc::where('id', $id)->first();
            if (empty($kyc)) throw new ModelNotFoundException;

            $full_name = $kyc?->name ?? '';
            $aux_name = explode(' ', $full_name);
            $first_name = $aux_name[0] ?? '';
            $last_name = substr($full_name, strlen($first_name), strlen($full_name));
            $edit_enabled = empty($kyc) || $kyc?->status == EKycStatus::REJECTED->value;
            $is_cpf = strlen($kyc?->doc ?? '') <= 14;
            $is_cnpj = strlen($kyc?->doc ?? '') > 14;

            View::response($this->indexFile, compact(
                'title',
                'context',
                'admin',
                'kyc',
                'aux_name',
                'first_name',
                'last_name',
                'edit_enabled',
                'is_cpf',
                'is_cnpj'
            ));
        }

        catch (ModelNotFoundException $ex)
        {
            $notfound = new NotFoundController($this->application);
            $notfound->element($request);
        }
    }

    public function update(Request $request, $id)
    {
        $admin = $this->admin;
        $response = [];
        $body = $request->json();
        $status = $body->status;

        try
        {
            $kyc = Kyc::where('id', $id)->first();
            if (empty($kyc)) throw new KycNotFoundException;

            $kyc->status = $status;
            $kyc->save();

            $user = User::find($kyc->user_id);

            if ($status == EKycStatus::CONFIRMED->value)
                $user->kyc_confirmed = 1;
            else
                $user->kyc_confirmed = 0;

            $user->save();

            if ($status == EKycStatus::CONFIRMED->value)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name
                ];
                
                send_email($user->email, $email_data, EEmailTemplatePath::CONFIRMED_KYC, 'pt_BR');
            }
            else if ($status == EKycStatus::REJECTED->value)
            {
                $email_data = [
                    'site_url' => site_url(),
                    'platform' => site_name(),
                    'username' => $user->name
                ];
                
                send_email($user->email, $email_data, EEmailTemplatePath::REJECTED_KYC, 'pt_BR');
            }

            $response = ["status" => "success", "message" => "Kyc atualizado com sucesso."];
        }

        catch (KycNotFoundException $ex)
        {
            $response = ["status" => "error", "message" => "Kyc não encontrado."];
        }

        finally
        {
            Response::json($response);
        }
    }

    public function front_png(Request $request, $id)
    {
        try
        {
            $kyc = Kyc::find($id);
            if (empty($kyc)) throw new ModelNotFoundException;

            if (empty($kyc->doc_front)) throw new DocBackNotFoundException;

            $filename = safe_filename($kyc->doc_front);
            $file = base_path("safe_upload/kyc/$filename");

            if (!is_file($file)) throw new DocBackImageNotFoundException;

            header('Content-Type: image/png');
            readfile($file);
        }

        catch (ModelNotFoundException | DocBackNotFoundException | DocBackImageNotFoundException $ex)
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
        try
        {
            $kyc = Kyc::find($id);
            if (empty($kyc)) throw new ModelNotFoundException;

            if (empty($kyc->doc_back)) throw new DocBackNotFoundException;

            $filename = safe_filename($kyc->doc_back);
            $file = base_path("safe_upload/kyc/$filename");

            if (!is_file($file)) throw new DocBackImageNotFoundException;

            header('Content-Type: image/png');
            readfile($file);
        }

        catch (ModelNotFoundException | DocBackNotFoundException | DocBackImageNotFoundException $ex)
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
