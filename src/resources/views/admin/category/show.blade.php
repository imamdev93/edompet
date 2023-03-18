@extends('admin.layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Detail Kategori</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Nama</label>
                        <div class="col-lg-10" style="margin-top: 8px">
                            {{ $category->name }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Slug</label>
                        <div class="col-lg-10" style="margin-top: 8px">
                            {{ $category->slug }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Riwayat</label>
                        <div class="col-lg-10 table-responsive" style="margin-top: 8px">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Dompet</th>
                                        <th width="25%">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($category->transactionDetail) > 0)
                                        @foreach ($category->transactionDetail as $history)
                                            <tr>
                                                <td>#</td>
                                                <td><a href="#">{{ $history->wallet->name ?: null }}</a></td>
                                                <td>Rp. {{ number_format($history->total, 0, '.', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ditemukan</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-offset-2 col-lg-10">
                            <a class="btn btn-sm btn-primary" href="{{ route('category.edit', $category->id) }}">Edit</a>
                            <a class="btn btn-sm btn-secondary" href="{{ route('category.index') }}">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
