<?php defined('GX_LIB') or die('Direct Access Not Allowed!');
/**
 * GeniXCMS - Content Management System
 *
 * PHP Based Content Management System and Framework
 * @package GeniXCMS
 * @since 0.0.1 build date 20150221
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * 
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
?>
<h3 class="fw-bold fs-4 mb-4 text-center">Step 0: Database Configuration</h3>
<p class="text-muted text-center mb-5">Configure your database connectivity to persist your digital assets.</p>

<form action="?step=1" method="post">
    <div class="row g-4 mb-5">
        <div class="col-md-12">
            <label class="form-label">Database Engine</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i
                        class="fa-solid fa-server text-muted"></i></span>
                <select name="dbdriver" class="form-select border-start-0 ps-0">
                    <option value="mysql">MySQL / MariaDB (Recommended)</option>
                    <option value="pgsql">PostgreSQL</option>
                    <option value="sqlite">SQLite</option>
                </select>
            </div>
            <div class="form-text small opacity-75">Select your preferred database storage backend.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Database Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i
                        class="fa-solid fa-database text-muted"></i></span>
                <input type="text" name="dbname" class="form-control border-start-0 ps-0" placeholder="e.g. genix_db"
                    required>
            </div>
            <div class="form-text small opacity-75">Ensure the database exists before proceeding.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Database Host</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i
                        class="fa-solid fa-cloud-bolt text-muted"></i></span>
                <input type="text" name="dbhost" class="form-control border-start-0 ps-0" value="localhost"
                    placeholder="e.g. localhost" required>
            </div>
            <div class="form-text small opacity-75">Commonly <kbd
                    class="py-0 px-1 bg-light text-muted border">localhost</kbd> or IP address.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Database Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i
                        class="fa-solid fa-user-tag text-muted"></i></span>
                <input type="text" name="dbuser" class="form-control border-start-0 ps-0" placeholder="e.g. root"
                    required>
            </div>
            <div class="form-text small opacity-75">The user authorized to manage your DB.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Database Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-key text-muted"></i></span>
                <input type="password" name="dbpass" class="form-control border-start-0 ps-0" placeholder="••••••••">
            </div>
            <div class="form-text small opacity-75">The password paired with your DB user.</div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" name="step1" class="btn btn-primary d-inline-flex align-items-center">
            Validate Connectivity <i class="fa fa-arrow-right ms-2 scale-hover transition-base"></i>
        </button>
    </div>
</form>