<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OMEGA INFORMATIQUE CONSULTING - Gestion du Personnel GRH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0d1117; color: #c9d1d9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-omega { background: linear-gradient(135deg, #1a1e24 0%, #0d1117 100%); border-bottom: 2px solid #dc3545; }
        .card-omega { background-color: #161b22; border: 1px solid #30363d; border-top: 4px solid #dc3545; }
        .btn-omega { background-color: #dc3545; color: white; border: none; }
        .btn-omega:hover { background-color: #b02a37; color: white; }
        footer { background-color: #161b22; border-top: 1px solid #30363d; color: #8b949e; }
    </style>
</head>
<body>

<!-- Bannière de haute qualité OMEGA INFORMATIQUE CONSULTING -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-omega shadow-lg py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-uppercase d-flex align-items-center gap-3" href="index.php">
            <div class="bg-danger text-white p-2 rounded shadow"><i class="fas fa-network-wired fa-lg"></i></div>
            <div>
                <span class="text-danger fs-4">OMEGA INFORMATIQUE CONSULTING</span>
                <div class="small text-muted fw-normal" style="font-size: 0.75rem;">Gestion du Personnel GRH & Pointage Intelligent</div>
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-2">
                <li class="nav-item"><a class="nav-link text-light" href="index.php"><i class="fas fa-chart-line me-1 text-danger"></i> Tableau de bord</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="employes.php"><i class="fas fa-users me-1 text-danger"></i> Employés</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="pointage.php"><i class="fas fa-fingerprint me-1 text-danger"></i> Pointage</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="paie.php"><i class="fas fa-wallet me-1 text-danger"></i> Paie & Salaires</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="qrcodes_avis.php"><i class="fas fa-qrcode me-1 text-danger"></i> QR Codes</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="avis_admin.php"><i class="fas fa-comments me-1 text-danger"></i> Climat Social</a></li>
                <li class="nav-item ms-2">
                    <span class="badge bg-dark border border-danger text-light px-3 py-2">
                        <i class="fas fa-user-tie text-danger me-1"></i> Mr Mohamed Siby (+221 77 654 28 03)
                    </span>
                </li>
            </ul>
        </div>
    </div>
</nav>
