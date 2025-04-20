<?php
// Gerekli dosyaları dahil et
require_once 'includes/config.php';
require_once 'database/db.php';

// Sayfa başlığı ve açıklaması
$pageTitle = "Hakkımızda | ModaVista";
$description = "ModaVista'nın hikayesi, değerlerimiz ve vizyonumuz. Moda dünyasına bakış açımızı ve sürdürülebilir tasarım anlayışımızı keşfedin.";

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

// Header'ı dahil et
include_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb-wrapper">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
        <li class="breadcrumb-item active" aria-current="page">Hakkımızda</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Ana Bölüm - Hakkımızda -->
<section class="about-hero">
  <div class="container">
    <div class="about-hero-content">
      <div class="about-hero-image">
        <img src="/api/placeholder/800/600" alt="ModaVista Mağaza" class="img-fluid">
      </div>
      <div class="about-hero-text">
        <h1 class="section-title">Moda Tutkumuzla Tanışın</h1>
        <p class="lead">2015 yılında, Yozgat'ta küçük bir butik olarak başlayan yolculuğumuz, bugün Türkiye'nin dört bir yanında moda tutkunlarına hizmet veren bir markaya dönüştü.</p>
        <p>ModaVista olarak, sadece kıyafet satmıyoruz; insanların kendilerini en iyi şekilde ifade etmelerine yardımcı oluyoruz. Her bir parçamız, özgün tasarımlar ve kaliteli malzemelerle, sürdürülebilir moda anlayışımızı yansıtıyor.</p>
        <div class="about-stats">
          <div class="stat-item">
            <span class="stat-number">8+</span>
            <span class="stat-text">Yıllık Deneyim</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">10K+</span>
            <span class="stat-text">Mutlu Müşteri</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">5K+</span>
            <span class="stat-text">Ürün Çeşidi</span>
          </div>
          <div class="stat-item">
            <span class="stat-number">25+</span>
            <span class="stat-text">Tasarımcı</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Hikayemiz -->
<section class="about-story">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">Hikayemiz</h2>
      <p class="section-description">Nasıl başladık ve bugünlere nasıl geldik?</p>
    </div>
    
    <div class="timeline">
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <div class="timeline-date">2015</div>
          <h3 class="timeline-title">Başlangıç</h3>
          <p>Yozgat Sorgun'da küçük bir butik ile moda dünyasına ilk adımımızı attık. Özgün tasarımlarımız ve kaliteli ürünlerimiz ile kısa sürede ilgi görmeye başladık.</p>
        </div>
      </div>
      
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content right">
          <div class="timeline-date">2017</div>
          <h3 class="timeline-title">Online Mağaza</h3>
          <p>Artan talep üzerine çevrimiçi mağazamızı açtık ve Türkiye'nin farklı şehirlerinden müşterilere ulaşmaya başladık. Bu dönemde ürün yelpazemizi genişlettik.</p>
        </div>
      </div>
      
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <div class="timeline-date">2019</div>
          <h3 class="timeline-title">Sürdürülebilir Moda</h3>
          <p>Çevresel sorumluluk bilinciyle sürdürülebilir moda anlayışını benimsedik. Organik kumaşlar ve geri dönüştürülmüş malzemelerle üretilen koleksiyonlarımızı piyasaya sürdük.</p>
        </div>
      </div>
      
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content right">
          <div class="timeline-date">2021</div>
          <h3 class="timeline-title">Ulusal Büyüme</h3>
          <p>İstanbul ve Ankara'da fiziksel mağazalarımızı açtık. Sosyal medya varlığımızı güçlendirerek genç kitlelere daha fazla ulaşmaya başladık.</p>
        </div>
      </div>
      
      <div class="timeline-item">
        <div class="timeline-dot"></div>
        <div class="timeline-content">
          <div class="timeline-date">2023</div>
          <h3 class="timeline-title">Yenilikçi Adımlar</h3>
          <p>Mobil uygulamamızı yayınladık ve artırılmış gerçeklik teknolojisi ile müşterilerimize sanal deneme imkanı sunduk. Özel tasarım koleksiyonlarımızla moda dünyasında ses getirmeye devam ediyoruz.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Değerlerimiz -->
