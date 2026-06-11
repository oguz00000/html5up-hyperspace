<?php
session_start();

// Eğer zaten giriş yapılmışsa admin paneline yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->query("SELECT * FROM admin");
    $contact_info = $stmt->fetch();

    // Basit kimlik doğrulama (gerçek uygulamada veritabanından kontrol edin)
    if ($username === $contact_info['name'] && password_verify($password, $contact_info['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_id'] = $contact_info['id'];
        session_regenerate_id(true); // Güvenlik için session ID'yi yenile

        $stmt = $pdo->prepare("UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$contact_info['id']]);

        header('Location: index.php');
        exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre!';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Hyperspace</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Giriş</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn full-width login">Giriş Yap</button>
        </form>
    </div>
</body>
</html>


