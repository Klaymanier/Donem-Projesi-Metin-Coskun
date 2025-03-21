<?php
// Header'ı dahil edin - eğer bu dosya başka bir dosyadan devam ediyorsa bu satırı ekleyin
require_once 'includes/header.php';
include_once 'database/db.php';
// Eğer formatPrice fonksiyonu tanımlanmamışsa
if (!function_exists('formatPrice')) {
    function formatPrice($price)
    {
        // Güvenli çevirme - sayısal değer kontrolü
        $price = is_numeric($price) ? $price : 0;
        return number_format($price, 2, ',', '.') . ' ₺';
    }
}

// Değişkenler tanımlanmış mı kontrol edin
if (!isset($sliders) || !is_array($sliders)) {
    // Eğer $sliders tanımlanmamışsa veya dizi değilse, varsayılan değerler atayın
    $sliders = [];
}

if (!isset($kategoriler) || !is_array($kategoriler)) {
    $kategoriler = [];
}

if (!isset($vitrinUrunleri) || !is_array($vitrinUrunleri)) {
    $vitrinUrunleri = [];
}

if (!isset($yeniUrunler) || !is_array($yeniUrunler)) {
    $yeniUrunler = [];
}

// İndirim yüzdesini hesaplayan yardımcı fonksiyon
if (!function_exists('calculateDiscountPercentage')) {
    function calculateDiscountPercentage($original, $discounted)
    {
        if ($original <= 0 || $discounted <= 0 || $discounted >= $original) {
            return 0;
        }
        return round(100 - ($discounted / $original * 100));
    }
}
?>

<?php if (!empty($sliders)): ?>
    <!-- Ana Slider -->
    <div id="homeSlider" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <?php foreach ($sliders as $key => $slider): ?>
                <button type="button" data-bs-target="#homeSlider" data-bs-slide-to="<?= $key ?>" <?= $key === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Slide <?= $key + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <div class="carousel-inner">
            <?php foreach ($sliders as $key => $slider): ?>
                <div class="carousel-item <?= $key === 0 ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($slider['gorsel_yolu']) ?>" class="d-block w-100"
                        alt="<?= htmlspecialchars($slider['baslik']) ?>">
                    <div class="carousel-caption d-none d-md-block">
                        <h2><?= htmlspecialchars($slider['baslik']) ?></h2>
                        <h5><?= htmlspecialchars($slider['alt_baslik']) ?></h5>
                        <p><?= htmlspecialchars($slider['aciklama']) ?></p>
                        <?php if (!empty($slider['link'])): ?>
                            <a href="<?= htmlspecialchars($slider['link']) ?>" class="btn btn-primary">Daha Fazla</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homeSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Önceki</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Sonraki</span>
        </button>
    </div>
<?php endif; ?>

<div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- İlk Ürün -->
        <div class="carousel-item active">
            <img src="assets/img/product_1.jpg" class="d-block w-100" alt="Ürün 1">
            <div class="carousel-caption d-none d-md-block">
                <h5>Ürün 1</h5>
                <p>Kısa açıklama</p>
                <a href="urun1.html" class="btn btn-primary">Detaylı İncele</a>
            </div>
        </div>
        <!-- İkinci Ürün -->
        <div class="carousel-item">
            <img src="assets/img/product_2.jpg" class="d-block w-100" alt="Ürün 2">
            <div class="carousel-caption d-none d-md-block">
                <h5>Ürün 2</h5>
                <p>Kısa açıklama</p>
                <a href="urun2.html" class="btn btn-primary">Detaylı İncele</a>
            </div>
        </div>
        <!-- Üçüncü Ürün -->
        <div class="carousel-item">
            <img src="assets/img/product_3.jpg" class="d-block w-100" alt="Ürün 3">
            <div class="carousel-caption d-none d-md-block">
                <h5>Ürün 3</h5>
                <p>Kısa açıklama</p>
                <a href="urun3.html" class="btn btn-primary">Detaylı İncele</a>
            </div>
        </div>
    </div>
    <!-- Kontroller -->
    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Önceki</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Sonraki</span>
    </button>
</div>


<!-- Kategoriler -->
<section class="mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Kategoriler</h2>
        <div class="row g-3">
            <?php if (!empty($kategoriler)): ?>
                <?php foreach ($kategoriler as $kategori): ?>
                    <div class="col-6 col-md-3">
                        <a href="products.php?kategori=<?= htmlspecialchars($kategori['kategori_slug']) ?>"
                            class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i
                                        class="bi <?= isset($kategori['ikon']) ? htmlspecialchars($kategori['ikon']) : 'bi-bag-heart' ?> fs-1 text-primary mb-2"></i>
                                    <h5 class="card-title"><?= htmlspecialchars($kategori['kategori_adi']) ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Henüz kategori eklenmemiş.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Vitrin Ürünleri -->
