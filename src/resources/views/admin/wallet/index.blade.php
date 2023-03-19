@extends('admin.layouts.app')

@section('content')
    <div class="row">
        @include('admin.notification')
        @include('admin.wallet.create')
        <div class="col-lg-12">
            @livewire('wallet-index')
        </div>
    </div>
@endsection
