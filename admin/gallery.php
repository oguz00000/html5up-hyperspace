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

// Dosya yükleme fonksiyonu
function handleImageUpload($file, $existing_url = '') {
    global $message, $message_type;
    
    // Eğer dosya yüklenmişse
    if (isset($file['tmp_name']) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
        // Dosya tipi kontrolü
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            $message = 'Geçersiz dosya tipi! Sadece JPG, PNG, GIF ve WEBP formatları kabul edilir.';
            $message_type = 'error';
            return false;
        }
        
        // Dosya boyutu kontrolü (5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $message = 'Dosya boyutu çok büyük! Maksimum 5MB olmalıdır.';
            $message_type = 'error';
            return false;
        }
        
        // Dosya adını oluştur
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $extension;
        $upload_dir = '../images/';
        
        // Klasör yoksa oluştur
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $target_path = $upload_dir . $filename;
        
        // Dosyayı yükle
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            // Eski dosyayı sil (eğer varsa ve yüklenen bir dosyaysa)
            if (!empty($existing_url) && strpos($existing_url, 'images/gallery_') !== false) {
                $old_file = '../' . $existing_url;
                if (file_exists($old_file)) {
                    @unlink($old_file);
                }
            }
            return 'images/' . $filename;
        } else {
            $message = 'Dosya yüklenirken bir hata oluştu!';
            $message_type = 'error';
            return false;
        }
    }
    
    return null; // Dosya yüklenmedi
}

