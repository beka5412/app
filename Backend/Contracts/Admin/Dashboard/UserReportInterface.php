<?php

namespace Backend\Contracts\Admin\Dashboard;

interface UserReportInterface {
    public function withTotalUsers(): int;
    public function withLastUsers(): int;
}