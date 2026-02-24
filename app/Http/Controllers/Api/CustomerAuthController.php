<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $customer = \App\Models\CustomerMember::where('app_username', $request->username)
            ->where('app_password', $request->password)
            ->first();

        if ($customer) {
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'data' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'type' => $customer->type,
                    'location' => $customer->location,
                    'bill_amount' => $customer->bill_amount,
                    'device_name' => $customer->device_name,
                    'device_ip' => $customer->device_ip,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Username atau Password aplikasi salah'
        ], 401);
    }
}
