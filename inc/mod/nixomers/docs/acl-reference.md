# Nixomers ACL (Access Control List) Reference

Dokumen ini menjelaskan sistem ACL yang digunakan dalam modul Nixomers untuk mengontrol akses ke halaman dan tindakan tertentu berdasarkan level grup user GeniXCMS.

## 1. Pemetaan Role & Level User
Sistem ACL Nixomers menggunakan level grup user GeniXCMS (0-6) yang dipetakan ke role operasional:

| Role | Min. Level | Grup GeniXCMS | Deskripsi |
| :--- | :--- | :--- | :--- |
| **Super Admin** | 0 | Administrator | Akses penuh ke semua fitur dan pengaturan sistem. |
| **Admin** | 1 | Supervisor | Mengelola pesanan, analytics, dan operasional harian. |
| **Billing** | 2 | Editor | Fokus pada manajemen pembayaran dan transaksi keuangan. |
| **Fulfillment** | 3 | Author | Fokus pada proses packing, stok, dan pengiriman. |
| **CS (Customer Service)** | 4 | Contributor | Menangani detail pesanan dan follow-up pelanggan. |
| **Sales & Marketing** | 5 | VIP Member | Melihat data pesanan untuk kebutuhan pemasaran. |

---

## 2. Daftar ACL Halaman (Page Access)
Mengontrol siapa yang bisa melihat halaman/menu tertentu.

| Page ID | Required Role | Min. Level | Keterangan |
| :--- | :--- | :--- | :--- |
| `dashboard` | Sales | 5 | Melihat ringkasan statistik bisnis. |
| `orders` | Sales | 5 | Melihat daftar pesanan. |
| `orderdetail` | CS | 4 | Melihat detail lengkap pesanan & alamat. |
| `analytics` | Admin | 1 | Melihat laporan keuangan & performa mendalam. |
| `inventory` | Fulfillment | 3 | Melihat dan mengelola stok produk. |
| `settings` | Super Admin | 0 | Mengubah konfigurasi modul & gateway. |

---

## 3. Daftar ACL Tindakan (Action Access)
Mengontrol siapa yang bisa melakukan perubahan data.

| Action ID | Required Role | Min. Level | Keterangan |
| :--- | :--- | :--- | :--- |
| `order_create` | Sales | 5 | Membuat pesanan manual (POS). |
| `order_update_status` | Fulfillment | 3 | Mengubah status pesanan (Process, Ready, Ship). |
| `order_delete` | Super Admin | 0 | Menghapus data pesanan dari database. |
| `order_cancel` | Admin | 1 | Membatalkan pesanan & mengembalikan stok. |
| `order_refund` | Billing | 2 | Melakukan refund & pengembalian dana. |
| `payment_update` | Billing | 2 | Mengonfirmasi pembayaran & update detail transaksi. |
| `stock_update` | Fulfillment | 3 | Menambah/mengurangi stok secara manual. |
| `granular_update` | Fulfillment | 3 | Update SN/Barcode per unit produk. |

---

## 4. Implementasi Teknis
Nixomers menggunakan class standar `Acl.class.php` dari GeniXCMS. Semua izin diregistrasi secara otomatis saat modul diinisialisasi.

Gunakan method `Nixomers::checkACL($action)` untuk memvalidasi izin user di dalam kode. Method ini mendukung mapping internal atau penggunaan key ACL langsung.

```php
// Menggunakan internal action key (backward compatibility)
if (!Nixomers::checkACL('payment_update')) {
    die("Akses ditolak.");
}

// Atau menggunakan Key ACL langsung
if (!Acl::check('NIXOMERS_ORDER_REFUND')) {
    die("Akses ditolak.");
}
```

## 5. Daftar Permission Key (Acl.class.php)
| Key | Label | Default Groups |
| :--- | :--- | :--- |
| `NIXOMERS_DASHBOARD` | Access Nixomers Dashboard | 0, 1, 2, 3, 4, 5 |
| `NIXOMERS_ORDERS_VIEW` | View Orders List | 0, 1, 2, 3, 4, 5 |
| `NIXOMERS_ORDERS_DETAIL` | View Order Details | 0, 1, 2, 3, 4 |
| `NIXOMERS_ANALYTICS` | Access Analytics & Reports | 0, 1 |
| `NIXOMERS_INVENTORY` | Manage Inventory/Stock | 0, 1, 3 |
| `NIXOMERS_SETTINGS` | Modify Nixomers Settings | 0 |
| `NIXOMERS_ORDER_CREATE` | Create Manual Orders (POS) | 0, 1, 5 |
| `NIXOMERS_ORDER_STATUS` | Update Order Status | 0, 1, 3 |
| `NIXOMERS_ORDER_CANCEL` | Cancel Orders | 0, 1 |
| `NIXOMERS_ORDER_REFUND` | Process Refunds | 0, 1, 2 |
| `NIXOMERS_ORDER_DELETE` | Delete Orders Permanently | 0 |
| `NIXOMERS_PAYMENT_UPDATE` | Update Payment Details | 0, 1, 2 |
| `NIXOMERS_STOCK_UPDATE` | Manual Stock Adjustment | 0, 1, 3 |
| `NIXOMERS_GRANULAR_UPDATE` | Update SN/Barcode/Unit Tracking | 0, 1, 3 |
