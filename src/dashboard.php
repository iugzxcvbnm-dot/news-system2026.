<?php
require_once 'db_connect.php';
check_login();
try {
    $stmt = $pdo->query("SELECT news.id, news.title, news.details, news.image, news.created_at, categories.name AS category_name, users.name AS user_name FROM news JOIN categories ON news.category_id = categories.id JOIN users ON news.user_id = users.id WHERE news.status = 'published' ORDER BY news.created_at DESC LIMIT 6");
    $news_list = $stmt->fetchAll();
} catch (PDOException $e) { die("خطأ في جلب الأخبار: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">آخر الأخبار</h2>
            <a href="news_create.php" class="btn btn-primary"><i class="fa fa-plus"></i> إضافة خبر جديد</a>
        </div>
        <div class="row">
            <?php if (empty($news_list )): ?>
                <div class="col-12"><div class="alert alert-info text-center">لا توجد أخبار لعرضها حاليًا.</div></div>
            <?php else: ?>
                <?php foreach ($news_list as $news_item): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <img src="uploads/<?php echo htmlspecialchars($news_item['image']); ?>" class="card-img-top" alt="..." style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($news_item['title']); ?></h5>
                                <p class="card-text text-muted small"><span class="badge bg-primary me-2"><?php echo htmlspecialchars($news_item['category_name']); ?></span>بواسطة: <?php echo htmlspecialchars($news_item['user_name']); ?></p>
                                <p class="card-text flex-grow-1"><?php echo htmlspecialchars(mb_substr($news_item['details'], 0, 100)); ?>...</p>
                            </div>
                            <div class="card-footer text-muted small"><?php echo date('Y-m-d', strtotime($news_item['created_at'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

