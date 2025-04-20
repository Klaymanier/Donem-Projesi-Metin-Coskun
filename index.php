<?php
// Gerekli dosyaları dahil et
require_once 'includes/config.php';
require_once 'database/db.php';

// Ana sayfa başlığı ve açıklaması
$pageTitle = "ModaVista | Modern Giyim";
$description = "En yeni moda trendleri ve özel tasarım giyim ürünleri ModaVista'da!";

// Sosyal medya linkleri
$socialLinks = [
  'facebook' => '#',
  'twitter' => '#',
  'instagram' => '#',
  'linkedin' => '#',
  'discord' => '#'
];

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

// Kategoriler - SQL dosyanızdan gelecek
try {
    $stmt = $db->prepare("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY kategori_adi");
    $stmt->execute();
    $menuKategoriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $categories = $menuKategoriler; // index.php için gereken değişken adı
} catch (PDOException $e) {
    // Veritabanından çekilemezse varsayılan kategorileri kullan
    $categories = [
      ['kategori_id' => 1, 'kategori_adi' => 'Kadın', 'kategori_slug' => 'kadin'],
      ['kategori_id' => 2, 'kategori_adi' => 'Erkek', 'kategori_slug' => 'erkek'],
      ['kategori_id' => 3, 'kategori_adi' => 'Çocuk', 'kategori_slug' => 'cocuk'],
      ['kategori_id' => 4, 'kategori_adi' => 'Aksesuar', 'kategori_slug' => 'aksesuar']
    ];
    $menuKategoriler = $categories;
}

// Vitrindeki ürünler - SQL dosyanızdan gelecek
$featuredProducts = [
  [
    'urun_id' => 1,
    'urun_adi' => 'Premium Pamuklu T-Shirt',
    'kategori_adi' => 'Erkek',
    'kategori_slug' => 'erkek',
    'urun_fiyat' => 299.90,
    'indirimli_fiyat' => 249.90,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 1,
    'renkler' => [
      ['renk_adi' => 'Beyaz', 'renk_kodu' => '#FFFFFF'],
      ['renk_adi' => 'Siyah', 'renk_kodu' => '#000000'],
      ['renk_adi' => 'Lacivert', 'renk_kodu' => '#0A2463']
    ]
  ],
  [
    'urun_id' => 2,
    'urun_adi' => 'Slim Fit Denim Pantolon',
    'kategori_adi' => 'Erkek',
    'kategori_slug' => 'erkek',
    'urun_fiyat' => 549.90,
    'indirimli_fiyat' => null,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 0,
    'renkler' => [
      ['renk_adi' => 'Mavi', 'renk_kodu' => '#1E3888'],
      ['renk_adi' => 'Siyah', 'renk_kodu' => '#000000']
    ]
  ],
  [
    'urun_id' => 3,
    'urun_adi' => 'Oversize Sweatshirt',
    'kategori_adi' => 'Kadın',
    'kategori_slug' => 'kadin',
    'urun_fiyat' => 399.90,
    'indirimli_fiyat' => 329.90,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 1,
    'renkler' => [
      ['renk_adi' => 'Bej', 'renk_kodu' => '#E8DDCB'],
      ['renk_adi' => 'Siyah', 'renk_kodu' => '#000000'],
      ['renk_adi' => 'Gri', 'renk_kodu' => '#9EA3B0']
    ]
  ],
  [
    'urun_id' => 4,
    'urun_adi' => 'Yüksek Bel Palazzo Pantolon',
    'kategori_adi' => 'Kadın',
    'kategori_slug' => 'kadin',
    'urun_fiyat' => 459.90,
    'indirimli_fiyat' => null,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 0,
    'renkler' => [
      ['renk_adi' => 'Siyah', 'renk_kodu' => '#000000'],
      ['renk_adi' => 'Ekru', 'renk_kodu' => '#F2EEE3'],
      ['renk_adi' => 'Haki', 'renk_kodu' => '#5F6F52']
    ]
  ],
  [
    'urun_id' => 5,
    'urun_adi' => 'Organik Pamuk Bebek Tulum',
    'kategori_adi' => 'Çocuk',
    'kategori_slug' => 'cocuk',
    'urun_fiyat' => 279.90,
    'indirimli_fiyat' => 229.90,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 1,
    'renkler' => [
      ['renk_adi' => 'Sarı', 'renk_kodu' => '#F0C808'],
      ['renk_adi' => 'Mint', 'renk_kodu' => '#99E1D9'],
      ['renk_adi' => 'Beyaz', 'renk_kodu' => '#FFFFFF']
    ]
  ],
  [
    'urun_id' => 6,
    'urun_adi' => 'Deri Bileklik Set',
    'kategori_adi' => 'Aksesuar',
    'kategori_slug' => 'aksesuar',
    'urun_fiyat' => 189.90,
    'indirimli_fiyat' => 149.90,
    'gorsel_yolu' => '/api/placeholder/400/500',
    'yeni_urun' => 0,
    'renkler' => [
      ['renk_adi' => 'Kahverengi', 'renk_kodu' => '#5E3023'],
      ['renk_adi' => 'Siyah', 'renk_kodu' => '#000000']
    ]
  ]
];

