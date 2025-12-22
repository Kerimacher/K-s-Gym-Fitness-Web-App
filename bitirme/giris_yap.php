<?php

session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json; charset=utf-8'); 


require 'veritabani_baglantisi.php'; // Bu $conn değişkenini getirir

// Varsayılan yanıtı oluşturalım
$response = ['success' => false, 'message' => 'Bilinmeyen bir hata oluştu.'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $kullanici_adi_formdan = isset($_POST['loginKullaniciAdiPanel']) ? trim($_POST['loginKullaniciAdiPanel']) : '';
    $sifre_formdan = isset($_POST['loginSifrePanel']) ? $_POST['loginSifrePanel'] : '';

    
    if (empty($kullanici_adi_formdan) || empty($sifre_formdan)) {
        $response['message'] = 'Kullanıcı adı ve şifre boş bırakılamaz!';
    } else {
        
        try {
            
            $sql = "SELECT kullanici_id, kullanici_adi, sifre_hash, ad, soyad, e_posta FROM Kullanicilar WHERE kullanici_adi = :kullanici_adi_sorgu";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':kullanici_adi_sorgu', $kullanici_adi_formdan);
            $stmt->execute();

            $kullanici_db = $stmt->fetch(PDO::FETCH_ASSOC); 

            if ($kullanici_db) { 
                
                if (password_verify($sifre_formdan, $kullanici_db['sifre_hash'])) {
                    
                    $_SESSION['kullanici_id'] = $kullanici_db['kullanici_id']; 
                    $_SESSION['kullanici_adi'] = $kullanici_db['kullanici_adi']; 
                    $_SESSION['ad'] = $kullanici_db['ad'];
                    $_SESSION['soyad'] = $kullanici_db['soyad'];
                    $_SESSION['e_posta'] = $kullanici_db['e_posta']; 

                    $response['success'] = true;
                    $response['message'] = 'Giriş başarılı! Yönlendiriliyorsunuz...';
                } else {
                    
                    $response['message'] = 'Kullanıcı adı veya şifre hatalı.';
                }
            } else {
                
                $response['message'] = 'Kullanıcı adı veya şifre hatalı.'; 
            }

        } catch (PDOException $e) {
           
            $response['message'] = 'Veritabanı hatası. Lütfen daha sonra tekrar deneyin.'; 
            error_log("Giris Yap PDOException: " . $e->getMessage()); 
        }
    }
} else {
    
    $response['message'] = 'Geçersiz istek türü.';  //                  
}


echo json_encode($response);
?>
