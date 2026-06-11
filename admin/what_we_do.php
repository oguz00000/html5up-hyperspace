<?php
session_start();

// Giriş kontrolü
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config.php';

// CRUD İşlemleri
$message = '';
$message_type = '';

// CREATE - Yeni öğe ekleme
if (isset($_POST['create'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon_class = trim($_POST['icon_class'] ?? '');

    if (empty($title)) {
        $message = 'Başlık zorunludur!';
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO what_we_do (title, description, icon_class) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $icon_class]);
            $message = 'Hizmet başarıyla eklendi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// UPDATE - Öğe düzenleme
if (isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon_class = trim($_POST['icon_class'] ?? '');

    if (empty($title) || $id <= 0) {
        $message = 'Geçersiz veri!';
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE what_we_do SET title = ?, description = ?, icon_class = ? WHERE id = ?");
            $stmt->execute([$title, $description, $icon_class, $id]);
            $message = 'Hizmet başarıyla güncellendi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// DELETE - Öğe silme
if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM what_we_do WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Hizmet başarıyla silindi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// READ - Tüm öğeleri getir
try {
    $stmt = $pdo->query("SELECT * FROM what_we_do ORDER BY created_at DESC");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
    $message = 'Veri çekme hatası: ' . $e->getMessage();
    $message_type = 'error';
}

// Düzenleme için öğe seçme
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)($_GET['edit'] ?? 0);
    foreach ($services as $item) {
        if ($item['id'] == $edit_id) {
            $edit_item = $item;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>What We Do Yönetimi - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header flex">
            <h1>What We Do Yönetimi</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Ekleme/Düzenleme Formu -->
        <div class="form-container">
            <h2><?php echo $edit_item ? 'Hizmet Düzenle' : 'Yeni Hizmet Ekle'; ?></h2>
            <form method="post" action="">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Başlık *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_item['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="icon_class">İkon Sınıfı (FontAwesome)</label>
                    <input type="text" id="icon_class" name="icon_class" value="<?php echo htmlspecialchars($edit_item['icon_class'] ?? ''); ?>"
                           placeholder="Örnek: fa-code, fa-lock, fa-cog">
                    <small>FontAwesome ikon sınıflarını kullanın (fa- ile başlayan)</small>
                </div>

                <div class="form-group">
                    <button type="submit" name="<?php echo $edit_item ? 'update' : 'create'; ?>" class="btn <?php echo $edit_item ? 'btn-success' : 'btn-primary'; ?>">
                        <?php echo $edit_item ? 'Güncelle' : 'Ekle'; ?>
                    </button>
                    <?php if ($edit_item): ?>
                        <a href="what_we_do.php" class="btn btn-secondary">İptal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Hizmetler Listesi -->
        <div class="table-container">
            <h2>Hizmetler (<?php echo count($services); ?>)</h2>
            <?php if (empty($services)): ?>
                <p>Henüz hizmet eklenmemiş.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>İkon</th>
                            <th>Başlık</th>
                            <th>Açıklama</th>
                            <th>Oluşturulma</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td>
                                    <?php if ($service['icon_class']): ?>
                                        <i class="icon solid major <?php echo htmlspecialchars($service['icon_class']); ?> icon-preview"></i>
                                    <?php else: ?>
                                        İkon yok
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($service['title']); ?></td>
                                <td><?php echo htmlspecialchars(substr($service['description'] ?? '', 0, 100)) . (strlen($service['description'] ?? '') > 100 ? '...' : ''); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($service['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $service['id']; ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                    <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu hizmeti silmek istediğinizden emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


