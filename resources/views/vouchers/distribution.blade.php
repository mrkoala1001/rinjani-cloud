@extends('layouts.app')

@section('title', 'Voucher Distribusi')
@section('header_title', 'Voucher Distribusi per Reseller')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Generation Batches Section -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-green-500">
        <div class="p-4 bg-gray-50 border-b">
            <h3 class="text-lg font-bold text-gray-700"><i class="fas fa-history mr-2"></i>Riwayat Generate Voucher</h3>
            <p class="text-sm text-gray-500">Batch voucher yang baru saja di-generate (50 terakhir)</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Tanggal Generate</th>
                        <th class="py-3 px-6 text-left">Batch ID</th>
                        <th class="py-3 px-6 text-left">Reseller</th>
                        <th class="py-3 px-6 text-left">Profile</th>
                        <th class="py-3 px-6 text-left">Template</th>
                        <th class="py-3 px-6 text-right">Qty</th>
                        <th class="py-3 px-6 text-right">Total (Rp)</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($batches as $batch)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($batch->generated_at)->format('d M Y H:i') }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="font-mono text-xs bg-gray-100 px-1 rounded text-gray-600">{{ $batch->batch_id }}</span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="font-bold text-purple-700">{{ $batch->reseller_name ?? 'Admin' }}</span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold">
                                {{ $batch->profile }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="text-xs text-gray-600">{{ $batch->template_name ?? 'Default' }}</span>
                        </td>
                        <td class="py-3 px-6 text-right">
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 font-bold">
                                {{ $batch->qty }} pcs
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right font-bold text-green-600">
                            Rp {{ number_format($batch->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button onclick="openPrintModal('{{ $batch->batch_id }}')" 
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                   title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                                <a href="{{ route('voucher.viewBatch', $batch->batch_id) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->role !== 'mitra-reseller' || session()->has('impersonated_by'))
                                <button onclick="deleteBatch('{{ $batch->batch_id }}')" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-3 px-6 text-center text-gray-500">
                            Belum ada batch voucher yang di-generate. Silakan generate voucher terlebih dahulu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Distribution Summary Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-purple-500">
        <div class="p-4 bg-gray-50 border-b">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-700"><i class="fas fa-chart-pie mr-2"></i>Ringkasan Distribusi per Reseller</h3>
                    <p class="text-sm text-gray-500">Ringkasan voucher yang telah didistribusikan ke masing-masing reseller</p>
                </div>
                <div class="w-64">
                    <select id="resellerFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Semua Reseller</option>
                        @foreach($resellers as $reseller)
                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal" id="distributionTable">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                        <th class="py-3 px-6 text-left">Nama Reseller</th>
                        <th class="py-3 px-6 text-left">Profile Voucher</th>
                        <th class="py-3 px-6 text-right">Price (Rp)</th>
                        <th class="py-3 px-6 text-right">Total Voucher</th>
                        <th class="py-3 px-6 text-right">Total Amount (Rp)</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($distributions as $dist)
                    <tr class="border-b border-gray-200 hover:bg-gray-100" data-reseller-id="{{ $dist->reseller_id }}">
                        <td class="py-3 px-6 text-left">
                            <span class="font-bold text-purple-700">{{ $dist->reseller_name }}</span>
                        </td>
                        <td class="py-3 px-6 text-left">
                            <span class="px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs font-bold">
                                {{ $dist->profile }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right font-bold">
                            Rp {{ number_format($dist->price, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-right">
                            <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 font-bold">
                                {{ $dist->total_vouchers }} pcs
                            </span>
                        </td>
                        <td class="py-3 px-6 text-right font-bold text-green-600">
                            Rp {{ number_format($dist->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            @if($dist->payment_status == 'paid')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-700 text-xs font-bold">
                                <i class="fas fa-check-circle mr-1"></i>Lunas
                            </span>
                            @else
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs font-bold">
                                <i class="fas fa-clock mr-1"></i>Belum Lunas
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('voucher.viewDistribution', ['reseller_id' => $dist->reseller_id, 'profile' => $dist->profile]) }}" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->role !== 'mitra-reseller' || session()->has('impersonated_by'))
                                <button onclick="deleteDistribution({{ $dist->reseller_id }}, '{{ $dist->profile }}')" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                                @if($dist->payment_status != 'paid')
                                <button onclick="markPaid({{ $dist->reseller_id }}, '{{ $dist->profile }}')" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                        title="Tandai Lunas">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-3 px-6 text-center">Tidak ada data distribusi voucher.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($distributions->count() > 0)
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="3" class="py-3 px-6 text-right">GRAND TOTAL:</td>
                        <td class="py-3 px-6 text-right">
                            {{ $distributions->sum('total_vouchers') }} pcs
                        </td>
                        <td class="py-3 px-6 text-right text-green-600">
                            Rp {{ number_format($distributions->sum('total_amount'), 0, ',', '.') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Modal Print -->
<div id="printModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full mx-4 overflow-hidden">
        <form id="printForm" action="" method="GET" target="_blank">
            <div class="p-4 border-b bg-gray-50">
                <h3 class="font-black text-gray-800"><i class="fas fa-print mr-2 text-yellow-500"></i>Pilih Template Print</h3>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Batch: <br><span id="printBatchIdCode" class="font-mono text-gray-700 font-black mt-1 inline-block bg-gray-100 px-2 py-1 rounded"></span></p>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Gunakan Format</label>
                    <select name="template_id" required class="w-full border-2 border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:outline-none focus:border-indigo-500 bg-white">
                        <option value="" disabled selected>-- Pilih Template --</option>
                        @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}">{{ $tpl->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="p-4 border-t flex justify-end gap-3 bg-gray-50">
                <button type="button" onclick="closePrintModal()" class="px-5 py-2.5 rounded-xl text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition">Batal</button>
                <button type="submit" onclick="closePrintModal()" class="px-5 py-2.5 bg-yellow-500 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-yellow-600 select-none shadow-md shadow-yellow-500/30 transition">Lanjut Print</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPrintModal(batchId) {
    document.getElementById('printModal').classList.remove('hidden');
    document.getElementById('printBatchIdCode').textContent = batchId;
    document.getElementById('printForm').action = '/hotsupport/voucher/print/' + batchId; // Update dynamically if prefix changed
    
    // Auto replace prefix according to base url
    let baseUrl = "{{ url('/') }}";
    // Usually routes are scoped, so let's just use named route dynamically? Non-trivial via js
    document.getElementById('printForm').action = `{{ url('/') }}/voucher/print/${batchId}`;
}
function closePrintModal() {
    document.getElementById('printModal').classList.add('hidden');
}

// Reseller filter
document.getElementById('resellerFilter').addEventListener('change', function() {
    const selectedResellerId = this.value;
    const rows = document.querySelectorAll('#distributionTable tbody tr[data-reseller-id]');
    
    rows.forEach(row => {
        if (selectedResellerId === '' || row.dataset.resellerId === selectedResellerId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Delete batch
function deleteBatch(batchId) {
    if (!confirm('Yakin ingin menghapus batch ini? Semua voucher dalam batch akan dihapus dari MikroTik dan database.')) {
        return;
    }
    
    fetch(`/voucher/batch/${batchId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Delete distribution
function deleteDistribution(resellerId, profile) {
    if (!confirm(`Yakin ingin menghapus semua voucher profile "${profile}" untuk reseller ini?`)) {
        return;
    }
    
    fetch('/voucher/distribution/delete', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reseller_id: resellerId,
            profile: profile
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Mark as paid
function markPaid(resellerId, profile) {
    if (!confirm('Tandai distribusi ini sebagai LUNAS?')) {
        return;
    }
    
    fetch('/voucher/distribution/mark-paid', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            reseller_id: resellerId,
            profile: profile
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}
</script>
@endsection
