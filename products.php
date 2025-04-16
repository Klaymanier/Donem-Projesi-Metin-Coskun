<?php
// Gerekli dosyaları dahil et
require_once 'includes/config.php';
require_once 'database/db.php';

// Helper fonksiyonları
function formatPrice($price) {
    // Güvenli çevirme - sayısal değer kontrolü
    $price = is_numeric($price) ? $price : 0;
    return number_format($price, 2, ',', '.') . ' ₺';
}

function calculateDiscountPercentage($original, $discounted) {
    if ($original <= 0 || $discounted <= 0 || $discounted >= $original) {
        return 0;
    }
    return round(100 - ($discounted / $original * 100));
}

// URL parametrelerini al
$kategori_slug = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$arama = isset($_GET['arama']) ? $_GET['arama'] : '';
$siralama = isset($_GET['siralama']) ? $_GET['siralama'] : 'yeni';
$sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$limit = isset($ITEMS_PER_PAGE) ? $ITEMS_PER_PAGE : 12; // config.php'den veya varsayılan
$offset = ($sayfa - 1) * $limit;

// Varsayılan başlık
$sayfa_basligi = "Tüm Ürünler";

// Aktif kategoriyi izleme
$currentKategori = $kategori_slug;

// PDO ile SQL sorgularını hazırlama
$whereClauses = ["u.aktif = 1"];
$params = [];
$orderBy = "";

// Kategori filtreleme
if (!empty($kategori_slug)) {
    $whereClauses[] = "k.kategori_slug = :kategori_slug";
    $params[':kategori_slug'] = $kategori_slug;
    
    // Kategori adını al
    try {
        $kategoriStmt = $db->prepare("SELECT kategori_adi FROM kategoriler WHERE kategori_slug = :slug LIMIT 1");
        $kategoriStmt->bindParam(':slug', $kategori_slug);
        $kategoriStmt->execute();
        
        if ($kategoriData = $kategoriStmt->fetch()) {
            $sayfa_basligi = htmlspecialchars($kategoriData['kategori_adi']);
        }
    } catch (PDOException $e) {
        // Hata durumunda başlık değişmez
    }
}

// Özel filtreler
switch ($filter) {
    case 'indirimli':
        $whereClauses[] = "u.indirimli_fiyat > 0 AND u.indirimli_fiyat < u.urun_fiyat";
        $sayfa_basligi = "İndirimli Ürünler";
        break;
    case 'yeni':
        $whereClauses[] = "u.yeni_urun = 1";
        $sayfa_basligi = "Yeni Ürünler";
        break;
    case 'one_cikan':
        $whereClauses[] = "u.vitrin_urunu = 1";
        $sayfa_basligi = "Öne Çıkan Ürünler";
        break;
    case 'en_cok_satan':
        $whereClauses[] = "u.en_cok_satan = 1";
        $sayfa_basligi = "En Çok Satan Ürünler";
        break;
}

// Arama filtreleme
if (!empty($arama)) {
    $whereClauses[] = "(u.urun_adi LIKE :arama OR u.urun_aciklama LIKE :arama OR u.urun_kodu LIKE :arama)";
    $params[':arama'] = '%' . $arama . '%';
    $sayfa_basligi = "Arama Sonuçları: " . htmlspecialchars($arama);
}

// Sıralama seçenekleri
switch ($siralama) {
    case 'fiyat_artan':
        $orderBy = "CASE WHEN u.indirimli_fiyat > 0 AND u.indirimli_fiyat < u.urun_fiyat THEN u.indirimli_fiyat ELSE u.urun_fiyat END ASC";
        break;
    case 'fiyat_azalan':
        $orderBy = "CASE WHEN u.indirimli_fiyat > 0 AND u.indirimli_fiyat < u.urun_fiyat THEN u.indirimli_fiyat ELSE u.urun_fiyat END DESC";
        break;
    case 'eski':
        $orderBy = "u.created_at ASC";
        break;
    case 'yeni':
    default:
        $orderBy = "u.created_at DESC";
        break;
}

