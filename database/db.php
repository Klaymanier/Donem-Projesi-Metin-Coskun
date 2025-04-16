<?php
/**
 * DB.php - ModaVista Veritabanı Bağlantı Dosyası
 * 
 * PDO ile veritabanı bağlantısı oluşturur
 */

// Config dosyasını kontrol et
if (!defined('DB_HOST')) {
    require_once 'config.php';
}

try {
    // PDO bağlantısı oluştur
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $db = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    
    // Veritabanı sınıfı
    class Database {
        private $db;
        
        public function __construct($db) {
            $this->db = $db;
        }
        
        /**
         * Tek bir satır döndüren sorgu
         */
        public function getRow($sql, $params = []) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        }
        
        /**
         * Birden fazla satır döndüren sorgu
         */
        public function getRows($sql, $params = []) {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
        
        /**
         * INSERT, UPDATE, DELETE sorgusu
         */
        public function execute($sql, $params = []) {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        }
        
        /**
         * Son eklenen ID'yi döndür
         */
        public function lastInsertId() {
            return $this->db->lastInsertId();
        }
        
        /**
         * Transaction başlat
         */
        public function beginTransaction() {
            return $this->db->beginTransaction();
        }
        
        /**
         * Transaction onaylat
         */
        public function commit() {
            return $this->db->commit();
        }
        
        /**
         * Transaction geri al
         */
        public function rollBack() {
            return $this->db->rollBack();
        }
        
        /**
         * Girilen kelimeye göre arama yapar
         */
        public function search($table, $fields, $keyword, $additionalConditions = '', $params = []) {
            $searchFields = [];
            $searchParams = $params;
            
            foreach ($fields as $field) {
                $searchFields[] = "$field LIKE :keyword_$field";
                $searchParams["keyword_$field"] = "%$keyword%";
            }
            
            $searchCondition = implode(' OR ', $searchFields);
            $sql = "SELECT * FROM $table WHERE ($searchCondition)";
            
            if (!empty($additionalConditions)) {
                $sql .= " AND $additionalConditions";
            }
            
            return $this->getRows($sql, $searchParams);
        }
        
        /**
         * Kategori ürünlerini getirir
         */
        public function getCategoryProducts($categoryId, $limit = null, $offset = null) {
            $sql = "
                SELECT u.*, k.kategori_adi, k.kategori_slug,
                (SELECT gorsel_yolu FROM urun_gorselleri WHERE urun_id = u.urun_id AND ana_gorsel = 1 LIMIT 1) AS gorsel_yolu
                FROM urunler u 
                JOIN kategoriler k ON u.kategori_id = k.kategori_id
                WHERE u.kategori_id = :kategori_id AND u.aktif = 1
                ORDER BY u.created_at DESC
            ";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':kategori_id', $categoryId, PDO::PARAM_INT);
            
            if ($limit !== null) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        }
        
        /**
         * Ürün detaylarını getirir
         */
        public function getProductDetails($productId) {
            $sql = "
                SELECT u.*, k.kategori_adi, k.kategori_slug
                FROM urunler u 
                JOIN kategoriler k ON u.kategori_id = k.kategori_id
                WHERE u.urun_id = :urun_id AND u.aktif = 1
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':urun_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            $product = $stmt->fetch();
            
            if ($product) {
                // Ürün görsellerini getir
                $sql = "
                    SELECT * FROM urun_gorselleri
                    WHERE urun_id = :urun_id
                    ORDER BY ana_gorsel DESC, sira ASC
                ";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':urun_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                
                $product['gorseller'] = $stmt->fetchAll();
                
                // Ürün renklerini getir
                $sql = "
                    SELECT r.renk_id, r.renk_adi, r.renk_kodu
                    FROM urun_varyasyonlari uv
                    JOIN renkler r ON uv.renk_id = r.renk_id
                    WHERE uv.urun_id = :urun_id
                    GROUP BY r.renk_id
                ";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':urun_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                
                $product['renkler'] = $stmt->fetchAll();
                
                // Ürün bedenlerini getir
                $sql = "
                    SELECT b.beden_id, b.beden_adi
                    FROM urun_varyasyonlari uv
                    JOIN bedenler b ON uv.beden_id = b.beden_id
                    WHERE uv.urun_id = :urun_id
                    GROUP BY b.beden_id
                ";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':urun_id', $productId, PDO::PARAM_INT);
                $stmt->execute();
                
                $product['bedenler'] = $stmt->fetchAll();
            }
            
            return $product;
        }
    }
    
    // Database sınıfını başlat
    $database = new Database($db);
    
} catch (PDOException $e) {
    // Hata durumunda
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}