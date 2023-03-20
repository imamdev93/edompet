@extends('admin.layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('transaction.update', $transaction->id) }} " method="POST">
                @method('PATCH')
                @csrf
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Edit Transaksi</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Dompet</label>
                            <div class="col-lg-10">
                                <input type="hidden" name="wallet_id" value="{{ $transaction->wallet_id }}">
                                <input type="text" placeholder="Nama" class="form-control"
                                    value="{{ $transaction->wallet?->name }}" readonly>
                                @error('name')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('name') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Jumlah Sebelumnya</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Jumlah Sebelumnya" class="form-control" name="old_amount"
                                    value="{{ $transaction->amount }}" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Jumlah Seharusnya</label>
                            <div class="col-lg-10">
                                <input type="number" placeholder="Jumlah Seharusnya" class="form-control" name="amount"
                                    value="{{ old('amount') }}">
                                @error('amount')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('amount') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Catatan</label>
                            <div class="col-lg-10">
                                <textarea type="text" placeholder="Catatan" name="note" class="form-control" value="">{{ old('note', $transaction->note) }}</textarea>
                                @error('note')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('note') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Tipe Transaksi</label>
                            <div class="col-lg-10">
                                <select name="type" id="" class="form-control">
                                    <option value="">Pilih</option>
                                    <option value="pemasukan"
                                        {{ old('type', $transaction->type) == 'pemasukan' ? 'selected' : '' }}>Pemasukan
                                    </option>
                                    <option value="pengeluaran"
                                        {{ old('type', $transaction->type) == 'pengeluaran' ? 'selected' : '' }}>
                                        Pengeluaran</option>
                                </select>
                                @error('type')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('type') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Kategori Transaksi</label>
                            <div class="col-lg-10">
                                <select name="category_id" id="" class="select2_demo_3 form-control">
                                    <option value="">Pilih</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $transaction->categories()->first()?->id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach

                                </select>
                                @error('category_id')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('category_id') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
@endsection