// WHERE koşullarını birleştir
$whereClause = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Toplam ürün sayısını hesapla
$countQuery = "SELECT COUNT(*) as total FROM urunler u 
               LEFT JOIN kategoriler k ON u.kategori_id = k.kategori_id 
               $whereClause";

try {
    $countStmt = $db->prepare($countQuery);
    foreach($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total_count = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (PDOException $e) {
    $total_count = 0;
}

// Toplam sayfa sayısını hesapla
$total_pages = ceil($total_count / $limit);

// Ürünleri getir
$urunQuery = "SELECT u.*, k.kategori_adi, k.kategori_slug 
              FROM urunler u
              LEFT JOIN kategoriler k ON u.kategori_id = k.kategori_id
              $whereClause
              ORDER BY $orderBy
              LIMIT :limit OFFSET :offset";

$urunler = [];
try {
    $urunStmt = $db->prepare($urunQuery);
    
    // Parametre bağlama
    foreach($params as $key => $value) {
        $urunStmt->bindValue($key, $value);
    }
    $urunStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $urunStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    $urunStmt->execute();
    $urunler = $urunStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Her ürün için görsel ve diğer bilgileri getir
    foreach ($urunler as $key => $urun) {
        // Ana görseli getir
        $gorselStmt = $db->prepare("
            SELECT gorsel_yolu FROM urun_gorselleri 
            WHERE urun_id = :urun_id AND ana_gorsel = 1 
            LIMIT 1
        ");
        $gorselStmt->bindParam(':urun_id', $urun['urun_id']);
        $gorselStmt->execute();
        
        if ($gorsel = $gorselStmt->fetch(PDO::FETCH_ASSOC)) {
            $urunler[$key]['ana_gorsel'] = $gorsel['gorsel_yolu'];
        } else {
            // Ana görsel yoksa herhangi bir görsel getir
            $gorselStmt = $db->prepare("
                SELECT gorsel_yolu FROM urun_gorselleri 
                WHERE urun_id = :urun_id 
                ORDER BY sira ASC 
                LIMIT 1
            ");
            $gorselStmt->bindParam(':urun_id', $urun['urun_id']);
            $gorselStmt->execute();
            
            if ($gorsel = $gorselStmt->fetch(PDO::FETCH_ASSOC)) {
                $urunler[$key]['ana_gorsel'] = $gorsel['gorsel_yolu'];
            } else {
                $urunler[$key]['ana_gorsel'] = 'assets/img/products/default.jpg';
            }
        }
        
        // Ürün renklerini getir
        $renkStmt = $db->prepare("
            SELECT r.renk_adi, r.renk_kodu
            FROM urun_varyasyonlari uv
            JOIN renkler r ON uv.renk_id = r.renk_id
            WHERE uv.urun_id = :urun_id
            GROUP BY r.renk_id
        ");
        $renkStmt->bindParam(':urun_id', $urun['urun_id']);
        $renkStmt->execute();
        $urunler[$key]['renkler'] = $renkStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Hata durumunda log tutulabilir
}

// Kategorileri getir (filtre seçenekleri için)
try {
    $kategoriStmt = $db->prepare("
        SELECT * FROM kategoriler 
        WHERE aktif = 1 
        ORDER BY kategori_adi ASC
    ");
    $kategoriStmt->execute();
    $kategoriler = $kategoriStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Header için de menü kategorilerini burada set edelim
    $menuKategoriler = $kategoriler;
} catch (PDOException $e) {
    $kategoriler = [];
    $menuKategoriler = [];
}

// Sepet ürün sayısını getir
try {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $sessionId = session_id();
    
    $sepetStmt = $db->prepare("
        SELECT COUNT(*) AS count FROM sepet_ogeleri so
        JOIN sepet s ON so.sepet_id = s.sepet_id
        WHERE s.kullanici_id = :kullanici_id OR s.session_id = :session_id
    ");
    $sepetStmt->bindParam(':kullanici_id', $userId);
    $sepetStmt->bindParam(':session_id', $sessionId);
    $sepetStmt->execute();
    $sepetUrunSayisi = $sepetStmt->fetch()['count'] ?? 0;
} catch (PDOException $e) {
    $sepetUrunSayisi = 0;
}

// Sayfa başlığını ve açıklamasını ayarla
$pageTitle = $sayfa_basligi . " | ModaVista";
$description = "ModaVista'da " . strtolower($sayfa_basligi) . " - Yüksek kalite ve uygun fiyatlarla.";

// Header'ı dahil et
include_once 'includes/header.php';
?>

<!-- Sayfa Başlığı -->
<div class="page-title-section">
    <div class="container">
        <h1 class="page-title"><?= $sayfa_basligi ?></h1>
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                <?php if (!empty($kategori_slug)): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= $sayfa_basligi ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active" aria-current="page">Ürünler</li>
                <?php endif; ?>
            </ol>
        </nav>
    </div>
</div>

<div class="container">
    <div class="products-container">
        <!-- Filtreler -->
        <div class="filters-sidebar">
            <div class="filters-card">
                <div class="filters-header">
                    <h5>Filtreler</h5>
                </div>
                <div class="filters-body">
                    <!-- Arama -->
                    <form action="products.php" method="get" class="search-filter">
                        <div class="form-group">
                            <input type="text" name="arama" class="filter-search-input" placeholder="Ürün ara..." value="<?= htmlspecialchars($arama) ?>">
                            <button class="filter-search-button" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <?php if (!empty($kategori_slug)): ?>
                            <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori_slug) ?>">
                        <?php endif; ?>
                        <?php if (!empty($filter)): ?>
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                        <?php endif; ?>
                    </form>
                    
                    <!-- Kategoriler -->
                    <div class="filter-section">
                        <h6 class="filter-title">Kategoriler</h6>
                        <ul class="filter-list">
                            <li class="filter-item <?= empty($kategori_slug) && empty($filter) ? 'active' : '' ?>">
                                <a href="products.php" class="filter-link">Tüm Ürünler</a>
                            </li>
                            <?php foreach ($kategoriler as $kategori): ?>
                                <li class="filter-item <?= $kategori_slug === $kategori['kategori_slug'] ? 'active' : '' ?>">
                                    <a href="products.php?kategori=<?= htmlspecialchars($kategori['kategori_slug']) ?>" 
                                       class="filter-link">
                                        <?= htmlspecialchars($kategori['kategori_adi']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Özel Filtreler -->
                    <div class="filter-section">
                        <h6 class="filter-title">Özel Filtreler</h6>
                        <ul class="filter-list">
                            <li class="filter-item <?= $filter === 'indirimli' ? 'active' : '' ?>">
                                <a href="products.php?filter=indirimli<?= !empty($kategori_slug) ? '&kategori=' . htmlspecialchars($kategori_slug) : '' ?>" 
                                   class="filter-link">
                                    İndirimli Ürünler
                                </a>
                            </li>
                            <li class="filter-item <?= $filter === 'yeni' ? 'active' : '' ?>">
                                <a href="products.php?filter=yeni<?= !empty($kategori_slug) ? '&kategori=' . htmlspecialchars($kategori_slug) : '' ?>" 
                                   class="filter-link">
                                    Yeni Ürünler
                                </a>
                            </li>
                            <li class="filter-item <?= $filter === 'one_cikan' ? 'active' : '' ?>">
                                <a href="products.php?filter=one_cikan<?= !empty($kategori_slug) ? '&kategori=' . htmlspecialchars($kategori_slug) : '' ?>" 
                                   class="filter-link">
                                    Öne Çıkan Ürünler
                                </a>
                            </li>
                            <li class="filter-item <?= $filter === 'en_cok_satan' ? 'active' : '' ?>">
                                <a href="products.php?filter=en_cok_satan<?= !empty($kategori_slug) ? '&kategori=' . htmlspecialchars($kategori_slug) : '' ?>" 
                                   class="filter-link">
                                    En Çok Satan Ürünler
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ürünler Listesi -->
        <div class="products-content">
            <!-- Sıralama ve Ürün Sayısı -->
            <div class="products-header">
                <div class="products-result">
                    <p>Toplam <span class="products-count"><?= $total_count ?></span> ürün bulundu</p>
                </div>
                <div class="products-sort">
                    <label for="siralama">Sırala:</label>
                    <select id="siralama" class="sort-select" onchange="window.location.href=this.value">
                        <?php
                        $queryParams = $_GET;
                        
                        // Sıralama seçenekleri
                        $sortOptions = [
                            'yeni' => 'En Yeniler',
                            'eski' => 'En Eskiler',
                            'fiyat_artan' => 'Fiyat: Düşükten Yükseğe',
                            'fiyat_azalan' => 'Fiyat: Yüksekten Düşüğe'
                        ];
                        
                        foreach($sortOptions as $value => $label) {
                            $queryParams['siralama'] = $value;
                            $queryString = http_build_query($queryParams);
                            $isSelected = $siralama === $value ? 'selected' : '';
                            echo "<option value=\"products.php?{$queryString}\" {$isSelected}>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            
            <!-- Ürünler -->
            <div class="products-grid">
                <?php if (!empty($urunler)): ?>
                    <?php foreach ($urunler as $urun): ?>
                        <div class="product-card">
                            <div class="product-thumb">
                                <img src="<?= isset($urun['ana_gorsel']) ? htmlspecialchars($urun['ana_gorsel']) : 'assets/img/products/default.jpg' ?>" 
                                     alt="<?= htmlspecialchars($urun['urun_adi']) ?>">
                                
                                <div class="product-badges">
                                    <?php if (isset($urun['yeni_urun']) && $urun['yeni_urun'] == 1): ?>
                                        <span class="product-badge badge-new">Yeni</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                        <?php $indirimYuzdesi = calculateDiscountPercentage($urun['urun_fiyat'], $urun['indirimli_fiyat']); ?>
                                        <span class="product-badge badge-sale">%<?= $indirimYuzdesi ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-actions">
                                    <button class="product-action" title="Favorilere Ekle" data-product-id="<?= $urun['urun_id'] ?>">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="product-action" title="Hızlı Bakış" data-product-id="<?= $urun['urun_id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="product-action" title="Karşılaştır" data-product-id="<?= $urun['urun_id'] ?>">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <span class="product-category"><?= isset($urun['kategori_adi']) ? htmlspecialchars($urun['kategori_adi']) : '' ?></span>
                                
                                <h3 class="product-title">
                                    <a href="product-detail.php?id=<?= $urun['urun_id'] ?>">
                                        <?= htmlspecialchars($urun['urun_adi']) ?>
                                    </a>
                                </h3>
                                
                                <div class="product-price">
                                    <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                        <span class="current-price"><?= formatPrice($urun['indirimli_fiyat']) ?></span>
                                        <span class="old-price"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                    <?php else: ?>
                                        <span class="current-price"><?= formatPrice($urun['urun_fiyat']) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if(!empty($urun['renkler'])): ?>
                                <div class="product-colors">
                                    <?php foreach($urun['renkler'] as $index => $renk): ?>
                                        <div class="color-option <?= $index === 0 ? 'active' : '' ?>" 
                                            style="background-color: <?= $renk['renk_kodu'] ?>" 
                                            title="<?= $renk['renk_adi'] ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <button class="add-to-cart" data-product-id="<?= $urun['urun_id'] ?>">
                                    <i class="fas fa-shopping-cart"></i> Sepete Ekle
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-products">
                        <div class="no-products-content">
                            <i class="fas fa-exclamation-circle"></i>
                            <h4>Ürün Bulunamadı</h4>
                            <p>Seçili kriterlere uygun ürün bulunamadı. Lütfen filtrelerinizi değiştirin veya tüm ürünleri görüntüleyin.</p>
                            <a href="products.php" class="btn-view-all">Tüm Ürünleri Göster</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sayfalama -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <ul class="pagination">
                        <?php if ($sayfa > 1): ?>
                            <?php
                            $queryParams = $_GET;
                            $queryParams['sayfa'] = $sayfa - 1;
                            $queryString = http_build_query($queryParams);
                            ?>
                            <li class="page-item">
                                <a class="page-link" href="products.php?<?= $queryString ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php
                            $queryParams = $_GET;
                            $queryParams['sayfa'] = $i;
                            $queryString = http_build_query($queryParams);
                            ?>
                            <li class="page-item <?= $i === $sayfa ? 'active' : '' ?>">
                                <a class="page-link" href="products.php?<?= $queryString ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($sayfa < $total_pages): ?>
                            <?php
                            $queryParams = $_GET;
                            $queryParams['sayfa'] = $sayfa + 1;
                            $queryString = http_build_query($queryParams);
                            ?>
                            <li class="page-item">
                                <a class="page-link" href="products.php?<?= $queryString ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Sepete Ekleme JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const favoriteButtons = document.querySelectorAll('.product-action[title="Favorilere Ekle"]');
    
    // Sepete ekle butonları
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
            // Sepete ekle butonu animasyonu
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ekleniyor...';
            this.disabled = true;
            
            // AJAX ile sepete ekleme
            fetch('ajax/sepet-ekle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'urun_id=' + productId + '&adet=1&_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Sepet sayısını güncelle
                    const sepetAdetElement = document.querySelector('.cart-count');
                    if (sepetAdetElement) {
                        sepetAdetElement.textContent = data.sepet_adet;
                        sepetAdetElement.style.display = 'inline-block';
                    }
                    
                    // Başarılı buton animasyonu
                    this.innerHTML = '<i class="fas fa-check"></i> Eklendi';
                    this.style.backgroundColor = 'var(--success-color)';
                    
                    // 2 saniye sonra butonu eski haline getir
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-shopping-cart"></i> Sepete Ekle';
                        this.style.backgroundColor = '';
                        this.disabled = false;
                    }, 2000);
                } else {
                    // Hata mesajı
                    this.innerHTML = '<i class="fas fa-times"></i> Hata';
                    this.style.backgroundColor = 'var(--error-color)';
                    
                    // 2 saniye sonra butonu eski haline getir
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-shopping-cart"></i> Sepete Ekle';
                        this.style.backgroundColor = '';
                        this.disabled = false;
                    }, 2000);
                    
                    alert(data.message || 'Ürün sepete eklenirken bir hata oluştu!');
                }
            })
            .catch(error => {
                console.error('Sepete ekleme hatası:', error);
                
                // Hata durumunda butonu eski haline getir
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Sepete Ekle';
                this.disabled = false;
                
                alert('Ürün sepete eklenirken bir hata oluştu!');
            });
        });
    });
    
    // Favori butonları
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const icon = this.querySelector('i');
            
            // Favori durumunu değiştir
            const isFavorite = icon.classList.contains('fas');
            
            // AJAX ile favori ekleme/çıkarma
            fetch('ajax/favori-toggle.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'urun_id=' + productId + '&islem=' + (isFavorite ? 'cikar' : 'ekle') + '&_token=<?= $_SESSION['csrf_token'] ?? '' ?>'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Favori icon'unu güncelle
                    if (isFavorite) {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.backgroundColor = '';
                    } else {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.backgroundColor = 'var(--secondary-color)';
                    }
                } else {
                    // Kullanıcı giriş yapmamışsa login sayfasına yönlendir
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'Bir hata oluştu, lütfen tekrar deneyin.');
                    }
                }
            })
            .catch(error => {
                console.error('Favori işlemi hatası:', error);
                alert('Bir hata oluştu, lütfen tekrar deneyin.');
            });
        });
    });
});
</script>

<?php
// Footer'ı dahil et
require_once 'includes/footer.php';