<?php
/**
 * اتصال آمن بقاعدة البيانات باستخدام PDO
 * متوافق مع Docker (يستخدم خدمة 'db' الداخلية)
 */

// منع الوصول المباشر لهذا الملف
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    die('Direct access not allowed.');
}

try {
    // قراءة إعدادات الاتصال من متغيرات البيئة (محددة في docker-compose.yml)
    $host = getenv('DB_HOST') ?: 'db';           // اسم خدمة MySQL في Docker
    $dbname = getenv('DB_NAME') ?: 'newsdb';     // اسم قاعدة البيانات
    $user = getenv('DB_USER') ?: 'user';         // اسم المستخدم
    $pass = getenv('DB_PASS') ?: 'securepass123'; // كلمة المرور

    // إنشاء اتصال PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

} catch (PDOException $e) {
    // سجّل الخطأ في السجل (للمراجعة لاحقًا)
    error_log("Database connection failed: " . $e->getMessage());

    // عطّل الصفحة ووجّه المستخدم لصفحة خطأ
    header("HTTP/1.1 500 Internal Server Error");
    header("Location: /error.php");
    exit();
}
?>


