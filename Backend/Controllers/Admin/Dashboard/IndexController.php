<?php

namespace Backend\Controllers\Admin\Dashboard;

use Backend\App;
use Backend\app\Services\ReportService;
use Backend\Enums\Order\EOrderStatus;
use Backend\Http\Request;
use Backend\Models\Invoice;
use Backend\Models\Order;
use Backend\Repositories\Admin\Dashboard\ReportRepository;
use Backend\Template\View;
use Backend\Models\Administrator;

class IndexController
{
    public App $application;
    private $reportService;

    public function __construct(App $application)
    {
        $this->application = $application;
        $this->title = 'Dashboard';
        $this->context = 'dashboard';
        $this->indexFile = 'frontend/view/admin/dashboard/indexView.php';
        $this->admin = admin();
    }

    private function getReportService()
    {
        if ($this->reportService === null)
        {
            $reportRepository = new ReportRepository();
            $this->reportService = new ReportService($reportRepository);
        }
        return $this->reportService;
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;

        $statistics = $this->getReportService()->getSelectedStatistics()
            ->withOrderAmount()
            ->withOrderCount()
            ->withSalestoday()
            ->withAverageOrderValue()
            ->withGatewayTax()
            ->withTotalRevenue()
            ->withTotalUsers()
            ->withLastUsers()
            ->build();

        // Todo refatorar código, o controller deve ter apenas uma responsabilidade
        $today = today();
        $raw_start_datetime = $today;
        $raw_end_datetime = $today;
        // $raw_start_datetime = "2024-06-01 11:47:39";
        // $raw_end_datetime = "2024-06-22 11:47:39";
        $start_date = date("Y-m-d", strtotime($raw_start_datetime));
        $end_date = date("Y-m-d", strtotime($raw_end_datetime));
        $start_datetime = "$start_date 00:00:00";
        $end_datetime = "$end_date 23:59:59";

        $pending_orders_today = Order::where('status', EOrderStatus::PENDING->value)
            ->where('created_at', '>=', $start_datetime)
            ->where('created_at', '<=', $end_datetime)
            ->sum('total');

        $approved_orders_today = Order::where('status', EOrderStatus::APPROVED->value)
            ->where('created_at', '>=', $start_datetime)
            ->where('created_at', '<=', $end_datetime)
            ->sum('total');

        $invoices_paid_today = Invoice::join('orders', 'invoices.order_id', '=', 'orders.id')
            ->where('invoices.paid', 1)
            ->where('invoices.paid_at', '>=', $start_datetime)
            ->where('invoices.paid_at', '<=', $end_datetime)
            ->sum('orders.total');

        $sales_today = $approved_orders_today + $invoices_paid_today;
        $conversion_today = $pending_orders_today == 0 ? 0 : ($approved_orders_today / $pending_orders_today) * 100;

        $last_approved_orders = Order::where('status', EOrderStatus::APPROVED->value)
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
            $total = Order::where('status', EOrderStatus::APPROVED->value)
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
            $total = Order::where('status', EOrderStatus::APPROVED->value)
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
            $total = Order::where('status', EOrderStatus::APPROVED->value)
                ->where('created_at', '>=', $months_start_datetime)
                ->where('created_at', '<=', $months_end_datetime)
                ->orderBy('id', 'DESC')
                ->sum('total') ?: 0;
            $sales_last_12_months[] = (float) number_format($total, 2);
        }
        $last_12_months = array_reverse($last_12_months);
        $sales_last_12_months = array_reverse($sales_last_12_months);

        View::render($this->indexFile, compact(
            'context',
            'title',
            'admin',
            'statistics',
            'sales_last_week',
            'sales_last_month',
            'sales_last_12_months',
            'last_7_days',
            'last_30_days',
            'last_12_months',
        ));
    }

    public function element(Request $request)
    {
        $title = $this->title;
        $context = $this->context;
        $admin = $this->admin;
        View::response($this->indexFile, compact('context', 'title', 'admin'));
    }
}