// Koleksiyonları getir
$collections = [
  [
    'title' => 'Yaz Koleksiyonu',
    'description' => 'Sıcak yaz günleri için ferah ve şık tasarımlar',
    'image' => '/api/placeholder/600/400',
    'link' => '#yaz-koleksiyonu'
  ],
  [
    'title' => 'Sürdürülebilir Moda',
    'description' => 'Doğaya saygılı, organik kumaşlardan üretilen çevre dostu ürünler',
    'image' => '/api/placeholder/600/400',
    'link' => '#surdurulebilir-moda'
  ],
  [
    'title' => 'Limitli Koleksiyon',
    'description' => 'Sınırlı sayıda üretilen özel tasarımlar',
    'image' => '/api/placeholder/600/400',
    'link' => '#limitli-koleksiyon'
  ]
];

// Ürün filtrelerini belirle
$productFilters = [
  'Tümü',
  'Yeni Gelenler',
  'Popüler',
  'İndirimli',
  'Erkek',
  'Kadın'
];

// Site varsayılan dil bilgisi
$lang = 'tr';

// Tema modu (aydınlık/karanlık)
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// Header'ı dahil et
include_once 'includes/header.php';
?>

<!-- Hero Banner -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="container">
    <div class="hero-content">
      <h1 class="hero-title">Modern Tasarım, <br>Yenilikçi Stil</h1>
      <p class="hero-subtitle">En yeni koleksiyonlar ve sezonun trend parçalarıyla tarzınızı yansıtın. Sürdürülebilir moda anlayışımızla çevreye duyarlı alışverişin keyfini çıkarın.</p>
      <div class="hero-buttons">
        <a href="#products" class="btn btn-primary btn-lg">Alışverişe Başla</a>
        <a href="#collections" class="btn btn-outline btn-lg">Koleksiyonlar</a>
      </div>
    </div>
    <div class="hero-image-container">
      <img src="/api/placeholder/800/1000" alt="ModaVista Hero" class="hero-image">
    </div>
  </div>
</section>

<!-- Kategoriler -->
<section class="categories-section" id="categories">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Kategoriler</h2>
      <a href="categories.php" class="view-all">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="categories-grid">
      <?php foreach ($categories as $category): ?>
        <a href="products.php?kategori=<?php echo $category['kategori_slug']; ?>" class="category-card">
          <img src="/api/placeholder/500/500" alt="<?php echo $category['kategori_adi']; ?>" class="category-bg">
          <div class="category-content">
            <h3 class="category-name"><?php echo $category['kategori_adi']; ?></h3>
            <div class="category-count">24+ Ürün</div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Ürünler -->
