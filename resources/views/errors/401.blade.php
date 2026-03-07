@extends('errors.layout')

@section('title', 'Unauthorized')
@section('code', '401')
@section('icon', 'fa-door-closed')
@section('message', 'Login dulu ya, jangan asal dobrak pintu')
@section('description', 'Ups! Sepertinya Anda mencoba mengakses area terlarang tanpa kunci akses.')

@section('extra_button')
<a href="{{ route('depootcom.login') }}" class="bg-white/10 hover:bg-white/20 text-white font-bold py-4 px-8 rounded-2xl transition-all border border-white/10">
    <i class="fas fa-key mr-2"></i>Ke Halaman Login
</a>
@endsection
