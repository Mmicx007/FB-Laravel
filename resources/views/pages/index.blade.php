@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mt-5 mb-4">Facebook Pages</h1>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-4 p-4">
        @foreach ($pages as $page)
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="cover-image-container" style="height: 150px; overflow: hidden;">
                <img src="{{ $page->cover_url }}" style="object-fit: cover; width: 100%; height: 100%;">
            </div>
            <div class="p-4">
                <div class="font-semibold text-sm">{{ $page->name }}</div>
                @if (!empty($page->email))
                    <div class="text-xs text-gray-500">{{ $page->email }}</div>
                @else
                    <div class="text-xs text-gray-500">&nbsp;</div>
                @endif

                @if (!empty($page->username))
                    <div class="text-xs text-gray-500">{{ $page->username }}</div>
                @else
                    <div class="text-xs text-gray-500">&nbsp;</div>
                @endif
            </div>
            <div class=" border-t px-4 py-2 font-bold text-sm">{{ $page->page_id }}</div>
        </div>
        @endforeach
    </div>
</div>
@endsection
