# 2. Database: Keuangan & Billing

Dokumen ini menjelaskan struktur tabel yang berkaitan dengan pengelolaan uang (Pemasukan, Pengeluaran, Hutang).

---

## 1. Tabel `incomes` (Pemasukan)
Mencatat semua pemasukan uang, baik dari voucher, tagihan bulanan, atau sumber lain.
**Fitur Terkait:** Menu Billing -> Sales -> Monitor / Laporan Keuangan.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Transaksi. |
| `user_id` | `BIGINT` | **(PENTING)** ID Pemilik Data (Mitra). Relasi ke tabel `users`. |
| `date` | `DATE` | Tanggal Transaksi. |
| `category` | `VARCHAR` | Kategori: `Voucher`, `Member` (Tagihan), `Reseller`, `PPPoE`, `Other`. |
| `amount` | `DECIMAL` | Jumlah Uang Masuk (Rupiah). |
| `description` | `TEXT` | Keterangan Transaksi. |
| `payment_method` | `VARCHAR` | Metode Bayar: `Cash`, `Transfer`, `E-Wallet`. |
| `customer_id` | `BIGINT` | ID Pelanggan (Relasi ke `customer_members`). |
| `proof_image` | `VARCHAR` | Path file foto bukti pembayaran. |
| `created_at` | `TIMESTAMP` | Waktu pencatatan. |

---

## 2. Tabel `expenses` (Pengeluaran)
Mencatat biaya operasional seperti gaji, maintenance, listrik, dll.
**Fitur Terkait:** Menu Billing -> Expenses.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Transaksi. |
| `user_id` | `BIGINT` | **(PENTING)** ID Pemilik Data (Mitra). |
| `date` | `DATE` | Tanggal Pengeluaran. |
| `category` | `VARCHAR` | Kategori: `Gaji`, `Operasional`, `Maintenance`, `Alat`. |
| `amount` | `DECIMAL` | Jumlah Uang Keluar. |
| `description` | `TEXT` | Keterangan Pengeluaran. |

---

## 3. Tabel `debts` (Hutang)
Mencatat hutang perusahaan atau piutang.
**Fitur Terkait:** Menu Billing -> Debts.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Hutang. |
| `user_id` | `BIGINT` | **(PENTING)** ID Pemilik Data. |
| `creditor_name` | `VARCHAR` | Nama Pemberi Hutang. |
| `amount` | `DECIMAL` | Jumlah Hutang. |
| `date` | `DATE` | Tanggal Hutang. |
| `due_date` | `DATE` | Tanggal Jatuh Tempo. |
| `status` | `VARCHAR` | Status: `Unpaid`, `Paid`. |
| `description` | `TEXT` | Keterangan. |

---

## 4. Tabel `bank_accounts`
Menyimpan data rekening bank untuk tujuan transfer pelanggan.
**Fitur Terkait:** Menu Settings -> Bank Accounts.

| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | `BIGINT (PK)` | ID Rekening. |
| `user_id` | `BIGINT` | ID Pemilik Data. |
| `bank_name` | `VARCHAR` | Nama Bank (BCA, BRI, Mandiri, dll). |
| `account_number` | `VARCHAR` | Nomor Rekening. |
| `account_name` | `VARCHAR` | Atas Nama. |
