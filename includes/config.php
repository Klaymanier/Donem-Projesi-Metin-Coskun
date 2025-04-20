<?php
/**
 * Config.php - ModaVista Yapılandırma Dosyası
 * 
 * Site ayarları ve yapılandırması için kullanılır
 */

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Zaman dilimi ayarları
date_default_timezone_set('Europe/Istanbul');

// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'giyim_sitesi');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8mb4');

// Site ayarları
define('SITE_URL', 'http://localhost/modavista');
define('SITE_NAME', 'ModaVista');
define('SITE_EMAIL', 'info@modavista.com');
define('SITE_DESCRIPTION', 'En yeni moda trendleri ve özel tasarım giyim ürünleri ModaVista\'da!');

// Sayfalama ayarları
define('ITEMS_PER_PAGE', 12);

// Dosya yükleme ayarları
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// API anahtarları
define('GOOGLE_MAPS_API_KEY', '');
define('IYZICO_API_KEY', '');
define('IYZICO_SECRET_KEY', '');

// Oturum ayarları
ini_set('session.cookie_httponly', 1);
session_start();

// Güvenlik fonksiyonları
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function validateToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// CSRF token oluştur
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateToken();
}

// Para birimi formatla
function formatMoney($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

// Anlık zaman oluştur
function timeAgo($timestamp) {
    $diff = time() - strtotime($timestamp);
    
    if ($diff < 60) {
        return $diff . ' saniye önce';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' dakika önce';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' saat önce';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' gün önce';
    } elseif ($diff < 2592000) {
        return floor($diff / 604800) . ' hafta önce';
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . ' ay önce';
    } else {
        return floor($diff / 31536000) . ' yıl önce';
    }
}

// Slug oluştur
function createSlug($str) {
    $str = mb_strtolower($str, 'UTF-8');
    $str = str_replace(
        ['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'],
        ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'],
        $str
    );
    $str = preg_replace('/[^a-z0-9]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}