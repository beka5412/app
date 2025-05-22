<?php

namespace Backend\app\Services;

use Backend\Repositories\Admin\Dashboard\ReportRepository;
use ReflectionException;
use ReflectionMethod;

class ReportBuilder {
    private ReportRepository $reportRepository;
    private array $selectedStats = [];
    private array $methodMap = [
        'order_amount' => 'withOrderAmount',
        'order_count' => 'withOrderCount',
        'sales_today' => 'withSalestoday',
        'sales_average' => 'withAverageOrderValue',
        'gateway_tax' => 'withGatewayTax',
        'revenue' => 'withTotalRevenue',
        'total_users' => 'withTotalUsers',
        'last_users' => 'withLastUsers'
    ];

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function __call($name, $arguments): static
    {
        if (method_exists($this, $name)) {
            $this->$name(...$arguments);
        } elseif (in_array($name, $this->methodMap)) {
            $this->selectedStats[] = array_search($name, $this->methodMap);
        }
        return $this;
    }

    public function build(): array
    {
        $statistics = [];

        foreach ($this->selectedStats as $stat) {
            if (isset($this->methodMap[$stat])) {
                $method = $this->methodMap[$stat];
                try {
                    $reflection = new ReflectionMethod($this->reportRepository, $method);
                    $statistics[$stat] = $reflection->invoke($this->reportRepository);
                } catch (ReflectionException $e) {
                    // Log the error or handle it as appropriate for your application
                    continue;
                }
            }
        }

        return $statistics;
    }
}