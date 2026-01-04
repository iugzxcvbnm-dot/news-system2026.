<?php
require_once 'db_connect.php';
check_login();
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: news_view.php"); exit; }
$news_id = $_GET['id'];
$errors = [];
$success = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$news_id]);
    $news_item = $stmt->fetch();
    if (!$news_item) { header("Location: news_view.php"); exit; }
    $stmt_cat = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt_cat->fetchAll();
} catch (PDOException $e) { die("خطأ في جلب البيانات: " . $e->getMessage()); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $details = trim($_POST['details']);
    $category_id = $_POST['category_id'];
    $image = $_FILES['image'];
    $new_image_name = $news_item['image'];

    if (empty($title) || empty($details) || empty($category_id)) { $errors[] = "جميع الحقول مطلوبة."; }

    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) { $errors[] = "امتداد الصورة غير مسموح به."; }
        elseif ($image['size'] > 2097152) { $errors[] = "حجم الصورة كبير جدًا."; }
        else { $new_image_name = uniqid('news_', true) . '.' . $ext; }
    }

    if (empty($errors)) {
        try {
            if ($new_image_name !== $news_item['image']) {
                $upload_path = 'uploads/' . $new_image_name;
                if (move_uploaded_file($image['tmp_name'], $upload_path)) {
                    $old_image_path = 'uploads/' . $news_item['image'];
                    if (file_exists($old_image_path)) { unlink($old_image_path); }
                } else { $errors[] = "فشل رفع الصورة الجديدة."; }
            }
            if (empty($errors)) {
                $sql = "UPDATE news SET title = ?, details = ?, category_id = ?, image = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$title, $details, $category_id, $new_image_name, $news_id])) {
                    $success = "تم تحديث الخبر بنجاح!";
                    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
                    $stmt->execute([$news_id]);
                    $news_item = $stmt->fetch();
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
    <title>تعديل الخبر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h4 class="mb-0">تعديل الخبر</h4></div>
                    <div class="card-body">
                        <?php if ($success ): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
                        <?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach ($errors as $error): ?><p class="mb-0"><?php echo $error; ?></p><?php endforeach; ?></div><?php endif; ?>
                        <form action="news_edit.php?id=<?php echo $news_id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3"><label class="form-label">عنوان الخبر</label><input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($news_item['title']); ?>" required></div>
                            <div class="mb-3"><label class="form-label">الفئة</label><select class="form-select" name="category_id" required><?php foreach ($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $news_item['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option><?php endforeach; ?></select></div>
                            <div class="mb-3"><label class="form-label">تفاصيل الخبر</label><textarea class="form-control" name="details" rows="6" required><?php echo htmlspecialchars($news_item['details']); ?></textarea></div>
                            <div class="mb-3"><label class="form-label">تغيير صورة الخبر (اختياري)</label><input type="file" class="form-control" name="image" accept="image/*"><div class="mt-2"><p>الصورة الحالية:</p><img src="uploads/<?php echo htmlspecialchars($news_item['image']); ?>" alt="صورة" width="150" class="img-thumbnail"></div></div>
                            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> تحديث الخبر</button>
                            <a href="news_view.php" class="btn btn-secondary">العودة</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
