@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('icon', 'fa-bug')
@section('message', 'Halaman dalam problamatika, hubungi admin sekarang')
@section('description', 'Terjadi kesalahan sistem di dapur kami. Tim teknis sedang berusaha memulangkan bug ini ke habitatnya.')

@section('extra_button')
<a href="https://wa.me/628123456789" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-8 rounded-2xl transition-all shadow-lg shadow-green-500/25">
    <i class="fab fa-whatsapp mr-2"></i>Lapor Admin
</a>
@endsection
