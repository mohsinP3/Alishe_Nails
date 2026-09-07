<?php

namespace App\Console\Commands;

use App\Services\InstagramFeedService;
use Illuminate\Console\Command;

class InstagramSyncCommand extends Command
{
    protected $signature = 'instagram:sync
        {--limit= : Maximum number of recent posts to fetch from the Graph API}';

    protected $description = 'Fetch the latest @alishe_nails Instagram posts into the instagram_posts table (homepage gallery)';

    public function handle(InstagramFeedService $service): int
    {
        $limit = $this->option('limit') !== null ? (int) $this->option('limit') : null;

        $result = $service->sync($limit);

        if ($result['success']) {
            $this->info($result['message']);

            return self::SUCCESS;
        }

        // Old cached posts stay untouched in the DB, so the site keeps
        // working — but the operator must see why nothing new arrived.
        $this->error($result['message']);

        return self::FAILURE;
    }
}
