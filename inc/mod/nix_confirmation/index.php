<?php
/**
 * Name: Nixomers Confirmation
 * Desc: Manual Payment Confirmation for Nixomers
 * Version: 1.0.0
 * Build: 1.0.0
 * Developer: GeniXCMS
 * URI: https://genixcms.web.id/
 * License: MIT License
 * Icon: bi bi-check2-circle
 */

defined('GX_LIB') or die('Direct Access Not Allowed!');

// Register Autoloader for nix_confirmation Classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/lib/' . $class . '.class.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// 1. Initialization & DB Setup (Run once on Activation)
Hooks::attach('nix_confirmation_activate', function () {
    $sql = "CREATE TABLE IF NOT EXISTS `nix_confirmations` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `order_id` TEXT,
        `customer_name` TEXT,
        `bank_name` TEXT,
        `amount` REAL,
        `proof_image` TEXT,
        `status` TEXT DEFAULT 'pending', -- pending, approved, rejected
        `notes` TEXT,
        `date` DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    try {
        if (Db::connect()) {
            Db::$pdo->exec($sql);
        }
    } catch (Exception $e) {
    }

    // Ensure upload directory exists
    $dir = GX_PATH . '/assets/media/images/confirmations/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
});

Hooks::attach('nix_confirmation_delete', function () {
    try {
        if (Db::connect()) {
            Db::$pdo->exec("DROP TABLE IF EXISTS `nix_confirmations` ");
        }
    } catch (Exception $e) {
    }
});

// 2. Admin Menu Integration + Register Frontend Route
Hooks::attach('init', function () {
    if (class_exists('AdminMenu')) {
        AdminMenu::addChild('nixomers', [
            'label' => 'Payment Confirmations',
            'url' => 'index.php?page=mods&mod=nix_confirmation',
            'access' => 1
        ]);
    }

    // Register the 'confirmation' slug early
    Mod::addMenuList(['confirmation' => 'Payment Confirmation']);
});

// Enqueue GeniXCMS Base Styles before header output
Hooks::attach('header_load_lib', function () {
    $mod = $_GET['mod'] ?? '';
    if (empty($mod)) {
        $uri = $_SERVER['REQUEST_URI'];
        if (preg_match('/\/mod\/confirmation/i', $uri) || preg_match('/\/confirmation/i', $uri)) {
             $mod = 'confirmation';
        }
    }
    
    if ($mod == 'confirmation') {
        Asset::enqueue('genixcms-css');
    }
});

