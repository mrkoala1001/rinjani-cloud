@extends('p3pot.layouts.app')

@section('title', 'My Services')
@section('header_title', 'Customer Dashboard')

@section('content')
<div class="glass-card p-8 rounded-3xl text-center">
    <div class="w-16 h-16 bg-indigo-500/20 rounded-2xl flex items-center justify-center text-indigo-400 mx-auto mb-4">
        <i class="fas fa-box-open text-3xl"></i>
    </div>
    <h2 class="text-2xl font-bold text-white mb-2">Welcome Back!</h2>
    <p class="text-slate-400 max-w-md mx-auto">
        This is your personalized service dashboard. You'll be able to view your active subscriptions, billing history, and usage stats here.
    </p>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
         <div class="p-4 bg-slate-900/50 rounded-2xl border border-slate-800">
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest mb-1">Active Pack</p>
            <p class="text-white font-bold">No active packages</p>
         </div>
         <div class="p-4 bg-slate-900/50 rounded-2xl border border-slate-800">
            <p class="text-xs text-slate-500 uppercase font-bold tracking-widest mb-1">Next Billing</p>
            <p class="text-white font-bold">-</p>
         </div>
    </div>
</div>
@endsection
