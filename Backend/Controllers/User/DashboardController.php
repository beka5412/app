<?php

namespace Backend\Controllers\User;

use Backend\App;
use Backend\Controllers\Controller\AFrontendController;
use Backend\Controllers\Controller\TFrontendController;
use Backend\Http\Request;
use Backend\Template\View;
use Illuminate\Database\Capsule\Manager as DB;
use Backend\Enums\Order\{EOrderStatus, EOrderStatusDetail};
use Backend\Http\Response;
use Backend\Models\Award;
use Backend\Models\Balance;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Models\Customer;
use Backend\Models\OrderMeta;
use Backend\Models\Product;
use Backend\Models\User;
use Backend\Types\Response\EResponseDataStatus;
use Backend\Types\Response\ResponseData;
use Backend\Types\Response\ResponseStatus;

class DashboardController extends AFrontendController
{
    use TFrontendController;
    public string $title = 'Dashboard';
    public string $context = 'dashboard';
    public string $indexFile = 'frontend/view/user/dashboardView.php';

    public function view(string $view_method, Request $request, array $params=[], array $pagination=[])
    {
        extract((array) $this);
        extract($params);
        extract($pagination);

        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $balance = Balance::where('user_id', $user->id)->first();

        $today = today();
        $raw_start_datetime = $today;
        $raw_end_datetime = $today;
        // $raw_start_datetime = "2024-06-01 11:47:39";
        // $raw_end_datetime = "2024-06-22 11:47:39";
        $start_date = date("Y-m-d", strtotime($raw_start_datetime));
        $end_date = date("Y-m-d", strtotime($raw_end_datetime));
        $start_datetime = "$start_date 00:00:00";
        $end_datetime = "$end_date 23:59:59";
        

        $pending_orders_today = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::PENDING->value)
            ->where('created_at', '>=', $start_datetime)
            ->where('created_at', '<=', $end_datetime)
            ->sum('total');