// 3. Frontend Route Dispatcher
Hooks::attach('mod_control', function ($args) {
    $data = $args[0] ?? $args ?? [];
    if (($data['mod'] ?? '') !== 'confirmation')
        return;

    // Handle Form Submission
    if (isset($_POST['order_id']) && isset($_POST['submit_confirmation'])) {
        $orderId = Typo::cleanX($_POST['order_id']);
        $order = Query::table('nix_orders')->where('order_id', $orderId)->first();

        if (!$order) {
            $GLOBALS['alertDanger'][] = "Order ID #{$orderId} not found.";
        } else {
            // Handle Upload
            $proof = "";
            if (!empty($_FILES['proof_image']['name'])) {
                $up = Upload::go('proof_image', '/assets/media/images/confirmations/', ['jpg', 'jpeg', 'png'], true);
                if (empty($up['error'])) {
                    $proof = 'assets/media/images/confirmations/' . $up['filename'];
                } else {
                    $GLOBALS['alertDanger'][] = "Upload Error: " . $up['error'];
                }
            }

            $ins = Query::table('nix_confirmations')->insert([
                'order_id' => $orderId,
                'customer_name' => Typo::cleanX($_POST['customer_name'] ?? ''),
                'bank_name' => Typo::cleanX($_POST['bank_name'] ?? ''),
                'amount' => (float) ($_POST['amount'] ?? 0),
                'proof_image' => $proof,
                'status' => 'pending'
            ]);

            if ($ins) {
                $GLOBALS['alertSuccess'][] = "Confirmation submitted! We will verify it shortly.";
            } else {
                $GLOBALS['alertDanger'][] = "Failed to save confirmation.";
            }
        }
    }

    // Render Frontend UI
    $themeOut = Nixomers::renderThemeView('confirmation', []);
    if ($themeOut !== false)
        return $themeOut;

    // Default UI (Premium Modern Design)
    $orderId = htmlspecialchars($_GET['order_id'] ?? '');
    $html = '
    <style>
        .nix-confirm-wrap { 
            margin: 4rem auto; 
            max-width: 1000px;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        
        .nix-sidebar { position: sticky; top: 20px; }
        .nix-confirm-card { 
            background: #ffffff;
            border-radius: 1.5rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        
        /* Subtle Decorative Blob */
        .nix-confirm-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, var(--gx-primary) 0%, #818cf8 100%);
            opacity: 0.05;
            border-radius: 50%;
            z-index: 0;
        }

        .nix-confirm-icon { 
            width: 76px; height: 76px; 
            border-radius: 20px; 
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            display: flex; align-items: center; justify-content: center; 
            margin-bottom: 1.25rem;
            box-shadow: 0 8px 16px -4px rgba(79, 70, 229, 0.15);
            transform: rotate(-5deg);
        }
        .nix-title { font-weight: 800; color: #0f172a; margin-bottom: 0.5rem; font-size: 1.75rem; letter-spacing: -0.025em; }
        .nix-subtitle { color: #64748b; font-size: 0.95rem; font-weight: 400; line-height: 1.6; }

        .nix-form { position: relative; z-index: 1; }
        
        .nix-upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            text-align: center;
            background: #f8fafc;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .nix-upload-zone:hover {
            border-color: var(--gx-primary);
            background: #eff6ff;
        }
        .nix-upload-zone input[type="file"] {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer; z-index: 2;
        }
        .nix-upload-icon { font-size: 2.5rem; color: #94a3b8; margin-bottom: 0.5rem; }
        .nix-upload-text { font-weight: 700; color: #334155; margin-bottom: 0.25rem; }
        .nix-upload-hint { font-size: 0.8rem; color: #94a3b8; margin: 0; }

        .nix-footer { font-size: 0.85rem; color: #94a3b8; font-weight: 500; }

        @media (max-width: 768px) {
            .nix-confirm-wrap { margin: 2rem 1rem; }
            .nix-sidebar { text-align: center; }
            .nix-confirm-icon { margin-left: auto; margin-right: auto; }
            .nix-confirm-card { padding: 1.5rem; }
            .nix-title { font-size: 1.5rem; }
        }
    </style>

    <div class="gx-container gx-container-lg nix-confirm-wrap">
        <div class="gx-row gx-g-4 gx-items-start gx-justify-center">
            
            <!-- Sidebar / Instructions -->
            <div class="gx-col gx-md-col-4">
                <div class="gx-card gx-glass gx-p-4 nix-sidebar shadow-lg">
                    <div class="nix-confirm-icon gx-shadow-sm"><i class="bi bi-shield-check"></i></div>
                    <h2 class="gx-h3 gx-mb-2">Konfirmasi Pembayaran</h2>
                    <p class="gx-text-muted gx-text-sm gx-mb-4 gx-fw-medium">Verifikasi transaksi Anda agar pesanan segera dikirim.</p>
                    
                    <div class="gx-mb-4">
                        <h4 class="gx-h6 gx-mb-3 gx-fw-bold gx-text-primary">Langkah-langkah:</h4>
                        <div class="gx-list-group gx-list-group-flush gx-bg-white gx-rounded gx-border gx-shadow-sm">
                            <div class="gx-list-group-item gx-d-flex gx-items-center gx-gap-3 gx-p-3">
                                <span class="gx-badge gx-badge-primary gx-p-2" style="width:24px; height:24px; display:flex; align-items:center; justify-content:center;">1</span>
                                <span class="gx-text-sm gx-fw-semibold">Isi Nomor Order</span>
                            </div>
                            <div class="gx-list-group-item gx-d-flex gx-items-center gx-gap-3 gx-p-3">
                                <span class="gx-badge gx-badge-primary gx-p-2" style="width:24px; height:24px; display:flex; align-items:center; justify-content:center;">2</span>
                                <span class="gx-text-sm gx-fw-semibold">Unggah Bukti Transfer</span>
                            </div>
                            <div class="gx-list-group-item gx-d-flex gx-items-center gx-gap-3 gx-p-3">
                                <span class="gx-badge gx-badge-primary gx-p-2" style="width:24px; height:24px; display:flex; align-items:center; justify-content:center;">3</span>
                                <span class="gx-text-sm gx-fw-semibold">Kirim Verifikasi</span>
                            </div>
                        </div>
                    </div>

                    <div class="gx-p-3 gx-rounded gx-bg-white gx-border gx-shadow-sm">
                        <p class="gx-text-xs gx-text-muted gx-m-0">
                            <strong>Butuh bantuan?</strong> Hubungi kami jika Anda mengalami kendala saat proses konfirmasi.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Area -->
            <div class="gx-col gx-md-col-8">
                ';
    // Handle Alerts
    if (isset($GLOBALS['alertSuccess'])) {
        $msgs = is_array($GLOBALS['alertSuccess']) ? $GLOBALS['alertSuccess'] : [$GLOBALS['alertSuccess']];
        foreach ($msgs as $msg) {
            $html .= '<div class="gx-alert gx-alert-success gx-items-center gx-shadow-lg gx-mb-4"><i class="bi bi-check-circle-fill gx-text-lg"></i><span>' . $msg . '</span></div>';
        }
    }
    if (isset($GLOBALS['alertDanger'])) {
        $msgs = is_array($GLOBALS['alertDanger']) ? $GLOBALS['alertDanger'] : [$GLOBALS['alertDanger']];
        foreach ($msgs as $msg) {
            $html .= '<div class="gx-alert gx-alert-danger gx-items-center gx-shadow-lg gx-mb-4"><i class="bi bi-exclamation-triangle-fill gx-text-lg"></i><span>' . $msg . '</span></div>';
        }
    }
    $html .= '
                <div class="gx-card gx-shadow-lg nix-confirm-card">
                    <form method="POST" enctype="multipart/form-data" class="nix-form">
                        
                        <div class="gx-form-group">
                            <label for="order_id" class="gx-label">Informasi Pesanan</label>
                            <div class="gx-input-group gx-shadow-sm gx-rounded-lg overflow-hidden">
                                <span class="gx-input-group-text gx-bg-light gx-px-4"><i class="bi bi-hash gx-text-primary"></i></span>
                                <input type="text" id="order_id" name="order_id" class="gx-input gx-input-lg gx-border-0" placeholder="Masukkan Order ID (Contoh: ORD-12345)" value="' . $orderId . '" required>
                            </div>
                        </div>

                        <div class="gx-form-row gx-g-3">
                            <div class="gx-col gx-md-col-6 gx-form-group">
                                <label for="customer_name" class="gx-label">Nama Pengirim</label>
                                <div class="gx-input-group gx-shadow-sm gx-rounded-lg overflow-hidden">
                                    <span class="gx-input-group-text gx-bg-light gx-px-3"><i class="bi bi-person gx-text-primary"></i></span>
                                    <input type="text" id="customer_name" name="customer_name" class="gx-input gx-border-0" placeholder="Nama Rekening" required>
                                </div>
                            </div>
                            <div class="gx-col gx-md-col-6 gx-form-group">
                                <label for="amount" class="gx-label">Jumlah Transfer</label>
                                <div class="gx-input-group gx-shadow-sm gx-rounded-lg overflow-hidden">
                                    <span class="gx-input-group-text gx-bg-light gx-px-3 gx-fw-bold gx-text-primary">Rp</span>
                                    <input type="number" id="amount" name="amount" class="gx-input gx-border-0" placeholder="0" min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="gx-form-group">
                            <label for="bank_name" class="gx-label">Bank Tujuan</label>
                            <div class="gx-input-group gx-shadow-sm gx-rounded-lg overflow-hidden">
                                <span class="gx-input-group-text gx-bg-light gx-px-3"><i class="bi bi-bank gx-text-primary"></i></span>
                                <input type="text" id="bank_name" name="bank_name" class="gx-input gx-border-0" placeholder="Contoh: BCA / Mandiri / BNI" required>
                            </div>
                        </div>

                        <div class="gx-form-group">
                            <label for="proofImage" class="gx-label">Bukti Pembayaran</label>
                            <div class="nix-upload-zone gx-shadow-sm gx-rounded-lg" id="nixUploadZone">
                                <i class="bi bi-cloud-arrow-up-fill nix-upload-icon gx-text-primary gx-mb-3" style="font-size:3rem"></i>
                                <div class="nix-upload-text gx-h5 gx-mb-2" id="nixUploadText">Ketuk untuk Mengunggah</div>
                                <p class="gx-text-sm gx-text-muted gx-m-0" id="nixUploadHint">Format file: JPG, PNG (Maks 2MB)</p>
                                <input type="file" name="proof_image" id="proofImage" accept="image/jpeg, image/png" required>
                            </div>
                        </div>

                        <div class="gx-mt-5">
                            <button type="submit" name="submit_confirmation" class="gx-btn gx-btn-primary gx-btn-block gx-p-4 gx-rounded-lg gx-shadow-lg">
                                <span class="gx-h5 gx-mb-0 text-white">Kirim Konfirmasi Sekarang</span> <i class="bi bi-arrow-right-circle-fill gx-ml-3 gx-h4 gx-mb-0"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="gx-text-center gx-mt-5 nix-footer gx-pb-5">
                    <div class="gx-d-inline-block gx-bg-white gx-px-4 gx-py-2 gx-rounded-full gx-shadow-sm gx-border">
                        <i class="bi bi-shield-lock-fill gx-text-success gx-mr-2"></i> <span class="gx-text-xs gx-fw-bold gx-text-muted">SSL SECURED & ENCRYPTED TRANSFORMATION</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const fileInput = document.getElementById("proofImage");
            const uploadText = document.getElementById("nixUploadText");
            const uploadHint = document.getElementById("nixUploadHint");
            const zone = document.getElementById("nixUploadZone");

            fileInput.addEventListener("change", function(e) {
                if (e.target.files.length > 0) {
                    const fileName = e.target.files[0].name;
                    uploadText.innerHTML = \'<i class="bi bi-check-circle-fill text-success me-2"></i>\' + fileName;
                    uploadHint.textContent = "Ready to upload";
                    zone.style.borderColor = "#10b981";
                    zone.style.background = "#f0fdf4";
                }
            });
            
            // Drag and drop visuals
            ["dragenter", "dragover"].forEach(eventName => {
                zone.addEventListener(eventName, e => {
                    zone.style.background = "#eff6ff";
                    zone.style.borderColor = "var(--nix-primary)";
                }, false);
            });
            ["dragleave", "drop"].forEach(eventName => {
                zone.addEventListener(eventName, e => {
                    if(fileInput.files.length === 0) {
                        zone.style.background = "#f8fafc";
                        zone.style.borderColor = "#cbd5e1";
                    }
                }, false);
            });
        });
    </script>';
    return $html;
});
