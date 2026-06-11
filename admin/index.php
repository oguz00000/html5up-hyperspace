<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Hyperspace</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Hyperspace Admin Paneli</h1>
        <a href="logout.php" class="logout-btn">Çıkış Yap</a>
    </header>

    <div class="admin-container">
        <div class="welcome-message">
            <h2>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>!</h2>
            <p>İçerik yönetimi için aşağıdaki seçeneklerden birini seçin.</p>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <h3>📸 Galeri Yönetimi</h3>
                <p>Galeri öğelerini ekleyin, düzenleyin veya silin.</p>
                <a href="gallery.php">Galeri Yönet</a>
            </div>

            <div class="admin-card">
                <h3>⚙️ What We Do Yönetimi</h3>
                <p>"What We Do" bölümündeki hizmetleri yönetin.</p>
                <a href="what_we_do.php">Hizmetleri Yönet</a>
            </div>

            <div class="admin-card">
                <h3>💬 Gelen Mesajlar</h3>
                <p>İletişim formundan gelen mesajları görüntüleyin.</p>
                <a href="messages.php">Mesajları Görüntüle</a>
            </div>

            <div class="admin-card">
                <h3>📞 İletişim Bilgileri</h3>
                <p>İletişim bilgilerini ve sosyal medya linklerini düzenleyin.</p>
                <a href="contact.php">İletişimi Düzenle</a>
            </div>

            <div class="admin-card">
                <h3>📝 Açıklamalar Yönetimi</h3>
                <p>Sitedeki açıklama metinlerini düzenleyin.</p>
                <a href="descriptions.php">Açıklamaları Yönet</a>
            </div>

            <div class="admin-card">
                <h3>⚙️ Hesap Yönetimi</h3>
                <p>Kullanıcı bilgilerinizi düzenleyin.</p>
                <a href="update_account.php">Admin Hesabını Yönet</a>
            </div>
        </div>
    </div>
</body>
</html>


