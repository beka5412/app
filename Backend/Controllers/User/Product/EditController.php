<?php

namespace Backend\Controllers\User\Product;

use Backend\App;
use Backend\Enums\EmailTemplate\EEmailTemplatePath;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Exceptions\Product\
{
    ProductNotFoundException,
    EmptyNameException,
    EmptyPriceException,
    EmptyDescriptionException,
    EmptyImageException,
    EmptyIsFreeException,
    EmptyPricePromoException,
    EmptyStockControlException,
    EmptyStockQtyException,
    EmptyLandingPageException,
    EmptySupportEmailException,
    EmptyAuthorException,
    EmptyWarrantyTimeException,
    EmptyProductTypeException,
    EmptyProductDeliveryException,
    InvalidImageException,
    EmptyCategoryException,
    CategoryNotFoundException,
    EmptyAttachmentURLException,
    FileNotAllowedException,
    EmptyAttachmentFileException,
    TooBigFileException,
    EmptyPixDiscountAmountException,
    EmptyCreditCardDiscountAmountException,
    EmptyBilletDiscountAmountException,
    EmptyPixThanksPageURLException,
    EmptyBilletThanksPageURLException,
    EmptyCreditCardThanksPageURLException,
    MinimumProductPriceNotReachedException
};
use Backend\Enums\Product\EProductDelivery;
use Backend\Enums\Product\EProductPaymentType;
use Backend\Enums\Product\EProductRequestStatus;
use Backend\Enums\Product\ERecurrenceInterval;
use Backend\Models\User;
use Backend\Models\Product;
use Backend\Models\Category;
use Backend\Models\Checkout;
use Backend\Models\FeedEmail;
use Backend\Models\ProductLink;
use Backend\Models\Plan;
use Backend\Models\ProductRequest;
use Backend\Services\Iugu\IuguRest;
use Exception;
use Stripe\Exception\InvalidRequestException;

