<?php

header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı Bağlantı Bilgileri
$serverName = "KERIMROG\SQLEXPRESS"; 
$databaseName = "K's_Gym_VeriTabanı";

$php_username = null;
$php_password = null;

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$databaseName", $php_username, $php_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Bağlantı hatası durumunda JSON formatında bir hata mesajı döndür.
    // Bu, istemci tarafındaki (JavaScript) kodunun hatayı daha kolay işlemesini sağlar.
    $response = [
        'success' => false,
        'message' => 'Veritabanı bağlantı hatası: Lütfen daha sonra tekrar deneyin.'
    ];
    // Geliştirme aşamasında daha detaylı hata mesajı logla
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    echo json_encode($response);
    
    // Scriptin çalışmasını durdur
   die();
}
?>
