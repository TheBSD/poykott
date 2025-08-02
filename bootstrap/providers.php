<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use OwenIt\Auditing\AuditingServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    AuditingServiceProvider::class,
];
