<?php
// Başlangıçta çıktı tamponlamasını başlat
ob_start();

// Header'ı dahil et (header.php zaten HTML yapısını başlatıyor)
include_once 'includes/header.php';



// Sepet sayfası başlığı ve açıklaması
$pageTitle = "Sepetim | ModaVista";
$description = "ModaVista alışveriş sepetiniz. Seçtiğiniz ürünleri görüntüleyin, düzenleyin ve siparişinizi tamamlayın.";

// Sepet ürün sayısını tanımla
$sepetUrunSayisi = isset($cartItemCount) ? $cartItemCount : 0;

// Sosyal medya linkleri
$socialLinks = [
  'facebook' => '#',
  'twitter' => '#',
  'instagram' => '#',
  'linkedin' => '#',
  'discord' => '#'
];

// Sepet ürün sayısını getir
$cartItemCount = 3; // Gerçek uygulamada bu değer veritabanından çekilecek

// Kategorileri veritabanından çek
try {
  $stmt = $db->prepare("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY kategori_adi");
  $stmt->execute();
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Veritabanı hatası durumunda örnek kategoriler kullanılabilir
  $categories = [
    ['kategori_id' => 1, 'kategori_adi' => 'Kadın', 'kategori_slug' => 'kadin'],
    ['kategori_id' => 2, 'kategori_adi' => 'Erkek', 'kategori_slug' => 'erkek'],
    ['kategori_id' => 3, 'kategori_adi' => 'Çocuk', 'kategori_slug' => 'cocuk'],
    ['kategori_id' => 4, 'kategori_adi' => 'Aksesuar', 'kategori_slug' => 'aksesuar']
  ];
}

