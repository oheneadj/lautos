<?php

/**
 * @author Ohene Adjei
 */

namespace App\Console\Commands;

use App\Enums\BlogStatus;
use App\Models\BlogPost;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('blog:publish-scheduled')]
#[Description('Promote scheduled blog posts whose publish_at time has passed to Published.')]
class PublishScheduledPosts extends Command
{
    public function handle(): void
    {
        $count = BlogPost::where('status', BlogStatus::Scheduled)
            ->where('published_at', '<=', now())
            ->update(['status' => BlogStatus::Published]);

        $this->info("Published {$count} scheduled post(s).");
    }
}
