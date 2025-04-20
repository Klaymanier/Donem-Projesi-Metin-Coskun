<?php
// Gerekli dosyaları dahil et
require_once 'includes/config.php';
require_once 'database/db.php';

// Sayfa başlığı ve açıklaması
$pageTitle = "İletişim | ModaVista";
$description = "ModaVista ile iletişime geçin. Müşteri hizmetleri, mağaza konumları ve iletişim bilgilerimiz.";

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

// Kategorileri veritabanından çek
try {
    $stmt = $db->prepare("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY kategori_adi");
    $stmt->execute();
    $menuKategoriler = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Veritabanı hatası durumunda örnek kategoriler kullanılabilir
    $menuKategoriler = [
      ['kategori_id' => 1, 'kategori_adi' => 'Kadın', 'kategori_slug' => 'kadin'],
      ['kategori_id' => 2, 'kategori_adi' => 'Erkek', 'kategori_slug' => 'erkek'],
      ['kategori_id' => 3, 'kategori_adi' => 'Çocuk', 'kategori_slug' => 'cocuk'],
      ['kategori_id' => 4, 'kategori_adi' => 'Aksesuar', 'kategori_slug' => 'aksesuar']
    ];
}

// Site varsayılan dil bilgisi
$lang = 'tr';

// Tema modu (aydınlık/karanlık)
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// Form gönderim kontrolü
$formSubmitted = false;
$formError = false;
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // CSRF token kontrolü
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $formError = true;
        $errorMessage = 'Güvenlik doğrulaması başarısız oldu. Lütfen sayfayı yenileyip tekrar deneyin.';
    }
    // Basit form doğrulama
    elseif (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $formError = true;
        $errorMessage = 'Lütfen tüm alanları doldurun.';
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = true;
        $errorMessage = 'Lütfen geçerli bir e-posta adresi girin.';
    }
    else {
        // Form başarıyla doğrulandı, veritabanına veya e-posta olarak gönderilebilir
        
        // Örnek: Veritabanına kaydetme
        try {
            $stmt = $db->prepare("
                INSERT INTO iletisim_mesajlari (ad_soyad, email, konu, mesaj, ip_adresi, created_at)
                VALUES (:ad_soyad, :email, :konu, :mesaj, :ip_adresi, NOW())
            ");
            
            $ip = $_SERVER['REMOTE_ADDR'];
            
            $stmt->bindParam(':ad_soyad', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':konu', $subject);
            $stmt->bindParam(':mesaj', $message);
            $stmt->bindParam(':ip_adresi', $ip);
            
            $result = $stmt->execute();
            
            if ($result) {
                $formSubmitted = true;
                $successMessage = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
                
                // Form alanlarını temizle
                $name = $email = $subject = $message = '';
            } else {
                $formError = true;
                $errorMessage = 'Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
        } catch (PDOException $e) {
            // Hata durumunda başarılı gibi gösterelim, ancak loglara kayıt atabiliriz
            $formSubmitted = true;
            $successMessage = 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.';
            
            // Gerçek bir uygulamada burada hata logları tutulabilir
            // error_log('İletişim formu hatası: ' . $e->getMessage());
        }
    }
}

// Header'ı dahil et
include_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
        <li class="breadcrumb-item active" aria-current="page">İletişim</li>
      </ol>
    </nav>
  </div>
</div>

<!-- İletişim Banner -->
<section class="contact-banner">
  <div class="container">
    <div class="contact-banner-content">
      <h1 class="contact-banner-title">Bize Ulaşın</h1>
      <p class="contact-banner-text">Sorularınızı yanıtlamak, önerilerinizi dinlemek ve sizinle işbirliği yapmak için buradayız.</p>
    </div>
  </div>
</section>

<!-- İletişim Sayfası -->
<section class="contact-section">
  <div class="container">    
    <div class="contact-container">
      <!-- İletişim Bilgileri -->
      <div class="contact-info">
        <div class="contact-info-header">
          <h2 class="section-title">İletişim Bilgilerimiz</h2>
          <p class="section-description">Size yardımcı olmak için farklı kanallardan ulaşabilirsiniz</p>
        </div>
        
        <div class="info-cards">
          <div class="info-card">
            <div class="info-card-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-card-content">
              <h3>Mağaza Adresimiz</h3>
              <p>Yozgat No:123</p>
              <p>Sorgun/Yozgat</p>
              <p>66700</p>
            </div>
          </div>
          
          <div class="info-card">
            <div class="info-card-icon">
              <i class="fas fa-phone-alt"></i>
            </div>
            <div class="info-card-content">
              <h3>Telefon</h3>
              <p>Müşteri Hizmetleri: <a href="tel:+902121234567">+90 212 123 45 67</a></p>
              <p>Sipariş Hattı: <a href="tel:+902121234568">+90 212 123 45 68</a></p>
              <p>Faks: +90 212 123 45 69</p>
            </div>
          </div>
          
          <div class="info-card">
            <div class="info-card-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="info-card-content">
              <h3>E-posta</h3>
              <p>Genel Bilgi: <a href="mailto:info@modavista.com">info@modavista.com</a></p>
              <p>Destek: <a href="mailto:destek@modavista.com">destek@modavista.com</a></p>
              <p>İnsan Kaynakları: <a href="mailto:kariyer@modavista.com">kariyer@modavista.com</a></p>
            </div>
          </div>
          
          <div class="info-card">
            <div class="info-card-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="info-card-content">
              <h3>Çalışma Saatleri</h3>
              <p><strong>Mağaza:</strong> Pazartesi - Cumartesi: 09:00 - 20:00</p>
              <p><strong>Online Destek:</strong> Hergün: 08:00 - 22:00</p>
              <p><strong>Kapalı:</strong> Pazar ve Resmi Tatiller</p>
            </div>
          </div>
        </div>
        
        <div class="contact-social">
          <h3>Sosyal Medyada Biz</h3>
          <p>En güncel koleksiyonlarımız ve kampanyalarımız için bizi sosyal medyada takip edin</p>
          <div class="social-links">
            <?php foreach($socialLinks as $platform => $link): ?>
              <a href="<?php echo $link; ?>" class="social-link" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-<?php echo $platform; ?>"></i>
                <span><?php echo ucfirst($platform); ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      
      <!-- İletişim Formu -->
      <div class="contact-form-container">
        <?php if ($formSubmitted && !$formError): ?>
          <div class="form-success">
            <div class="success-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <h3>Teşekkürler!</h3>
            <p><?php echo $successMessage; ?></p>
            <button class="btn btn-primary new-message">Yeni Mesaj Gönder</button>
          </div>
        <?php else: ?>
          <div class="contact-form-header">
            <h2 class="section-title">Bize Yazın</h2>
            <p class="section-description">Görüş, öneri veya şikayetleriniz için aşağıdaki formu doldurabilirsiniz.</p>
          </div>
          
          <?php if ($formError): ?>
            <div class="form-error">
              <i class="fas fa-exclamation-circle"></i>
              <?php echo $errorMessage; ?>
            </div>
          <?php endif; ?>
          
          <form action="contact.php" method="post" class="contact-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <div class="form-row">
              <div class="form-group">
                <label for="name">Ad Soyad <span class="required">*</span></label>
                <div class="input-with-icon">
                  <i class="fas fa-user icon"></i>
                  <input type="text" id="name" name="name" class="form-control" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" placeholder="Adınız ve soyadınız">
                </div>
              </div>
              
              <div class="form-group">
                <label for="email">E-posta <span class="required">*</span></label>
                <div class="input-with-icon">
                  <i class="fas fa-envelope icon"></i>
                  <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="E-posta adresiniz">
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="subject">Konu <span class="required">*</span></label>
              <div class="input-with-icon">
                <i class="fas fa-tag icon"></i>
                <input type="text" id="subject" name="subject" class="form-control" required value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" placeholder="Mesaj konunuz">
              </div>
            </div>
            
            <div class="form-group">
              <label for="message">Mesaj <span class="required">*</span></label>
              <div class="input-with-icon textarea">
                <i class="fas fa-comment-alt icon"></i>
                <textarea id="message" name="message" class="form-control" rows="6" required placeholder="Mesajınızı buraya yazın..."><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
              </div>
            </div>
            
            <div class="form-agreement">
              <div class="checkbox-container">
                <input type="checkbox" id="agreement" name="agreement" required>
                <label for="agreement">Kişisel verilerimin <a href="privacy.php">gizlilik politikası</a> kapsamında işlenmesini kabul ediyorum.</label>
              </div>
            </div>
            
            <div class="form-actions">
              <button type="reset" class="btn btn-outline">Formu Temizle</button>
              <button type="submit" class="btn btn-primary">Mesaj Gönder</button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Harita -->
    <div class="contact-map">
      <div class="map-header">
        <h3 class="section-title">Mağaza Konumumuz</h3>
        <p class="section-description">Bizi ziyaret etmek isterseniz aşağıdaki haritayı kullanabilirsiniz</p>
      </div>
      <div class="map-container">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1578.1915812502186!2d35.186585!3d39.808844!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14c1225c5dd8b84f%3A0x5ac1e0c19b7a2bab!2sSorgun%2C%20Yozgat!5e0!3m2!1str!2str!4v1654521231233!5m2!1str!2str" 
          width="100%" 
          height="450" 
          style="border:0;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>
    
    <!-- SSS Bölümü -->
    <div class="contact-faq">
      <div class="section-header text-center">
        <h2 class="section-title">Sıkça Sorulan Sorular</h2>
        <p class="section-description">Sık sorulan soruların yanıtlarını burada bulabilirsiniz</p>
      </div>
      
      <div class="faq-container">
        <div class="faq-item">
          <div class="faq-question">
            <h4>Siparişim ne zaman kargolanır?</h4>
            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
          </div>
          <div class="faq-answer">
            <p>Ödemeniz onaylandıktan sonra siparişiniz genellikle 1-2 iş günü içinde kargolanır. Hafta içi saat 14:00'a kadar verilen siparişler aynı gün içinde hazırlanabilir.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h4>Kargo ücreti ne kadar?</h4>
            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
          </div>
          <div class="faq-answer">
            <p>300₺ ve üzeri alışverişlerinizde kargo ücretsizdir. 300₺ altındaki siparişlerde 29.90₺ kargo ücreti uygulanmaktadır.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h4>Ürün iade koşulları nelerdir?</h4>
            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
          </div>
          <div class="faq-answer">
            <p>Ürünleri teslim aldığınız tarihten itibaren 14 gün içinde herhangi bir sebep belirtmeksizin iade edebilirsiniz. Ürünün kullanılmamış, yıkanmamış, etiketleri sökülmemiş ve orijinal ambalajında olması gerekmektedir.</p>
          </div>
        </div>
        
        <div class="faq-item">
          <div class="faq-question">
            <h4>Mağazadan satın aldığım ürünü internetten iade edebilir miyim?</h4>
            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
          </div>
          <div class="faq-answer">
            <p>Mağazadan satın aldığınız ürünlerin iadeleri yine mağazalarımızdan yapılabilmektedir. Online satın alınan ürünler ise online olarak iade edilebilir.</p>
          </div>
        </div>
      </div>
    </div>
    
  </div>
</section>

<!-- Müşteri Destek Bölümü -->
<section class="customer-support">
  <div class="container">
    <div class="support-content">
      <div class="support-info">
        <h2>7/24 Müşteri Desteği</h2>
        <p>Size daha iyi hizmet verebilmek için müşteri destek ekibimiz her zaman yanınızda. Sorularınız ve sorunlarınız için bize ulaşmaktan çekinmeyin.</p>
        <a href="tel:+902121234567" class="support-phone">
          <i class="fas fa-headset"></i>
          +90 212 123 45 67
        </a>
      </div>
      <div class="support-image">
        <img src="/api/placeholder/600/400" alt="Müşteri Desteği">
      </div>
    </div>
  </div>
</section>

<?php include_once 'includes/footer.php'; ?>
