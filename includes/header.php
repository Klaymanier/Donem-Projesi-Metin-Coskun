<?php
// CSRF Token oluştur - eğer yoksa
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="<?php echo isset($lang) ? $lang : 'tr'; ?>" data-theme="<?php echo isset($theme) ? $theme : 'light'; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo isset($description) ? $description : 'ModaVista - Modern Giyim'; ?>">
  <meta name="theme-color" content="<?php echo isset($theme) && $theme === 'dark' ? '#111827' : '#ffffff'; ?>">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'ModaVista'; ?></title>
  
  <!-- Favicon -->
  <link rel="icon" href="assets/images/favicon<?php echo isset($theme) && $theme === 'dark' ? '-dark' : ''; ?>.png" type="image/png">
  
  <!-- Fontlar -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  
  <!-- CSS Dosyaları -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- Preloader -->
  <div class="preloader">
    <div class="loader"></div>
  </div>
  
  <!-- Header -->
  <header class="header">
    <div class="header-inner">
      <!-- Logo -->
      <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fas fa-tshirt"></i></div>
        ModaVista
      </a>
      
      <!-- Ana Navigasyon -->
      <nav class="main-nav">
        <ul class="nav-links">
          <!-- Tüm Ürünler Dropdown -->
          <li class="dropdown">
            <a href="products.php" class="nav-link">Tüm Ürünler</a>
            <div class="dropdown-menu">
              <?php 
              // Kategorileri göster - veritabanı veya varsayılan
              foreach ($menuKategoriler as $category): 
              ?>
                <a href="products.php?kategori=<?php echo $category['kategori_slug']; ?>" 
                  class="<?php echo (isset($currentKategori) && $currentKategori == $category['kategori_slug']) ? 'active' : ''; ?>">
                  <?php echo $category['kategori_adi']; ?>
                </a>
              <?php endforeach; ?>
            </div>
          </li>
          <li>
            <a href="about.php" 
              class="nav-link <?php echo (isset($pageTitle) && strpos($pageTitle, 'Hakkımızda') !== false) ? 'active' : ''; ?>">
              Hakkımızda
            </a>
          </li>
          <li>
            <a href="contact.php" 
              class="nav-link <?php echo (isset($pageTitle) && strpos($pageTitle, 'İletişim') !== false) ? 'active' : ''; ?>">
              İletişim
            </a>
          </li>
        </ul>
      </nav>
      
      <!-- Sağ Taraf İkonları -->
      <div class="header-actions">
        <!-- Arama butonu -->
        <button class="header-action search-toggle">
          <i class="fas fa-search"></i>
        </button>
        
        <!-- Sepet butonu -->
        <a href="cart.php" class="header-action">
          <i class="fas fa-shopping-bag"></i>
          <?php if (isset($sepetUrunSayisi) && $sepetUrunSayisi > 0): ?>
            <span class="badge"><?php echo $sepetUrunSayisi; ?></span>
          <?php endif; ?>
        </a>
        
        <!-- Tema Açılır Menüsü -->
        <div class="theme-dropdown">
          <button class="header-action theme-toggle">
            <?php if (isset($theme) && $theme === 'dark'): ?>
              <i class="fas fa-moon"></i>
            <?php else: ?>
              <i class="fas fa-sun"></i>
            <?php endif; ?>
          </button>
          <div class="theme-dropdown-menu">
            <button class="theme-option" data-theme="light">
              <i class="fas fa-sun"></i>
              <span>Aydınlık Mod</span>
            </button>
            <button class="theme-option" data-theme="dark">
              <i class="fas fa-moon"></i>
              <span>Karanlık Mod</span>
            </button>
            <button class="theme-option" data-theme="auto">
              <i class="fas fa-circle-half-stroke"></i>
              <span>Sistem Ayarı</span>
            </button>
          </div>
        </div>
        
        <!-- Kullanıcı butonu -->
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="user-dropdown">
            <button class="header-action user-toggle">
              <i class="fas fa-user"></i>
            </button>
            <div class="user-dropdown-menu">
              <a href="profile.php" class="user-dropdown-item">
                <i class="fas fa-user-circle"></i>
                <span>Profilim</span>
              </a>
              <a href="orders.php" class="user-dropdown-item">
                <i class="fas fa-box"></i>
                <span>Siparişlerim</span>
              </a>
              <a href="wishlist.php" class="user-dropdown-item">
                <i class="fas fa-heart"></i>
                <span>Favorilerim</span>
              </a>
              <a href="logout.php" class="user-dropdown-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Çıkış Yap</span>
              </a>
            </div>
          </div>
        <?php else: ?>
          <a href="login.php" class="header-action">
            <i class="fas fa-user"></i>
          </a>
        <?php endif; ?>
        
        <!-- Hamburger Menü (En sağda) -->
        <button class="header-action menu-toggle" id="menuToggle">
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </div>
  </header>
  
  <!-- Mobil Menü -->
  <div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
      <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fas fa-tshirt"></i></div>
        ModaVista
      </a>
      <button class="mobile-menu-close" id="menuClose">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <div class="mobile-menu-content">
      <ul class="mobile-nav-links">
        <li>
          <div class="mobile-nav-dropdown">
            <div class="mobile-nav-link dropdown-toggle">
              Tüm Ürünler
              <i class="fas fa-chevron-down"></i>
            </div>
            <div class="mobile-dropdown-menu">
              <?php foreach ($menuKategoriler as $category): ?>
                <a href="products.php?kategori=<?php echo $category['kategori_slug']; ?>" 
                  class="<?php echo (isset($currentKategori) && $currentKategori == $category['kategori_slug']) ? 'active' : ''; ?>">
                  <?php echo $category['kategori_adi']; ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </li>
        <li>
          <a href="about.php" 
            class="mobile-nav-link <?php echo (isset($pageTitle) && strpos($pageTitle, 'Hakkımızda') !== false) ? 'active' : ''; ?>">
            Hakkımızda
          </a>
        </li>
        <li>
          <a href="contact.php" 
            class="mobile-nav-link <?php echo (isset($pageTitle) && strpos($pageTitle, 'İletişim') !== false) ? 'active' : ''; ?>">
            İletişim
          </a>
        </li>
      </ul>
      
      <div class="mobile-header-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="profile.php" class="btn btn-outline btn-sm">
            <i class="fas fa-user-circle"></i>
            Profilim
          </a>
          <a href="logout.php" class="btn btn-primary btn-sm">
            <i class="fas fa-sign-out-alt"></i>
            Çıkış Yap
          </a>
        <?php else: ?>
          <a href="login.php" class="btn btn-outline btn-sm">
            <i class="fas fa-sign-in-alt"></i>
            Giriş Yap
          </a>
          <a href="register.php" class="btn btn-primary btn-sm">
            <i class="fas fa-user-plus"></i>
            Kayıt Ol
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Arama Paneli -->
  <div class="search-panel" id="searchPanel">
    <div class="container">
      <form class="search-form" action="products.php" method="get">
        <input type="text" class="search-input" name="arama" placeholder="Ne aramıştınız?" required>
        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
      </form>
      <button class="search-close" id="searchClose"><i class="fas fa-times"></i></button>
    </div>
  </div>
</body>
</html>