<?php

session_start();                      

$kullaniciGirisYapmis = isset($_SESSION['e_posta']); 
$kullaniciTamAdi = '';                

if ($kullaniciGirisYapmis) {          
    $kullaniciAdi    = $_SESSION['ad']    ?? ''; 
    $kullaniciSoyadi = $_SESSION['soyad'] ?? ''; 
    $kullaniciTamAdi = trim("$kullaniciAdi $kullaniciSoyadi"); 

    // Ad‑soyad boşsa e‑posta göster
    if (empty($kullaniciTamAdi) && !empty($_SESSION['e_posta'])) {
        $kullaniciTamAdi = $_SESSION['e_posta']; 
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">                                     
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>K's Gym</title>    
    <link rel="stylesheet" href="BİTİRME.css">                 
</head>
<body>
    <?php if ($kullaniciGirisYapmis && $kullaniciTamAdi): ?>   
        <div class="kullanici-selamlama-anasayfa">
            Merhaba, <?php echo htmlspecialchars($kullaniciTamAdi); ?>! 
        </div>
    <?php endif; ?>

   
    <header>
        <nav>
            <ul>
                <li><a href="Hikayemiz.html">Hikayemiz</a></li>
                <li><a href="FitTarifler.html">Fit Tarifler</a></li>
                <li><a href="Kalori_Ihtiyaci.html">Kalori İhtiyacını Hesapla</a></li>
                <li><a href="diyetisyen.php">Diyetisyen Randevu Sistemi</a></li>
                <li><a href="salon_üyelik.html">Üyelik</a></li>
            </ul>
        </nav>
    </header>

    
    <main>
        <div class="container">
            <div class="left">
                <img src="logo.png" alt="K's Gym Logo">  
                <h1>K's GYM <br> SOLID SPORTS</h1>
                <p>Zirve Yolundaki Destekçiniz</p>
            </div>
            <div class="right">
                <h2>Her Zaman <br> Önde Olun</h2>
                <?php if (!$kullaniciGirisYapmis): ?>        <!-- Henüz giriş yoksa -->
                    <button id="anaSayfaGirisBtn" class="giris-butonu">Giriş Yap</button>
                    <br><br>
                    <button id="anaSayfaKayitBtn" class="kayit-butonu">Kayıt Ol</button>
                <?php else: ?>                               <!-- Giriş yapılmışsa -->
                    <a href="cikis_yap.php" class="cikis-butonu-anasayfa kayit-butonu">Çıkış Yap</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    
    <div id="girisPaneli" class="panel-overlay">
        <div class="panel-pencere">
            <span class="panel-kapat-btn" data-target-panel="girisPaneli">&times;</span> 
            <h2>Giriş Yap</h2>
            <form id="loginFormPanel">
                <div>
                    <label for="loginKullaniciAdiPanel">Kullanıcı Adı:</label>
                    <input type="text" id="loginKullaniciAdiPanel" name="loginKullaniciAdiPanel" required>
                </div>
                <div>
                    <label for="loginSifrePanel">Şifre:</label>
                    <input type="password" id="loginSifrePanel" name="loginSifrePanel" required>
                </div>
                <button type="submit" class="panel-form-btn">Giriş Yap</button>
            </form>
        </div>
    </div>

    
    <div id="kayitPaneli" class="panel-overlay">
        <div class="panel-pencere">
            <span class="panel-kapat-btn" data-target-panel="kayitPaneli">&times;</span>
            <h2>Kayıt Ol</h2>
            <form id="registerFormPanel">
                <div>
                    <label for="regKullaniciAdiPanel">Kullanıcı Adı:</label>
                    <input type="text" id="regKullaniciAdiPanel" name="regKullaniciAdiPanel" required>
                </div>
                <div>
                    <label for="regAdPanel">Ad:</label>
                    <input type="text" id="regAdPanel" name="regAdPanel" required>
                </div>
                <div>
                    <label for="regSoyadPanel">Soyad:</label>
                    <input type="text" id="regSoyadPanel" name="regSoyadPanel" required>
                </div>
                <div>
                    <label for="regSifrePanel">Şifre:</label>
                    <input type="password" id="regSifrePanel" name="regSifrePanel" required>
                </div>
                <div>
                    <label for="regEpostaPanel">E-Posta:</label>
                    <input type="email" id="regEpostaPanel" name="regEpostaPanel" required>
                </div>
                <button type="submit" class="panel-form-btn">Kayıt Ol</button>
            </form>
        </div>
    </div>

    <script src="BİTİRME.js"></script> 
</body>
</html>
