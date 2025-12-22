document.addEventListener('DOMContentLoaded', () => {
    // HTML elemanlarını seç
    const filtreButonlari = document.querySelectorAll('#kategoriButonlari .filtre-btn');
    const tarifKartlari = document.querySelectorAll('#tarifListesi .tarif-karti');
    const aramaKutusu = document.getElementById('aramaKutusu');
    const sonucYokMesaji = document.getElementById('sonucYokMesaji ');

    // Ana filtreleme fonksiyonu
    function filtreleVeGoster() {
        // Aktif kategori butonunu bul ve data-kategori değerini al
        const aktifKategoriButonu = document.querySelector('#kategoriButonlari .filtre-btn.aktif');
        // Eğer aktif buton bulunamazsa veya bir şekilde null ise, 'tumu' varsay
        const secilenKategori = aktifKategoriButonu ? aktifKategoriButonu.getAttribute('data-kategori') : 'tumu';
        
        // Arama kutusundaki metni al, küçük harfe çevir ve baştaki/sondaki boşlukları sil
        const aramaTerimi = aramaKutusu.value.toLowerCase().trim();
        let gorunenKartSayisi = 0;

        // Her bir tarif kartını kontrol et
        tarifKartlari.forEach(kart => {
            const kartKategorisi = kart.getAttribute('data-kategori');
            const kartBasligi = kart.querySelector('.tarif-baslik') ? kart.querySelector('.tarif-baslik').textContent.toLowerCase() : '';
            const kartAciklamasi = kart.querySelector('.tarif-aciklama') ? kart.querySelector('.tarif-aciklama').textContent.toLowerCase() : '';
            
            // Arama için kartın başlık ve açıklamasını birleştir
            const aranacakMetin = kartBasligi + ' ' + kartAciklamasi;

            // Kategori eşleşiyor mu? (Seçilen kategori "tumu" ise veya kartın kategorisi seçilenle aynı ise true)
            const kategoriEslesmesi = (secilenKategori === 'tumu' || secilenKategori === kartKategorisi);
            // Arama terimi eşleşiyor mu? (Arama terimi boşsa veya kartın metninde geçiyorsa true)
            const aramaEslesmesi = (aramaTerimi === '' || aranacakMetin.includes(aramaTerimi));

            // Hem kategori hem de arama terimi eşleşiyorsa kartı göster, değilse gizle
            if (kategoriEslesmesi && aramaEslesmesi) {
                kart.classList.remove('gizli');
                gorunenKartSayisi++;
            } else {
                kart.classList.add('gizli');
            }
        });

        // Eğer hiç kart görünmüyorsa "Sonuç Yok" mesajını göster, aksi halde gizle
        if (gorunenKartSayisi === 0) {
            sonucYokMesaji.classList.remove('gizli');
        } else {
            sonucYokMesaji.classList.add('gizli');
        }
    }

    // Kategori butonlarına tıklama olayı ekle
    filtreButonlari.forEach(buton => {
        buton.addEventListener('click', () => {
            // Diğer butonlardan 'aktif' class'ını kaldır
            filtreButonlari.forEach(btn => btn.classList.remove('aktif'));
            // Tıklanan butona 'aktif' class'ını ekle
            buton.classList.add('aktif');
            // Filtrelemeyi güncelle
            filtreleVeGoster();
        });
    });

    // Arama kutusuna her yazı yazıldığında filtrelemeyi güncelle
    if(aramaKutusu){ // Eğer arama kutusu varsa (null değilse)
        aramaKutusu.addEventListener('input', filtreleVeGoster);
    }


    // Sayfa ilk yüklendiğinde filtrelemeyi çalıştır (Tümü kategorisi varsayılan olarak aktif)
    // Bu çağrı, DOM'un tamamen hazır olduğundan emin olmak için en sonda veya DOMContentLoaded içinde olmalı
    filtreleVeGoster(); 
});