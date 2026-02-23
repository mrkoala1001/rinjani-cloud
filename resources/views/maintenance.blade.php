@extends('layouts.app')

@section('title', $title)
@section('header_title', $title)

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ $title }}</h3>
    <div class="p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700">
        <p class="font-bold">Under Construction</p>
        <p>This module is currently being ported to Laravel. Please check back later.</p>
    </div>
</div>
@endsection