<section class="products-section" id="products">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Öne Çıkan Ürünler</h2>
      <a href="products.php" class="view-all">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="products-filter">
      <?php foreach($productFilters as $index => $filter): ?>
        <button class="filter-button <?php echo $index === 0 ? 'active' : ''; ?>" data-filter="<?php echo strtolower(str_replace(' ', '-', $filter)); ?>">
          <?php echo $filter; ?>
        </button>
      <?php endforeach; ?>
    </div>
    
    <div class="products-grid">
      <?php foreach($featuredProducts as $product): ?>
        <div class="product-card">
          <div class="product-thumb">
            <img src="<?php echo $product['gorsel_yolu']; ?>" alt="<?php echo $product['urun_adi']; ?>">
            
            <div class="product-badges">
              <?php if($product['yeni_urun'] == 1): ?>
                <span class="product-badge badge-new">Yeni</span>
              <?php endif; ?>
              
              <?php if($product['indirimli_fiyat'] !== null): ?>
                <span class="product-badge badge-sale">
                  %<?php echo round((1 - $product['indirimli_fiyat'] / $product['urun_fiyat']) * 100); ?>
                </span>
              <?php endif; ?>
            </div>
            
            <div class="product-actions">
              <button class="product-action" title="Favorilere Ekle">
                <i class="far fa-heart"></i>
              </button>
              <button class="product-action" title="Hızlı Bakış">
                <i class="fas fa-eye"></i>
              </button>
              <button class="product-action" title="Karşılaştır">
                <i class="fas fa-exchange-alt"></i>
              </button>
            </div>
          </div>
          
          <div class="product-info">
            <span class="product-category"><?php echo $product['kategori_adi']; ?></span>
            
            <h3 class="product-title">
              <a href="product-detail.php?id=<?php echo $product['urun_id']; ?>">
                <?php echo $product['urun_adi']; ?>
              </a>
            </h3>
            
            <div class="product-price">
              <?php if($product['indirimli_fiyat'] !== null): ?>
                <span class="current-price"><?php echo number_format($product['indirimli_fiyat'], 2, ',', '.'); ?> ₺</span>
                <span class="old-price"><?php echo number_format($product['urun_fiyat'], 2, ',', '.'); ?> ₺</span>
                <span class="discount-percentage">%<?php echo round((1 - $product['indirimli_fiyat'] / $product['urun_fiyat']) * 100); ?></span>
              <?php else: ?>
                <span class="current-price"><?php echo number_format($product['urun_fiyat'], 2, ',', '.'); ?> ₺</span>
              <?php endif; ?>
            </div>
            
            <?php if(!empty($product['renkler'])): ?>
            <div class="product-colors">
              <?php foreach($product['renkler'] as $index => $renk): ?>
                <div class="color-option <?php echo $index === 0 ? 'active' : ''; ?>" 
                     style="background-color: <?php echo $renk['renk_kodu']; ?>" 
                     title="<?php echo $renk['renk_adi']; ?>">
                </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <button class="add-to-cart" data-product-id="<?php echo $product['urun_id']; ?>">
              <i class="fas fa-shopping-cart"></i> Sepete Ekle
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Koleksiyonlar -->
<section class="collections-section" id="collections">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Koleksiyonlar</h2>
      <a href="collections.php" class="view-all">Tümünü Gör <i class="fas fa-arrow-right"></i></a>
    </div>
    
    <div class="collections-grid">
      <?php foreach($collections as $collection): ?>
        <div class="collection-card">
          <img src="<?php echo $collection['image']; ?>" alt="<?php echo $collection['title']; ?>" class="collection-image">
          <div class="collection-content">
            <h3 class="collection-title"><?php echo $collection['title']; ?></h3>
            <p class="collection-description"><?php echo $collection['description']; ?></p>
            <a href="<?php echo $collection['link']; ?>" class="collection-link">Koleksiyonu Keşfet <i class="fas fa-arrow-right"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Özellikler -->
<section class="features-section">
  <div class="container">
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-truck"></i>
        </div>
        <h3 class="feature-title">Ücretsiz Kargo</h3>
        <p class="feature-description">300₺ ve üzeri tüm siparişlerde ücretsiz kargo imkanı</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-sync"></i>
        </div>
        <h3 class="feature-title">Kolay İade</h3>
        <p class="feature-description">30 gün içinde ücretsiz iade garantisi</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-shield-alt"></i>
        </div>
        <h3 class="feature-title">Güvenli Ödeme</h3>
        <p class="feature-description">SSL güvenlik sertifikası ile güvenli ödeme</p>
      </div>
      
      <div class="feature-card">
        <div class="feature-icon">
          <i class="fas fa-headset"></i>
        </div>
        <h3 class="feature-title">7/24 Destek</h3>
        <p class="feature-description">Her zaman yanınızda olan müşteri hizmetleri</p>
      </div>
    </div>
  </div>
</section>

<!-- Bülten -->
<section class="newsletter-section">
  <div class="container">
    <div class="newsletter-content">
      <h2 class="newsletter-title">Bültenimize Abone Olun</h2>
      <p class="newsletter-description">Yeni koleksiyonlar, özel teklifler ve indirimlerden haberdar olmak için abone olun.</p>
      
      <form class="newsletter-form">
        <input type="email" class="newsletter-input" placeholder="E-posta adresiniz" required>
        <button type="submit" class="newsletter-button">Abone Ol</button>
      </form>
    </div>
  </div>
</section>

<?php include_once 'includes/footer.php'; ?>