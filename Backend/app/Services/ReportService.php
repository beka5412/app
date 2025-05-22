<?php

namespace Backend\app\Services;

use Backend\Repositories\Admin\Dashboard\ReportRepository;

class ReportService
{
    private ReportRepository $reportRepository;
    private ReportBuilder $reportBuilder;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->reportBuilder = new ReportBuilder($reportRepository);
    }

    public function getOrders(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->reportRepository->getOrders();
    }

    public function getSelectedStatistics(): ReportBuilder
    {
        return $this->reportBuilder;
    }
}