<?php

namespace App\Http\Livewire;

use App\Models\Wallet;
use Livewire\Component;
use Livewire\WithPagination;

class WalletIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search;
    public $perPage = 10;
    public $type;

    public function getWalletsProperty()
    {
        return $this->walletsQuery->paginate($this->perPage);
    }

    public function getWalletsQueryProperty()
    {
        return Wallet::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })->when($this->type, function ($query) {
            $query->where('type', $this->type);
        });
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.wallet-index', [
            'wallets' => $this->wallets
        ]);
    }
}