<section class="mb-5 bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Öne Çıkan Ürünler</h2>
        <div class="row g-4">
            <?php if (!empty($vitrinUrunleri)): ?>
                <?php foreach ($vitrinUrunleri as $urun): ?>
                    <div class="col-6 col-md-3">
                        <div class="card h-100 card-product">
                            <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                <?php $indirimYuzdesi = calculateDiscountPercentage($urun['urun_fiyat'], $urun['indirimli_fiyat']); ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-2">%<?= $indirimYuzdesi ?>
                                    İndirim</span>
                            <?php endif; ?>
                            <a href="product-detail.php?urun=<?= htmlspecialchars($urun['urun_slug']) ?>">
                                <img src="<?= isset($urun['ana_gorsel']) && $urun['ana_gorsel'] ? htmlspecialchars($urun['ana_gorsel']) : 'assets/img/products/default.jpg' ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($urun['urun_adi']) ?></h5>
                                <p class="card-text small text-muted">
                                    <?= isset($urun['kategori_adi']) ? htmlspecialchars($urun['kategori_adi']) : '' ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                            <span
                                                class="text-decoration-line-through text-muted"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                            <span
                                                class="text-danger fw-bold ms-1"><?= formatPrice($urun['indirimli_fiyat']) ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="product-detail.php?urun=<?= htmlspecialchars($urun['urun_slug']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Henüz vitrin ürünü eklenmemiş.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Yeni Ürünler -->
<section class="mb-5">
    <div class="container">
        <h2 class="text-center mb-4">Yeni Ürünler</h2>
        <div class="row g-4">
            <?php if (!empty($yeniUrunler)): ?>
                <?php foreach ($yeniUrunler as $urun): ?>
                    <div class="col-6 col-md-3">
                        <div class="card h-100 card-product">
                            <span class="badge bg-success position-absolute top-0 end-0 m-2">Yeni</span>
                            <a href="product-detail.php?urun=<?= htmlspecialchars($urun['urun_slug']) ?>">
                                <img src="<?= isset($urun['ana_gorsel']) && $urun['ana_gorsel'] ? htmlspecialchars($urun['ana_gorsel']) : 'assets/img/products/default.jpg' ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($urun['urun_adi']) ?></h5>
                                <p class="card-text small text-muted">
                                    <?= isset($urun['kategori_adi']) ? htmlspecialchars($urun['kategori_adi']) : '' ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                            <?php $indirimYuzdesi = calculateDiscountPercentage($urun['urun_fiyat'], $urun['indirimli_fiyat']); ?>
                                            <span
                                                class="text-decoration-line-through text-muted"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                            <span
                                                class="text-danger fw-bold ms-1"><?= formatPrice($urun['indirimli_fiyat']) ?></span>
                                        <?php else: ?>
                                            <span class="fw-bold"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="product-detail.php?urun=<?= htmlspecialchars($urun['urun_slug']) ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Henüz yeni ürün eklenmemiş.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="products.php?filter=yeni" class="btn btn-outline-primary">Tüm Yeni Ürünleri Gör</a>
        </div>
    </div>
</section>

<!-- Avantajlar Bölümü -->
<section class="mb-5 bg-light py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                    <h5>Ücretsiz Kargo</h5>
                    <p class="text-muted">250 TL ve üzeri alışverişlerde ücretsiz kargo</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
                    <h5>Güvenli Alışveriş</h5>
                    <p class="text-muted">SSL sertifikası ile güvenli ödeme</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-arrow-repeat fs-1 text-primary mb-3"></i>
                    <h5>Kolay İade</h5>
                    <p class="text-muted">14 gün içinde kolay iade imkanı</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                    <h5>7/24 Destek</h5>
                    <p class="text-muted">Müşteri hizmetleri desteği</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Abone Ol -->
<section class="mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h3>Bültenimize Abone Olun</h3>
                        <p class="text-muted">Yeni ürünler, indirimler ve kampanyalardan haberdar olmak için e-posta
                            listemize kaydolun.</p>
                        <form class="row g-3 justify-content-center" action="newsletter-subscribe.php" method="post">
                            <div class="col-md-8">
                                <input type="email" name="email" class="form-control" placeholder="E-posta adresiniz"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Abone Ol</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Footer'ı dahil edin
require_once 'includes/footer.php';
?>