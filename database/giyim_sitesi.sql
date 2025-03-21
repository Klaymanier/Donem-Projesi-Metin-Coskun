-- --------------------------------------------------------
-- Sunucu:                       127.0.0.1
-- Sunucu sürümü:                8.4.3 - MySQL Community Server - GPL
-- Sunucu İşletim Sistemi:       Win64
-- HeidiSQL Sürüm:               12.8.0.6908
-- --------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET NAMES utf8 */;

/*!50503 SET NAMES utf8mb4 */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;

/*!40103 SET TIME_ZONE='+00:00' */;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- giyim_sitesi için veritabanı yapısı dökülüyor
CREATE DATABASE IF NOT EXISTS `giyim_sitesi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `giyim_sitesi`;

-- tablo yapısı dökülüyor giyim_sitesi.adresler
CREATE TABLE
  IF NOT EXISTS `adresler` (
    `adres_id` int NOT NULL AUTO_INCREMENT,
    `kullanici_id` int DEFAULT NULL,
    `adres_basligi` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `ad_soyad` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `telefon` varchar(20) COLLATE utf8mb4_turkish_ci NOT NULL,
    `adres_satiri1` text COLLATE utf8mb4_turkish_ci NOT NULL,
    `adres_satiri2` text COLLATE utf8mb4_turkish_ci,
    `ilce` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `sehir` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `posta_kodu` varchar(20) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `varsayilan` tinyint (1) DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`adres_id`),
    KEY `kullanici_id` (`kullanici_id`),
    CONSTRAINT `adresler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.bedenler