// Sepetteki ürünleri veritabanından çek
try {
  $userID = 1; // Örnek kullanıcı ID'si - giriş yapmış kullanıcı için dinamik olarak alınacak
  
  $stmt = $db->prepare("
    SELECT s.sepet_id, s.urun_id, s.adet, s.varyasyon_id, 
           u.urun_adi, u.urun_fiyat, u.indirimli_fiyat,
           (SELECT gorsel_yolu FROM urun_gorselleri WHERE urun_id = s.urun_id AND ana_gorsel = 1 LIMIT 1) AS gorsel_yolu,
           uv.beden, r.renk_adi, r.renk_kodu
    FROM sepet s
    JOIN urunler u ON s.urun_id = u.urun_id
    LEFT JOIN urun_varyasyonlari uv ON s.varyasyon_id = uv.varyasyon_id
    LEFT JOIN renkler r ON uv.renk_id = r.renk_id
    WHERE s.user_id = :user_id AND s.siparis_id IS NULL
  ");
  $stmt->bindParam(':user_id', $userID);
  $stmt->execute();
  $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Veritabanı hatası durumunda örnek sepet ürünleri
  $cartItems = [
    [
      'sepet_id' => 1,
      'urun_id' => 1,
      'adet' => 2,
      'urun_adi' => 'Premium Pamuklu T-Shirt',
      'urun_fiyat' => 299.90,
      'indirimli_fiyat' => 249.90,
      'gorsel_yolu' => '/api/placeholder/300/400',
      'beden' => 'M',
      'renk_adi' => 'Beyaz',
      'renk_kodu' => '#FFFFFF'
    ],
    [
      'sepet_id' => 2,
      'urun_id' => 3,
      'adet' => 1,
      'urun_adi' => 'Oversize Sweatshirt',
      'urun_fiyat' => 399.90,
      'indirimli_fiyat' => 329.90,
      'gorsel_yolu' => '/api/placeholder/300/400',
      'beden' => 'L',
      'renk_adi' => 'Siyah',
      'renk_kodu' => '#000000'
    ],
    [
      'sepet_id' => 3,
      'urun_id' => 6,
      'adet' => 1,
      'urun_adi' => 'Deri Bileklik Set',
      'urun_fiyat' => 189.90,
      'indirimli_fiyat' => 149.90,
      'gorsel_yolu' => '/api/placeholder/300/400',
      'beden' => null,
      'renk_adi' => 'Kahverengi',
      'renk_kodu' => '#5E3023'
    ]
  ];
}

// Sepet özeti hesapla
$cartSummary = [
  'subtotal' => 0,
  'discount' => 0,
  'shipping' => 0,
  'total' => 0
];

// Ürünleri döngüyle gezerek toplamları hesapla
foreach ($cartItems as $item) {
  $price = $item['indirimli_fiyat'] ?? $item['urun_fiyat'];
  $itemTotal = $price * $item['adet'];
  $itemDiscount = (($item['urun_fiyat'] - $price) * $item['adet']);
  
  $cartSummary['subtotal'] += $item['urun_fiyat'] * $item['adet'];
  $cartSummary['discount'] += $itemDiscount;
}

// Kargo ücreti hesaplama (300 TL üzeri ücretsiz)
$cartSummary['shipping'] = ($cartSummary['subtotal'] - $cartSummary['discount'] < 300) ? 29.90 : 0;

// Toplam tutarı hesapla
$cartSummary['total'] = $cartSummary['subtotal'] - $cartSummary['discount'] + $cartSummary['shipping'];

// Kupon kodları (örnek)
$availableCoupons = [
  'MODA25' => [
    'code' => 'MODA25',
    'description' => '250₺ ve üzeri alışverişlerde %15 indirim',
    'min_amount' => 250,
    'discount_rate' => 15
  ],
  'YENISEZON' => [
    'code' => 'YENISEZON',
    'description' => 'Yeni sezon ürünlerinde ekstra %10 indirim',
    'min_amount' => 0,
    'discount_rate' => 10
  ]
];
?>

<!-- Sayfa Başlığı ve Breadcrumb -->
<div class="page-header">
  <div class="container">
    <h1 class="page-title">Sepetim</h1>
    <div class="breadcrumb">
      <a href="index.php">Ana Sayfa</a>
      <span>/</span>
      <span>Sepetim</span>
    </div>
  </div>
</div>

<!-- Sepet Bölümü -->
<section class="cart-section">
  <div class="container">
    <?php if (!empty($cartItems)): ?>
      <div class="cart-container">
        <div class="cart-items">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Ürün</th>
                <th>Fiyat</th>
                <th>Adet</th>
                <th>Toplam</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cartItems as $item): ?>
                <tr class="cart-item">
                  <td>
                    <div class="item-product">
                      <img src="<?php echo $item['gorsel_yolu']; ?>" alt="<?php echo $item['urun_adi']; ?>" class="item-image">
                    </div>
                  </td>
                  <td class="item-info">
                    <a href="product-detail.php?id=<?php echo $item['urun_id']; ?>" class="item-name"><?php echo $item['urun_adi']; ?></a>
                    
                    <?php if (!empty($item['beden'])): ?>
                      <div class="item-variant">
                        <span>Beden: <?php echo $item['beden']; ?></span>
                      </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['renk_adi'])): ?>
                      <div class="item-variant">
                        <span class="color-dot" style="background-color: <?php echo $item['renk_kodu']; ?>"></span>
                        <span>Renk: <?php echo $item['renk_adi']; ?></span>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="price-column">
                    <?php if ($item['indirimli_fiyat']): ?>
                      <span class="discount-price"><?php echo number_format($item['urun_fiyat'], 2, ',', '.'); ?> ₺</span>
                      <span><?php echo number_format($item['indirimli_fiyat'], 2, ',', '.'); ?> ₺</span>
                    <?php else: ?>
                      <span><?php echo number_format($item['urun_fiyat'], 2, ',', '.'); ?> ₺</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="quantity-controls">
                      <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['sepet_id']; ?>, 'decrease')">
                        <i class="fas fa-minus"></i>
                      </button>
                      <input type="text" class="quantity-input" value="<?php echo $item['adet']; ?>" min="1" max="10" readonly>
                      <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $item['sepet_id']; ?>, 'increase')">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </td>
                  <td class="price-column">
                    <?php
                      $price = $item['indirimli_fiyat'] ?? $item['urun_fiyat'];
                      $totalPrice = $price * $item['adet'];
                      echo number_format($totalPrice, 2, ',', '.') . ' ₺';
                    ?>
                  </td>
                  <td>
                    <button type="button" class="remove-item" onclick="removeItem(<?php echo $item['sepet_id']; ?>)">
                      <i class="fas fa-times"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          
          <div class="cart-actions">
            <a href="products.php" class="continue-shopping">
              <i class="fas fa-arrow-left"></i> Alışverişe Devam Et
            </a>
            <button type="button" class="clear-cart" onclick="clearCart()">
              <i class="fas fa-trash-alt"></i> Sepeti Temizle
            </button>
          </div>
        </div>
        
        <div class="cart-summary">
          <div class="summary-header">
            <h3 class="summary-title">Sipariş Özeti</h3>
          </div>
          <div class="summary-content">
            <div class="summary-row">
              <span>Ara Toplam</span>
              <span><?php echo number_format($cartSummary['subtotal'], 2, ',', '.'); ?> ₺</span>
            </div>
            <div class="summary-row">
              <span>İndirim</span>
              <span>-<?php echo number_format($cartSummary['discount'], 2, ',', '.'); ?> ₺</span>
            </div>
            <div class="summary-row">
              <span>Kargo</span>
              <?php if ($cartSummary['shipping'] > 0): ?>
                <span><?php echo number_format($cartSummary['shipping'], 2, ',', '.'); ?> ₺</span>
              <?php else: ?>
                <span class="text-success">Ücretsiz</span>
              <?php endif; ?>
            </div>
            
            <div class="summary-total">
              <span>Toplam</span>
              <span><?php echo number_format($cartSummary['total'], 2, ',', '.'); ?> ₺</span>
            </div>
            
            <a href="checkout.php" class="btn btn-primary checkout-btn">
              Siparişi Tamamla <i class="fas fa-arrow-right"></i>
            </a>
            
            <!-- Kupon kodu bölümü -->
            <div class="coupon-form">
              <h4 class="coupon-title">İndirim Kuponu</h4>
              <div class="coupon-input-group">
                <input type="text" class="coupon-input" placeholder="Kupon kodu girin" id="couponCode">
                <button type="button" class="apply-coupon" onclick="applyCoupon()">Uygula</button>
              </div>
            </div>
            
            <!-- Kullanılabilir kuponlar -->
            <div class="available-coupons">
              <?php foreach($availableCoupons as $coupon): ?>
                <div class="coupon-card">
                  <div>
                    <span class="coupon-code"><?php echo $coupon['code']; ?></span>
                    <p class="coupon-info"><?php echo $coupon['description']; ?></p>
                  </div>
                  <button type="button" class="use-coupon" onclick="useCoupon('<?php echo $coupon['code']; ?>')">Kullan</button>
                </div>
              <?php endforeach; ?>
            </div>
            
            <!-- Güvenli ödeme bilgisi -->
            <div class="security-info">
              <i class="fas fa-lock"></i>
              <span class="security-text">Tüm ödemeleriniz 256-bit SSL sertifikası ile şifrelenmektedir.</span>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Boş Sepet Durumu -->
      <div class="cart-empty">
        <div class="empty-icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <h2>Sepetiniz Boş</h2>
        <p>Henüz sepetinize ürün eklemediniz. Alışverişe başlamak için ürünlerimize göz atabilirsiniz.</p>
        <a href="products.php" class="btn btn-primary btn-lg" style="margin-top: 1.5rem;">
          Alışverişe Başla
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
  // Sepetteki ürün miktarını güncelle
  function updateQuantity(cartItemId, action) {
    let quantityInput = event.target.closest('.quantity-controls').querySelector('.quantity-input');
    let currentQuantity = parseInt(quantityInput.value);
    
    if (action === 'increase' && currentQuantity < 10) {
      quantityInput.value = currentQuantity + 1;
      updateCart(cartItemId, currentQuantity + 1);
    } else if (action === 'decrease' && currentQuantity > 1) {
      quantityInput.value = currentQuantity - 1;
      updateCart(cartItemId, currentQuantity - 1);
    }
  }
  
  // Sepet güncellemesi Ajax işlemi
  function updateCart(cartItemId, quantity) {
    // Gerçek uygulamada burada AJAX isteği olacak
    console.log('Sepet güncelleniyor: ID=' + cartItemId + ', Adet=' + quantity);
    
    // Normalde sunucudan gelen cevap sonrası sayfayı yenilemek yerine, 
    // sadece ilgili elemanları güncelleyeceğiz
    setTimeout(function() {
      // Örnek güncelleme (gerçek uygulamada sunucudan dönen verilerle yapılacak)
      // Bu sadece demo amaçlıdır
      location.reload();
    }, 300);
  }
  
  // Ürünü sepetten kaldır
  function removeItem(cartItemId) {
    if (confirm('Bu ürünü sepetten kaldırmak istediğinize emin misiniz?')) {
      // Gerçek uygulamada burada AJAX isteği olacak
      console.log('Ürün sepetten kaldırılıyor: ID=' + cartItemId);
      
      // Animasyon ile ürünü gizle ve sonra sayfayı yenile
      const item = event.target.closest('.cart-item');
      item.style.opacity = '0';
      item.style.transform = 'translateX(50px)';
      
      setTimeout(function() {
        // Gerçek uygulamada sunucudan başarılı cevap alındığında yapılacak
        location.reload();
      }, 300);
    }
  }
  
  // Sepeti tamamen temizle
  function clearCart() {
    if (confirm('Sepetinizdeki tüm ürünleri kaldırmak istediğinize emin misiniz?')) {
      // Gerçek uygulamada burada AJAX isteği olacak
      console.log('Sepet tamamen temizleniyor');
      
      // Animasyon ile tüm ürünleri gizle
      const items = document.querySelectorAll('.cart-item');
      items.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateX(50px)';
      });
      
      setTimeout(function() {
        location.reload();
      }, 300);
    }
  }
  
  // Kupon kodunu uygula
  function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    
    if (couponCode !== '') {
      // Gerçek uygulamada burada AJAX isteği olacak
      console.log('Kupon kodu uygulanıyor: ' + couponCode);
      
      // Başarılı kupon uygulaması gösterimi (gerçek uygulamada sunucudan dönen verilerle yapılacak)
      alert('Kupon kodu başarıyla uygulandı!');
      location.reload();
    } else {
      alert('Lütfen bir kupon kodu girin');
    }
  }
  
  // Önerilen kuponu kullan
  function useCoupon(couponCode) {
    document.getElementById('couponCode').value = couponCode;
    applyCoupon();
  }
</script>

<?php 
// Footer'ı dahil et (footer.php zaten HTML yapısını kapatıyor)
include_once 'includes/footer.php'; 

// Çıktı tamponlamasını bitir ve içeriği gönder
ob_end_flush();
?>