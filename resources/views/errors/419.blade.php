@extends('errors.layout')

@section('title', 'Page Expired')
@section('code', '419')
@section('icon', 'fa-history')
@section('message', 'Halamannya terlalu lama, silakan refresh')
@section('description', 'Koneksi keamanan (CSRF Token) telah kedaluwarsa demi keamanan data Anda. Silakan muat ulang halaman.')

@section('extra_button')
<button onclick="window.location.reload()" class="bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-2xl transition-all border border-white/10">
    <i class="fas fa-sync mr-2"></i>Refresh Sekarang
</button>
@endsection