        $approved_orders_today = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $start_datetime)
            ->where('created_at', '<=', $end_datetime)
            ->sum('total');

        
        $invoices_paid_today = 0;
        
        $sales_today = $approved_orders_today + $invoices_paid_today;
        $conversion_today = $pending_orders_today == 0 ? 0 : ($approved_orders_today / $pending_orders_today) * 100;

        $last_approved_orders = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::APPROVED->value)
            ->limit(10)
            ->orderBy('id', 'DESC')
            ->get();

        $last_7_days = [];
        $sales_last_week = [];
        for ($i = 0; $i < 7; $i++)
        {
            $last_7_days[] = date("d", strtotime(today() . " - $i days"));
            $start_days = $i + 1;
            $end_days = $i;
            $week_start_datetime = date("Y-m-d 00:00:00", strtotime(today() . " - $start_days days"));
            $week_end_datetime = date("Y-m-d 23:59:59", strtotime(today() . " - $end_days days"));
            // TODO: somar array com ultimos invoices e limitar por data
            $total = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $week_start_datetime)
            ->where('created_at', '<=', $week_end_datetime)
            ->orderBy('id', 'DESC')
            ->sum('total') ?: 0;
            $sales_last_week[] = (float) number_format($total, 2);
        }

        $last_7_days = array_reverse($last_7_days);
        $sales_last_week = array_reverse($sales_last_week);
     
        $last_30_days = [];
        $sales_last_month = [];
        for ($i = 0; $i < 30; $i++)
        {
            $last_30_days[] = date("d", strtotime(today() . " - $i days"));
            $start_days = $i + 1;
            $end_days = $i;
            $month_start_datetime = date("Y-m-d 00:00:00", strtotime(today() . " - $start_days days"));
            $month_end_datetime = date("Y-m-d 23:59:59", strtotime(today() . " - $end_days days"));
            // TODO: somar array com ultimos invoices e limitar por data
            $total = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $month_start_datetime)
            ->where('created_at', '<=', $month_end_datetime)
            ->orderBy('id', 'DESC')
            ->sum('total') ?: 0;
            $sales_last_month[] = (float) number_format($total, 2);
        }
        $last_30_days = array_reverse($last_30_days);
        $sales_last_month = array_reverse($sales_last_month);

        $last_12_months = [];
        $sales_last_12_months = [];
        for ($i = 0; $i < 12; $i++)
        {
            $last_12_months[] = date("F", strtotime(today() . " - $i months"));
            $start_months = $i + 1;
            $end_months = $i;
            $months_start_datetime = date("Y-m-d 00:00:00", strtotime(today() . " - $start_months months"));
            $months_end_datetime = date("Y-m-d 23:59:59", strtotime(today() . " - $end_months months"));
            // TODO: somar array com ultimos invoices e limitar por data
            $total = Order::where('user_id', $user->id)
            ->where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $months_start_datetime)
            ->where('created_at', '<=', $months_end_datetime)
            ->orderBy('id', 'DESC')
            ->sum('total') ?: 0;
            $sales_last_12_months[] = (float) number_format($total, 2);
        }

        $last_12_months = array_reverse($last_12_months);
        $sales_last_12_months = array_reverse($sales_last_12_months);

        $balance_amount = $balance->amount ?: 0;

        $awards = Award::orderBy('amount', 'ASC')->get();
        $target_balance = $awards[0]?->amount ?: 10_000;
        foreach ($awards as $award) if ($balance_amount >= $target_balance) $target_balance = $award->amount;

        $balance_percent = ($balance_amount / $target_balance) * 100;
        $balance_percent = $balance_percent > 100 ? 100 : $balance_percent;
        $products = Product::where('user_id', $user->id)->orderBy('id', 'DESC')->get();
           
        $count_orders = Order::where('user_id', $user->id)->count() ?? 0;

            $count_pix_sales = 0;
            $count_billet_sales = 0;
            $count_credit_card_sales = 0;
            $count_approved_sales = 0;
            $total_approved = 0;
    
            $aux = Order::select('id', 'total')->where('user_id', $user->id)->where('status', EOrderStatus::APPROVED->value)->get();
            foreach ($aux as $order)
            {
                $info_payment_method = $order->meta("info_payment_method");
                if ($info_payment_method == "pix") $count_pix_sales++;
                if ($info_payment_method == "billet") $count_billet_sales++;
                if ($info_payment_method == "credit_card") $count_credit_card_sales++;
                $count_approved_sales++;
                $total_approved += $order->total;
            }
    
            $sales_last_30_days = [];
            
                $sales = Order::where('user_id', $user->id)
                ->where('status', 'approved') // Filtra apenas vendas aprovadas
                ->orderBy('created_at', 'desc')
                ->take(5) // Pega apenas as últimas 5 vendas
                ->get();

                
                // 1. Buscar todas as ordens com status "aprovado" do usuário
                    $approvedOrders = Order::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->get(); // Pega as ordens aprovadas

                // 2. Criar um array para armazenar o resumo dos produtos
                $productsSummary = [];

                // 3. Iterar sobre as ordens aprovadas e processar os dados
                foreach ($approvedOrders as $order) {
                    // Obter o product_id da meta
                    $productId = $order->metas()->product_id ?? null;
                    $quantity = $order->metas()->quantity ?? 1;

                    if ($productId) {
                        if (!isset($productsSummary[$productId])) {
                            // Inicializa o resumo do produto
                            $product = Product::find($productId);
                            if ($product) {
                                $productsSummary[$productId] = [
                                    'name' => $product->name,
                                    'image' => $product->image,
                                    'price' => $product->price,
                                    'currency_symbol' => $product->currency_symbol,
                                    'total_quantity' => 0,
                                    'total_value' => 0,
                                ];
                            }
                        }

                        if (isset($productsSummary[$productId])) {
                            // Incrementa a quantidade de vendas
                            $productsSummary[$productId]['total_quantity'] += $quantity;

                            // Soma o valor total da ordem ao valor total de vendas
                            $productsSummary[$productId]['total_value'] += $order->total;
                        }
                    }
                }

                // 4. Ordenar os produtos do mais vendido para o menos vendido
                usort($productsSummary, function ($a, $b) {
                    return $b['total_quantity'] <=> $a['total_quantity'];
                });

                // 1. Quantidade total de vendas aprovadas
                $totalSalesCount = Order::where('user_id', $user->id)
                ->where('status', '!=', 'initiated') // Excluir ordens com status 'initiated'
                ->count();

                // 2. Quantidade de clientes únicos
                $totalCustomers = Customer::where('user_id', $user->id)
                ->count();

                // 3. Quantidade total de produtos
                $totalProducts = Product::where('user_id', $user->id)
                ->count();

        return View::$view_method($this->indexFile, compact(
            'context',
            'title',
            'user',
            'approved_orders_today',
            'pending_orders_today',
            'invoices_paid_today',
            'last_approved_orders',
            'sales_last_week',
            'sales_last_month',
            'sales_last_12_months',
            'sales_today',
            'conversion_today',
            'balance',
            'last_7_days',
            'last_30_days',
            'last_12_months',
            'balance_amount',
            'target_balance',
            'balance_percent',
            'products', 'total_approved', 'count_orders', 'count_pix_sales',
            'count_billet_sales', 'count_credit_card_sales', 'count_approved_sales', 
            'sales_last_30_days','sales','productsSummary','totalSalesCount', 
            'totalCustomers', 'totalProducts'
        ));
    }

    public function sales_filter(Request $request)
    {
        extract((array) $this);

        $start = $request->query('start');
        $end = $request->query('end');
        $product_id = $request->query('product_id');
        $data = [];

        // $start = date("Y-m-d 00:00:00", strtotime($start));
        // $end = date("Y-m-d 23:59:59", strtotime($end));

        // $last_12_months[] = date("F", strtotime(today() . " - $i months")); // se a quantidade de dias for > 31 dias, usar meses

        $days = round((strtotime($end) - strtotime($start)) / 60 / 60 / 24);
        $interval_type = $days > 31 ? 'months' : 'days';
        $interval_count = $days;

        $months = round((strtotime($end) - strtotime($start)) / 60 / 60 / 24 / 30);
        if ($interval_type === 'months') $interval_count = $months;

        $labels = [];
        $values = [];
        $dates = [];
        for ($i = 0; $i < $interval_count; $i++)
        {
            $step = date("d", strtotime($end . " - $i days"));

            $start_period = $i;
            $end_period = $i;
            if ($interval_count == 1) 
            {
                $end_period = 0;
                $start_period = 0;
                $start_datetime = $start;
                $end_datetime = $end;
            }
            else 
            {
                if ($interval_type === 'months')
                {
                    $end_period = $i + 1;
                    $start_datetime = date("Y-m-d 00:00:00", strtotime($start . " + $start_period $interval_type"));
                    $end_datetime = date("Y-m-d 23:59:59", strtotime($start . " + $end_period $interval_type"));
                    
                    if ($interval_type === 'months')
                        $step = date("F", strtotime($start_datetime));
                }
                else
                {
                    $start_datetime = date("Y-m-d 00:00:00", strtotime($start . " + $start_period $interval_type"));
                    $end_datetime = date("Y-m-d 23:59:59", strtotime($start . " + $end_period $interval_type"));
                }
            }

            $dates[] = [$start_datetime, $end_datetime];
            $labels[] = $step;
            // TODO: somar array com ultimos invoices e limitar por data
            $total = Order::where('user_id', $user->id)
            ->whereHas('new_meta', function($query) use($product_id) {
                $query->where('name', 'product_id')->where('value', $product_id);
            })
            ->where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $start_datetime)
            ->where('created_at', '<=', $end_datetime)
            ->orderBy('id', 'DESC')
            ->sum('total') ?: 0;
            $values[] = (float) number_format($total, 2);
        }
        if ($interval_type === 'days') $labels = array_reverse($labels);
        $data = compact('labels', 'values');
        // $data = compact('start', 'end', 'product_id', 'days', 'labels', 'values', 'dates', 'interval_count');

        $response_data = new ResponseData([
            'status' => EResponseDataStatus::SUCCESS,
            'message' => 'Vendas filtradas com sucesso.',
            'data' => $data
        ]);
        $response_status = new ResponseStatus('200 OK');
        return Response::json($response_data, $response_status);
    }

    public function members_area(Request $request)
    {
        $user = $this->user;

        if (!$user->members_access_token)
        {
            return header("location: /dashboard");
        }
        
        $token = base64_encode(env('PLATFORMS_AUTOLOGIN_USER'). ":" . env('PLATFORMS_AUTOLOGIN_PASS'));
        
        $headers = [
            'Authorization: Basic ' . $token,
            'Content-Type: application/json',
            'User-Agent: Migraz/1.0'
        ];

        $payload = [
            'token' => $token,
            'access_token' => $user->members_access_token
        ];

        $curl = curl_init("https://member.migraz.com/rocketpanel/login");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($curl);
        curl_close($curl);

        $body = json_decode($response);
        return !empty($body->url) ? header("location: $body->url") : header("location: /dashboard");
    }
}
