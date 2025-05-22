<?php

namespace Backend\Repositories\Admin\Dashboard;

use Backend\Enums\Order\EOrderStatus;
use Backend\Models\Order;
use Backend\Contracts\Admin\Dashboard\ReportRepositoryInterface;
use Backend\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReportRepository implements ReportRepositoryInterface {
    /**
     * Returns a query builder for approved orders.
     *
     * @return Builder
     */
    private function approvedOrders(): Builder
    {
        return Order::where('status', EOrderStatus::APPROVED->value);
    }

    /**
     * Returns all approved orders.
     *
     * @return Builder
     */
    public function getOrders(): Builder
    {
        return Order::where('status', EOrderStatus::APPROVED->value);
    }

    /**
     * Calculates the total value of all approved orders.
     *
     * @return float
     */
    public function withOrderAmount(): float
    {
        return $this->approvedOrders()->sum('total');
    }

    /**
     * Counts the total number of approved orders.
     *
     * @return int
     */
    public function withOrderCount(): int
    {
        return $this->approvedOrders()->count();
    }

    /**
     * Calculates the total value of sales for the current day.
     *
     * @return float
     */
    public function withSalesToday(): float
    {
        return $this->approvedOrders()->where('created_at', '>=', Carbon::today()->toDateString())->sum('total');
    }

    /**
     * Calculates the average value of approved orders.
     *
     * @return float
     */
    public function withAverageOrderValue(): float
    {
        $total_order = $this->approvedOrders()->sum('total');
        $total_sales = Order::all()->count();

        if ($total_sales === 0) {
            return 0.0;
        }

        return $total_order / $total_sales;
    }

    /**
     * Calculates the total gateway fees for approved orders.
     *
     * @return float
     */
    public function withGatewayTax(): float
    {
        return $this->approvedOrders()->sum('total_gateway');
    }

    /**
     * Calculates the total revenue for vendors from approved orders.
     *
     * @return float
     */
    public function withTotalRevenue(): float
    {
        return $this->approvedOrders()->sum('total_vendor');
    }

    /**
     * Counts the total number of users.
     *
     * @return int
     */
    public function withTotalUsers(): int
    {
        return User::all()->count();
    }

    /**
     * Counts the number of new users in the last week.
     *
     * @return int
     */
    public function withLastUsers(): int
    {
        return User::where('created_at', '>=', Carbon::now()->subWeek())->orderBy('Desc')->count();
    }
}