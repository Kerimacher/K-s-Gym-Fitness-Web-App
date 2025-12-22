<?php

// 1. Oturum yönetimi, hata ayıklama ve başlık ayarları
session_start();
header('Content-Type: application/json; charset=utf-8');

// Hata ayıklama ayarları - Canlı sunucuda kapatılmalıdır.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. PHPMailer ve Veritabanı Bağlantısı dahil etme
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer_src/Exception.php';
require 'PHPMailer_src/PHPMailer.php';
require 'PHPMailer_src/SMTP.php';

require 'veritabani_baglantisi.php';

// 3. Varsayılan yanıt ve oturum kontrolü
$response = ['success' => false, 'message' => 'İstek işlenemedi. (Sunucu)'];

// Kullanıcının oturum açıp açmadığını kontrol etme
if (!isset($_SESSION['e_posta']) || empty($_SESSION['e_posta'])) {
    $response['message'] = 'Bu işlemi yapmak için giriş yapmalısınız.';
    echo json_encode($response);
    exit;
}
$kullanici_eposta = $_SESSION['e_posta'];
$kullanici_adi_session = $_SESSION['ad'] ?? ''; 
$kullanici_soyadi_session = $_SESSION['soyad'] ?? '';
$kullanici_tam_adi = trim($kullanici_adi_session . " " . $kullanici_soyadi_session);

if (empty($kullanici_tam_adi)) {
    $kullanici_tam_adi = 'Değerli Üyemiz';
}

