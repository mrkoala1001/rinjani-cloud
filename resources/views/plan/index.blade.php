@extends('layouts.app')

@section('title', 'Pilih Plan HotPot')

@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Pricing Plans</h2>
            <p class="mt-2 text-4xl font-extrabold text-slate-900 sm:text-5xl lg:text-6xl">
                Tingkatkan Jangkauan Bisnis Anda
            </p>
            <p class="mt-4 max-w-2xl text-xl text-slate-500 mx-auto">
                Pilih paket yang sesuai dengan skala operasional RT/RW Net Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
            @foreach($plans as $code => $config)
            <div class="relative flex flex-col bg-white border {{ $code === 'pro' ? 'border-green-500 ring-2 ring-green-500 ring-opacity-50' : 'border-slate-200' }} rounded-2xl shadow-sm overflow-hidden transition-all hover:shadow-xl hover:-translate-y-1">
                @if($code === 'pro')
                <div class="absolute top-0 right-0 mt-4 mr-4">
                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        Paling Populer
                    </span>
                </div>
                @endif

                <div class="p-8 pb-0">
                    <h3 class="text-2xl font-bold text-slate-900">{{ $config['name'] }}</h3>
                    <p class="mt-4 flex items-baseline text-slate-900">
                        <span class="text-4xl font-extrabold tracking-tight">
                            {{ $code === 'basic' ? 'Gratis' : ($code === 'medium' ? 'Rp 40rb' : 'Rp 75rb') }}
                        </span>
                        @if($code !== 'basic')
                        <span class="ml-1 text-xl font-semibold text-slate-500">/bulan</span>
                        @else
                        <span class="ml-1 text-xl font-semibold text-slate-500">/3 bln</span>
                        @endif
                    </p>
                </div>

                <div class="flex-1 p-8 pt-6">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">Max Generate: {{ $config['quotas']['voucher_generate_max'] }} pcs</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">Max Distribusi: {{ $config['quotas']['voucher_distribution_max'] == -1 ? 'Unlimited' : number_format($config['quotas']['voucher_distribution_max']) }} voucher</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">Max Online: {{ $config['quotas']['voucher_online_max'] == -1 ? 'Unlimited' : $config['quotas']['voucher_online_max'] }} user</p>
                        </li>
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">PPPoE Active: {{ $config['quotas']['pppoe_active_max'] == -1 ? 'Unlimited' : $config['quotas']['pppoe_active_max'] }} user</p>
                        </li>
                        
                        @if($code !== 'basic')
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">WAN-IP STATIC & Pelanggan</p>
                        </li>
                        @endif

                        @if($code === 'pro')
                        <li class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check text-{{ $config['color'] }}-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm text-slate-600">Billing & Sales (Finansial)</p>
                        </li>
                        <li class="flex items-start text-green-600 font-bold">
                            <div class="flex-shrink-0">
                                <i class="fas fa-star text-green-500 mt-1"></i>
                            </div>
                            <p class="ml-3 text-sm">Full Akses Semua Fitur</p>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="p-8 border-t border-slate-100 bg-slate-50">
                    @if($user->plan === $code)
                        <button disabled class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-slate-400 cursor-not-allowed">
                            Plan Aktif Saat Ini
                        </button>
                    @elseif($code === 'basic')
                        <button disabled class="w-full flex items-center justify-center px-6 py-3 border border-slate-300 text-base font-medium rounded-xl text-slate-500 bg-white cursor-not-allowed">
                            Paket Dasar
                        </button>
                    @else
                        <button onclick="openPaymentModal('{{ $code }}', '{{ $config['name'] }}', '{{ $code === 'medium' ? '40.000' : '75.000' }}')" 
                                class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-{{ $config['color'] }}-600 hover:bg-{{ $config['color'] }}-700 shadow-md transition-colors">
                            Upgrade Sekarang
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12 text-center text-slate-500 text-sm">
            <p>Butuh bantuan? <a href="https://wa.me/6281234567890" class="text-blue-600 font-medium hover:underline">Hubungi Mr. Koala</a></p>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity" @click="closePaymentModal()" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-bold text-slate-900" id="modal-title">
                            Pembayaran Upgrade Plan
                        </h3>
                        <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-slate-500">Plan:</span>
                                <span class="font-bold text-slate-900" id="modalPlanName">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Total Tagihan:</span>
                                <span class="font-extrabold text-blue-600 text-lg" id="modalPlanPrice">Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="text-sm font-semibold text-slate-700 block mb-3">Pilih Metode Pembayaran:</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="selectMethod('qris')" id="btn-qris" class="flex flex-col items-center justify-center p-4 border-2 border-slate-200 rounded-xl hover:border-blue-500 transition-all group">
                                    <i class="fas fa-qrcode text-2xl text-slate-400 group-hover:text-blue-500"></i>
                                    <span class="mt-2 text-xs font-bold text-slate-600">QRIS</span>
                                </button>
                                <button onclick="selectMethod('bri_va')" id="btn-bri_va" class="flex flex-col items-center justify-center p-4 border-2 border-slate-200 rounded-xl hover:border-blue-500 transition-all group">
                                    <i class="fas fa-university text-2xl text-slate-400 group-hover:text-blue-500"></i>
                                    <span class="mt-2 text-xs font-bold text-slate-600">BRI VA</span>
                                </button>
                                <button onclick="selectMethod('bni_va')" id="btn-bni_va" class="flex flex-col items-center justify-center p-4 border-2 border-slate-200 rounded-xl hover:border-blue-500 transition-all group">
                                    <i class="fas fa-university text-2xl text-slate-400 group-hover:text-blue-500"></i>
                                    <span class="mt-2 text-xs font-bold text-slate-600">BNI VA</span>
                                </button>
                                <button onclick="selectMethod('permata_va')" id="btn-permata_va" class="flex flex-col items-center justify-center p-4 border-2 border-slate-200 rounded-xl hover:border-blue-500 transition-all group">
                                    <i class="fas fa-university text-2xl text-slate-400 group-hover:text-blue-500"></i>
                                    <span class="mt-2 text-xs font-bold text-slate-600">Permata VA</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-2xl">
                <button type="button" id="btnProsesBayar" onclick="processPayment()" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-3 bg-blue-600 text-base font-bold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-all disabled:opacity-50" disabled>
                    Proses Pembayaran
                </button>
                <button type="button" onclick="closePaymentModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-3 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedPlan = '';
    let selectedMethod = '';

    function openPaymentModal(plan, name, price) {
        selectedPlan = plan;
        document.getElementById('modalPlanName').innerText = name;
        document.getElementById('modalPlanPrice').innerText = 'Rp ' + price;
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        resetButtons();
    }

    function selectMethod(method) {
        selectedMethod = method;
        resetButtons();
        document.getElementById('btn-' + method).classList.add('border-blue-600', 'bg-blue-50', 'ring-2', 'ring-blue-200');
        document.getElementById('btn-' + method).querySelector('i').classList.add('text-blue-600');
        document.getElementById('btnProsesBayar').disabled = false;
    }

    function resetButtons() {
        const methods = ['qris', 'bri_va', 'bni_va', 'permata_va'];
        methods.forEach(m => {
            const btn = document.getElementById('btn-' + m);
            btn.classList.remove('border-blue-600', 'bg-blue-50', 'ring-2', 'ring-blue-200');
            btn.querySelector('i').classList.remove('text-blue-600');
        });
    }

    async function processPayment() {
        if (!selectedPlan || !selectedMethod) return;

        const btn = document.getElementById('btnProsesBayar');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

        try {
            const response = await fetch('{{ route('plan.purchase') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    plan: selectedPlan,
                    method: selectedMethod
                })
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.checkout_url;
            } else {
                alert('Error: ' + result.message);
                btn.disabled = false;
                btn.innerHTML = 'Proses Pembayaran';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem.');
            btn.disabled = false;
            btn.innerHTML = 'Proses Pembayaran';
        }
    }
</script>
@endsection
