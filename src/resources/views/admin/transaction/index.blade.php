@extends('admin.layouts.app')

@section('content')
    <div class="row">
        @include('admin.notification')
        @include('admin.transaction.create')
        <div class="col-lg-12">
            @livewire('transaction-index')
        </div>
    </div>
@endsection
