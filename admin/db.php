<?php
$host = 'localhost';        // หรือ 127.0.0.1
$db   = 'mango_information';      // ชื่อฐานข้อมูล
$user = 'root';             // ชื่อผู้ใช้ฐานข้อมูล
$pass = '';                 // รหัสผ่าน (ถ้ามี)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // แจ้ง error แบบ exception
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // ดึงข้อมูลแบบ array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // ใช้ prepared statement จริง
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage());
}
?>
