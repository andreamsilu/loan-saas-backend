<?php

namespace App\Console\Commands;

use App\Modules\Subscription\Services\SubscriptionStatusService;
use Illuminate\Console\Command;

class SyncSubscriptionStatuses extends Command
{
    protected $signature = 'subscription:sync-statuses';

    protected $description = 'Sync tenant subscription statuses based on trial and billing state';

    public function handle(SubscriptionStatusService $service): int
    {
        $service->syncStatuses();
        $this->info('Subscription statuses synced');
        return Command::SUCCESS;
    }
}

