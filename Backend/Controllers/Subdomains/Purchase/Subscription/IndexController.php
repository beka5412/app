<?php

namespace Backend\Controllers\Subdomains\Purchase\Subscription;

use Backend\App;
use Backend\Http\Request;
use Backend\Http\Response;
use Backend\Template\View;
use Backend\Enums\Order\EOrderStatusDetail;
use Ezeksoft\RocketZap\Enum\Event as RocketZapEvent;
use Backend\Entities\Abstracts\CustomerSubscription;
use Backend\Entities\Abstracts\Iugu\IuguChargeQueue;
use Backend\Enums\Subscription\ESubscriptionStatus;
use Backend\Services\
{
    IPag\IPag,
    PagarMe\PagarMe
};
use Backend\Models\Subscription;
use Backend\Models\Order;
use Backend\Models\User;
use Backend\Services\Iugu\IuguRest;

class IndexController
{
    public App $application;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Minhas Assinaturas';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/subdomains/purchase/subscriptions/indexView.php';
        $this->customer = customer();
        $this->per_page = 10;
        $this->subdomain = 'purchase';
        $this->domain = get_subdomain_serialized($this->subdomain);
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;
        $subdomain = $this->subdomain;

        $page = $request->query('page') ?: 1;
        $per_page = $this->per_page;
        $url = get_subdomain_serialized($subdomain).'/subscriptions';

        $info = $subscriptions = Subscription::where('customer_id', $customer->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);

        $locale = $_COOKIE['locale'] ?? 'pt_BR';

        View::render($this->indexFile, compact('subdomain', 'title', 'context', 'subscriptions', 'info', 'url', 'locale'));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $customer = $this->customer;
        $subdomain = $this->subdomain;

        $params = $request->pageParams();

        $page = $request->query('page') ?: 1;
        $per_page = $this->per_page;
        $url = get_subdomain_serialized($subdomain).'/subscriptions';

        $info = $subscriptions = Subscription::where('customer_id', $customer->id)->orderBy('id', 'DESC')
            ->paginate(perPage: $per_page, columns: ['*'], pageName: 'page', page: $page);
        
        $locale = $_COOKIE['locale'] ?? 'pt_BR';

        View::response($this->indexFile, compact('subdomain', 'title', 'context', 'subscriptions', 'info', 'url', 'locale'));
    }

    public function cancel(Request $request, $subscription_id)
    {
        $ipag = new IPag;
        $pagarme = new PagarMe;

        $customer = $this->customer;

        $subscription = Subscription::where('customer_id', $customer->id)->where('id', $subscription_id)->first();
        if (empty($subscription)) return Response::json(['status' => 'error', 'message' => 'Assinatura não encontrada.']);
        
        $order = Order::where('id', $subscription->order_id)->first();
        if (empty($order)) return Response::json(['status' => 'error', 'message' => 'Pedido não encontrado.']);

        $meta_payment_method = $order->meta('info_payment_method'); // metodo de pagamento (das metas do pedido)
        
        $user = User::find($order->user_id);
        if (empty($user)) return Response::json(['status' => 'error', 'message' => 'Erro ao tentar localizar o vendedor deste produto.']);
        
        $status_detail = EOrderStatusDetail::CANCELED;
        $rocketzap_event = RocketZapEvent::CANCELED;

        // busca os produtos comprados separando em duas categorias: "padrao do checkout" e "extras"
        extract(purchased_products($order));

        $email_data = (object) [
            'title' => 'Compra cancelada',
            'subject' => strtr("Cancelamento de :products", [":products" => join(", ", $product_names)]),
            'view' => 'canceledPurchaseCustomer'
        ];

        $gateway = $order->gateway; // ?: get_setting('gateway');

        if ($subscription->status === ESubscriptionStatus::CANCELED->value)
            return Response::json(['status' => 'error', 'message' => 'Esta assinatura já está cancelada.']);

        if (!$gateway)
            return Response::json(['status' => 'error', 'message' => 'Erro. O gateway dessa transação não foi encontrado.']);

        CustomerSubscription::cancel($order, $status_detail);
        // CustomerSubscription::cancelServices(compact('order', 'email_data', 'rocketzap_event', 'products', 'status_detail', 'user',
        //     'meta_payment_method', 'customer', 'products_base'));

        if ($gateway == 'ipag')
        {
            $ipag->cancelSubscription($order->meta('payment_ipag_subscription_id'));
        }

        else if ($gateway == 'pagarme')
        {
            $pagarme->cancelSubscription($order->meta('payment_pagarme_subscription_id'));            
        }
        
        else if ($gateway == 'stripe')
        {
            // $stripe->cancelSubscription($order->meta(''));            
        }

        else if ($gateway == 'iugu')
        {
            IuguChargeQueue::cancel($order->id);            
        }

        return Response::json(['status' => 'success', 'message' => 'Assinatura cancelada com sucesso.']);
    }
}