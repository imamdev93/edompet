@extends('admin.layouts.app')

@section('content')
    <div class="row">
        @include('admin.notification')
        @include('admin.category.create')
        <div class="col-lg-12">
            @livewire('category-index')
        </div>
    </div>
@endsection
