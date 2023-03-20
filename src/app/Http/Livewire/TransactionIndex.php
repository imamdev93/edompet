<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $search;
    public $perPage = 10;
    public $type;
    public $wallet_id;
    public $start_date;
    public $end_date;

    public function getTransactionsProperty()
    {
        return $this->transactionsQuery->paginate($this->perPage);
    }

    public function getTransactionsQueryProperty()
    {
        return Transaction::when($this->search, function ($query) {
            $query->whereHas('wallet', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
                ->orWhere('note', 'like', '%' . $this->search . '%')
                ->orWhere('amount', $this->search);
        })->when($this->type, function ($query) {
            $query->where('type', $this->type);
        })->when($this->wallet_id, function ($query) {
            $query->where('wallet_id', $this->wallet_id);
        })->when($this->start_date && $this->end_date, function ($query) {
            $query->whereBetween(DB::raw('date_format(created_at,"%Y-%m-%d")'), [$this->start_date, $this->end_date]);
        })->orderByDesc('created_at');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingWalletId()
    {
        $this->resetPage();
    }

    public function updatingStartDate()
    {
        $this->resetPage();
    }

    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->type = '';
        $this->wallet_id = '';
        $this->start_date = '';
        $this->end_date = '';
    }

    public function render()
    {
        return view('livewire.transaction-index', [
            'transactions' => $this->transactions,
            'wallets' => Wallet::get()
        ]);
    }
}
