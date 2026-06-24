<?php

namespace App\Livewire\Blog;

use App\Enums\BlogStatus;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;

class BlogListing extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';

    protected $queryString = [
        'search'         => ['except' => ''],
        'categoryFilter' => ['except' => '', 'as' => 'category'],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedCategoryFilter(): void { $this->resetPage(); }

    public function render()
    {
        $categories = BlogCategory::orderBy('name')->get();

        $viewData = [
            'categories' => $categories,
            'isFiltered' => !empty($this->search) || !empty($this->categoryFilter),
        ];

        if ($viewData['isFiltered']) {
            // Standard Paginated Search/Filter Results
            $viewData['posts'] = BlogPost::with(['category', 'author'])
                ->published()
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('excerpt', 'like', "%{$this->search}%");
                }))
                ->when($this->categoryFilter, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->categoryFilter)))
                ->latest('published_at')
                ->paginate(9);
        } else {
            // Complex Landing Page Data
            $latestPosts = BlogPost::with(['category', 'author'])
                ->published()
                ->latest('published_at')
                ->limit(8)
                ->get();

            // Slicing the latest 8 posts for the various top slots
            $viewData['featuredNews'] = $latestPosts->slice(0, 1)->first();
            $viewData['subFeaturedNews'] = $latestPosts->slice(1, 2);
            $viewData['topStory'] = $latestPosts->slice(3, 1)->first();
            $viewData['latestNews'] = $latestPosts->slice(4, 4);

            // Fetching sidebar content
            $excludedIds = $latestPosts->pluck('id')->toArray();
            
            $viewData['featuredStories'] = BlogPost::with(['category', 'author'])
                ->published()
                ->whereNotIn('id', $excludedIds)
                ->inRandomOrder()
                ->limit(3)
                ->get();
        }

        return view('livewire.blog.blog-listing', $viewData);
    }
}
