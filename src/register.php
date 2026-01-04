<?php
require_once 'db_connect.php';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($name)) { $errors[] = "حقل الاسم مطلوب."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "صيغة البريد الإلكتروني غير صحيحة."; }
    if (strlen($password) < 6) { $errors[] = "يجب أن تكون كلمة المرور 6 أحرف على الأقل."; }
    if ($password !== $password_confirm) { $errors[] = "كلمتا المرور غير متطابقتين."; }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "هذا البريد الإلكتروني مستخدم بالفعل.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$name, $email, $hashed_password])) {
                    $success_message = "تم إنشاء حسابك بنجاح! يمكنك الآن <a href='login.php'>تسجيل الدخول</a>.";
                }
            }
        } catch (PDOException $e) { $errors[] = "خطأ في قاعدة البيانات."; }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style> body { background-color: #f8f9fa; } .register-container { max-width: 500px; margin-top: 80px; } </style>
</head>
<body>
    <div class="container">
        <div class="card shadow-sm border-0 mx-auto register-container">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">إنشاء حساب جديد</h3>
                <?php if (!empty($errors )): ?>
                    <div class="alert alert-danger"><?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php else: ?>
                    <form action="register.php" method="POST">
                        <div class="mb-3"><label for="name" class="form-label">الاسم الكامل</label><input type="text" class="form-control" name="name" required></div>
                        <div class="mb-3"><label for="email" class="form-label">البريد الإلكتروني</label><input type="email" class="form-control" name="email" required></div>
                        <div class="mb-3"><label for="password" class="form-label">كلمة المرور</label><input type="password" class="form-control" name="password" required></div>
                        <div class="mb-3"><label for="password_confirm" class="form-label">تأكيد كلمة المرور</label><input type="password" class="form-control" name="password_confirm" required></div>
                        <div class="d-grid"><button type="submit" class="btn btn-primary">إنشاء الحساب</button></div>
                    </form>
                <?php endif; ?>
                <div class="text-center mt-3"><p>لديك حساب بالفعل؟ <a href="login.php">سجل الدخول</a></p></div>
            </div>
        </div>
    </div>
</body>
</html>

