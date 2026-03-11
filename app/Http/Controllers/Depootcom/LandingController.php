<?php

namespace App\Http\Controllers\Depootcom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the Depootcom landing page.
     */
    public function landing()
    {
        \Illuminate\Support\Facades\Log::info('Depootcom LandingController landing hit');
        $services = [
            [
                'title' => 'Hardware Solutions',
                'description' => 'Penyediaan dan instalasi perangkat keras mulai dari printer, laptop, cctv, hingga perangkat kasir pintar.',
                'icon' => 'microchip'
            ],
            [
                'title' => 'Software & Development',
                'description' => 'Pembuatan sistem berbasis web, mobile app, hingga integrasi API untuk otomatisasi bisnis Anda.',
                'icon' => 'code'
            ],
            [
                'title' => 'Networking',
                'description' => 'Melayani jasa konfigurasi internet seperti mikrotik dan perangkat jaringan lainnya.',
                'icon' => 'network-wired'
            ]
        ];

        $projects = [
            [
                'name' => 'Rinsride',
                'description' => 'Layanan rental motor modern dengan sistem manajemen armada yang terintegrasi.',
                'url' => 'https://rinsride.com',
                'tag' => 'Automotive Solution'
            ],
            [
                'name' => 'Hotpot Management',
                'description' => 'Solusi manajemen hotspot dan billing otomatis untuk ISP, Cafe, dan RT-RW Net.',
                'url' => 'http://hotpot.depootcom.site',
                'tag' => 'Network Management'
            ]
        ];

        return view('depootcom.index', [
            'services' => $services,
            'projects' => $projects
        ]);
    }

    /**
     * Handle the contact form submission.
     */
    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $data = $request->only('name', 'email', 'subject', 'message');

        try {
            // Send email to hudaipiwardani@gmail.com
            // Note: Requires SMTP setup in .env
            \Illuminate\Support\Facades\Mail::to('hudaipiwardani@gmail.com')->send(new \App\Mail\Depootcom\ContactMail($data));
            
            return back()->with('success', 'Pesan Anda telah berhasil dikirim! Tim kami akan segera menghubungi Anda.');
        } catch (\Exception $e) {
            // Fallback for demo or if mail fails
            return back()->with('success', 'Pesan Anda telah diterima! (Simulasi: Email akan terkirim jika SMTP sudah aktif).');
        }
    }
}
