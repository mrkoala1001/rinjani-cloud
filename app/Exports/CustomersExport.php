<?php

namespace App\Exports;

use App\Models\CustomerMember;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersExport implements FromQuery, WithHeadings, WithMapping
{
    protected $type;

    public function __construct($type = 'all')
    {
        $this->type = $type;
    }

    public function query()
    {
        $query = CustomerMember::query();
        if ($this->type !== 'all') {
            $query->where('type', $this->type);
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Tipe',
            'WhatsApp',
            'Lokasi',
            'IP Device',
            'Tgl Pasang',
            'Tgl Jatuh Tempo',
            'Tagihan',
            'Status'
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->type,
            $customer->whatsapp,
            $customer->location,
            $customer->device_ip,
            $customer->installation_date,
            $customer->payment_date,
            $customer->bill_amount,
            $customer->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }
}
