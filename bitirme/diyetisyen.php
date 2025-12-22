<?php

session_start();


if (!isset($_SESSION['e_posta'])) { 
    
    header('Location: BİTİRME.php'); 
    exit; 
}


?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diyetisyenlerimiz</title>
    <link rel="stylesheet" href="diyetisyen.css"> 
    <style>
       
        nav {
            display: flex;
            justify-content: center;
            padding: 20px 0;
            background-color: rgba(13, 13, 13, 0.65); 
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 60px;
            padding: 0;
            margin: 0;
            font-size: 20px;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 700;
        }
        nav ul li a:hover {
            color: #82caff;
        }
    </style>
</head>
<body>

    
    <header class="sayfa-basligi">
        <h1>Diyetisyenlerimizle Tanışın</h1>
        <p>Size en uygun diyetisyenimizden program alın.</p>
    </header>

    <main class="diyetisyen-kart-konteyner">
        <div class="diyetisyen-kart" id="diyetisyen-kart-1">
            <div class="kart-fotograf-cerceve">
                <img src="kerim.jpg" alt="Kerimcan Çakmakcı">
            </div>
            <div class="kart-icerik">
                <h3>Kerimcan Çakmakcı</h3>
                <p class="uzmanlik-alani">Kilo Verme, Vücut Geliştirme</p>
                <p class="kisa-hikaye">25 yaşında olan Kerim, vücut geliştirme alanında orta-üst seviyelerde dereceler elde etmiş bir sporcudur. 3 yıldır aktif olarak kişisel koçluk yapmakta, spor salonlarında özellikle kilo verme odaklı bireylerle çalışmaktadır. Disiplinli antrenman programları ve bireysel motivasyon konusundaki başarısıyla tanınır. Amacı, danışanlarının sadece fiziksel değil, mental olarak da güçlenmesini sağlamaktır.</p>
                <p class="calisma-saatleri-info">Çalışma Saatleri: Hafta içi 07:00-20:59, Hafta sonu çalışmıyor.</p>
                <button class="program-iste-btn" data-diyetisyen-id="1">Programı E-postana İste</button>
            </div>
        </div>

        <div class="diyetisyen-kart" id="diyetisyen-kart-2">
            <div class="kart-fotograf-cerceve">
                <img src=" " alt=" ">
            </div>
            <div class="kart-icerik">
                <h3>Anıl Gürel</h3>
                <p class="uzmanlik-alani">Sporcu Beslenmesi, Kas Gelişimi</p>
                <p class="kisa-hikaye">21 yaşındaki Anıl, kas gelişimi ve sporcu beslenmesi üzerine yoğunlaşmış genç bir antrenördür. Spor hayatına lise yıllarında başlamış, kısa sürede antrenman teknikleri ve beslenme bilimiyle ilgilenmeye başlamıştır. Özellikle doğal yollarla kas geliştirme ve makro takibi konusunda bilgi sahibidir. Hedefi, sporcu performansını en üst seviyeye taşımak isteyenlere bilinçli destek sunmak.</p>
                <p class="calisma-saatleri-info">Çalışma Saatleri: 09:00-17:30, Hafta sonu çalışmıyor.</p>
                <button class="program-iste-btn" data-diyetisyen-id="2">Programı E-postana İste</button>
            </div>
        </div>

        <div class="diyetisyen-kart" id="diyetisyen-kart-3"> 
            <div class="kart-fotograf-cerceve">
                <img src=" " alt=" ">
            </div>
            <div class="kart-icerik">
                <h3>Eren Aydoğan</h3>
                <p class="uzmanlik-alani">Futbolcu Beslenmesi Ve Antrenörlüğü</p>
                <p class="kisa-hikaye">22 yaşındaki Eren, futbolcu beslenmesi ve antrenörlük alanlarına ilgi duyan, sahada ve mutfakta aktif bir spor tutkunudur. Futbolcuların performansını artırmak için özel diyetler ve antrenman planları hazırlamaktadır. Genç sporcularla birebir çalışarak sahadaki gelişimlerini yakından takip eder. Hedefi, sporculara hem fiziksel hem zihinsel olarak güç katmak.</p>
                <p class="calisma-saatleri-info">Çalışma Saatleri: Hafta içi 07:00-11:30 ve 20:00-22:00, Hafta sonu çalışmıyor.</p>
                <button class="program-iste-btn" data-diyetisyen-id="3">Programı E-postana İste</button>
            </div>
        </div>
        
        <div class="diyetisyen-kart" id="diyetisyen-kart-4"> 
            <div class="kart-fotograf-cerceve">
                <img src=" " alt=" ">
            </div>
            <div class="kart-icerik">
                <h3>Ulukan Yıldırım</h3>
                <p class="uzmanlik-alani">Yoga ve Sağlıklı Beslenme</p>
                <p class="kisa-hikaye">20 yaşında olan Ulukan, spor ve sağlıklı yaşamı hayat tarzı haline getirmiş genç bir eğitmendir. Beden-zihin dengesine büyük önem verir; yoga seanslarında hem fiziksel rahatlama hem de ruhsal denge sağlamayı amaçlar. Sağlıklı beslenme ile içsel huzuru birleştirdiği özel programlar sunar. Sakin ve farkındalığı yüksek yaşam tarzıyla çevresine ilham olur.</p>
                <p class="calisma-saatleri-info">Çalışma Saatleri: Hafta içi 09.00-12.00 ve 15.00-18.00 Hafta sonu çalışmıyor.</p>
                <button class="program-iste-btn" data-diyetisyen-id="4">Programı E-postana İste</button> 
            </div>
        </div>
    </main>

    <script src="diyetisyen.js"></script> 
</body>
</html>