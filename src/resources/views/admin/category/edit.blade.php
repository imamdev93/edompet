@extends('admin.layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('category.update', $category->id) }} " method="POST">
                @method('PATCH')
                @csrf
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Edit Kategori</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Nama</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Nama" name="name" class="form-control"
                                    value="{{ old('name', $category->name) }}">
                                @error('name')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('name') }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Kode Warna</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Kode Warna" name="color" class="form-control"
                                    value="{{ old('color', $category->color) }}">
                                @error('color')
                                    <span class="form-text m-b-none text-danger">{{ $errors->first('color') }}</span>
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
