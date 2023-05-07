<div class="form-group row">
    <label class="col-lg-2 col-form-label">Riwayat Dompet</label>
    <div class="col-lg-10 table-responsive" style="margin-top: 8px">
        <div class="row mb-3">
            <div class="col-md-3">
               <input type="text" wire:model.debounce.500ms="search" class="form-control" placeholder="search">
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Tipe Transaksi</th>
                    <th width="25%">Jumlah</th>
                    <th width="25%">Catatan</th>
                    <th width="25%">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @if (count($histories) > 0)
                    @foreach ($histories as $history)
                        <tr>
                            <td>#</td>
                            <td>
                                @if ($history->type == \App\Enums\TypeStatusEnum::pemasukan())
                                    <span class="badge badge-success">{{ $history->type }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $history->type }}</span>
                                @endif
                            </td>
                            <td>Rp. {{ number_format($history->amount, 0, '.', '.') }}</td>
                            <td>{{ $history->note ?? '-' }}</td>
                            <td>{{ $history->created_at }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center">Data tidak ditemukan</td>
                    </tr>
                @endif
            {{$histories->links()}}

            </tbody>

        </table>
    </div>
</div>