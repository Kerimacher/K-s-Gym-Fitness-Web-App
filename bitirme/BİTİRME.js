// DOM tamamen yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {

    const anaGirisButonu = document.getElementById('anaSayfaGirisBtn');
    const anaKayitButonu = document.getElementById('anaSayfaKayitBtn');

    const girisPaneli = document.getElementById('girisPaneli');
    const kayitPaneli = document.getElementById('kayitPaneli');

    const panelKapatButonlari = document.querySelectorAll('.panel-kapat-btn');

    // Paneli gösteren yardımcı fonksiyon
    function paneliGoster(panelElementi) {
        if (panelElementi) {
            panelElementi.style.display = 'flex';
        }
    }

    // Paneli gizleyen yardımcı fonksiyon
    function paneliGizle(panelElementi) {
        if (panelElementi) {
            panelElementi.style.display = 'none';
        }
    }

    // Giriş butonuna tıklama olayı
    if (anaGirisButonu) {
        anaGirisButonu.addEventListener('click', function() {
            paneliGoster(girisPaneli);
        });
    }

    // Kayıt butonuna tıklama olayı
    if (anaKayitButonu) {
        anaKayitButonu.addEventListener('click', function() {
            paneliGoster(kayitPaneli);
        });
    }

    // Panel kapatma butonları için olay dinleyiciler
    panelKapatButonlari.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const panelToClose = this.closest('.panel-overlay');
            paneliGizle(panelToClose);
        });
    });

    // Panel dışına tıklama ile kapatma
    window.addEventListener('click', function(event) {
        if (girisPaneli && event.target === girisPaneli) {
            paneliGizle(girisPaneli);
        }
        if (kayitPaneli && event.target === kayitPaneli) {
            paneliGizle(kayitPaneli);
        }
    });


    // Kayıt Formu İşleme
    const registerForm = document.getElementById('registerFormPanel');

    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Formun varsayılan gönderimini engelle
            const formData = new FormData(registerForm); // Form verilerini al

            fetch('kayit_ol.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // HTTP yanıtının başarılı olup olmadığını kontrol et (200-299 arası durum kodları)
                if (!response.ok) {
                    // Eğer yanıt başarılı değilse, hatanın nedenini görmek için yanıtı metin olarak oku
                    return response.text().then(text => {
                        console.error("Sunucu Hatası (Kayıt) - HTTP Durum Kodu:", response.status, "Yanıt:", text);
                        // Kendi özel hatamızı fırlat, böylece .catch bloğunda yakalanırız
                        throw new Error(`Sunucu hatası: ${response.status} - ${text || 'Boş yanıt'}`);
                    });
                }
                // Yanıt başarılı ise, JSON olarak ayrıştırmaya çalış
                // Eğer sunucu geçerli JSON göndermezse, bu adımda hata fırlatılacaktır.
                return response.json();
            })
            .then(data => {
                // PHP'den gelen başarılı JSON yanıtını işle
                if (data && data.success) {
                    alert('Kayıt Başarılı: ' + data.message);
                    registerForm.reset(); // Formu temizle
                    paneliGizle(kayitPaneli); // Kayıt panelini gizle
                } else if (data && data.message) {
                    // Sunucu başarılı (200 OK) bir yanıt dönmüş ancak işlem başarısız olmuş (örneğin, kullanıcı zaten var)
                    alert('Kayıt Bilgisi: ' + data.message);
                } else {
                    // Beklenmeyen bir JSON formatı veya boş data durumu
                    alert('Kayıt sırasında bilinmeyen bir sorun oluştu veya yanıt eksik.');
                }
            })
            .catch(error => {
                // Fetch işlemi sırasında oluşan ağ hataları veya response.json() ayrıştırma hataları buraya düşer.
                console.error('Kayıt Fetch Hatası:', error);
                alert('Kayıt işlemi sırasında bir sorun oluştu: ' + error.message);
            });
        });
    }

    

    // Giriş Formu İşleme (Kayıt formu ile aynı mantık)
    const loginForm = document.getElementById('loginFormPanel');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(loginForm);

            fetch('giris_yap.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error("Sunucu Hatası (Giriş) - HTTP Durum Kodu:", response.status, "Yanıt:", text);
                        throw new Error(`Sunucu hatası: ${response.status} - ${text || 'Boş yanıt'}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    loginForm.reset();
                    paneliGizle(girisPaneli);
                    // Giriş başarılı olduğunda sayfayı yenile veya yönlendir
                    window.location.reload();
                } else if (data && data.message) {
                    alert('Giriş Bilgisi: ' + data.message);
                } else {
                    alert('Giriş sırasında bilinmeyen bir sorun oluştu veya yanıt eksik.');
                }
            })
            .catch(error => {
                console.error('Giriş Fetch Hatası:', error);
                alert('Giriş işlemi sırasında bir sorun oluştu: ' + error.message);
            });
        });
    }

});