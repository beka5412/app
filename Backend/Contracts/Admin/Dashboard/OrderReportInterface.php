<?php

namespace Backend\Contracts\Admin\Dashboard;

interface OrderReportInterface {
    public function getOrders();
    public function withOrderAmount(): float;
    public function withOrderCount(): int;
    public function withSalestoday(): float;
    public function withAverageOrderValue(): float;
    public function withGatewayTax(): float;
    public function withTotalRevenue(): float;
}