<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

// İşlemleri
$message = '';
$message_type = '';

// UPDATE - İletişim bilgilerini güncelleme
if (isset($_POST['update'])) {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $twitter_url = trim($_POST['twitter_url'] ?? '');
    $facebook_url = trim($_POST['facebook_url'] ?? '');
    $github_url = trim($_POST['github_url'] ?? '');
    $instagram_url = trim($_POST['instagram_url'] ?? '');
    $linkedin_url = trim($_POST['linkedin_url'] ?? '');

    try {
        // Önce mevcut kayıt var mı kontrol et
        $stmt = $pdo->query("SELECT id FROM contact_info LIMIT 1");
        $existing = $stmt->fetch();

        if ($existing) {
            // Güncelleme
            $stmt = $pdo->prepare("UPDATE contact_info SET address = ?, phone = ?, email = ?, twitter_url = ?, facebook_url = ?, github_url = ?, instagram_url = ?, linkedin_url = ? WHERE id = ?");
            $stmt->execute([$address, $phone, $email, $twitter_url, $facebook_url, $github_url, $instagram_url, $linkedin_url, $existing['id']]);
        } else {
            // Yeni kayıt oluştur
            $stmt = $pdo->prepare("INSERT INTO contact_info (address, phone, email, twitter_url, facebook_url, github_url, instagram_url, linkedin_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$address, $phone, $email, $twitter_url, $facebook_url, $github_url, $instagram_url, $linkedin_url]);
        }

        $message = 'İletişim bilgileri başarıyla güncellendi!';
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = 'Hata: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// READ - İletişim bilgilerini getir
try {
    $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
    $contact_info = $stmt->fetch();
} catch (PDOException $e) {
    $contact_info = null;
    $message = 'Veri çekme hatası: ' . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim Bilgileri - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container narrow">
        <div class="admin-header flex">
            <h1>İletişim Bilgileri Yönetimi</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Mevcut Bilgiler -->
        <?php if ($contact_info): ?>
        <div class="info-section">
            <h3>Mevcut İletişim Bilgileri</h3>
            <div class="info-item">
                <strong>Adres:</strong> <?php echo htmlspecialchars($contact_info['address'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>Telefon:</strong> <?php echo htmlspecialchars($contact_info['phone'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>E-posta:</strong> <?php echo htmlspecialchars($contact_info['email'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>Twitter:</strong> <?php echo htmlspecialchars($contact_info['twitter_url'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>Facebook:</strong> <?php echo htmlspecialchars($contact_info['facebook_url'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>GitHub:</strong> <?php echo htmlspecialchars($contact_info['github_url'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>Instagram:</strong> <?php echo htmlspecialchars($contact_info['instagram_url'] ?? ''); ?>
            </div>
            <div class="info-item">
                <strong>LinkedIn:</strong> <?php echo htmlspecialchars($contact_info['linkedin_url'] ?? ''); ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Düzenleme Formu -->
        <div class="form-container">
            <h2>İletişim Bilgilerini Düzenle</h2>
            <form method="post" action="">
                <div class="form-group">
                    <label for="address">Adres</label>
                    <textarea id="address" name="address" class="tall"><?php echo htmlspecialchars($contact_info['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="phone">Telefon</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($contact_info['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-posta</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($contact_info['email'] ?? ''); ?>">
                </div>

                <div class="social-group">
                    <div class="form-group">
                        <label for="twitter_url">Twitter URL</label>
                        <input type="text" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($contact_info['twitter_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="facebook_url">Facebook URL</label>
                        <input type="text" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($contact_info['facebook_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="github_url">GitHub URL</label>
                        <input type="text" id="github_url" name="github_url" value="<?php echo htmlspecialchars($contact_info['github_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="instagram_url">Instagram URL</label>
                        <input type="text" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($contact_info['instagram_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="linkedin_url">LinkedIn URL</label>
                        <input type="text" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($contact_info['linkedin_url'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" name="update" class="btn btn-success">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


