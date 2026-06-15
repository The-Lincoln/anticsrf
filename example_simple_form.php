<?php
require_once '../classes/AntiCSRF.php';

$csrf = new AntiCSRF();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if ($csrf->validate($token)) {
        // Process form data safely (e.g., save to DB)
        $success = "Form submitted successfully!";
        // Optionally redirect to avoid resubmission
        // header('Location: same-page');
        // exit;
    } else {
        $error = "Invalid or expired CSRF token. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>AntiCSRF – Simple Form Example</title>
    <style>
        body { font-family: Arial; margin: 2em; }
        .error { color: red; }
        .success { color: green; }
        input, button { padding: 8px; margin: 5px; }
        form { border: 1px solid #ccc; padding: 20px; width: 300px; }
    </style>
</head>
<body>
    <h2>Protected Form</h2>
    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Your Name:</label>
        <input type="text" name="username" required>
        <label>Message:</label>
        <textarea name="message" rows="3"></textarea>
        <?= $csrf->field() ?>  <!-- hidden token inserted here -->
        <button type="submit">Send</button>
    </form>
</body>
</html>