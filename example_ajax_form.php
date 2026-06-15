<?php
require_once '../classes/AntiCSRF.php';
$csrf = new AntiCSRF();
// Set token to last longer for AJAX heavy forms
$csrf->setDefaultTtl(3600); // 1 hour
?>
<!DOCTYPE html>
<html>
<head>
    <title>AntiCSRF – AJAX Example</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <h2>AJAX Comment Form (with CSRF)</h2>
    <form id="commentForm">
        <input type="text" name="comment" placeholder="Your comment">
        <div id="csrf-field-container">
            <!-- CSRF field will be injected here -->
        </div>
        <button type="submit">Submit</button>
    </form>
    <div id="result"></div>

    <script>
        // Fetch a fresh CSRF token from server (endpoint)
        function refreshToken() {
            return fetch('ajax_token.php')
                .then(response => response.json())
                .then(data => data.token);
        }

        document.getElementById('commentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const token = await refreshToken();
            const formData = new FormData(this);
            formData.append('csrf_token', token);
            fetch('ajax_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('result').innerHTML = data.message;
                if (data.success) {
                    document.querySelector('input[name="comment"]').value = '';
                }
            });
        });

        // Initial load of token container
        refreshToken().then(token => {
            document.getElementById('csrf-field-container').innerHTML = 
                '<input type="hidden" name="csrf_token" value="' + token + '">';
        });
    </script>
</body>
</html>