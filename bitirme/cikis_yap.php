// cikis_yap.php
<?php
session_start();                  // Mevcut oturumu başlat / eriş
session_unset();                  // Tüm oturum değişkenlerini temizle
session_destroy();                // Oturumu tamamen sonlandır
header('Location: BİTİRME.php');  // Ana sayfaya yönlendir
exit;                             // Kodun çalışmasını burada durdur
?>
