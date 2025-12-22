<?php
// JSON çıktısı için header
header('Content-Type: application/json; charset=utf-8');

// Geliştirme aşaması için hata raporlamasını açalım
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// veritabani_baglantisi.php dosyasını dahil et
// Bu dosyanın, bağlantı hatasını yakalamak için kendi try-catch bloğu olmalıdır.
require 'veritabani_baglantisi.php';

// Varsayılan yanıt dizisini hazırla
$response = ['success' => false, 'message' => ''];

// Sadece POST isteklerini kabul et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Güvenlik için trim() ile boşlukları temizle
    $kullanici_adi = isset($_POST['regKullaniciAdiPanel']) ? trim($_POST['regKullaniciAdiPanel']) : '';
    $ad = isset($_POST['regAdPanel']) ? trim($_POST['regAdPanel']) : '';
    $soyad = isset($_POST['regSoyadPanel']) ? trim($_POST['regSoyadPanel']) : '';
    $sifre = isset($_POST['regSifrePanel']) ? $_POST['regSifrePanel'] : ''; // Şifreyi trim etme
    $e_posta = isset($_POST['regEpostaPanel']) ? trim($_POST['regEpostaPanel']) : '';

    // Boş alan kontrolü
    if (empty($kullanici_adi) || empty($ad) || empty($soyad) || empty($sifre) || empty($e_posta)) {
        $response['message'] = 'Lütfen tüm alanları doldurun!';
        echo json_encode($response);
        exit; 
    }

    // E-posta format kontrolü
    if (!filter_var($e_posta, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Lütfen geçerli bir e-posta adresi girin!';
        echo json_encode($response);
        exit;
    }

    // Şifre güvenliği için hash'le
    $sifre_hash = password_hash($sifre, PASSWORD_DEFAULT);

    try {
        // SQL sorgusu - INSERT INTO
        // NOT: Veritabanında `kullanici_adi` ve `e_posta` sütunlarının UNIQUE (benzersiz) kısıtlaması olmalıdır.
        $sql = "INSERT INTO Kullanicilar (kullanici_adi, sifre_hash, ad, soyad, e_posta) VALUES (:kullanici_adi, :sifre_hash, :ad, :soyad, :e_posta)";
        
        // Sorguyu hazırla
        $stmt = $conn->prepare($sql);

        // Parametreleri bağla
        $stmt->bindParam(':kullanici_adi', $kullanici_adi);
        $stmt->bindParam(':sifre_hash', $sifre_hash);
        $stmt->bindParam(':ad', $ad);
        $stmt->bindParam(':soyad', $soyad);
        $stmt->bindParam(':e_posta', $e_posta);

        // Sorguyu çalıştır
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Kayıt başarıyla tamamlandı! Giriş yapabilirsiniz.';
        } else {
            $response['message'] = 'Kayıt sırasında bir sorun oluştu. Lütfen tekrar deneyin.';
        }

    } catch (PDOException $e) {
        // Hata durumunda, SQLSTATE kodunu kontrol ederek benzersizlik hatasını yakala.
        // '23000' SQLSTATE kodu, genel bir benzersiz kısıtlama (unique constraint) ihlali hatasıdır.
        if (isset($e->errorInfo[0]) && $e->errorInfo[0] === '23000') {
            $response['message'] = 'Bu kullanıcı adı veya e-posta adresi zaten kayıtlı. Lütfen farklı bilgiler deneyin.';
        } else {
            // Diğer veritabanı hatalarını genel bir mesajla gizle, detayı logla
            $response['message'] = 'Veritabanı hatası oluştu. Lütfen daha sonra tekrar deneyin.'; 
            // error_log() fonksiyonu hatayı sunucu log dosyasına yazar
            error_log("Veritabani Hatasi: " . $e->getMessage()); 
        }
    }

} else {
    // POST olmayan istekler için hata mesajı
    $response['message'] = 'Geçersiz istek türü.';
}

// JSON formatında yanıtı döndür
echo json_encode($response);

?>
