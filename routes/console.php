<?php

use App\Support\WorkspaceInstallation;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('workspace:recover', function (WorkspaceInstallation $installation): int {
    try {
        $this->info($installation->recover());

        return 0;
    } catch (Throwable) {
        $this->error('Workspace setup could not be recovered. Check that the original database and workspace storage are available.');

        return 1;
    }
})->purpose('Recover an interrupted first-time workspace setup from the hosting terminal');
