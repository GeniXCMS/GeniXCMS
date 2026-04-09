<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GeniXCMS Installation Hub</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --gx-primary: #0f172a;
            --gx-accent: #3b82f6;
            --gx-bg: #f8fafc;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gx-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .install-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .install-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .install-header {
            background: var(--gx-primary);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        .install-header img {
            width: 80px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }
        .install-body {
            padding: 50px;
        }
        .install-footer {
            background: #f1f5f9;
            padding: 20px 40px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        .progress-steps::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }
        .step-item {
            z-index: 2;
            background: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s ease;
        }
        .step-item.active {
            border-color: var(--gx-accent);
            background: var(--gx-accent);
            color: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
        }
        .step-item.completed {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: var(--gx-accent);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .btn-primary {
            background: var(--gx-accent);
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card" data-aos="zoom-in" data-aos-duration="600">
            <div class="install-header">
                <img src="assets/images/genixcms-logo-sign-small.png" alt="GeniXCMS Logo">
                <h1 class="h3 fw-bold mb-1">GeniXCMS Persistence Hub</h1>
                <p class="mb-0 opacity-75">Welcome to the future of digital management</p>
            </div>
            <div class="install-body">
                <?php $step_num = isset($_GET['step']) ? (int)$_GET['step'] : 0; ?>
                <div class="progress-steps">
                    <div class="step-item <?=($step_num >= 0) ? ($step_num == 0 ? 'active' : 'completed') : ''; ?>">
                        <i class="fa <?= $step_num > 0 ? 'fa-check' : 'fa-database'; ?>"></i>
                    </div>
                    <div class="step-item <?=($step_num >= 1) ? ($step_num == 1 ? 'active' : 'completed') : ''; ?>">
                        <i class="fa <?= $step_num > 1 ? 'fa-check' : 'fa-globe'; ?>"></i>
                    </div>
                    <div class="step-item <?=($step_num >= 2) ? ($step_num == 2 ? 'active' : 'completed') : ''; ?>">
                        <i class="fa <?= $step_num > 2 ? 'fa-check' : 'fa-user-shield'; ?>"></i>
                    </div>
                    <div class="step-item <?=($step_num >= 3) ? ($step_num == 3 ? 'active' : 'completed') : ''; ?>">
                        <i class="fa <?= $step_num > 3 ? 'fa-check' : 'fa-flag-checkered'; ?>"></i>
                    </div>
                </div>
