<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Enums\BlogStatus;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Models\BlogPost;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListBlogPosts extends ListRecords
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /** I add a tab per BlogStatus so admins can jump straight to Drafts/Scheduled/Published. */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All'),
        ];

        foreach (BlogStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->label())
                ->modifyQueryUsing(fn ($query) => $query->where('status', $status))
                ->badge(BlogPost::where('status', $status)->count())
                ->badgeColor($status->colour());
        }

        return $tabs;
    }
}
