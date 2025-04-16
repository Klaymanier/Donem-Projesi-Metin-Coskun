<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-column">
                <div class="footer-logo">
                    <div class="footer-logo-icon"><i class="fas fa-tshirt"></i></div>
                    ModaVista
                </div>
                <p class="footer-description">Modern ve yenilikçi tasarımlarla tarzınızı yeniden keşfedin. Sürdürülebilir moda anlayışımızla çevreye duyarlı alışveriş yapın.</p>
                <div class="footer-social">
                    <?php
                    // Sosyal medya linklerini tanımla (eğer tanımlanmamışsa)
                    if (!isset($socialLinks)) {
                        $socialLinks = [
                            'facebook' => '#',
                            'twitter' => '#',
                            'instagram' => '#',
                            'linkedin' => '#',
                            'discord' => '#'
                        ];
                    }
                    
                    foreach($socialLinks as $platform => $link): ?>
                        <a href="<?php echo $link; ?>" class="footer-social-link" target="_blank" rel="noopener">
                            <i class="fab fa-<?php echo $platform; ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">Kategoriler</h3>
                <ul class="footer-links">
                    <?php
                    // Kategorileri veritabanından çek veya mevcut değişkenden al
                    if (!isset($menuKategoriler) || empty($menuKategoriler)) {
                        try {
                            $stmt = $db->prepare("SELECT * FROM kategoriler WHERE aktif = 1 ORDER BY kategori_adi LIMIT 6");
                            $stmt->execute();
                            $footerCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            // Hata durumunda boş dizi kullan
                            $footerCategories = [];
                        }
                    } else {
                        // Eğer menü kategorileri mevcutsa, onları kullan (sayfa sınırı uygula)
                        $footerCategories = array_slice($menuKategoriler, 0, 6);
                    }
                    
                    foreach($footerCategories as $category): ?>
                        <li>
                            <a href="products.php?kategori=<?php echo $category['kategori_slug']; ?>">
                                <?php echo $category['kategori_adi']; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">Bilgi</h3>
                <ul class="footer-links">
                    <li><a href="about.php">Hakkımızda</a></li>
                    <li><a href="shipping.php">Teslimat Bilgileri</a></li>
                    <li><a href="privacy.php">Gizlilik Politikası</a></li>
                    <li><a href="terms.php">Kullanım Koşulları</a></li>
                    <li><a href="returns.php">İade Politikası</a></li>
                    <li><a href="faq.php">Sıkça Sorulan Sorular</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3 class="footer-title">İletişim</h3>
                <div class="footer-contact">
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt footer-contact-icon"></i>
                        <div>Yozgat No:123 Sorgun/Yozgat</div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone footer-contact-icon"></i>
                        <div>+90 212 123 45 67</div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope footer-contact-icon"></i>
                        <div>info@modavista.com</div>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-clock footer-contact-icon"></i>
                        <div>Pazartesi-Cumartesi: 09:00 - 20:00</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> ModaVista. Tüm hakları saklıdır.</p>
            <div class="footer-bottom-links">
                <a href="privacy.php" class="footer-bottom-link">Gizlilik</a>
                <a href="terms.php" class="footer-bottom-link">Şartlar</a>
                <a href="cookies.php" class="footer-bottom-link">Çerezler</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-chevron-up"></i>
</a>

<!-- JavaScript -->
<script src="assets/js/app.js"></script>
</body>
</html>