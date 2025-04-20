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
$pageTitle = "Tüm Ürünler";

// Aktif kategoriyi izleme
$currentKategori = $kategori_slug;

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
            $pageTitle = htmlspecialchars($kategoriData['kategori_adi']);
        }
    } catch (PDOException $e) {
        // Hata durumunda başlık değişmez
    }
}

// Özel filtreler
switch ($filter) {
    case 'indirimli':
        $whereClauses[] = "u.indirimli_fiyat > 0 AND u.indirimli_fiyat < u.urun_fiyat";
        $pageTitle = "İndirimli Ürünler";
        break;
    case 'yeni':
        $whereClauses[] = "u.yeni_urun = 1";
        $pageTitle = "Yeni Ürünler";
        break;
    case 'one_cikan':
        $whereClauses[] = "u.vitrin_urunu = 1";
        $pageTitle = "Öne Çıkan Ürünler";
        break;
    case 'en_cok_satan':
        $whereClauses[] = "u.en_cok_satan = 1";
        $pageTitle = "En Çok Satan Ürünler";
        break;
}

// Arama filtreleme
if (!empty($arama)) {
    $whereClauses[] = "(u.urun_adi LIKE :arama OR u.urun_aciklama LIKE :arama OR u.urun_kodu LIKE :arama)";
    $params[':arama'] = '%' . $arama . '%';
    $pageTitle = "Arama Sonuçları: " . htmlspecialchars($arama);
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
    $menuKategoriler = $kategoriStmt->fetchAll(PDO::FETCH_ASSOC);
    $kategoriler = $menuKategoriler;
} catch (PDOException $e) {
    // Hata durumunda varsayılan kategoriler kullanılabilir
    $kategoriler = [
        ['kategori_id' => 1, 'kategori_adi' => 'Kadın', 'kategori_slug' => 'kadin'],
        ['kategori_id' => 2, 'kategori_adi' => 'Erkek', 'kategori_slug' => 'erkek'],
        ['kategori_id' => 3, 'kategori_adi' => 'Çocuk', 'kategori_slug' => 'cocuk'],
        ['kategori_id' => 4, 'kategori_adi' => 'Aksesuar', 'kategori_slug' => 'aksesuar']
    ];
    $menuKategoriler = $kategoriler;
}

// Sayfa açıklaması
$description = "ModaVista ürün kataloğu - " . $pageTitle;

// Tema modu (aydınlık/karanlık)
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// Dil bilgisi
$lang = 'tr';

// Tüm URL parametrelerini kullanarak sayfalama linki oluşturan fonksiyon
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['sayfa'] = $page;
    return 'products.php?' . http_build_query($params);
}

// Sıralama linki oluşturan fonksiyon
function buildSortUrl($sort) {
    $params = $_GET;
    $params['siralama'] = $sort;
    $params['sayfa'] = 1; // Sıralama değiştiğinde ilk sayfaya dön
    return 'products.php?' . http_build_query($params);
}

// URL filter parametresi için temel string
$filter_url = '';
if (!empty($kategori_slug)) {
    $filter_url .= 'kategori=' . urlencode($kategori_slug);
}

if (!empty($filter)) {
    $filter_url .= (!empty($filter_url) ? '&' : '') . 'filter=' . urlencode($filter);
}

// Header'ı dahil et
include_once 'includes/header.php';
?>

<!-- Sayfa CSS'ini dahil et -->
<link rel="stylesheet" href="assets/css/products.css">

