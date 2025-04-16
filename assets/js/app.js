/**
 * ModaVista 2025 - Ana JavaScript Dosyası
 */

// DOM'un yüklenmesini bekle
document.addEventListener('DOMContentLoaded', function() {
    // Değişkenler
    const header = document.querySelector('.header');
    const searchToggle = document.querySelector('.search-toggle');
    const searchPanel = document.querySelector('.search-panel');
    const searchClose = document.querySelector('.search-close');
    const menuToggle = document.getElementById('menuToggle');
    const themeSwitch = document.getElementById('themeSwitch');
    const body = document.body;
    const productFilters = document.querySelectorAll('.filter-button');
    const productCards = document.querySelectorAll('.product-card');
    const colorOptions = document.querySelectorAll('.color-option');
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    // Header scroll efekti
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
    
    // Arama panelini aç/kapa
    if (searchToggle && searchPanel && searchClose) {
      searchToggle.addEventListener('click', function(e) {
        e.preventDefault();
        searchPanel.classList.add('active');
        document.querySelector('.search-input').focus();
      });
      
      searchClose.addEventListener('click', function() {
        searchPanel.classList.remove('active');
      });
      
      // ESC tuşu ile kapatma
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchPanel.classList.contains('active')) {
          searchPanel.classList.remove('active');
        }
      });
    }
    
    // Mobil menü toggle
    if (menuToggle) {
      menuToggle.addEventListener('click', function() {
        body.classList.toggle('menu-open');
      });
    }
    
    // Tema değiştirme (aydınlık/karanlık)
    if (themeSwitch) {
      themeSwitch.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        // Geçiş animasyonu için sınıf ekle
        document.documentElement.classList.add('theme-transition');
        
        // Tema değiştir
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Geçiş animasyonu sınıfını kaldır
        setTimeout(() => {
          document.documentElement.classList.remove('theme-transition');
        }, 600);
        
        // Cookie'ye temayı kaydet (1 yıl geçerli)
        const date = new Date();
        date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000));
        document.cookie = `theme=${newTheme}; expires=${date.toUTCString()}; path=/`;
        
        // Kullanıcıya bildirim göster
        showToast(`${newTheme === 'dark' ? 'Karanlık' : 'Aydınlık'} tema aktifleştirildi`, 'info');
  
        // UI elemanlarını güncelle
        updateThemeUI(newTheme);
      });
    }
    
    // Sayfa ilk yüklendiğinde tema kontrolü
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      document.documentElement.setAttribute('data-theme', savedTheme);
      updateThemeUI(savedTheme);
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      // Kullanıcının sistem tercihi karanlık modsa
      document.documentElement.setAttribute('data-theme', 'dark');
      updateThemeUI('dark');
    }
    
    // Tema değiştiğinde UI güncellemesi
    function updateThemeUI(theme) {
      // Favicon güncelleme
      const favicon = document.querySelector('link[rel="icon"]');
      if (favicon) {
        favicon.href = theme === 'dark' ? 'assets/images/favicon-dark.png' : 'assets/images/favicon.png';
      }
      
      // Mobil statüs çubuğu rengini güncelleme
      const metaThemeColor = document.querySelector('meta[name="theme-color"]');
      if (metaThemeColor) {
        metaThemeColor.content = theme === 'dark' ? '#111827' : '#ffffff';
      }
      
      // Header logosu güncelleme (eğer farklı logo kullanılıyorsa)
      // const logo = document.querySelector('.logo img');
      // if (logo) {
      //   logo.src = theme === 'dark' ? 'assets/images/logo-dark.png' : 'assets/images/logo.png';
      // }
    }
    
    // Ürün filtresi
    if (productFilters.length > 0) {
      productFilters.forEach(button => {
        button.addEventListener('click', function() {
          // Aktif filtreyi kaldır
          document.querySelector('.filter-button.active').classList.remove('active');
          // Tıklanan filtreyi aktif yap
          this.classList.add('active');
          
          const filter = this.getAttribute('data-filter');
          
          // Burada filtreleme işlemi AJAX ile yapılabilir
          // Örnek olarak basit bir animasyon ekliyoruz
          productCards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, 300);
          });
        });
      });
    }
    
    // Renk seçenekleri
    if (colorOptions.length > 0) {
      colorOptions.forEach(option => {
        option.addEventListener('click', function() {
          // Renk gruplarını bul
          const parent = this.parentElement;
          
          // Aktif rengi kaldır
          parent.querySelector('.color-option.active')?.classList.remove('active');
          
          // Tıklanan rengi aktif yap
          this.classList.add('active');
        });
      });
    }
    
    // Sepete ekle butonları
    if (addToCartButtons.length > 0) {
      addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
          const productId = this.getAttribute('data-product-id');
          const cartCount = document.querySelector('.cart-count');
          
          // Sepete ekleme animasyonu
          this.innerHTML = '<i class="fas fa-check"></i> Sepete Eklendi';
          this.style.backgroundColor = 'var(--success-color)';
          this.style.color = 'white';
          
          // Sepet sayısını güncelle
          if (cartCount) {
            const currentCount = parseInt(cartCount.textContent || '0');
            cartCount.textContent = currentCount + 1;
            
            // Animasyon ekle
            cartCount.style.transform = 'scale(1.3)';
            setTimeout(() => {
              cartCount.style.transform = 'scale(1)';
            }, 300);
          }
          
          // AJAX ile sepete ürün eklenebilir
          // fetch('/sepete-ekle', {
          //   method: 'POST',
          //   body: JSON.stringify({ urun_id: productId }),
          //   headers: {
          //     'Content-Type': 'application/json'
          //   }
          // })
          
          // Buton durumunu geri al
          setTimeout(() => {
            this.innerHTML = '<i class="fas fa-shopping-cart"></i> Sepete Ekle';
            this.style.backgroundColor = '';
            this.style.color = '';
          }, 2000);
  
          // Kullanıcıya bildirim göster
          showToast('Ürün sepete eklendi', 'success');
        });
      });
    }
    
    // Favori butonları
    const favoriteButtons = document.querySelectorAll('.product-action');
    if (favoriteButtons.length > 0) {
      favoriteButtons.forEach(button => {
        if (button.title === 'Favorilere Ekle') {
          button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            
            if (icon.classList.contains('far')) {
              icon.classList.remove('far');
              icon.classList.add('fas');
              this.style.backgroundColor = 'var(--secondary-color)';
              showToast('Ürün favorilere eklendi');
            } else {
              icon.classList.remove('fas');
              icon.classList.add('far');
              this.style.backgroundColor = '';
              showToast('Ürün favorilerden çıkarıldı');
            }
          });
        }
      });
    }
    
    // Toast mesajı gösterme fonksiyonu
  function showToast(message, type = 'success') {
    // Toast elementi yoksa oluştur
    if (!document.querySelector('.toast-container')) {
      const toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container';
      document.body.appendChild(toastContainer);
    }
    
    const toastContainer = document.querySelector('.toast-container');
    
    // Toast elementi oluştur
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    
    // Toast'u ekle
    toastContainer.appendChild(toast);
    
    // Gösterme animasyonu
    setTimeout(() => {
      toast.classList.add('show');
    }, 10);
    
    // Otomatik kapatma
    setTimeout(() => {
      toast.classList.remove('show');
      
      // Kapatma animasyonu bittikten sonra elementi kaldır
      setTimeout(() => {
        toast.remove();
      }, 300);
    }, 3000);
  }
  
  // Lazy loading görsel yükleme
  if ('IntersectionObserver' in window) {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          observer.unobserve(img);
        }
      });
    });
    
    lazyImages.forEach(img => {
      imageObserver.observe(img);
    });
  }
  
  // Animasyonlar için scroll observer
  if ('IntersectionObserver' in window) {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const animationObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animated');
        }
      });
    }, { threshold: 0.1 });
    
    animatedElements.forEach(element => {
      animationObserver.observe(element);
    });
  }
  
  // Dil ve para birimi değiştirici
  const currencySelector = document.getElementById('currencySelector');
  const languageSelector = document.getElementById('languageSelector');
  
  if (currencySelector) {
    currencySelector.addEventListener('change', function() {
      // Para birimi değiştirme işlemi
      const currency = this.value;
      
      // Bu kısımda AJAX ile sunucudan güncel kurlar alınabilir
      // ve ürün fiyatları güncellenebilir
    });
  }
  
  if (languageSelector) {
    languageSelector.addEventListener('change', function() {
      // Dil değiştirme işlemi
      const language = this.value;
      
      // Yönlendirme yapılabilir veya AJAX ile içerik değiştirilebilir
      window.location.href = `?lang=${language}`;
    });
  }
  
  // 3D animasyon efekti için mouse takibi
  const hero = document.querySelector('.hero');
  const heroImage = document.querySelector('.hero-image');
  
  if (hero && heroImage) {
    hero.addEventListener('mousemove', (e) => {
      // Farenin hero içindeki pozisyonunu hesapla (0-1 aralığında)
      const x = e.clientX / hero.offsetWidth - 0.5;
      const y = e.clientY / hero.offsetHeight - 0.5;
      
      // Hero görselini hareket ettir
      heroImage.style.transform = `translateZ(20px) rotateY(${-x * 10}deg) rotateX(${y * 10}deg)`;
    });
    
    // Fareyi üzerinden çekince pozisyonu resetle
    hero.addEventListener('mouseleave', () => {
      heroImage.style.transform = 'translateZ(0) rotateY(0) rotateX(0)';
    });
  }
  
  // Toast stili için CSS ekle
  const style = document.createElement('style');
  style.textContent = `
    .toast-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: var(--z-toast);
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    
    .toast {
      background-color: var(--bg-surface);
      color: var(--text-color);
      box-shadow: 0 5px 15px var(--shadow-color);
      padding: 12px 20px;
      border-radius: var(--radius-md);
      transform: translateX(100%);
      opacity: 0;
      transition: all 0.3s ease;
      min-width: 200px;
      max-width: 350px;
    }
    
    .toast.show {
      transform: translateX(0);
      opacity: 1;
    }
    
    .toast-success {
      border-left: 4px solid var(--success-color);
    }
    
    .toast-error {
      border-left: 4px solid var(--error-color);
    }
    
    .toast-warning {
      border-left: 4px solid var(--warning-color);
    }
    
    .toast-info {
      border-left: 4px solid var(--info-color);
    }
  `;
  
  document.head.appendChild(style);
});

