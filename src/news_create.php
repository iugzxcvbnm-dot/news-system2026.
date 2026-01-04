<?php
require_once 'db_connect.php';
check_login();
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) { die("خطأ في جلب الفئات: " . $e->getMessage()); }
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $details = trim($_POST['details']);
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    $image = $_FILES['image'];

    if (empty($title)) { $errors[] = "عنوان الخبر مطلوب."; }
    if (empty($details)) { $errors[] = "تفاصيل الخبر مطلوبة."; }
    if (empty($category_id)) { $errors[] = "يجب اختيار فئة للخبر."; }
    if ($image['error'] === UPLOAD_ERR_NO_FILE) { $errors[] = "صورة الخبر مطلوبة."; }

    if ($image['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) { $errors[] = "امتداد الصورة غير مسموح به."; }
        if ($image['size'] > 2097152) { $errors[] = "حجم الصورة يجب أن يكون أقل من 2 ميجابايت."; }
    }

    if (empty($errors)) {
        try {
            $new_image_name = uniqid('news_', true) . '.' . $ext;
            $upload_path = 'uploads/' . $new_image_name;
            if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                $sql = "INSERT INTO news (title, details, category_id, user_id, image) VALUES (?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$title, $details, $category_id, $user_id, $new_image_name])) {
                    $success = "تم نشر الخبر بنجاح!";
                } else { unlink($upload_path); $errors[] = "حدث خطأ أثناء حفظ الخبر."; }
            } else { $errors[] = "فشل رفع الصورة."; }
        } catch (PDOException $e) { $errors[] = "خطأ في قاعدة البيانات."; }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة خبر جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">إضافة خبر جديد</h4></div>
                    <div class="card-body">
                        <?php if ($success ): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger"><?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?></div>
                        <?php endif; ?>
                        <form action="news_create.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3"><label class="form-label">عنوان الخبر</label><input type="text" class="form-control" name="title" required></div>
                            <div class="mb-3"><label class="form-label">الفئة</label><select class="form-select" name="category_id" required><option value="">-- اختر فئة --</option><?php foreach ($categories as $cat): ?><option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div>
                            <div class="mb-3"><label class="form-label">تفاصيل الخبر</label><textarea class="form-control" name="details" rows="6" required></textarea></div>
                            <div class="mb-3"><label class="form-label">صورة الخبر</label><input type="file" class="form-control" name="image" accept="image/*" required></div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> نشر الخبر</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

