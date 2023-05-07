<div class="ibox ">
    <div class="ibox-title">
        <h5>Transaction List</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#" class="dropdown-item">Config option 1</a>
                </li>
                <li><a href="#" class="dropdown-item">Config option 2</a>
                </li>
            </ul>
            <a class="close-link">
                <i class="fa fa-times"></i>
            </a>
        </div>
    </div>
    <div class="ibox-content table-responsive">
        <div class="row mb-3">
            <div class="col-md-2">
                <select wire:model.lazy="wallet_id" class="form-control">
                    <option value="">Semua Dompet</option>
                    @foreach ($wallets as $wallet)
                        <option value="{{ $wallet->id }}">{{ $wallet->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select wire:model.lazy="category_id" class="form-control">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select wire:model.lazy="type" class="form-control">
                    <option value="">Semua Tipe Transaksi</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.lazy="start_date">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" wire:model.lazy="end_date">
            </div>
            <div class="col-md-1">
                <button wire:click="resetFilter" class="btn btn-danger">Reset</button>
            </div>
        </div>
        <div class="row justify-content-between mb-3">
            <div class="col-md-1">
                <select class="form-control" wire:model="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="cari">
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="15%">Dompet</th>
                    <th width="15%">Jumlah</th>
                    <th width="25%">Catatan</th>
                    <th width="10%">Tipe</th>
                    <th width="15%">Tanggal</th>
                    <th width="15%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if (count($transactions) > 0)
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>#</td>
                            <td>{{ $transaction->wallet?->name }}</td>
                            <td>{{ number_format($transaction->amount, 0, '.', '.') }}</td>
                            <td>{{ $transaction->note }}</td>
                            <td>
                                @if ($transaction->type == \App\Enums\TypeStatusEnum::pemasukan())
                                    <span class="badge badge-success">{{ $transaction->type }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $transaction->type }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($transaction->created_at)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y H:i') }}</td>
                            <td>
                                <form method="POST" action="{{ route('transaction.destroy', $transaction->id) }}">
                                    @method('DELETE')
                                    @csrf
                                    <a class="btn btn-sm btn-secondary"
                                        href="{{ route('transaction.show', $transaction->id) }}"><i
                                            class="fa fa-eye"></i></a>
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('transaction.edit', $transaction->id) }}"><i
                                            class="fa fa-edit"></i></a>
                                    {{-- <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button> --}}
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">Data tidak ditemukan</td>
                    </tr>
                @endif
            </tbody>
        </table>
        {{ $transactions->links() }}
    </div>
</div>
