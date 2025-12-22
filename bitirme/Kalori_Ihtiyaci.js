document.addEventListener('DOMContentLoaded', function () {
    const kaloriForm = document.getElementById('kaloriForm');
    const sonucAlani = document.getElementById('sonucAlani');

    if (kaloriForm) {
        kaloriForm.addEventListener('submit', function (event) {
            event.preventDefault(); 

            
            const cinsiyetElement = document.querySelector('input[name="cinsiyet"]:checked');
            if (!cinsiyetElement) {
                alert("Lütfen cinsiyetinizi seçin.");
                return; 
            }
            const cinsiyet = cinsiyetElement.value;
            
            const yas = parseFloat(document.getElementById('yas').value);
            const boy = parseFloat(document.getElementById('boy').value);
            const kilo = parseFloat(document.getElementById('kilo').value);
            const aktiviteKatsayisi = parseFloat(document.getElementById('aktivite').value);
            const hedef = document.getElementById('hedef').value;

           
            if (isNaN(yas) || isNaN(boy) || isNaN(kilo) || isNaN(aktiviteKatsayisi) || !hedef) {
                alert("Lütfen tüm alanları doğru bir şekilde doldurun.");
                return;
            }

            // 1. Bazal Metabolizma Hızı (BMH) Hesaplama (Mifflin-St Jeor) 
            let bmh;
            if (cinsiyet === 'erkek') {
                bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) + 5;
            } else { // kadın
                bmh = (10 * kilo) + (6.25 * boy) - (5 * yas) - 161;
            }
            bmh = Math.round(bmh);

            // Günlük Toplam Kalori İhtiyacı (TDEE)
            let tdee = Math.round(bmh * aktiviteKatsayisi);

            // Hedefe Göre Kalori Ayarlaması
            let hedefKalori = tdee;
            let ekstraNot = "";

            switch (hedef) {
                case 'kilo_vermek_normal':
                    hedefKalori -= 500; 
                    ekstraNot = "Sağlıklı kilo kaybı için günlük ~500 kalori açığı hedeflenmiştir.";
                    break;
                case 'kilo_vermek_hizli':
                    hedefKalori -= 800; 
                    ekstraNot = "Hızlı kilo kaybı için günlük ~800 kalori açığı hedeflenmiştir. Bu yaklaşımı bir sağlık uzmanına danışarak uygulamanız önerilir.";
                    break;
                case 'kilo_almak_normal':
                    hedefKalori += 400; 
                    ekstraNot = "Sağlıklı kilo alımı için günlük ~400 kalori fazlası hedeflenmiştir.";
                    break;
                case 'kilo_almak_hizli':
                    hedefKalori += 700; 
                    ekstraNot = "Hızlı kilo alımı için günlük ~700 kalori fazlası hedeflenmiştir.";
                    break;
                case 'kilo_korumak':
                    ekstraNot = "Mevcut kilonuzu korumanız için gereken günlük kalori miktarıdır.";
                    break;
            }
            
            hedefKalori = Math.max(1200, Math.round(hedefKalori)); // Minimum 1200 kalori sınırı 
             if (hedef === 'kilo_vermek_hizli' && hedefKalori === 1200 && (tdee - 800 < 1200) ) {
                ekstraNot += " Minimum kalori sınırına (1200 kcal) ulaşıldı.";
            }


            
            
            let proteinHedefOrani = 0.30; 
            let yagHedefOrani = 0.25;    

            if (hedef.includes('kilo_vermek')) {
                proteinHedefOrani = 0.35; 
                yagHedefOrani = 0.25;
            } else if (hedef.includes('kilo_almak')) {
                proteinHedefOrani = 0.25;
                yagHedefOrani = 0.25; 
            }

            let proteinKalori = Math.round(hedefKalori * proteinHedefOrani);
            let proteinGram = Math.round(proteinKalori / 4);
            
            let yagKalori = Math.round(hedefKalori * yagHedefOrani);
            let yagGram = Math.round(yagKalori / 9);

            let karbonhidratKalori = hedefKalori - proteinKalori - yagKalori;
            let karbonhidratGram = Math.round(karbonhidratKalori / 4);

            
            let proteinYuzde = Math.round((proteinKalori / hedefKalori) * 100);
            let yagYuzde = Math.round((yagKalori / hedefKalori) * 100);
            
            let karbonhidratYuzde = 100 - proteinYuzde - yagYuzde; 
            
            document.getElementById('sonucKalori').textContent = hedefKalori;
            document.getElementById('sonucProtein').textContent = proteinGram;
            document.getElementById('sonucProteinYuzde').textContent = proteinYuzde;
            document.getElementById('sonucKarbonhidrat').textContent = karbonhidratGram;
            document.getElementById('sonucKarbonhidratYuzde').textContent = karbonhidratYuzde;
            document.getElementById('sonucYag').textContent = yagGram;
            document.getElementById('sonucYagYuzde').textContent = yagYuzde;
            document.getElementById('ekstraNotlar').textContent = ekstraNot;

            sonucAlani.style.display = 'block'; 
            sonucAlani.scrollIntoView({ behavior: 'smooth' }); 
        });
    }
});