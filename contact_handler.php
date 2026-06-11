<?php
require_once 'config.php';

// Set response headers for AJAX
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validate inputs
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($message)) $errors[] = 'Message is required';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';

        if (empty($errors)) {
            // First, check if the title column exists in the messages table
            $stmt = $pdo->query("SHOW COLUMNS FROM messages LIKE 'title'");
            $titleColumnExists = $stmt->rowCount() > 0;

            if ($titleColumnExists) {
                // If title column exists, use the full query
                $stmt = $pdo->prepare("INSERT INTO messages (name, email, title, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $title, $message]);
            } else {
                // If title column doesn't exist, use query without title
                $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $message]);
            }

            $response['success'] = true;
            $response['message'] = 'Message sent successfully!';
        } else {
            $response['message'] = implode(', ', $errors);
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>