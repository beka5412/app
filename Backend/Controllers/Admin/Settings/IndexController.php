<?php

namespace Backend\Controllers\Admin\Settings;

use Backend\App;
use Backend\Http\Request;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Settings;
use Backend\Http\Response;


class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Settings';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/settings/indexView.php';
        $this->editFile = 'frontend/view/admin/settings/editView.php';
        $this->admin = admin();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        
        // Obtenha todas as configurações
        $settings = Settings::all();
        
        View::render($this->indexFile, compact('context', 'title', 'admin', 'settings'));
    }
    
    public function update()
{
    // Acesse os valores diretamente do $_POST
    $values = $_POST['values'] ?? null;

    // Verifique se os valores estão presentes e são um array
    if (!isset($values) || !is_array($values)) {
        return Response::redirect('/admin/settings')->with('error', 'Dados inválidos.');
    }

    // Percorra os valores e atualize cada configuração
    foreach ($values as $id => $value) {
        // Certifique-se de que o ID é um número e o valor é uma string
        if (is_numeric($id) && is_string($value)) {
            $setting = Settings::find($id);
            if ($setting) {
                $setting->value = $value;
                $setting->save();
            }
        }
    }

    return Response::redirect('/admin/settings')->with('success', 'Configurações atualizadas com sucesso!');
}

    
}