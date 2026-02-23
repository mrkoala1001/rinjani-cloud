@extends('layouts.app')

@section('title', 'Detail Distribusi Voucher')
@section('header_title', 'Detail Distribusi Voucher')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-4">
        <a href="{{ route('voucher.distribution') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-purple-500">
        <div class="p-4 bg-gray-50 border-b">
            <h3 class="text-lg font-bold text-gray-700"><i class="fas fa-list mr-2"></i>Detail Distribusi</h3>
            <div class="mt-2 text-sm text-gray-600">
                <p><strong>Reseller:</strong> {{ $resellerName }}</p>
                <p><strong>Profile:</strong> {{ $profile }}</p>
                <p><strong>Total Vouchers:</strong> {{ $vouchers->count() }} pcs</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">No</th>
                        <th class="py-3 px-6 text-left">Username</th>
                        <th class="py-3 px-6 text-left">Password</th>
                        <th class="py-3 px-6 text-left">Profile</th>
                        <th class="py-3 px-6 text-right">Price</th>
                        <th class="py-3 px-6 text-center">Batch ID</th>
                        <th class="py-3 px-6 text-center">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach($vouchers as $index => $voucher)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ $index + 1 }}</td>
                        <td class="py-3 px-6 text-left">
                            <span class="font-mono font-bold">{{ $voucher->username }}</span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="font-mono">{{ $voucher->password }}</span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold">
                                {{ $voucher->profile }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right font-bold">
                            Rp {{ number_format($voucher->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <span class="text-xs text-gray-500">{{ $voucher->batch_id ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            @if($voucher->payment_status == 'paid')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                                <i class="fas fa-check-circle"></i> Lunas
                            </span>
                            @else
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs font-bold">
                                <i class="fas fa-clock"></i> Belum Lunas
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="4" class="py-3 px-6 text-right">TOTAL:</td>
                        <td class="py-3 px-6 text-right text-green-600">
                            Rp {{ number_format($vouchers->sum('price'), 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
