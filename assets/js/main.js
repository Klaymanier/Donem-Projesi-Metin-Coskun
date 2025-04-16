// DOM elementlerini seçme
const header = document.querySelector('.header');
const menuToggle = document.getElementById('menuToggle');
const mobileMenu = document.getElementById('mobileMenu');
const menuClose = document.getElementById('menuClose');
const searchToggle = document.querySelector('.search-toggle');
const searchPanel = document.getElementById('searchPanel');
const searchClose = document.getElementById('searchClose');
const searchInput = document.querySelector('.search-input');
const themeToggle = document.querySelector('.theme-toggle');
const themeDropdownMenu = document.querySelector('.theme-dropdown-menu');
const themeOptions = document.querySelectorAll('.theme-option');
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
  // Tema dropdown dışına tıklama
  if (themeDropdownMenu && themeToggle) {
    if (!themeToggle.contains(e.target) && !themeDropdownMenu.contains(e.target)) {
      themeDropdownMenu.classList.remove('active');
    }
  }
  
  // User dropdown dışına tıklama
  if (userDropdownMenu && userToggle) {
    if (!userToggle.contains(e.target) && !userDropdownMenu.contains(e.target)) {
      userDropdownMenu.classList.remove('active');
    }
  }
});

// Mobil menü açma/kapama
if (menuToggle) {
  menuToggle.addEventListener('click', () => {
    mobileMenu.classList.add('active');
    document.body.style.overflow = 'hidden';
  });
}

if (menuClose) {
  menuClose.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
    document.body.style.overflow = '';
  });
}

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

// Tema değiştirme açılır menüsü
if (themeToggle && themeDropdownMenu) {
  themeToggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    themeDropdownMenu.classList.toggle('active');
    
    // Diğer açık menüleri kapat
    if (userDropdownMenu) {
      userDropdownMenu.classList.remove('active');
    }
  });
  
  if (themeOptions) {
    themeOptions.forEach(option => {
      option.addEventListener('click', () => {
        const theme = option.getAttribute('data-theme');
        setTheme(theme);
        themeDropdownMenu.classList.remove('active');
      });
    });
  }
}

// Kullanıcı dropdown menüsü
if (userToggle && userDropdownMenu) {
  userToggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    userDropdownMenu.classList.toggle('active');
    
    // Diğer açık menüleri kapat
    if (themeDropdownMenu) {
      themeDropdownMenu.classList.remove('active');
    }
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
  
  if (theme === 'auto') {
    // Sistem temasını kontrol et
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      html.setAttribute('data-theme', 'dark');
      updateThemeIcon('dark');
    } else {
      html.setAttribute('data-theme', 'light');
      updateThemeIcon('light');
    }
    
    // Sistem teması değişikliğini dinle
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', (e) => {
      const newTheme = e.matches ? 'dark' : 'light';
      html.setAttribute('data-theme', newTheme);
      updateThemeIcon(newTheme);
    });
  } else {
    // Manuel tema değiştirme
    html.setAttribute('data-theme', theme);
    updateThemeIcon(theme);
  }
  
  // Tema cookie'sini ayarla
  document.cookie = `theme=${theme}; path=/; max-age=${60*60*24*365}`;
}

// Tema ikonunu güncelleme
function updateThemeIcon(theme) {
  if (themeToggle) {
    themeToggle.innerHTML = theme === 'dark' ? 
      '<i class="fas fa-moon"></i>' : 
      '<i class="fas fa-sun"></i>';
  }
}

// Sayfa yüklendiğinde mevcut temayı kontrol et
document.addEventListener('DOMContentLoaded', () => {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  if (currentTheme) {
    updateThemeIcon(currentTheme);
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