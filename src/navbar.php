<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">لوحة تحكم الأخبار</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="main-nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">الفئات</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="category_create.php">إضافة فئة</a></li>
                        <li><a class="dropdown-item" href="categories_view.php">عرض الفئات</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">الأخبار</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="news_create.php">إضافة خبر</a></li>
                        <li><a class="dropdown-item" href="news_view.php">عرض جميع الأخبار</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="news_trashed.php">الأخبار المحذوفة</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                     <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa fa-user"></i> مرحباً, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                     </a>
                     <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="logout.php">تسجيل الخروج</a></li>
                     </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