/**
 * Sayfa yüklendikten sonraki işlemler
 */
window.addEventListener('load', function() {
  // Sayfa yükleme göstergesini kaldır
  const preloader = document.querySelector('.preloader');
  if (preloader) {
    preloader.classList.add('loaded');
    setTimeout(() => {
      preloader.style.display = 'none';
    }, 500);
  }
  
  // Hero bölümü için animasyon efekti
  const heroContent = document.querySelector('.hero-content');
  const heroImage = document.querySelector('.hero-image');
  
  if (heroContent) {
    heroContent.style.opacity = '1';
    heroContent.style.transform = 'translateY(0)';
  }
  
  if (heroImage) {
    heroImage.style.opacity = '1';
    heroImage.style.transform = 'scale(1)';
  }

  // Sayfa ilk yüklendiğinde ürün kartlarına kademeli görünme animasyonu ekle
  const productCards = document.querySelectorAll('.product-card');
  productCards.forEach((card, index) => {
    setTimeout(() => {
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 100 * index);
  });
});

/**
 * Sayfadan ayrılırken görünecek animasyonlar
 */
document.addEventListener('visibilitychange', function() {
  const title = document.title;
  const siteName = 'ModaVista';
  
  if (document.visibilityState === 'hidden') {
    document.title = '🛍️ Sizi özledik! | ' + siteName;
  } else {
    document.title = title;
  }
});

/**
 * Hata yakalama
 */
window.onerror = function(message, source, lineno, colno, error) {
  console.error('Hata oluştu:', message, source, lineno, colno, error);
  
  // Kritik hatalar için kullanıcıya bildirim göster
  if (typeof showToast === 'function') {
    showToast('Bir hata oluştu. Lütfen sayfayı yenileyin.', 'error');
  }
  
  return true; // Hata işlendi
};