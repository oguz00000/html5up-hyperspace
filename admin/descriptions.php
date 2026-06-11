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

// CREATE - Yeni açıklama ekleme
if (isset($_POST['create'])) {
    $section_key = trim($_POST['section_key'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($section_key)) {
        $message = 'Bölüm anahtarı zorunludur!';
        $message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO descriptions (section_key, content) VALUES (?, ?)");
            $stmt->execute([$section_key, $content]);
            $message = 'Açıklama başarıyla eklendi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Bu bölüm anahtarı zaten kullanılıyor!';
            } else {
                $message = 'Hata: ' . $e->getMessage();
            }
            $message_type = 'error';
        }
    }
}

// UPDATE - Açıklama düzenleme
if (isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $section_key = trim($_POST['section_key'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($section_key) || $id <= 0) {
        $message = 'Geçersiz veri!';
        $message_type = 'error';
    } else {
        try {
            // Mevcut kaydın section_key'ini al
            $stmt = $pdo->prepare("SELECT section_key FROM descriptions WHERE id = ?");
            $stmt->execute([$id]);
            $current_item = $stmt->fetch();
            $old_section_key = $current_item['section_key'] ?? '';

            // Eğer section_key değiştiyse ve başka bir kayıt tarafından kullanılıyorsa, değiş tokuş yap
            if ($section_key !== $old_section_key) {
                // Yeni section_key'in başka bir kayıt tarafından kullanılıp kullanılmadığını kontrol et
                $stmt = $pdo->prepare("SELECT id FROM descriptions WHERE section_key = ? AND id != ?");
                $stmt->execute([$section_key, $id]);
                $conflicting_item = $stmt->fetch();

                if ($conflicting_item) {
                    // Değiş tokuş yap: Önce çakışan kaydın section_key'ini geçici bir değere değiştir
                    $temp_key = '_temp_' . time() . '_' . $conflicting_item['id'];
                    $stmt = $pdo->prepare("UPDATE descriptions SET section_key = ? WHERE id = ?");
                    $stmt->execute([$temp_key, $conflicting_item['id']]);

                    // Mevcut kaydın section_key'ini yeni değere güncelle
                    $stmt = $pdo->prepare("UPDATE descriptions SET section_key = ?, content = ? WHERE id = ?");
                    $stmt->execute([$section_key, $content, $id]);

                    // Çakışan kaydın section_key'ini eski değere güncelle
                    $stmt = $pdo->prepare("UPDATE descriptions SET section_key = ? WHERE id = ?");
                    $stmt->execute([$old_section_key, $conflicting_item['id']]);

                    $message = 'Açıklama başarıyla güncellendi! Bölüm anahtarları değiş tokuş edildi.';
                    $message_type = 'success';
                } else {
                    // Çakışma yok, normal güncelleme
                    $stmt = $pdo->prepare("UPDATE descriptions SET section_key = ?, content = ? WHERE id = ?");
                    $stmt->execute([$section_key, $content, $id]);
                    $message = 'Açıklama başarıyla güncellendi!';
                    $message_type = 'success';
                }
            } else {
                // Section_key değişmedi, sadece content güncelle
                $stmt = $pdo->prepare("UPDATE descriptions SET content = ? WHERE id = ?");
                $stmt->execute([$content, $id]);
                $message = 'Açıklama başarıyla güncellendi!';
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// DELETE - Açıklama silme
if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM descriptions WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Açıklama başarıyla silindi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// READ - Tüm açıklamaları getir
try {
    $stmt = $pdo->query("SELECT * FROM descriptions ORDER BY section_key ASC");
    $descriptions = $stmt->fetchAll();
} catch (PDOException $e) {
    $descriptions = [];
    $message = 'Veri çekme hatası: ' . $e->getMessage();
    $message_type = 'error';
}

// Düzenleme için öğe seçme
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)($_GET['edit'] ?? 0);
    foreach ($descriptions as $item) {
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
    <title>Açıklamalar Yönetimi - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header flex">
            <h1>Açıklamalar Yönetimi</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Ekleme/Düzenleme Formu -->
        <div class="form-container">
            <h2><?php echo $edit_item ? 'Açıklama Düzenle' : 'Yeni Açıklama Ekle'; ?></h2>
            <form method="post" action="">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="section_key">Bölüm Seçimi *</label>
                    <select id="section_key" name="section_key" required onchange="updateCustomInput()">
                        <option value="">-- Bölüm Seçin --</option>
                        <option value="intro" <?php echo (isset($edit_item['section_key']) && $edit_item['section_key'] === 'intro') ? 'selected' : ''; ?>>Ana Sayfa Intro (Welcome Bölümü)</option>
                        <option value="what_we_do_intro" <?php echo (isset($edit_item['section_key']) && $edit_item['section_key'] === 'what_we_do_intro') ? 'selected' : ''; ?>>What We Do Bölümü Açıklaması</option>
                        <option value="contact_intro" <?php echo (isset($edit_item['section_key']) && $edit_item['section_key'] === 'contact_intro') ? 'selected' : ''; ?>>Contact Bölümü Açıklaması</option>
                        <option value="custom">Özel Bölüm (Manuel Giriş)</option>
                    </select>
                    <input type="text" id="custom_section_key" name="custom_section_key" 
                           style="display: none; margin-top: 10px;"
                           placeholder="Özel bölüm anahtarı girin"
                           value="<?php echo (isset($edit_item['section_key']) && !in_array($edit_item['section_key'], ['intro', 'what_we_do_intro', 'contact_intro'])) ? htmlspecialchars($edit_item['section_key']) : ''; ?>">
                    <small>Bu açıklamanın sitede hangi bölümde kullanılacağını seçin.</small>
                </div>
                
                <script>
                function updateCustomInput() {
                    var select = document.getElementById('section_key');
                    var customInput = document.getElementById('custom_section_key');
                    if (select.value === 'custom') {
                        customInput.style.display = 'block';
                        customInput.setAttribute('required', 'required');
                        select.removeAttribute('name');
                        customInput.setAttribute('name', 'section_key');
                    } else {
                        customInput.style.display = 'none';
                        customInput.removeAttribute('required');
                        customInput.removeAttribute('name');
                        select.setAttribute('name', 'section_key');
                    }
                }
                
                // Sayfa yüklendiğinde kontrol et
                document.addEventListener('DOMContentLoaded', function() {
                    var select = document.getElementById('section_key');
                    var customInput = document.getElementById('custom_section_key');
                    var currentValue = '<?php echo isset($edit_item['section_key']) ? htmlspecialchars($edit_item['section_key'], ENT_QUOTES) : ''; ?>';
                    
                    if (currentValue && !['intro', 'what_we_do_intro', 'contact_intro'].includes(currentValue)) {
                        select.value = 'custom';
                        customInput.value = currentValue;
                        updateCustomInput();
                    }
                });
                </script>

                <div class="form-group">
                    <label for="content">İçerik</label>
                    <textarea id="content" name="content" class="tall"><?php echo htmlspecialchars($edit_item['content'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" name="<?php echo $edit_item ? 'update' : 'create'; ?>" class="btn <?php echo $edit_item ? 'btn-success' : 'btn-primary'; ?>">
                        <?php echo $edit_item ? 'Güncelle' : 'Ekle'; ?>
                    </button>
                    <?php if ($edit_item): ?>
                        <a href="descriptions.php" class="btn btn-secondary">İptal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Açıklamalar Listesi -->
        <div class="table-container">
            <h2>Açıklamalar (<?php echo count($descriptions); ?>)</h2>
            <?php if (empty($descriptions)): ?>
                <p>Henüz açıklama eklenmemiş.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Bölüm Anahtarı</th>
                            <th>İçerik</th>
                            <th>Güncellenme</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($descriptions as $desc): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($desc['section_key']); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($desc['content'] ?? '', 0, 100)) . (strlen($desc['content'] ?? '') > 100 ? '...' : ''); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($desc['updated_at'])); ?></td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $desc['id']; ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                    <a href="?delete=<?php echo $desc['id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu açıklamayı silmek istediğinizden emin misiniz?')">Sil</a>
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