class EditController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Editar produto';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/products/editView.php';
        $this->user = user();
    }

    public function index(Request $request, $id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $product = Product::where('id', $id)->where('user_id', $user->id)->with('category')->with('product_links')->first();
        $categories = Category::all();
        $first_checkout = Checkout::where('product_id', $id)->where('user_id', $user->id)->orderBy('id', 'DESC')->first();

        View::render($this->indexFile, compact('title', 'context', 'user', 'product', 'categories', 'first_checkout'));
    }

    public function element(Request $request)
    {
        $body = $request->pageParams();
        $id = $body?->id;
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        $categories = Category::all();
        $product = Product::where('id', $id)->where('user_id', $user->id)->with('category')->with('product_links')->first();
        // $checkouts = Checkout::where('product_id', $id)->where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        $first_checkout = Checkout::where('product_id', $id)->where('user_id', $user->id)->orderBy('id', 'DESC')->first();

        View::response($this->indexFile, compact('title', 'context', 'user', 'product', 'categories', 'first_checkout'));
    }

    /**
     * Upload product image
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @param mixed $id                         Product ID
     * @throws \Backend\Exceptions\Product\InvalidImageException
     * @throws \Backend\Exceptions\Product\ProductNotFoundException
     */
    public function uploadImage(Request $request, $id)
    {
        $user = $this->user;
        $response = [];

        $filename = uniqid() . ".png";

        $source = $_FILES['image']['tmp_name'] ?? '';
        $destination = base_path('frontend/public/upload') . "/" . $filename;

        try
        {
            $check = getimagesize($source);
            $mime = $check['mime'] ?? '';
            $is_image = strlen($mime) > 0 && strpos($mime, "image/") >= 0;
            if (!$is_image) throw new InvalidImageException;

            $product = Product::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;

            $uploaded_image = move_uploaded_file($source, $destination);

            $response = ['status' => 'success', 'message' => "Imagem carregada com sucesso.", "data" => ["image" => "/upload/$filename"]];
        }

        catch (InvalidImageException $ex)
        {
            $response = ['status' => 'error', 'message' => "Este arquivo não é uma imagem válida."];
        }

        catch (ProductNotFoundException $ex)
        {
            $response = ['status' => 'error', 'message' => "Produto não encontrado ou não pertence ao seu usuário."];
        }

        finally
        {
            Response::json($response);
        }
    }

    /**
     * Upload product file
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @param Mixed $id                         Product ID
     * @throws \Backend\Exceptions\Product\FileNotAllowedException
     * @throws \Backend\Exceptions\Product\ProductNotFoundException
     */
    public function uploadAttachment(Request $request, $id)
    {
        $user = $this->user;
        $response = [];


        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        if ($ext == 'php') return;
        
        $filename = uniqid() . "." . $ext;

        $source = $_FILES['file']['tmp_name'] ?? '';
        $destination = base_path('frontend/public/upload') . "/" . $filename;

        $error = $_FILES['file']['error'] ?? '';
        $size = $_FILES['file']['size'] ?? '';
        $mime = $_FILES['file']['type'] ?? '';
        
        $bytes = $size; // filesize($source);
        $mb = $bytes / 1000000;

        if ($error === UPLOAD_ERR_INI_SIZE) return Response::json(['status' => 'error', 'message' => "O arquivo excede o limite da diretiva de configuração."]);
        if ($error === UPLOAD_ERR_FORM_SIZE) return Response::json(['status' => 'error', 'message' => "O arquivo excede o limite de formulário."]);
        if ($error === UPLOAD_ERR_PARTIAL) return Response::json(['status' => 'error', 'message' => "O upload foi feito parcialmente."]);
        if ($error === UPLOAD_ERR_NO_FILE) return Response::json(['status' => 'error', 'message' => "Nenhum arquivo foi enviado."]);
        if ($error === UPLOAD_ERR_NO_TMP_DIR) return Response::json(['status' => 'error', 'message' => "Pasta temporária ausente."]);
        if ($error === UPLOAD_ERR_CANT_WRITE) return Response::json(['status' => 'error', 'message' => "Falha ao escrever o arquivo no disco."]);
        if ($error === UPLOAD_ERR_EXTENSION) return Response::json(['status' => 'error', 'message' => "Uma extensão interrompeu o upload."]);
        if ($error !== UPLOAD_ERR_OK) return Response::json(['status' => 'error', 'message' => "Nada foi carregado."]);
        
        try 
        {
            if ($mb > 4096) throw new TooBigFileException;

            // $mime = '';
            // $finfo = finfo_open(FILEINFO_MIME_TYPE);
            // if (!empty($finfo))
            // {
            //     $mime = @finfo_file($finfo, $source);
            //     finfo_close($finfo);
            // }
        
            if (!in_array($mime, [
                'image/gif', 
                'image/jpeg', 
                'image/png', 
                'application/zip', 
                'application/x-zip-compressed',
                'application/pdf',
                'text/plain',
                'application/vnd.ms-powerpoint',
                'application/msword',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/csv'
            ])) throw new FileNotAllowedException;

            $product = Product::where('id', $id)->where('user_id', $user->id)->first();
            if (empty($product)) throw new ProductNotFoundException;

            $uploaded_image = move_uploaded_file($source, $destination);

            $icon_url = "/images/extensions/$ext.png";
            $icon_url = file_exists(base_path("frontend/public$icon_url")) ? $icon_url : '';

            $response = [
                'status' => 'success', 
                'message' => "Arquivo carregado com sucesso.", 
                'data' => [
                    'file' => "/upload/$filename",
                    'ext' => $ext,
                    'icon_url' => $icon_url
                ]
            ];
        }

        catch (FileNotAllowedException $ex)
        {
            $response = ['status' => 'error', 'message' => "Este arquivo tem um formato não permitido."];
        }

        catch (ProductNotFoundException $ex)
        {
            $response = ['status' => 'error', 'message' => "Produto não encontrado ou não pertence ao seu usuário."];
        }

        catch (TooBigFileException $ex)
        {
            $response = ['status' => 'error', 'message' => "O arquivo não pode ser maior que 100 MB."];
        }

        catch (\Exception $ex)
        {
            $response = ['status' => 'error', 'message' => "Erro interno."];
        }

        finally
        {
            Response::json($response);
        }
    }


    /**
     * Update product
     * 
     * @access public
     * @param \Backend\Http\Request $request    Request object
     * @param mixed $id                         Product ID
     * @throws \Backend\Exceptions\Product\ProductNotFoundException
     * @throws \Backend\Exceptions\Product\EmptyNameException
     * @throws \Backend\Exceptions\Product\EmptyPriceException
     * @throws \Backend\Exceptions\Product\EmptyDescriptionException
     * @throws \Backend\Exceptions\Product\EmptyImageException
     * @throws \Backend\Exceptions\Product\EmptyIsFreeException
     * @throws \Backend\Exceptions\Product\EmptyStockControlException
     * @throws \Backend\Exceptions\Product\EmptyStockQtyException
     * @throws \Backend\Exceptions\Product\EmptyLandingPageException
     * @throws \Backend\Exceptions\Product\EmptySupportEmailException
     * @throws \Backend\Exceptions\Product\EmptyAuthorException
     * @throws \Backend\Exceptions\Product\EmptyWarrantyTimeException
     * @throws \Backend\Exceptions\Product\EmptyProductTypeException
     * @throws \Backend\Exceptions\Product\EmptyProductDeliveryException
     * @throws \Backend\Exceptions\Product\EmptyPaymentTypeException
     * @throws \Backend\Exceptions\Product\EmptyCategoryException
     * @throws \Backend\Exceptions\Product\CategoryNotFoundException
     * @throws \Backend\Exceptions\Product\EmptyAttachmentURLException
     * @throws \Backend\Exceptions\Product\MinimumProductPriceNotReachedException
     */

    public function update(Request $request, Mixed $id)
    {
        $user = $this->user;
        
        $stripe_conf = [
            'api_key' => env('STRIPE_SECRET'),
            'stripe_version' => '2023-10-16',
        ];

        if (env('STRIPE_CONNECT') == 'true' && env('STRIPE_CONNECT_ACCOUNT')) $stripe_conf['stripe_account'] = env('STRIPE_CONNECT_ACCOUNT');
        
        $stripe = new \Stripe\StripeClient($stripe_conf);

        $response = [];
        $response_error = ["status" => "error", "message" => ""];

        $body = $request->json();
        $name = $body->name ?? '';
        $price = $body->price ?? '';
        $description = $body->description ?? '';
        $image = $body->image ?? '';
        $is_free = $body->is_free ?? '';
        $price_promo = $body->price_promo ?? '';
        $stock_control = $body->stock_control ?? '';
        $stock_qty = $body->stock_qty ?? '';
        $landing_page = $body->landing_page ?? '';
        $support_email = $body->support_email ?? '';
        $author = $body->author ?? '';
        $warranty_time = $body->warranty_time ?? '';
        $type = $body->type ?? '';
        $delivery = $body->delivery ?? '';
        $payment_type = $body->payment_type ?? '';
        $category_id = $body->category_id ?? '';
        $attachment_url = $body->attachment_url ?? '';
        $attachment_file = $body->attachment_file ?? '';
        $recurrence_period = $body->recurrence_period ?? '';
        $shipping_cost = $body->shipping_cost ?? '';
        $currency = $body->currency ?? '';
        $lang = $body->lang ?? '';
        $links = $body->links ?? []; // se nao existir o campo id: criar, senao: atualizar
        // $has_upsell = $body->has_upsell ?? 0;
        // $upsell_link = $body->upsell_link ?? '';

        $price = (double) str_replace(",", ".", str_replace(".", "", $price));
        $price_promo = (double) str_replace(",", ".", str_replace(".", "", $price_promo));

        try
        {
            $product = Product::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($product)) throw new ProductNotFoundException;
            if (!$name) throw new EmptyNameException;
            if (!$price) throw new EmptyPriceException;
            if (!$description) throw new EmptyDescriptionException;
            if (!$image) throw new EmptyImageException;
            if ($is_free === '') throw new EmptyIsFreeException;
            // if (!$price_promo) throw new EmptyPricePromoException;
            if ($stock_control === '') throw new EmptyStockControlException;
            if ($stock_control === 1 && !$stock_qty) throw new EmptyStockQtyException;
            if (!$landing_page) throw new EmptyLandingPageException;
            if (!$support_email) throw new EmptySupportEmailException;
            if (!$author) throw new EmptyAuthorException;
            if (!$warranty_time) throw new EmptyWarrantyTimeException;
            // if (!$type) throw new EmptyProductTypeException;
            if (!$delivery) throw new EmptyProductDeliveryException;
            // if (!$payment_type) throw new EmptyPaymentTypeException;
            if (!$category_id) throw new EmptyCategoryException;
            if ($delivery == EProductDelivery::EXTERNAL->value && !$attachment_url) throw new EmptyAttachmentURLException;
            if ($delivery == EProductDelivery::DOWNLOAD->value && !$attachment_file) throw new EmptyAttachmentFileException;

            $category = Category::find($category_id);
            if (empty($category)) throw new CategoryNotFoundException;

            // if (!empty($links)) 
            $keep_links = [];
            foreach ($links as $item)
            {
                $link = ProductLink::where('id', $item?->id ?? 0)
                    ->where('user_id', $user->id)
                    ->where('product_id', $product->id)->first();

                if (empty($link))
                {
                    $link = new ProductLink;
                    $link->user_id = $user->id;
                    $link->product_id = $product->id;
                }

                $link->slug = $item->slug;
                $link->amount = $item->val;
                $link->qty = $item->qty;
                $link->save();
                $keep_links[] = $link->id;
            }
            
            // deleta links que nao esta na lista que chegou na requisicao
            $link = ProductLink::whereNotIn('id', $keep_links)->where('user_id', $user->id)->where('product_id', $product->id)->delete();

            if ($is_free) 
            {
                $price = 0;
                $price_promo = 0;
            }

            else
            {
                // if ($price < 10 && $payment_type === EProductPaymentType::UNIQUE->value)
                if ($price < 5 && $currency == 'brl')
                    throw new MinimumProductPriceNotReachedException(currency_code_to_symbol($currency)->value." $price");
                if ($price < 1 && $currency == 'usd')
                    throw new MinimumProductPriceNotReachedException(currency_code_to_symbol($currency)->value." $price");
                if ($price < 1 && $currency == 'eur')
                    throw new MinimumProductPriceNotReachedException("$price ".currency_code_to_symbol($currency)->value);
            }

            $recurrence_interval = ERecurrenceInterval::MONTH; 
            $recurrence_interval_count = 1;

            switch ($recurrence_period)
            {
                case 'daily': 
                    $recurrence_interval = ERecurrenceInterval::DAY; 
                    $recurrence_interval_count = 1; 
                    break;
            
                case 'weekly': 
                    $recurrence_interval = ERecurrenceInterval::WEEK; 
                    $recurrence_interval_count = 1; 
                    break;
            
                case 'fortnightly': 
                    $recurrence_interval = ERecurrenceInterval::WEEK; 
                    $recurrence_interval_count = 2; 
                    break;

                case 'monthly': 
                    $recurrence_interval = ERecurrenceInterval::MONTH; 
                    $recurrence_interval_count = 1; 
                    break;
                    
                case 'twomonthly':
                    $recurrence_interval = ERecurrenceInterval::MONTH; 
                    $recurrence_interval_count = 2; 
                    break;
                    
                case 'quarterly': 
                    $recurrence_interval = ERecurrenceInterval::MONTH; 
                    $recurrence_interval_count = 3; 
                    break;
                    
                case 'semiannual':
                    $recurrence_interval = ERecurrenceInterval::MONTH; 
                    $recurrence_interval_count = 6; 
                    break;
                    
                case 'yearly': 
                    $recurrence_interval = ERecurrenceInterval::YEAR; 
                    $recurrence_interval_count = 1; 
                    break;
            }

            // TODO: criar e editar assinatura na stripe
            // criar apenas se nao existir 

            // produto
            $stripe_product = null;
            $stripe_product_id = '';
            
            try
            {
                $stripe_product = $product->gateway_product_id ? $stripe->products->retrieve($product->gateway_product_id) : null;
                $stripe_product_id = $stripe_product->id ?? '';

                if (!$stripe_product_id) 
                {
                    $stripe_product = $stripe->products->create([
                        // 'name' => $name,
                        'name' => "Product #".$product->id,
                        'images' => [site_url().$image]
                    ]);
                    
                    $stripe_product_id = $stripe_product->id ?? '';
                }

                else $stripe->products->update($stripe_product_id, [
                    // 'name' => $name,
                    'name' => "Product #".$product->id,
                    'images' => [site_url().$image]
                ]);
            } catch (Exception|InvalidRequestException) {}

            $iugu_plan_id = $product->iugu_plan_id;
            
            if ($product->payment_type === EProductPaymentType::RECURRING->value)
            {
                if (!$iugu_plan_id) 
                {
                    $iugu_plan_id = strtolower(preg_replace("/[^\w]/", "", $product->name))."_".uniqid();
                    $headers = ['Content-Type' => 'application/json'];

                    $payload = [
                        "name" => $product->name,
                        "identifier" => $iugu_plan_id,
                        "interval" => $recurrence_interval_count,
                        "interval_type" => $recurrence_interval->value."s",
                        "value_cents" => $product->price_promo ?: $product->price,
                        "payable_with" => [
                            "all"
                        ],
                        "features" => [
                            [
                                "name" => $product->name,
                                "identifier" => "users",
                                "value" => 10
                            ]
                        ],
                        "billing_days" => 5,
                        "max_cycles" => 0
                    ];

                    $response = IuguRest::request(
                        verb: 'POST',
                        url: '/plans?api_token=' . env('IUGU_API_TOKEN'),
                        headers: $headers,
                        body: json_encode($payload),
                        timeout: 10
                    );
                }

                $product->iugu_plan_id = $iugu_plan_id;
            }
            

            // plano
            // $stripe_plan = $product->gateway_plan_id ? $stripe->plans->retrieve($product->gateway_plan_id) : null;
            // $stripe_plan_id = $stripe_plan->id ?? '';
            
            // if (!$stripe_plan_id) 
            // {
            //     $stripe_plan = $stripe->plans->create([
            //         'amount' => intval(($product->price_promo ?: $product->price) * 100),
            //         'currency' => 'usd',
            //         'interval' => $recurrence_interval,
            //         'interval_count' => $recurrence_interval_count,
            //         'product' => $stripe_product_id,
            //     ]);
                
            //     $stripe_plan_id = $stripe_plan->id ?? '';
            // }

            // $stripe_plan = $stripe->plans->create([
            //     'amount' => intval(($product->price_promo ?: $product->price) * 100),
            //     'currency' => 'usd',
            //     'interval' => 'month',
            //     'interval_count' => 1,
            //     'product' => 'prod_NjpI7DbZx6AlWQ',
            // ]);

            if ($stripe_product_id) $product->gateway_product_id = $stripe_product_id;
            // if ($stripe_plan_id) $product->gateway_plan_id = $stripe_plan_id;
            $product->name = $name;
            $product->price = $price;
            $product->image = $image;
            $product->description = $description;
            $product->is_free = $is_free;
            $product->price_promo = $price_promo;
            $product->stock_control = $stock_control;
            if ($stock_qty > 0) $product->stock_qty = $stock_qty;
            $product->landing_page = $landing_page;
            $product->support_email = $support_email;
            $product->author = $author;
            $product->warranty_time = $warranty_time;
            // $product->type = $type;
            $product->delivery = $delivery;            
            // $product->payment_type = $payment_type; 
            $product->category_id = $category_id; 
            $product->payment_type = $payment_type; 
            if ($attachment_url) $product->attachment_url = $attachment_url; 
            if ($attachment_file) $product->attachment_file = $attachment_file; 
            $product->recurrence_interval = $recurrence_interval;
            $product->recurrence_interval_count = $recurrence_interval_count;
            $product->shipping_cost = (double) str_replace(",", ".", str_replace(".", "", $shipping_cost));
            $product->currency = $currency;
            $product->currency_symbol = currency_code_to_symbol($currency);
            $product->language_id = $lang;
            // $product->has_upsell = $has_upsell;
            // $product->upsell_link = $upsell_link;
            $product->save();

            if ($payment_type === EProductPaymentType::RECURRING->value
            && !Plan::where('user_id', $user->id)->where('product_id', $product->id)->exists())
            {
                $plan = new Plan;
                $plan->user_id = $user->id;
                $plan->product_id = $product->id;
                $plan->name = __('Default plan');
                $plan->price = 49;
                $plan->slug = strtoupper(uniqid());
                $plan->recurrence_interval = $product->recurrence_interval ?: ERecurrenceInterval::MONTH;
                $plan->recurrence_interval_count = $product->recurrence_interval_count ?: 1;
                $plan->save();
            }

            $send_email = false;
            $product_request = ProductRequest::where('user_id', $user->id)->where('product_id', $product->id)->first();
            if ($product_request)
            {
                if ($product_request->status == EProductRequestStatus::REJECTED->value)
                {
                    $product_request->status = EProductRequestStatus::PENDING;
                    $product_request->save();
                    $send_email = true;
                }
            }
            else
            {
                $product_request = new ProductRequest;
                $product_request->user_id = $user->id;
                $product_request->product_id = $product->id;
                $product_request->status = EProductRequestStatus::PENDING;
                $product_request->save();
                $send_email = true;
            }

	    $send_email = false;
            if ($send_email)
            {
                $feed_emails = FeedEmail::all();
                foreach ($feed_emails as $feed_email)
                {
                    $email_data = [
                        'site_url' => site_url(),
                        'platform' => site_name(),
                        'username' => $user->name,
                        'product_name' => $product->name,
                        'product_image' => $product->image,
                    ];
                    $email_data['total'] = number_to_currency_by_symbol($product->price_promo ?: $product->price, 'brl');
                    $email_data['symbol'] = currency_code_to_symbol('brl')->value;
                    $email_data['product_price'] = "$email_data[symbol] $email_data[total]";
        
                    send_email($feed_email->email, $email_data, EEmailTemplatePath::REQUESTED_PRODUCT, 'pt_BR');
                }
            }
            
            $response = ["status" => "success", "message" => "Produto atualizado com sucesso."];
        }

        catch(ProductNotFoundException $ex)
        {
            $response = $response_error;
            $response["message"] = "Produto não encontrado.";
        }

        catch(EmptyNameException $ex)
        {
            $response = $response_error;
            $response["message"] = "O nome do produto não pode estar em branco.";
        }

        catch(EmptyPriceException $ex)
        {
            $response = $response_error;
            $response["message"] = "O preço do produto não pode estar em branco.";
        }

        catch(EmptyDescriptionException $ex)
        {
            $response = $response_error;
            $response["message"] = "A descrição do produto não pode estar em branco.";
        }

        catch (EmptyImageException $ex)
        {
            $response = $response_error;
            $response["message"] = "Carregue uma imagem para o produto.";
        }

        catch (EmptyIsFreeException $ex)
        {
            $response = $response_error;
            $response["message"] = "Escolha se o produto é grátis ou não.";
        }

        catch (EmptyPricePromoException $ex)
        {
            $response = $response_error;
            $response["message"] = "O preço promocional não pode estar em branco.";
        }

        catch (EmptyStockControlException $ex)
        {
            $response = $response_error;
            $response["message"] = "Escolha se o produto tem controle de estoque ou não.";
        }

        catch (EmptyStockQtyException $ex)
        {
            $response = $response_error;
            $response["message"] = "A quantidade em estoque não pode estar em branco.";
        }

        catch (EmptyLandingPageException $ex)
        {
            $response = $response_error;
            $response["message"] = "O link da página de vendas não pode estar em branco.";
        }

        catch (EmptySupportEmailException $ex)
        {
            $response = $response_error;
            $response["message"] = "O e-mail de suporte não pode estar em branco.";
        }

        catch (EmptyAuthorException $ex)
        {
            $response = $response_error;
            $response["message"] = "O nome do produtor não pode estar em branco.";
        }

        catch (EmptyWarrantyTimeException $ex)
        {
            $response = $response_error;
            $response["message"] = "A garantia não pode estar em branco.";
        }

        catch (EmptyProductTypeException $ex)
        {
            $response = $response_error;
            $response["message"] = "O tipo do produto não pode estar em branco.";
        }

        catch (EmptyProductDeliveryException $ex)
        {
            $response = $response_error;
            $response["message"] = "O tipo de entrega do produto não pode estar em branco.";
        }

        catch (EmptyPaymentTypeException $ex)
        {
            $response = $response_error;
            $response["message"] = "O tipo de pagamento não pode estar em branco.";
        }

        catch (EmptyCategoryException $ex)
        {
            $response = $response_error;
            $response["message"] = "A categoria não pode estar em branco.";
        }

        catch (EmptyAttachmentURLException $ex)
        {
            $response = $response_error;
            $response["message"] = "A url do conteúdo do produto não pode estar em branco.";
        }

        catch (EmptyAttachmentFileException $ex)
        {
            $response = $response_error;
            $response["message"] = "Faça upload do conteúdo do produto.";
        }

        catch (CategoryNotFoundException $ex)
        {
            $response = $response_error;
            $response["message"] = "A categoria não foi encontrada.";
        }

        catch (MinimumProductPriceNotReachedException $ex)
        {
            $response = $response_error;
            $response["message"] = "O valor mínimo para o produto é ".$ex->getMessage().".";
        }

        finally
        {
            Response::json($response);
        }
    }

    public function update_settings(Request $request, $id)
    {
        $user = $this->user;

        $response = [];
        $response_error = ["status" => "error", "message" => ""];

        $body = $request->json();
        $pix_enabled = ($body->pix_enabled ?? '') ? 1 : 0;
        $credit_card_enabled = ($body->credit_card_enabled ?? '') ? 1 : 0;
        $billet_enabled = ($body->billet_enabled ?? '') ? 1 : 0;
        $pix_discount_enabled = ($body->pix_discount_enabled ?? '') ? 1 : 0;
        $credit_card_discount_enabled = ($body->credit_card_discount_enabled ?? '') ? 1 : 0;
        $billet_discount_enabled = ($body->billet_discount_enabled ?? '') ? 1 : 0;
        $pix_discount_amount = $body->pix_discount_amount ?? '';
        $credit_card_discount_amount = $body->credit_card_discount_amount ?? '';
        $billet_discount_amount = $body->billet_discount_amount ?? '';
        $max_installments = $body->max_installments ?? 1;
        $pix_thanks_page_enabled = ($body->pix_thanks_page_enabled ?? '') ? 1 : 0;
        $pix_thanks_page_url = $body->pix_thanks_page_url ?? '';
        $credit_card_thanks_page_enabled = ($body->credit_card_thanks_page_enabled ?? '') ? 1 : 0;
        $credit_card_thanks_page_url = $body->credit_card_thanks_page_url ?? '';
        $billet_thanks_page_enabled = ($body->billet_thanks_page_enabled ?? '') ? 1 : 0;
        $billet_thanks_page_url = $body->billet_thanks_page_url ?? '';

        try
        {
            $product = Product::where('id', $id)->where('user_id', $user->id)->first();

            if (empty($product)) throw new ProductNotFoundException;

            $product->pix_enabled = $pix_enabled;
            $product->credit_card_enabled = $credit_card_enabled;
            $product->billet_enabled = $billet_enabled;
            $product->pix_discount_enabled = $pix_discount_enabled;
            $product->credit_card_discount_enabled = $credit_card_discount_enabled;
            $product->billet_discount_enabled = $billet_discount_enabled;
            if ($product->pix_discount_enabled && !$pix_discount_amount) throw new EmptyPixDiscountAmountException;
            if ($product->credit_card_discount_enabled && !$credit_card_discount_amount) throw new EmptyCreditCardDiscountAmountException;
            if ($product->billet_discount_enabled && !$billet_discount_amount) throw new EmptyBilletDiscountAmountException;
            $product->pix_discount_amount = $pix_discount_amount;
            $product->credit_card_discount_amount = $credit_card_discount_amount;
            $product->billet_discount_amount = $billet_discount_amount;
            $product->max_installments = $max_installments;
            $product->pix_thanks_page_enabled = $pix_thanks_page_enabled;
            if ($product->pix_thanks_page_enabled && !$pix_thanks_page_url) throw new EmptyPixThanksPageURLException;
            $product->pix_thanks_page_url = $pix_thanks_page_url;
            $product->credit_card_thanks_page_enabled = $credit_card_thanks_page_enabled;
            if ($product->credit_card_thanks_page_enabled && !$credit_card_thanks_page_url) throw new EmptyCreditCardThanksPageURLException;
            $product->credit_card_thanks_page_url = $credit_card_thanks_page_url;
            $product->billet_thanks_page_enabled = $billet_thanks_page_enabled;
            if ($product->billet_thanks_page_enabled && !$billet_thanks_page_url) throw new EmptyBilletThanksPageURLException;
            $product->billet_thanks_page_url = $billet_thanks_page_url;
            
            $product->save();

            $response = ["status" => "success", "message" => "Produto atualizado com sucesso."];
        }

        catch(ProductNotFoundException $ex)
        {
            $response = $response_error;
            $response["message"] = "Produto não encontrado.";
        }

        catch(EmptyPixDiscountAmountException $ex)
        {
            $response = $response_error;
            $response["message"] = "O valor de desconto no pix não pode estar vazio.";
        }

        catch(EmptyCreditCardDiscountAmountException $ex)
        {
            $response = $response_error;
            $response["message"] = "O valor de desconto no cartão de crédito não pode estar vazio.";
        }

        catch(EmptyBilletDiscountAmountException $ex)
        {
            $response = $response_error;
            $response["message"] = "O valor de desconto no boleto não pode estar vazio.";
        }

        catch(EmptyPixThanksPageURLException $ex)
        {
            $response = $response_error;
            $response["message"] = "A URL da página de obrigado no pix não pode estar em branco.";
        }

        catch(EmptyCreditCardThanksPageURLException $ex)
        {
            $response = $response_error;
            $response["message"] = "A URL da página de obrigado no cartão de crédito não pode estar em branco.";
        }

        catch(EmptyBilletThanksPageURLException $ex)
        {
            $response = $response_error;
            $response["message"] = "A URL da página de obrigado no boleto não pode estar em branco.";
        }

        finally
        {
            Response::json($response);
        }
    }
}