<section class="products-page">
    <!-- Başlık ve Breadcrumb -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            <div class="page-breadcrumb">
                <a href="index.php">Ana Sayfa</a> / 
                <?php if ($kategori_slug): ?>
                <a href="products.php">Ürünler</a> / <span><?php echo $pageTitle; ?></span>
                <?php else: ?>
                <span>Ürünler</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Ürün Sayısı ve Sıralama -->
        <div class="products-header">
            <div class="product-count">
                <p><?php echo $total_count; ?> ürün bulundu</p>
            </div>
            
            <button class="mobile-filter-toggle d-lg-none">
                <i class="fas fa-filter"></i> Filtreler
            </button>
            
            <div class="sort-options">
                <select id="sort-select" onchange="window.location.href=this.value">
                    <option value="<?php echo buildSortUrl('yeni'); ?>" <?php echo $siralama == 'yeni' ? 'selected' : ''; ?>>En Yeniler</option>
                    <option value="<?php echo buildSortUrl('eski'); ?>" <?php echo $siralama == 'eski' ? 'selected' : ''; ?>>En Eskiler</option>
                    <option value="<?php echo buildSortUrl('fiyat_artan'); ?>" <?php echo $siralama == 'fiyat_artan' ? 'selected' : ''; ?>>Fiyat: Düşükten Yükseğe</option>
                    <option value="<?php echo buildSortUrl('fiyat_azalan'); ?>" <?php echo $siralama == 'fiyat_azalan' ? 'selected' : ''; ?>>Fiyat: Yüksekten Düşüğe</option>
                </select>
            </div>
        </div>

        <!-- Ana İçerik Alanı -->
        <div class="products-container">
            <!-- Sol Taraf: Filtreler -->
            <aside class="filter-sidebar minimal">
                <div class="filter-header">
                    <h3>Filtreler</h3>
                    <button class="mobile-filter-close"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="filter-group">
                    <h4>Kategoriler</h4>
                    <ul class="category-filter">
                        <li><a href="products.php" class="<?php echo empty($kategori_slug) ? 'active' : ''; ?>">Tüm Ürünler</a></li>
                        <?php foreach ($kategoriler as $kategori): ?>
                            <li>
                                <a href="products.php?kategori=<?php echo $kategori['kategori_slug']; ?>" 
                                   class="<?php echo $kategori_slug == $kategori['kategori_slug'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($kategori['kategori_adi']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="filter-group">
                    <h4>Fiyat</h4>
                    <form action="products.php" method="get" class="price-filter-form">
                        <?php if (!empty($kategori_slug)): ?>
                            <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori_slug); ?>">
                        <?php endif; ?>
                        
                        <?php if (!empty($filter)): ?>
                            <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <?php endif; ?>
                        
                        <?php if (!empty($arama)): ?>
                            <input type="hidden" name="arama" value="<?php echo htmlspecialchars($arama); ?>">
                        <?php endif; ?>
                        
                        <?php if (!empty($siralama)): ?>
                            <input type="hidden" name="siralama" value="<?php echo htmlspecialchars($siralama); ?>">
                        <?php endif; ?>
                        
                        <div class="price-inputs">
                            <div class="price-input">
                                <input type="number" id="min_fiyat" name="min_fiyat" placeholder="Min" value="<?php echo isset($_GET['min_fiyat']) ? htmlspecialchars($_GET['min_fiyat']) : ''; ?>" min="0">
                            </div>
                            <div class="price-input">
                                <input type="number" id="max_fiyat" name="max_fiyat" placeholder="Max" value="<?php echo isset($_GET['max_fiyat']) ? htmlspecialchars($_GET['max_fiyat']) : ''; ?>" min="0">
                            </div>
                        </div>
                        <button type="submit" class="filter-button">Uygula</button>
                    </form>
                </div>
                
                <div class="filter-group">
                    <h4>Özel</h4>
                    <ul class="category-filter compact">
                        <li><a href="products.php?filter=indirimli" class="<?php echo $filter == 'indirimli' ? 'active' : ''; ?>">İndirimli</a></li>
                        <li><a href="products.php?filter=yeni" class="<?php echo $filter == 'yeni' ? 'active' : ''; ?>">Yeni Ürünler</a></li>
                        <li><a href="products.php?filter=one_cikan" class="<?php echo $filter == 'one_cikan' ? 'active' : ''; ?>">Öne Çıkanlar</a></li>
                        <li><a href="products.php?filter=en_cok_satan" class="<?php echo $filter == 'en_cok_satan' ? 'active' : ''; ?>">Çok Satanlar</a></li>
                    </ul>
                </div>
            </aside>
            
            <!-- Sağ Taraf: Ürünler -->
            <div class="products-content">
                <?php if (empty($urunler)): ?>
                <div class="no-products">
                    <div class="no-products-content">
                        <i class="fas fa-search"></i>
                        <h3>Ürün Bulunamadı</h3>
                        <p>Aradığınız kriterlere uygun ürün bulunamadı. Lütfen farklı filtreler ile tekrar deneyin.</p>
                        <a href="products.php" class="btn btn-primary">Tüm Ürünleri Göster</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($urunler as $urun): ?>
                    <div class="product-item">
                        <div class="product-card">
                            <div class="product-thumb">
                                <a href="product-detail.php?id=<?php echo $urun['urun_id']; ?>">
                                    <img src="<?php echo $urun['ana_gorsel']; ?>" alt="<?php echo htmlspecialchars($urun['urun_adi']); ?>" class="img-fluid">
                                </a>
                                
                                <div class="product-badges">
                                    <?php if (isset($urun['yeni_urun']) && $urun['yeni_urun']): ?>
                                    <span class="product-badge badge-new">Yeni</span>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                    <span class="product-badge badge-sale">İndirim</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-actions">
                                    <button class="product-action" data-toggle="tooltip" title="Favorilere Ekle">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="product-action" data-toggle="tooltip" title="Karşılaştır">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <button class="product-action" data-toggle="tooltip" title="Hızlı Bakış">
                                        <i class="far fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category">
                                    <a href="products.php?kategori=<?php echo $urun['kategori_slug']; ?>"><?php echo htmlspecialchars($urun['kategori_adi']); ?></a>
                                </div>
                                
                                <h3 class="product-title">
                                    <a href="product-detail.php?id=<?php echo $urun['urun_id']; ?>"><?php echo htmlspecialchars($urun['urun_adi']); ?></a>
                                </h3>
                                
                                <?php if (!empty($urun['renkler'])): ?>
                                <div class="product-colors">
                                    <?php 
                                    $colorCount = count($urun['renkler']);
                                    $maxColorsToShow = 4;
                                    $displayedColors = min($colorCount, $maxColorsToShow);
                                    
                                    for ($i = 0; $i < $displayedColors; $i++): 
                                        $color = $urun['renkler'][$i];
                                    ?>
                                    <div class="color-option <?php echo $i === 0 ? 'active' : ''; ?>" 
                                         style="background-color: <?php echo $color['renk_kodu']; ?>" 
                                         data-color="<?php echo htmlspecialchars($color['renk_adi']); ?>"
                                         title="<?php echo htmlspecialchars($color['renk_adi']); ?>">
                                    </div>
                                    <?php endfor; ?>
                                    
                                    <?php if ($colorCount > $maxColorsToShow): ?>
                                    <span class="color-more">+<?php echo $colorCount - $maxColorsToShow; ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="product-price">
                                    <?php if (isset($urun['indirimli_fiyat']) && $urun['indirimli_fiyat'] > 0 && $urun['indirimli_fiyat'] < $urun['urun_fiyat']): ?>
                                    <span class="current-price"><?php echo formatPrice($urun['indirimli_fiyat']); ?></span>
                                    <span class="old-price"><?php echo formatPrice($urun['urun_fiyat']); ?></span>
                                    <span class="discount-percentage">%<?php echo calculateDiscountPercentage($urun['urun_fiyat'], $urun['indirimli_fiyat']); ?></span>
                                    <?php else: ?>
                                    <span class="current-price"><?php echo formatPrice($urun['urun_fiyat']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="add-to-cart" data-product-id="<?php echo $urun['urun_id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Sepete Ekle
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Sayfalama -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <ul class="pagination">
                        <?php if ($sayfa > 1): ?>
                        <li class="page-item">
                            <a href="<?php echo buildPaginationUrl($sayfa - 1); ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php
                        $maxPagesToShow = 5;
                        $startPage = max(1, $sayfa - floor($maxPagesToShow / 2));
                        $endPage = min($total_pages, $startPage + $maxPagesToShow - 1);
                        
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a href="<?php echo buildPaginationUrl(1); ?>" class="page-link">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item dots">...</li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo ($i == $sayfa) ? 'active' : ''; ?>">
                                <a href="<?php echo buildPaginationUrl($i); ?>" class="page-link"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $total_pages): ?>
                            <?php if ($endPage < $total_pages - 1): ?>
                                <li class="page-item dots">...</li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a href="<?php echo buildPaginationUrl($total_pages); ?>" class="page-link"><?php echo $total_pages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($sayfa < $total_pages): ?>
                        <li class="page-item">
                            <a href="<?php echo buildPaginationUrl($sayfa + 1); ?>" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div class="filter-overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobil filtre toggle
    const filterToggle = document.querySelector('.mobile-filter-toggle');
    const filterClose = document.querySelector('.mobile-filter-close');
    const filterSidebar = document.querySelector('.filter-sidebar');
    const filterOverlay = document.querySelector('.filter-overlay');
    
    if (filterToggle && filterSidebar) {
        filterToggle.addEventListener('click', function() {
            filterSidebar.classList.add('active');
            filterOverlay.classList.add('active');
            document.body.classList.add('filter-open');
        });
    }
    
    if (filterClose && filterSidebar) {
        filterClose.addEventListener('click', function() {
            filterSidebar.classList.remove('active');
            filterOverlay.classList.remove('active');
            document.body.classList.remove('filter-open');
        });
    }
    
    if (filterOverlay) {
        filterOverlay.addEventListener('click', function() {
            filterSidebar.classList.remove('active');
            filterOverlay.classList.remove('active');
            document.body.classList.remove('filter-open');
        });
    }
    
    // Renk seçimi
    const colorOptions = document.querySelectorAll('.color-option');
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const colorOptions = productCard.querySelectorAll('.color-option');
            colorOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Tooltip
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});
</script>

<?php include_once 'includes/footer.php'; ?>