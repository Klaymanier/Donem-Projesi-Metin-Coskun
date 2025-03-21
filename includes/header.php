<!DOCTYPE html>
<html lang="tr" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Mağazamız' : 'Mağazamız'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/img/favicon.ico" type="image/x-icon">
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - Mağazamız' : 'Mağazamız'; ?>">
    <meta property="og:description" content="Kaliteli ürünler, uygun fiyatlar ve güvenilir alışveriş deneyimi">
    <meta property="og:image" content="assets/img/og-image.jpg">
    <meta property="og:url" content="https://www.magazamiz.com">
    <meta property="og:type" content="website">
</head>

<body>
    <!-- Top Bar -->
    <div class="bg-primary text-white py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 small">
                    <span class="me-3"><i class="bi bi-telephone me-1"></i> (0212) 123 45 67</span>
                    <span><i class="bi bi-envelope me-1"></i> info@magazamiz.com</span>
                </div>
                <div class="col-md-6 text-md-end small">
                    <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-dark shadow-sm sticky-top">
        <div class="container py-3">
            <div class="row align-items-center">
                <!-- Logo -->
                <div class="col-md-3 mb-3 mb-md-0">
                    <a href="index.php" class="text-decoration-none">
                        <h1 class="h2 mb-0 text-blue fw-bold">DonemGiyim</h1>
                    </a>
                </div>

                <!-- Search -->
                <div class="col-md-5 mb-3 mb-md-0">
                    <form action="products.php" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Ürün ara..."
                                aria-label="Search">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end align-items-center">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown me-3">
                                <a href="#" class="text-decoration-none text-dark dropdown-toggle" id="userDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle fs-5 me-1"></i>
                                    <span class="d-none d-md-inline"><?php echo $_SESSION['user_name']; ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="profile.php"><i
                                                class="bi bi-person me-2"></i>Profilim</a></li>
                                    <li><a class="dropdown-item" href="orders.php"><i
                                                class="bi bi-box me-2"></i>Siparişlerim</a></li>
                                    <li><a class="dropdown-item" href="wishlist.php"><i
                                                class="bi bi-heart me-2"></i>Favorilerim</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="logout.php"><i
                                                class="bi bi-box-arrow-right me-2"></i>Çıkış Yap</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary btn-sm me-2">
                                <i class="bi bi-person me-1"></i>
                                <span class="d-none d-md-inline">Giriş Yap</span>
                            </a>
                            <a href="register.php" class="btn btn-primary btn-sm me-3">
                                <i class="bi bi-person-plus me-1"></i>
                                <span class="d-none d-md-inline">Kayıt Ol</span>
                            </a>
                        <?php endif; ?>

                        <a href="cart.php" class="text-decoration-none position-relative">
                            <i class="bi bi-cart3 fs-4 text-primary"></i>
                            <?php

                            // Değişken tanımlı değilse varsayılan değer ata
                            $sepetUrunSayisi = $sepetUrunSayisi ?? 0;
                            ?>

                            <?php if ($sepetUrunSayisi > 0): ?>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $sepetUrunSayisi; ?>
                                </span>
                            <?php endif; ?>

                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo empty($pageTitle) || $pageTitle == 'Ana Sayfa' ? 'active' : ''; ?>"
                                href="index.php">Ana Sayfa</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Tüm Ürunler
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">T-shirt</a></li>
                                <li><a class="dropdown-item" href="#">Esofman Alti</a></li>
                            </ul>
                        </li>
                        <?php
                        // Değişken tanımlı değilse varsayılan bir boş dizi atanır
                        $menuKategoriler = $menuKategoriler ?? [];

                        foreach ($menuKategoriler as $kategori): ?>
                            <?php
                            // Güvenlik ve okunabilirlik için veriyi işle
                            $kategoriAdi = htmlspecialchars($kategori['kategori_adi']);
                            $kategoriSlug = htmlspecialchars($kategori['kategori_slug']);
                            $isActive = (isset($currentKategori) && $currentKategori == $kategori['kategori_slug']) ? 'active' : '';
                            ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $isActive; ?>"
                                    href="products.php?kategori=<?php echo $kategoriSlug; ?>">
                                    <?php echo $kategoriAdi; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $pageTitle == 'Kampanyalar' ? 'active' : ''; ?>"
                                href="campaigns.php">Kampanyalar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $pageTitle == 'Hakkımızda' ? 'active' : ''; ?>"
                                href="about.php">Hakkımızda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $pageTitle == 'İletişim' ? 'active' : ''; ?>"
                                href="contact.php">İletişim</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>