<?php

namespace Backend\Controllers\User\Subscription;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Illuminate\Database\Capsule\Manager as DB;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Enums\Order\EOrderStatusDetail;
use Ezeksoft\RocketZap\Enum\Event as RocketZapEvent;
use Backend\Exceptions\Subscription\SubscriptionNotFoundException;
use Backend\Exceptions\Subscription\OrderNotFoundException;
use Backend\Exceptions\Subscription\UserNotFoundException;
use Backend\Entities\Abstracts\CustomerSubscription;
use Backend\Models\Subscription;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Models\Customer;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'My Subscriptions';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/user/subscriptions/indexView.php';
        $this->user = user();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = get_current_route();

        $subscriptions = Subscription::with('order')
        ->whereHas('order', function($query) use ($user) { $query->where('user_id', $user->id); })
        ->orderBy('id', 'DESC')->paginate(10);

        $total_pending = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::PENDING->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_pending = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::PENDING->value)
        ->first()
        ->c ?? 0;
        
        $total_active = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::ACTIVE->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_active = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::ACTIVE->value)
        ->first()
        ->c ?? 0;
        
        $total_canceled = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::CANCELED->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_canceled = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::CANCELED->value)
        ->first()
        ->c ?? 0;

        View::render($this->indexFile, compact('title', 'context', 'user', 'subscriptions', 
        'total_pending', 'total_active', 'total_canceled', 'count_pending', 'count_active', 'count_canceled'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;
        
        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/subscriptions';

        $subscriptions = Subscription::with('order')
        ->whereHas('order', function($query) use ($user) { $query->where('user_id', $user->id); })
        ->orderBy('id', 'DESC')->paginate(10);

        $total_pending = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::PENDING->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_pending = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::PENDING->value)
        ->first()
        ->c ?? 0;
        
        $total_active = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::ACTIVE->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_active = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::ACTIVE->value)
        ->first()
        ->c ?? 0;
        
        $total_canceled = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('sum(o.total) as total_sum'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::CANCELED->value)
        ->first()
        ->total_sum ?? 0;
        
        $count_canceled = DB::table('subscriptions as s')
        ->join('orders as o', 's.order_id', '=', 'o.id')
        ->select(DB::raw('count(*) as c'))
        ->where('o.user_id', $user->id)
        ->where('s.status', ESubscriptionStatus::CANCELED->value)
        ->first()
        ->c ?? 0;

        View::response($this->indexFile, compact('title', 'context', 'user', 'subscriptions', 
        'total_pending', 'total_active', 'total_canceled', 'count_pending', 'count_active', 'count_canceled'));
    }

    public function cancel(Request $request, $subscription_id)
    {
        $title = $this->title;
        $context = $this->context;
        $user = $this->user;

        $page = $request->query('page') ?: 1;
        $per_page = 10;
        $url = site_url().'/subscriptions';
        
        try
        {
            if (
                !DB::table('subscriptions as s')
                ->join('orders as o', 's.order_id', '=', 'o.id')
                ->where('o.user_id', $user->id)
                ->where('s.id', $subscription_id)
                ->exists()
            ) return throw new SubscriptionNotFoundException;
    
            $subscription = Subscription::find($subscription_id);
            $subscription->status = ESubscriptionStatus::CANCELED;
            $subscription->save();
      
            $customer = Customer::find($subscription->customer_id);
            
            $order = Order::where('id', $subscription->id)->first();
            if (empty($order)) throw new OrderNotFoundException;
    
            $meta_payment_method = $order->meta('info_payment_method'); // metodo de pagamento (das metas do pedido)
            
            $user = User::find($order->user_id);
            if (empty($user)) throw new UserNotFoundException;
            
            $status_detail = EOrderStatusDetail::CANCELED;
            $rocketzap_event = RocketZapEvent::CANCELED;
    
            // busca os produtos comprados separando em duas categorias: "padrao do checkout" e "extras"
            extract(purchased_products($order));
    
            $email_data = (object) [
                'title' => 'Compra cancelada',
                'subject' => strtr("Cancelamento de :products", [":products" => join(", ", $product_names)]),
                'view' => 'canceledPurchaseCustomer'
            ];
    
            CustomerSubscription::cancel($order, $status_detail);
            CustomerSubscription::cancelServices(compact('order', 'email_data', 'rocketzap_event', 'products', 'status_detail', 'user',
                'meta_payment_method', 'customer', 'products_base'));

            $response = [
                'status' => 'success',
                'message' => 'Assinatura cancelada com sucesso.'
            ];
        }

        catch (SubscriptionNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Assinatura não encontrada.'];
        }

        catch (OrderNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'O pedido dessa assinatura não encontrado.'];
        }

        catch (UserNotFoundException)
        {
            $response = ['status' => 'error', 'message' => 'Erro ao tentar localizar o vendedor desta assinatura.'];
        }

        finally
        {
            Response::json($response);
        }
    }
}