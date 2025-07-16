@extends('layouts.app')

@section('content')
<div class="d-flex">
    <!-- القائمة الجانبية -->
    @include('admin.layouts.sidebar')

    <!-- المحتوى الرئيسي -->
    <div class="flex-grow-1 p-4">
        @yield('admin_content')
    </div>
</div>
@endsection 