</main>


<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- Hakkımızda -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-white">Mağazamız</h5>
                <p class="text-white">2010 yılından beri müşterilerimize kaliteli ürünler ve güvenilir alışveriş
                    deneyimi sunuyoruz.</p>
                <div class="mt-3">
                    <a href="#" class="text-white me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-white me-2"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

            <!-- Hızlı Linkler -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-white">Hızlı Linkler</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Ana Sayfa</a></li>
                    <li class="mb-2"><a href="products.php" class="text-white text-decoration-none">Ürünler</a></li>
                    <li class="mb-2"><a href="campaigns.php" class="text-white text-decoration-none">Kampanyalar</a>
                    </li>
                    <li class="mb-2"><a href="about.php" class="text-white text-decoration-none">Hakkımızda</a></li>
                    <li class="mb-2"><a href="contact.php" class="text-white text-decoration-none">İletişim</a></li>
                </ul>
            </div>

            <!-- Müşteri Hizmetleri -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-white">Müşteri Hizmetleri</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="faq.php" class="text-white text-decoration-none">Sıkça Sorulan Sorular</a>
                    </li>
                    <li class="mb-2"><a href="shipping.php" class="text-white text-decoration-none">Kargo ve
                            Teslimat</a></li>
                    <li class="mb-2"><a href="return-policy.php" class="text-white text-decoration-none">İade
                            Koşulları</a></li>
                    <li class="mb-2"><a href="privacy-policy.php" class="text-white text-decoration-none">Gizlilik
                            Politikası</a></li>
                    <li class="mb-2"><a href="terms.php" class="text-white text-decoration-none">Kullanım Koşulları</a>
                    </li>
                </ul>
            </div>

            <!-- İletişim -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3 text-white">İletişim</h5>
                <ul class="list-unstyled text-white">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Örnek Mah. Alışveriş Cad. No:123 İstanbul</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i>(0212) 123 45 67</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i>info@magazamiz.com</li>
                    <li class="mb-2"><i class="bi bi-clock me-2"></i>Pazartesi - Cuma: 09:00 - 18:00</li>
                </ul>
            </div>
        </div>

        <!-- Ödeme Yöntemleri -->
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-center mb-3 text-white">Ödeme Yöntemleri</h6>
                <div class="d-flex justify-content-center">
                    <img src="assets/img/payment/visa.png" alt="Visa" class="me-2" height="30">
                    <img src="assets/img/payment/mastercard.png" alt="Mastercard" class="me-2" height="30">
                    <img src="assets/img/payment/paypal.png" alt="PayPal" class="me-2" height="30">
                    <img src="assets/img/payment/bank-transfer.png" alt="Bank Transfer" height="30">
                </div>
            </div>
        </div>

        <hr class="my-4 text-white">

        <!-- Copyright -->
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="small text-white mb-0">&copy; <?php echo date('Y'); ?> Mağazamız. Tüm hakları saklıdır.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="small text-white mb-0">
                    <a href="privacy-policy.php" class="text-white text-decoration-none">Gizlilik Politikası</a> |
                    <a href="terms.php" class="text-white text-decoration-none">Kullanım Koşulları</a>
                </p>
            </div>
        </div>
    </div>
</footer>


<!-- Back to Top Button -->
<a href="#" class="btn btn-primary back-to-top" role="button" aria-label="Yukarı çık">
    <i class="bi bi-arrow-up"></i>
</a>

<!-- Newsletter Modal -->
<div class="modal fade" id="newsletterModal" tabindex="-1" aria-labelledby="newsletterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newsletterModalLabel">Bültenimize Abone Olun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yeni ürünlerden, indirimlerden ve kampanyalardan haberdar olmak için e-bültenimize abone olun.</p>
                <form id="newsletterForm">
                    <div class="mb-3">
                        <input type="email" class="form-control" id="newsletterEmail" placeholder="E-posta adresiniz"
                            required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Abone Ol</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JavaScript -->
<script src="assets/js/script.js"></script>

<script>
    // Back to top button
    window.onscroll = function () {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.querySelector('.back-to-top').style.display = "block";
        } else {
            document.querySelector('.back-to-top').style.display = "none";
        }
    };

    // Newsletter modal - göster (ilk ziyarette veya belirli bir süre sonra)
    document.addEventListener('DOMContentLoaded', function () {
        // Sayfaya ilk kez gelindiyse veya son gösterimden 7 gün geçtiyse
        if (!localStorage.getItem('newsletterModalShown') ||
            (Date.now() - localStorage.getItem('newsletterModalShown')) / (1000 * 60 * 60 * 24) > 7) {
            setTimeout(function () {
                var newsletterModal = new bootstrap.Modal(document.getElementById('newsletterModal'));
                newsletterModal.show();
                localStorage.setItem('newsletterModalShown', Date.now());
            }, 10000); // 10 saniye sonra göster
        }
    });

    // Newsletter form gönderimi
    document.getElementById('newsletterForm').addEventListener('submit', function (e) {
        e.preventDefault();
        // AJAX ile form gönderimi yapılabilir
        alert('Bültenimize abone olduğunuz için teşekkür ederiz!');
        bootstrap.Modal.getInstance(document.getElementById('newsletterModal')).hide();
    });
</script>
</body>

</html>