// CREATE - Yeni öğe ekleme
if (isset($_POST['create'])) {
    $title = trim($_POST['title'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $brief = trim($_POST['brief'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_source = $_POST['image_source'] ?? 'url';
    $image_upload = $_FILES['image_upload'] ?? null;

    if (empty($title)) {
        $message = 'Başlık zorunludur!';
        $message_type = 'error';
    } else {
        // Önce dosya yükleme kontrolü
        $final_image_url = '';
        
        if ($image_source === 'upload') {
            if ($image_upload && !empty($image_upload['tmp_name'])) {
                $upload_result = handleImageUpload($image_upload);
                if ($upload_result === false) {
                    // Hata mesajı zaten set edildi
                } else {
                    $final_image_url = $upload_result;
                }
            } else {
                $message = 'Lütfen bir görsel dosyası seçin!';
                $message_type = 'error';
            }
        } else {
            if (!empty($image_url)) {
                $final_image_url = $image_url;
            } else {
                $message = 'Lütfen görsel URL girin!';
                $message_type = 'error';
            }
        }
        
        if (!empty($final_image_url) && empty($message)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO gallery (title, image_url, brief, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $final_image_url, $brief, $description]);
                $message = 'Galeri öğesi başarıyla eklendi!';
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Hata: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// UPDATE - Öğe düzenleme
if (isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $brief = trim($_POST['brief'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_source = $_POST['image_source'] ?? 'url';
    $image_upload = $_FILES['image_upload'] ?? null;

    if (empty($title) || $id <= 0) {
        $message = 'Geçersiz veri!';
        $message_type = 'error';
    } else {
        // Mevcut görsel URL'ini al
        try {
            $stmt = $pdo->prepare("SELECT image_url FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $current_item = $stmt->fetch();
            $existing_url = $current_item['image_url'] ?? '';
        } catch (PDOException $e) {
            $existing_url = '';
        }
        
        // Görsel URL'ini belirle
        $final_image_url = $existing_url; // Varsayılan olarak mevcut URL
        $upload_error = false;
        $image_updated = false; // Görsel güncellendi mi?
        
        if ($image_source === 'upload') {
            // Dosya yükleme kontrolü
            if ($image_upload && isset($image_upload['error'])) {
                if ($image_upload['error'] === UPLOAD_ERR_OK) {
                    // Dosya başarıyla yüklendi, işle
                    if (!empty($image_upload['tmp_name']) && is_uploaded_file($image_upload['tmp_name'])) {
                        $upload_result = handleImageUpload($image_upload, $existing_url);
                        if ($upload_result === false) {
                            // Hata mesajı zaten set edildi
                            $upload_error = true;
                        } elseif (!empty($upload_result)) {
                            // Dosya başarıyla yüklendi (string döndü)
                            $final_image_url = $upload_result;
                            $image_updated = true;
                        } else {
                            // Dosya yüklenemedi (null döndü)
                            $message = 'Dosya yükleme başarısız oldu! Lütfen tekrar deneyin.';
                            $message_type = 'error';
                            $upload_error = true;
                        }
                    } else {
                        $message = 'Dosya yükleme hatası: Geçersiz dosya!';
                        $message_type = 'error';
                        $upload_error = true;
                    }
                } elseif ($image_upload['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Dosya yükleme hatası (NO_FILE hariç)
                    $upload_errors = [
                        UPLOAD_ERR_INI_SIZE => 'Dosya boyutu php.ini dosyasındaki upload_max_filesize limitini aşıyor.',
                        UPLOAD_ERR_FORM_SIZE => 'Dosya boyutu formdaki MAX_FILE_SIZE limitini aşıyor.',
                        UPLOAD_ERR_PARTIAL => 'Dosya kısmen yüklendi.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Geçici klasör bulunamadı.',
                        UPLOAD_ERR_CANT_WRITE => 'Dosya diske yazılamadı.',
                        UPLOAD_ERR_EXTENSION => 'Bir PHP uzantısı dosya yüklemeyi durdurdu.'
                    ];
                    $error_code = $image_upload['error'];
                    $message = 'Dosya yükleme hatası: ' . ($upload_errors[$error_code] ?? 'Bilinmeyen hata (Kod: ' . $error_code . ')');
                    $message_type = 'error';
                    $upload_error = true;
                }
                // UPLOAD_ERR_NO_FILE durumunda mevcut görseli koru (dosya seçilmemiş)
            }
        } else {
            // URL seçildi
            if (!empty($image_url)) {
                $final_image_url = $image_url;
                $image_updated = true;
            }
            // URL boşsa mevcut URL'i koru
        }
        
        // Eğer dosya yükleme hatası yoksa güncelleme yap
        if (!$upload_error && !empty($final_image_url)) {
            try {
                $stmt = $pdo->prepare("UPDATE gallery SET title = ?, image_url = ?, brief = ?, description = ? WHERE id = ?");
                $stmt->execute([$title, $final_image_url, $brief, $description, $id]);
                if ($image_updated) {
                    $message = 'Galeri öğesi başarıyla güncellendi! Görsel ' . ($image_source === 'upload' ? 'yüklendi' : 'güncellendi') . '.';
                } else {
                    $message = 'Galeri öğesi başarıyla güncellendi!';
                }
                $message_type = 'success';
            } catch (PDOException $e) {
                $message = 'Hata: ' . $e->getMessage();
                $message_type = 'error';
            }
        } elseif ($upload_error) {
            // Hata mesajı zaten set edildi
        } else {
            $message = 'Görsel URL veya dosya yüklemesi zorunludur!';
            $message_type = 'error';
        }
    }
}

// DELETE - Öğe silme
if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Galeri öğesi başarıyla silindi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// READ - Tüm öğeleri getir
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $gallery_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $gallery_items = [];
    $message = 'Veri çekme hatası: ' . $e->getMessage();
    $message_type = 'error';
}

// Düzenleme için öğe seçme
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)($_GET['edit'] ?? 0);
    foreach ($gallery_items as $item) {
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
    <title>Galeri Yönetimi - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header flex">
            <h1>Galeri Yönetimi</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- Ekleme/Düzenleme Formu -->
        <div class="form-container">
            <h2><?php echo $edit_item ? 'Galeri Öğesi Düzenle' : 'Yeni Galeri Öğesi Ekle'; ?></h2>
            <form method="post" action="" enctype="multipart/form-data" novalidate>
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Başlık *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($edit_item['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Görsel Seçimi *</label>
                    <div style="margin-bottom: 10px;">
                        <input type="radio" id="image_source_url" name="image_source" value="url" checked onchange="toggleImageInput()">
                        <label for="image_source_url" style="display: inline; margin-left: 5px; font-weight: normal;">URL ile Ekle</label>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <input type="radio" id="image_source_upload" name="image_source" value="upload" onchange="toggleImageInput()">
                        <label for="image_source_upload" style="display: inline; margin-left: 5px; font-weight: normal;">Dosya Yükle</label>
                    </div>
                </div>

                <div class="form-group" id="url_input_group">
                    <label for="image_url">Görsel URL</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($edit_item['image_url'] ?? ''); ?>" placeholder="https://example.com/image.jpg">
                    <small>Görselin internet adresini girin</small>
                </div>

                <div class="form-group" id="upload_input_group" style="display: none;">
                    <label for="image_upload">Görsel Dosyası</label>
                    <input type="file" id="image_upload" name="image_upload" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    <small>Maksimum dosya boyutu: 5MB. Desteklenen formatlar: JPG, PNG, GIF, WEBP</small>
                    <?php if ($edit_item && $edit_item['image_url']): ?>
                        <div style="margin-top: 10px;">
                            <strong>Mevcut Görsel:</strong><br>
                            <?php 
                            $current_img_src = $edit_item['image_url'];
                            // Eğer görsel yerel bir dosya ise (images/ ile başlıyorsa), ../ ekle
                            if (strpos($current_img_src, 'images/') === 0) {
                                $current_img_src = '../' . $current_img_src;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($current_img_src); ?>" alt="Mevcut görsel" style="max-width: 200px; max-height: 150px; margin-top: 5px; border: 1px solid #ddd; padding: 5px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span style="display: none; color: #dc3545;">Görsel yüklenemedi</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group" id="hidden_upload_group" style="display: none;">
                    <!-- Hidden file input for PHP file upload functionality -->
                    <input type="file" name="image_upload_temp" style="display: none;">
                </div>

                <div class="form-group">
                    <label for="brief">Kısa Açıklama</label>
                    <input type="text" id="brief" name="brief" value="<?php echo htmlspecialchars($edit_item['brief'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="description">Açıklama</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" name="<?php echo $edit_item ? 'update' : 'create'; ?>" class="btn <?php echo $edit_item ? 'btn-success' : 'btn-primary'; ?>">
                        <?php echo $edit_item ? 'Güncelle' : 'Ekle'; ?>
                    </button>
                    <?php if ($edit_item): ?>
                        <a href="gallery.php" class="btn btn-secondary">İptal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Galeri Öğeleri Listesi -->
        <div class="table-container">
            <h2>Galeri Öğeleri (<?php echo count($gallery_items); ?>)</h2>
            <?php if (empty($gallery_items)): ?>
                <p>Henüz galeri öğesi eklenmemiş.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Görsel</th>
                            <th>Başlık</th>
                            <th>Kısa Açıklama</th>
                            <th>Oluşturulma</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gallery_items as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['image_url']): ?>
                                        <?php 
                                        $img_src = $item['image_url'];
                                        // Eğer görsel yerel bir dosya ise (images/ ile başlıyorsa), ../ ekle
                                        if (strpos($img_src, 'images/') === 0) {
                                            $img_src = '../' . $img_src;
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Galeri görseli" class="image-preview" onerror="this.src='../images/pic01.jpg'; this.alt='Görsel yüklenemedi';">
                                    <?php else: ?>
                                        Görsel yok
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['brief'] ?? ''); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($item['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-primary btn-sm">Düzenle</a>
                                    <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bu öğeyi silmek istediğinizden emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleImageInput() {
        var urlRadio = document.getElementById('image_source_url');
        var urlGroup = document.getElementById('url_input_group');
        var uploadGroup = document.getElementById('upload_input_group');

        if (urlRadio.checked) {
            // URL modu: URL input visible, upload input hidden
            urlGroup.style.display = 'block';
            uploadGroup.style.display = 'none';
        } else {
            // Upload modu: URL input hidden, upload input visible
            urlGroup.style.display = 'none';
            uploadGroup.style.display = 'block';
        }
    }

    // Sayfa yüklendiğinde mevcut görsel varsa ve URL ise URL'yi seç
    document.addEventListener('DOMContentLoaded', function() {
        var currentImageUrl = '<?php echo isset($edit_item['image_url']) ? htmlspecialchars($edit_item['image_url'], ENT_QUOTES) : ''; ?>';
        if (currentImageUrl) {
            // Eğer mevcut görsel bir URL ise (http:// veya https:// ile başlıyorsa)
            if (currentImageUrl.startsWith('http://') || currentImageUrl.startsWith('https://')) {
                document.getElementById('image_source_url').checked = true;
            } else {
                // Yerel dosya ise upload'u seç
                document.getElementById('image_source_upload').checked = true;
            }
        }
        // toggleImageInput'ı çağır (radio değişimi olmadan)
        toggleImageInput();
    });
    </script>
</body>
</html>


