<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

$message = '';
$message_type = '';

if (isset($_POST['update_account'])) {
    $admin_id = $_SESSION['admin_id'];
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basit doğrulama: Eğer şifre girildiyse eşleşme kontrolü
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $message = 'Şifreler eşleşmiyor!';
        $message_type = 'error';
    } elseif (empty($username) || empty($email)) {
        $message = 'Kullanıcı adı ve e-posta zorunludur!';
        $message_type = 'error';
    } else {
        try {
            if (!empty($new_password)) {
                $stmt = $pdo->prepare("UPDATE admin SET name = ?, email = ?, password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$username, $email, $new_password, $admin_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE admin SET name = ?, email = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$username, $email, $admin_id]);
            }
            
            $_SESSION['admin_name'] = $username;
            $message = 'Hesap bilgileri başarıyla güncellendi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM admin WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Hesap Ayarları - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container narrow">
        <div class="admin-header flex">
            <h1>Hesap Ayarları</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="post" action="" onsubmit="return validatePassword()">
                <div class="form-group">
                    <label for="username">Kullanıcı Adı</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">E-posta Adresi</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Yeni Şifre</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Yeni şifre">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Şifreyi Yeniden Yazın</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Şifreyi onaylayın">
                </div>
                <div class="form-group" style="text-align: center;">
                    <button type="submit" name="update_account" class="btn btn-primary">Bilgileri Güncelle</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function validatePassword() {
        var pass = document.getElementById("new_password").value;
        var confirm = document.getElementById("confirm_password").value;
        if (pass !== confirm) {
            alert("Şifreler eşleşmiyor!");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>