CREATE TABLE
  IF NOT EXISTS `bedenler` (
    `beden_id` int NOT NULL AUTO_INCREMENT,
    `beden_adi` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`beden_id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.favoriler
CREATE TABLE
  IF NOT EXISTS `favoriler` (
    `favori_id` int NOT NULL AUTO_INCREMENT,
    `kullanici_id` int DEFAULT NULL,
    `urun_id` int DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`favori_id`),
    UNIQUE KEY `kullanici_id` (`kullanici_id`, `urun_id`),
    KEY `urun_id` (`urun_id`),
    CONSTRAINT `favoriler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE,
    CONSTRAINT `favoriler_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.iletisim_mesajlari
CREATE TABLE
  IF NOT EXISTS `iletisim_mesajlari` (
    `mesaj_id` int NOT NULL AUTO_INCREMENT,
    `ad_soyad` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `email` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `telefon` varchar(20) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `konu` varchar(200) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `mesaj` text COLLATE utf8mb4_turkish_ci NOT NULL,
    `ip_adresi` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `okundu` tinyint (1) DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mesaj_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.kategoriler
CREATE TABLE
  IF NOT EXISTS `kategoriler` (
    `kategori_id` int NOT NULL AUTO_INCREMENT,
    `kategori_adi` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `kategori_aciklama` text COLLATE utf8mb4_turkish_ci,
    `kategori_slug` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `aktif` tinyint (1) DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kategori_id`),
    UNIQUE KEY `kategori_slug` (`kategori_slug`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 5 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.kullanicilar
CREATE TABLE
  IF NOT EXISTS `kullanicilar` (
    `kullanici_id` int NOT NULL AUTO_INCREMENT,
    `ad` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `soyad` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `email` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `sifre` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
    `telefon` varchar(20) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `dogum_tarihi` date DEFAULT NULL,
    `kullanici_tipi` enum ('musteri', 'admin') COLLATE utf8mb4_turkish_ci DEFAULT 'musteri',
    `aktif` tinyint (1) DEFAULT '1',
    `son_giris` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`kullanici_id`),
    UNIQUE KEY `email` (`email`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.renkler
CREATE TABLE
  IF NOT EXISTS `renkler` (
    `renk_id` int NOT NULL AUTO_INCREMENT,
    `renk_adi` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `renk_kodu` varchar(20) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`renk_id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.sepet
CREATE TABLE
  IF NOT EXISTS `sepet` (
    `sepet_id` int NOT NULL AUTO_INCREMENT,
    `kullanici_id` int DEFAULT NULL,
    `session_id` varchar(100) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sepet_id`),
    KEY `kullanici_id` (`kullanici_id`),
    CONSTRAINT `sepet_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.sepet_ogeleri
CREATE TABLE
  IF NOT EXISTS `sepet_ogeleri` (
    `sepet_oge_id` int NOT NULL AUTO_INCREMENT,
    `sepet_id` int DEFAULT NULL,
    `urun_id` int DEFAULT NULL,
    `varyasyon_id` int DEFAULT NULL,
    `adet` int NOT NULL DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sepet_oge_id`),
    KEY `sepet_id` (`sepet_id`),
    KEY `urun_id` (`urun_id`),
    KEY `varyasyon_id` (`varyasyon_id`),
    CONSTRAINT `sepet_ogeleri_ibfk_1` FOREIGN KEY (`sepet_id`) REFERENCES `sepet` (`sepet_id`) ON DELETE CASCADE,
    CONSTRAINT `sepet_ogeleri_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`),
    CONSTRAINT `sepet_ogeleri_ibfk_3` FOREIGN KEY (`varyasyon_id`) REFERENCES `urun_varyasyonlari` (`varyasyon_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.siparisler
CREATE TABLE
  IF NOT EXISTS `siparisler` (
    `siparis_id` int NOT NULL AUTO_INCREMENT,
    `kullanici_id` int DEFAULT NULL,
    `adres_id` int DEFAULT NULL,
    `siparis_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `toplam_tutar` decimal(10, 2) NOT NULL,
    `indirim_tutari` decimal(10, 2) DEFAULT '0.00',
    `kargo_tutari` decimal(10, 2) DEFAULT '0.00',
    `odeme_tutari` decimal(10, 2) NOT NULL,
    `siparis_durumu` enum (
      'beklemede',
      'onaylandi',
      'hazirlaniyor',
      'kargoya_verildi',
      'tamamlandi',
      'iptal_edildi'
    ) COLLATE utf8mb4_turkish_ci DEFAULT 'beklemede',
    `odeme_yontemi` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `odeme_durumu` enum (
      'beklemede',
      'onaylandi',
      'iptal_edildi',
      'iade_edildi'
    ) COLLATE utf8mb4_turkish_ci DEFAULT 'beklemede',
    `takip_no` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `notlar` text COLLATE utf8mb4_turkish_ci,
    PRIMARY KEY (`siparis_id`),
    KEY `kullanici_id` (`kullanici_id`),
    KEY `adres_id` (`adres_id`),
    CONSTRAINT `siparisler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`),
    CONSTRAINT `siparisler_ibfk_2` FOREIGN KEY (`adres_id`) REFERENCES `adresler` (`adres_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.siparis_detaylari
CREATE TABLE
  IF NOT EXISTS `siparis_detaylari` (
    `detay_id` int NOT NULL AUTO_INCREMENT,
    `siparis_id` int DEFAULT NULL,
    `urun_id` int DEFAULT NULL,
    `varyasyon_id` int DEFAULT NULL,
    `urun_adi` varchar(200) COLLATE utf8mb4_turkish_ci NOT NULL,
    `urun_kodu` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `renk` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `beden` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `adet` int NOT NULL,
    `birim_fiyat` decimal(10, 2) NOT NULL,
    `toplam_fiyat` decimal(10, 2) NOT NULL,
    PRIMARY KEY (`detay_id`),
    KEY `siparis_id` (`siparis_id`),
    KEY `urun_id` (`urun_id`),
    KEY `varyasyon_id` (`varyasyon_id`),
    CONSTRAINT `siparis_detaylari_ibfk_1` FOREIGN KEY (`siparis_id`) REFERENCES `siparisler` (`siparis_id`) ON DELETE CASCADE,
    CONSTRAINT `siparis_detaylari_ibfk_2` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`),
    CONSTRAINT `siparis_detaylari_ibfk_3` FOREIGN KEY (`varyasyon_id`) REFERENCES `urun_varyasyonlari` (`varyasyon_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.sliderlar
CREATE TABLE
  IF NOT EXISTS `sliderlar` (
    `slider_id` int NOT NULL AUTO_INCREMENT,
    `baslik` varchar(200) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `alt_baslik` varchar(200) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `aciklama` text COLLATE utf8mb4_turkish_ci,
    `gorsel_yolu` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
    `link` varchar(255) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `sira` int DEFAULT '0',
    `aktif` tinyint (1) DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`slider_id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.urunler
CREATE TABLE
  IF NOT EXISTS `urunler` (
    `urun_id` int NOT NULL AUTO_INCREMENT,
    `kategori_id` int DEFAULT NULL,
    `urun_adi` varchar(200) COLLATE utf8mb4_turkish_ci NOT NULL,
    `urun_aciklama` text COLLATE utf8mb4_turkish_ci,
    `urun_fiyat` decimal(10, 2) NOT NULL,
    `indirimli_fiyat` decimal(10, 2) DEFAULT NULL,
    `stok_adedi` int NOT NULL DEFAULT '0',
    `urun_kodu` varchar(50) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `urun_slug` varchar(200) COLLATE utf8mb4_turkish_ci DEFAULT NULL,
    `vitrin_urunu` tinyint (1) DEFAULT '0',
    `yeni_urun` tinyint (1) DEFAULT '0',
    `en_cok_satan` tinyint (1) DEFAULT '0',
    `aktif` tinyint (1) DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`urun_id`),
    UNIQUE KEY `urun_kodu` (`urun_kodu`),
    UNIQUE KEY `urun_slug` (`urun_slug`),
    KEY `kategori_id` (`kategori_id`),
    CONSTRAINT `urunler_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategoriler` (`kategori_id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 7 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.urun_gorselleri
CREATE TABLE
  IF NOT EXISTS `urun_gorselleri` (
    `gorsel_id` int NOT NULL AUTO_INCREMENT,
    `urun_id` int DEFAULT NULL,
    `gorsel_yolu` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
    `ana_gorsel` tinyint (1) DEFAULT '0',
    `sira` int DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`gorsel_id`),
    KEY `urun_id` (`urun_id`),
    CONSTRAINT `urun_gorselleri_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 11 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
-- tablo yapısı dökülüyor giyim_sitesi.urun_varyasyonlari
CREATE TABLE
  IF NOT EXISTS `urun_varyasyonlari` (
    `varyasyon_id` int NOT NULL AUTO_INCREMENT,
    `urun_id` int DEFAULT NULL,
    `renk_id` int DEFAULT NULL,
    `beden_id` int DEFAULT NULL,
    `stok_adedi` int NOT NULL DEFAULT '0',
    `ek_fiyat` decimal(10, 2) DEFAULT '0.00',
    `aktif` tinyint (1) DEFAULT '1',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`varyasyon_id`),
    KEY `urun_id` (`urun_id`),
    KEY `renk_id` (`renk_id`),
    KEY `beden_id` (`beden_id`),
    CONSTRAINT `urun_varyasyonlari_ibfk_1` FOREIGN KEY (`urun_id`) REFERENCES `urunler` (`urun_id`) ON DELETE CASCADE,
    CONSTRAINT `urun_varyasyonlari_ibfk_2` FOREIGN KEY (`renk_id`) REFERENCES `renkler` (`renk_id`),
    CONSTRAINT `urun_varyasyonlari_ibfk_3` FOREIGN KEY (`beden_id`) REFERENCES `bedenler` (`beden_id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 25 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

-- Veri çıktısı seçilmemişti
/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;

/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;