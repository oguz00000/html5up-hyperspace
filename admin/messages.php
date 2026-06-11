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

// DELETE - Mesaj silme
if (isset($_GET['delete'])) {
    $id = (int)($_GET['delete'] ?? 0);
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Mesaj başarıyla silindi!';
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = 'Hata: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// READ - Tüm mesajları getir
try {
    // Check if title column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'title'");
    $titleColumnExists = $stmt->rowCount() > 0;

    if ($titleColumnExists) {
        $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC");
    } else {
        $stmt = $pdo->query("SELECT id, name, email, message, created_at FROM messages ORDER BY created_at DESC");
    }
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $messages = [];
    $message = 'Veri çekme hatası: ' . $e->getMessage();
    $message_type = 'error';
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gelen Mesajlar - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Gelen Mesajlar</h1>
            <a href="index.php" class="btn btn-secondary">← Admin Paneli</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <!-- İstatistikler -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number">Toplamda <?php echo count($messages); ?> mesajınız var.</div>
            </div>
        </div>

        <!-- Mesajlar Tablosu -->
        <div class="table-container">
            <h2>Mesajlar</h2>
            <?php if (empty($messages)): ?>
                <p>Henüz mesaj gelmemiş.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>E-posta</th>
                            <th>Başlık</th>
                            <th>Tarih</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr onclick="showMessageDetails(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars($msg['name']); ?>', '<?php echo htmlspecialchars($msg['email']); ?>', '<?php echo htmlspecialchars($msg['title']); ?>', '<?php echo htmlspecialchars($msg['message']); ?>')">
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['title']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></td>
                                <td class="actions narrow">
                                    <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="event.stopPropagation(); return confirm('Bu mesajı silmek istediğinizden emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for message details -->
    <div id="messageModal" class="modal">
        <div class="modal-content" style="background: #fff; color: #333;">
            <span class="close" onclick="closeModal()" style="color: #333;">&times;</span>
            <h3 id="modalTitleHeader" style="color: #333;">Mesaj Detayı</h3>
            <div id="modalDetails" style="margin-top: 20px; color: #333;">
                <div style="margin-bottom: 15px;">
                    <strong style="color: #333;">Ad Soyad:</strong>
                    <span id="modalName" style="color: #333;"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #333;">E-posta:</strong>
                    <span id="modalEmail" style="color: #333;"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #333;">Başlık:</strong>
                    <span id="modalTitle" style="color: #333;"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="color: #333;">Mesaj:</strong>
                    <div id="modalMessage" style="margin-top: 5px; line-height: 1.6; color: #333; max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMessageDetails(id, name, email, title, message) {
            document.getElementById('modalTitleHeader').textContent = 'Mesaj Detayı';
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalEmail').textContent = email;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('messageModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // Modal dışına tıklayınca kapat
        window.onclick = function(event) {
            var modal = document.getElementById('messageModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>


