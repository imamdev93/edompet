<?php

namespace App\Http\Livewire;

use App\Models\Wallet;
use App\Models\WalletHistory;
use Livewire\Component;
use Livewire\WithPagination;

class WalletDetail extends Component
{
    use WithPagination;

    public $wallet;
    public $search;
    protected $paginationTheme = 'bootstrap';

    public function mount($wallet)
    {
        $this->wallet = $wallet;
    }

    public function getHistoriesProperty()
    {
        return WalletHistory::when($this->search, function ($query) {
            $query->where('type', $this->search)
                ->orWhere('amount', $this->search)
                ->orWhere('note', 'like', '%' . $this->search . '%')
                ->orWhereDate('created_at', $this->search);
        })->where('wallet_id', $this->wallet->id)->paginate(10);
    }

    public function render()
    {
        return view('livewire.wallet-detail', [
            'histories' => $this->histories,
            'wallets' => Wallet::all(),
        ]);
    }
}
