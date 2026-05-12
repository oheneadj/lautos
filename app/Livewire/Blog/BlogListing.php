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
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('excerpt', 'like', "%{$this->search}%");
            }))
            ->when($this->categoryFilter, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $this->categoryFilter)))
            ->latest('published_at')
            ->paginate(9);

        $categories = BlogCategory::orderBy('name')->get();

        return view('livewire.blog.blog-listing', compact('posts', 'categories'));
    }
}