// 4. Sadece POST isteklerini işleme
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // POST verisi kontrolü
    if (!isset($_POST['diyetisyen_id']) || !is_numeric($_POST['diyetisyen_id']) || intval($_POST['diyetisyen_id']) <= 0) {
        $response['message'] = 'Diyetisyen seçimi yapılamadı (Geçersiz ID).';
        echo json_encode($response);
        exit;
    }
    $diyetisyen_id = intval($_POST['diyetisyen_id']);

    try {
        // 5. Diyetisyen bilgilerini çekme
        $sql_diyetisyen = "SELECT ad_soyad, program_pdf_yolu, calisma_saatleri, aktif_mi FROM diyetisyenler WHERE diyetisyen_id = :diyetisyen_id";
        $stmt_diyetisyen = $conn->prepare($sql_diyetisyen);
        $stmt_diyetisyen->bindParam(':diyetisyen_id', $diyetisyen_id, PDO::PARAM_INT);
        $stmt_diyetisyen->execute();
        $diyetisyen = $stmt_diyetisyen->fetch(PDO::FETCH_ASSOC);

        if (!$diyetisyen) {
            $response['message'] = 'Seçilen diyetisyen sistemde bulunamadı.';
            echo json_encode($response);
            exit;
        }
        if (empty($diyetisyen['aktif_mi']) || !$diyetisyen['aktif_mi']) {
            $response['message'] = 'Bu diyetisyen şu anda aktif olarak hizmet vermemektedir.';
            echo json_encode($response);
            exit;
        }

        $program_pdf_yolu_db = trim($diyetisyen['program_pdf_yolu']);
        $diyetisyen_adi_soyadi = $diyetisyen['ad_soyad'];
        $calisma_saatleri_json = $diyetisyen['calisma_saatleri'];

        if (empty($program_pdf_yolu_db) || empty($calisma_saatleri_json)) {
            $response['message'] = 'Diyetisyene ait program veya çalışma saati bilgisi eksik.';
            echo json_encode($response);
            exit;
        }

        $calisma_saatleri_dizi = json_decode($calisma_saatleri_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response['message'] = 'Diyetisyen çalışma saatleri formatı hatalı.';
            echo json_encode($response);
            exit;
        }

        // 6. Müsaitlik Kontrolü
        date_default_timezone_set('Europe/Istanbul');
        $gun_isimleri_php = ["Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi"];
        $bugun_gun_index_php = intval(date('w'));
        $bugun_gun_adi = $gun_isimleri_php[$bugun_gun_index_php];
        $su_an_timestamp = strtotime(date('H:i'));

        $diyetisyen_musait_mi = false;
        $aktif_gun_calisma_detayi = $bugun_gun_adi . " günü için tanımlı bir çalışma aralığı bulunamadı.";
        
        if (isset($calisma_saatleri_dizi[$bugun_gun_adi]) && $calisma_saatleri_dizi[$bugun_gun_adi]['aktif'] === true) {
            foreach ($calisma_saatleri_dizi[$bugun_gun_adi]['bloklar'] as $blok) {
                if (isset($blok['baslangic']) && isset($blok['bitis'])) {
                    $baslangic_saat_str = $blok['baslangic'];
                    $bitis_saat_str = $blok['bitis'];
                    $baslangic_timestamp = strtotime($baslangic_saat_str);
                    $bitis_timestamp = strtotime($bitis_saat_str);

                    if ($bitis_timestamp < $baslangic_timestamp) {
                        if ($su_an_timestamp >= $baslangic_timestamp || $su_an_timestamp < $bitis_timestamp) {
                            $diyetisyen_musait_mi = true;
                            break;
                        }
                    } else {
                        if ($su_an_timestamp >= $baslangic_timestamp && $su_an_timestamp < $bitis_timestamp) {
                            $diyetisyen_musait_mi = true;
                            break;
                        }
                    }
                }
            }
            if ($diyetisyen_musait_mi) {
                $aktif_gun_calisma_detayi = "Müsait.";
            } else {
                 $aktif_gun_calisma_detayi = "Şu an mesai saatleri dışı.";
            }
        }
        
        if (!$diyetisyen_musait_mi) {
            $response['message'] = "Diyetisyen {$diyetisyen_adi_soyadi} şu an müsait değil. " . $aktif_gun_calisma_detayi;
            echo json_encode($response);
            exit;
        }
        
        // 7. DÜZELTİLEN KISIM: Dosya Yolu Kontrolü
        // "/" işaretini silen kod kaldırıldı, sadece ".." (bir üst dizin) engellendi.
        $pdf_server_yolu = $_SERVER['DOCUMENT_ROOT'] . '/bitirme/' . str_replace(['..'], '', $program_pdf_yolu_db);

        if (!file_exists($pdf_server_yolu) || !is_readable($pdf_server_yolu)) {
            // Hata ayıklama için loga tam yolu yazdırıyoruz (kullanıcıya göstermiyoruz)
            error_log("PDF Dosyası Bulunamadı. Aranan Yol: " . $pdf_server_yolu);
            $response['message'] = 'Diyetisyene ait program dosyası sunucuda bulunamadı.';
            echo json_encode($response);
            exit;
        }
        
        // 8. E-posta Gönderme
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cakmakcikerimcan77@gmail.com'; // Mail adresin kalabilir
            $mail->Password   = 'BURAYA_GMAIL_UYGULAMA_SIFRENIZI_YAZIN'; // DİKKAT: GitHub'a yüklemeden önce burayı böyle bırak!
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('cakmakcikerimcan77@gmail.com', "K's GYM Bildirim");
            $mail->addAddress($kullanici_eposta, $kullanici_tam_adi);

            $mail->addAttachment($pdf_server_yolu);

            $mail->isHTML(true);
            $mail->Subject = "Diyet ve Antrenman Programınız - Dyt. " . $diyetisyen_adi_soyadi;
            $mail->Body    = "Merhaba Sayın " . htmlspecialchars($kullanici_tam_adi) . ",<br><br>Talep ettiğiniz program ektedir.<br><br>K's GYM Ekibi";
            $mail->AltBody = strip_tags(str_replace("<br>", "\n", $mail->Body));

            $mail->send();
            $response['success'] = true;
            $response['message'] = 'Programınız e-posta adresinize başarıyla gönderildi!';

        } catch (Exception $e) {
            $response['message'] = "E-posta gönderimi sırasında bir sorun oluştu.";
            error_log("Mail Hatası: " . $mail->ErrorInfo);
        }
    } catch (PDOException $e) {
        $response['message'] = 'Veritabanı hatası oluştu.';
        error_log("DB Hatası: " . $e->getMessage());
    } catch (Exception $ex) {
        $response['message'] = 'Beklenmedik bir hata oluştu.';
    }
} else {
    $response['message'] = 'Geçersiz istek.';
}

echo json_encode($response);
?>