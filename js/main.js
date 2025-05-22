// DOM elementlerini seçme
const header = document.querySelector('.header');
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.querySelector('.mobile-menu');
const menuClose = document.querySelector('.mobile-menu-close');
const searchToggle = document.querySelector('.search-toggle');
const searchPanel = document.getElementById('searchPanel');
const searchClose = document.getElementById('searchClose');
const searchInput = document.querySelector('.search-input');
const themeToggle = document.querySelector('.theme-toggle');
const userToggle = document.querySelector('.user-toggle');
const userDropdownMenu = document.querySelector('.user-dropdown-menu');
const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

// Sayfa kaydırılınca header stilini değiştirme
window.addEventListener('scroll', () => {
  if (window.scrollY > 100) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

// Dropdown menülerin dışına tıklayınca kapanması
document.addEventListener('click', (e) => {
  // User dropdown dışına tıklama
  if (userDropdownMenu && userToggle) {
    if (!userToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
      userDropdownMenu.classList.remove('active');
    }
  }
});

// Mobil menü açma/kapama
if (menuToggle && mobileMenu) {
  menuToggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    // Menü açık mı kontrol et
    if (mobileMenu.classList.contains('active')) {
      mobileMenu.classList.remove('active');
      document.body.style.overflow = ''; // Kaydırmayı geri aç
    } else {
      mobileMenu.classList.add('active');
      document.body.style.overflow = 'hidden'; // Kaydırmayı kapat
    }
  });
}


if (menuClose && mobileMenu) {
  menuClose.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    mobileMenu.classList.remove('active');
    document.body.style.overflow = 'hidden';
  });
}

// Mobil menü dışına tıklayınca kapanması
// Sadece bir tane document click event olsun ve toggle'a tıklama hariç tutulsun

document.addEventListener('click', (e) => {
  if (mobileMenu && mobileMenu.classList.contains('active')) {
    // Eğer tıklanan yer mobileMenu'nun veya menuToggle'ın içindeyse kapatma
    if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
      mobileMenu.classList.remove('active');
      document.body.style.overflow = '';
    }
  }
}, true); // capture modunda dinle

// ESC tuşuna basınca mobil menüyü kapat
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('active')) {
    mobileMenu.classList.remove('active');
    document.body.style.overflow = '';
  }
});

// Arama paneli açma/kapama
if (searchToggle && searchPanel && searchClose) {
  searchToggle.addEventListener('click', () => {
    searchPanel.classList.add('active');
    document.body.style.overflow = 'hidden';
    // Odağı arama kutusuna getir
    setTimeout(() => {
      searchInput.focus();
    }, 100);
  });
  
  searchClose.addEventListener('click', () => {
    searchPanel.classList.remove('active');
    document.body.style.overflow = '';
  });
  
  // ESC tuşuna basınca arama panelini kapat
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && searchPanel.classList.contains('active')) {
      searchPanel.classList.remove('active');
      document.body.style.overflow = '';
    }
  });
}

// Kullanıcı dropdown menüsü
if (userToggle && userDropdownMenu) {
  userToggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    userDropdownMenu.classList.toggle('active');
  });
}

// Dropdown menu işlemleri
dropdownToggles.forEach(toggle => {
  toggle.addEventListener('click', (e) => {
    e.preventDefault();
    const parent = toggle.closest('li');
    parent.classList.toggle('open');
  });
});

// Tema değiştirme fonksiyonu
function setTheme(theme) {
  const html = document.documentElement;
  
  // Tema geçişleri için geçiş efektini etkinleştir
  html.classList.add('theme-transition');
  
  // Temayı ayarla
  html.setAttribute('data-theme', theme);
  
  // Tema ikonunu güncelle
  updateThemeIcon(theme);
  
  // Tema bilgisini localStorage'a kaydet
  localStorage.setItem('theme', theme);
  
  // Geçiş efektini kaldır (animasyon tamamlandıktan sonra)
  setTimeout(() => {
    html.classList.remove('theme-transition');
  }, 500);
}

// Tema ikonunu güncelleme
function updateThemeIcon(theme) {
  if (themeToggle) {
    themeToggle.innerHTML = theme === 'dark' ? 
      '<i class="fas fa-moon"></i>' : 
      '<i class="fas fa-sun"></i>';
  }
}

// Tema değiştirme toggle butonu
if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
  });
}

// Sayfa yüklendiğinde mevcut temayı kontrol et ve uygula
document.addEventListener('DOMContentLoaded', () => {
  // localStorage'dan tema bilgisini al veya sistem tercihini kontrol et
  const savedTheme = localStorage.getItem('theme');
  
  if (savedTheme) {
    // Kaydedilmiş tema varsa onu kullan
    setTheme(savedTheme);
  } else {
    // Yoksa sistem tercihini kontrol et
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    setTheme(prefersDark ? 'dark' : 'light');
  }
  
  // Sistem teması değişikliğini dinle (sadece otomatik modda ise)
  if (!savedTheme) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
      const newTheme = e.matches ? 'dark' : 'light';
      setTheme(newTheme);
    });
  }
});

// Preloader
window.addEventListener('load', () => {
  const preloader = document.querySelector('.preloader');
  if (preloader) {
    setTimeout(() => {
      preloader.classList.add('loaded');
    }, 500);
  }
});