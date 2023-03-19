<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search;
    public $perPage = 10;

    public function getCategoriesProperty()
    {
        return $this->categoriesQuery->paginate($this->perPage);
    }

    public function getCategoriesQueryProperty()
    {
        return Category::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        });
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render()
    {
        return view('livewire.category-index', [
            'categories' => $this->categories
        ]);
    }
}
