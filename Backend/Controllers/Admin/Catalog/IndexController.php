<?php

namespace Backend\Controllers\Admin\Catalog;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Models\Administrator;
use Backend\Models\Catalogo;
use Backend\Models\Category;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Catálogos';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/catalogs/indexView.php';
        $this->admin = admin();
    }


    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $page = $request->query('page') ?: 1;

        $per_page = 9;
        $url = get_current_route();

        $catalogs = Catalogo::orderBy('id', 'ASC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $categories = Category::all();
        $info = $catalogs;
        $total = Catalogo::count();
        View::render($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'catalogs', 'total', 'categories'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        $page = $request->query('page') ?: 1;

        $per_page = 9;
        $url = site_url() . '/catalogs';

        $catalogs = Catalogo::orderBy('id', 'ASC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        $categories = Category::all();
        $info = $catalogs;
        $total = Catalogo::count();
        View::render($this->indexFile, compact('context', 'title', 'admin', 'info', 'url', 'catalogs', 'total', 'categories'));
    }

    public function new(Request $request)
    {
        try {
            $user = $this->admin;
            $catalog = new Catalogo;
            $catalog->user_id = $user->id;
            $catalog->name = $_POST['title'];
            $catalog->price = $_POST['price'];
            $catalog->description = $_POST['description'];
            $catalog->category_id = $_POST['category'];
            $catalog->sku = strtoupper(uniqid());

            $filename = uniqid() . ".png";

            if (isset($_FILES["image"]) && !empty($_FILES['image']['tmp_name'])) {
                $source = $_FILES['image']['tmp_name'] ?? '';
                $destination = base_path('frontend/public/upload') . "/" . $filename;

                try {
                    $check = getimagesize($source);
                    $mime = $check['mime'] ?? '';
                    $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
                    if (!$is_image) throw new InvalidImageException;
                    $uploaded_image = move_uploaded_file($source, $destination);
                    $catalog->image = "/upload/$filename";
                } catch (Exception $e) {
                    $catalog->image = "/images/default.png";
                }
            } else {
                $catalog->image = "/images/default.png";
            }
            $catalog->save();

            Response::json(["message" => "Produto criado com sucesso.", "id" => $catalog->id]);
        } catch (Exception $e) {
            Response::json(["message" => "Falha ao criar."]);
        }
    }

    public function update(Request $request)
    {
        try {
            Response::json($_POST);
            $cat = empty($_POST['category']) ? 0 : $_POST['category'];
            $catalog = Catalogo::find($_POST['id']);
            $catalog->name = $_POST['title'];
            $catalog->price = $_POST['price'];
            $catalog->description = $_POST['description'];
            $catalog->category_id = $cat;

            $filename = uniqid() . ".png";

            if (isset($_FILES["image"]) && !empty($_FILES['image']['tmp_name'])) {
                $source = $_FILES['image']['tmp_name'] ?? '';
                $destination = base_path('frontend/public/upload') . "/" . $filename;

                try {
                    $check = getimagesize($source);
                    $mime = $check['mime'] ?? '';
                    $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
                    if (!$is_image) throw new InvalidImageException;
                    $uploaded_image = move_uploaded_file($source, $destination);
                    $catalog->image = "/upload/$filename";
                } catch (Exception $e) {
                    
                }
            }
            $catalog->save();

            Response::json(["message" => "Produto alterado com sucesso.", "id" => $catalog->id]);
        } catch (Exception $e) {
            Response::json(["message" => "Falha ao criar."]);
        }
    }
}