<section class="about-values">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">Değerlerimiz</h2>
      <p class="section-description">Her adımımızda bizi yönlendiren temel ilkelerimiz</p>
    </div>
    
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-leaf"></i>
        </div>
        <h3 class="value-title">Sürdürülebilirlik</h3>
        <p class="value-description">Doğaya saygılı üretim süreçleri ve malzemeler kullanarak, gelecek nesillere daha yaşanabilir bir dünya bırakmayı hedefliyoruz.</p>
      </div>
      
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-gem"></i>
        </div>
        <h3 class="value-title">Kalite</h3>
        <p class="value-description">Her bir ürünümüzü en yüksek kalite standartlarında üretiyor, uzun ömürlü ve dayanıklı parçalar sunuyoruz.</p>
      </div>
      
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-lightbulb"></i>
        </div>
        <h3 class="value-title">Yenilikçilik</h3>
        <p class="value-description">Moda trendlerini takip ediyor, yenilikçi tasarımlar ve teknolojilerle müşterilerimize en iyi deneyimi sunuyoruz.</p>
      </div>
      
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-heart"></i>
        </div>
        <h3 class="value-title">Müşteri Memnuniyeti</h3>
        <p class="value-description">Müşteri memnuniyetini her şeyin üzerinde tutuyor, beklentileri aşan hizmet ve ürünler sunuyoruz.</p>
      </div>
      
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-handshake"></i>
        </div>
        <h3 class="value-title">Etik Çalışma</h3>
        <p class="value-description">Adil çalışma koşulları sağlıyor, tedarikçilerimizi bu anlayışla seçiyor ve iş ortaklarımızla uzun süreli ilişkiler kuruyoruz.</p>
      </div>
      
      <div class="value-card">
        <div class="value-icon">
          <i class="fas fa-globe"></i>
        </div>
        <h3 class="value-title">Sosyal Sorumluluk</h3>
        <p class="value-description">Topluma katkı sağlamak için sosyal sorumluluk projeleri geliştiriyor, gelirlerimizin bir kısmını bu amaçla kullanıyoruz.</p>
      </div>
    </div>
  </div>
</section>

<!-- Ekibimiz -->
<section class="about-team">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">Ekibimizle Tanışın</h2>
      <p class="section-description">ModaVista'nın arkasındaki yaratıcı ve tutkulu insanlar</p>
    </div>
    
    <div class="team-grid">
      <div class="team-member">
        <div class="member-photo">
          <img src="/api/placeholder/400/400" alt="Ahmet Yılmaz">
        </div>
        <h3 class="member-name">Ahmet Yılmaz</h3>
        <p class="member-position">Kurucu & CEO</p>
        <div class="member-social">
          <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      
      <div class="team-member">
        <div class="member-photo">
          <img src="/api/placeholder/400/400" alt="Zeynep Kaya">
        </div>
        <h3 class="member-name">Zeynep Kaya</h3>
        <p class="member-position">Kreatif Direktör</p>
        <div class="member-social">
          <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      
      <div class="team-member">
        <div class="member-photo">
          <img src="/api/placeholder/400/400" alt="Mehmet Demir">
        </div>
        <h3 class="member-name">Mehmet Demir</h3>
        <p class="member-position">Tasarım Direktörü</p>
        <div class="member-social">
          <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
      
      <div class="team-member">
        <div class="member-photo">
          <img src="/api/placeholder/400/400" alt="Elif Şahin">
        </div>
        <h3 class="member-name">Elif Şahin</h3>
        <p class="member-position">Pazarlama Müdürü</p>
        <div class="member-social">
          <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Markalarımız -->
<section class="about-partners">
  <div class="container">
    <div class="section-header text-center">
      <h2 class="section-title">İş Ortaklarımız</h2>
      <p class="section-description">Birlikte çalıştığımız değerli markalar</p>
    </div>
    
    <div class="partners-grid">
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 1">
      </div>
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 2">
      </div>
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 3">
      </div>
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 4">
      </div>
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 5">
      </div>
      <div class="partner-logo">
        <img src="/api/placeholder/200/100" alt="Partner 6">
      </div>
    </div>
  </div>
</section>

<!-- Çağrı Bölümü -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content">
      <h2 class="cta-title">ModaVista Ailesinin Bir Parçası Olun</h2>
      <p class="cta-description">En yeni koleksiyonlarımızdan ve özel indirimlerimizden ilk siz haberdar olun.</p>
      <form class="newsletter-form">
        <div class="input-group">
          <input type="email" class="form-control" placeholder="E-posta adresiniz" required>
          <button type="submit" class="btn btn-primary">Abone Ol</button>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include_once 'includes/footer.php'; ?>

<style>
.about-banner {
  background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('img/about-bg.jpg') no-repeat center center/cover;
  margin-top: 80px;
  padding: 80px 0;
  color: white;
}
</style> 