<!DOCTYPE html>
<html>
<head>
    <title>AntiCSRF – CSRF Protection for PHP Forms</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 1000px; margin: 2em auto; line-height: 1.6; }
        h1, h2 { color: #1e293b; }
        code { background: #f1f5f9; padding: 2px 5px; border-radius: 4px; }
        pre { background: #0f172a; color: #e2e8f0; padding: 1rem; border-radius: 0.75rem; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #e2e8f0; }
        .note { background: #fef9c3; border-left: 4px solid #eab308; padding: 0.75rem; margin: 1rem 0; }
    </style>
</head>
<body>
    <h1>AntiCSRF Documentation</h1>
    <p><strong>AntiCSRF</strong> is a lightweight PHP class that protects your forms against Cross‑Site Request Forgery (CSRF) attacks using one‑time tokens stored in the user’s session.</p>

    <h2>Features</h2>
    <ul>
        <li>✅ <strong>One‑time tokens</strong> – each token can be used only once (configurable).</li>
        <li>✅ <strong>Automatic expiration</strong> – default 30 minutes, custom TTL per token.</li>
        <li>✅ <strong>Simple integration</strong> – just call <code>$csrf->field()</code> inside your form.</li>
        <li>✅ <strong>Session‑based</strong> – no database required.</li>
        <li>✅ <strong>Secure token generation</strong> – uses <code>random_bytes()</code> (cryptographically secure).</li>
        <li>✅ <strong>Automatic cleanup</strong> – expired tokens removed on each validation.</li>
    </ul>

    <h2>Installation</h2>
    <ol>
        <li>Copy <code>AntiCSRF.php</code> to your project (e.g., <code>lib/</code>).</li>
        <li>Ensure sessions are started (the class starts them automatically).</li>
        <li>Include the class in your form handler.</li>
    </ol>

    <h2>Basic Usage</h2>
    <pre><code>require_once 'AntiCSRF.php';
$csrf = new AntiCSRF();

// In your HTML form:
echo $csrf->field();

// In your POST handler:
if ($csrf->validate($_POST['csrf_token'] ?? '')) {
    // Process request
} else {
    die('CSRF validation failed');
}</code></pre>

    <h2>Configuration Methods</h2>
    <table>
        <tr><th>Method</th><th>Description</th><th>Default</th></tr>
        <tr><td><code>setSessionKey(string $key)</code></td>
            <td>Change the session key used to store tokens.</td>
            <td><code>_csrf_tokens</code></td>
        </tr>
        <tr><td><code>setDefaultTtl(int $seconds)</code></td>
            <td>Default token lifetime.</td>
            <td><code>1800</code> (30 min)</td>
        </tr>
        <tr><td><code>setRegenerateAfterValidation(bool $bool)</code></td>
            <td>If <code>true</code>, tokens are deleted after validation (one‑time use). If <code>false</code>, tokens can be reused until they expire.</td>
            <td><code>true</code></td>
        </tr>
    </table>

    <h2>Advanced: Manual Token Handling</h2>
    <p>Instead of using <code>$csrf->field()</code>, you can generate a token manually and embed it yourself:</p>
    <pre><code>$token = $csrf->generate();
// Then in form: &lt;input type="hidden" name="my_csrf" value="&lt;?= $token ?&gt;"&gt;
// Validation: $csrf->validate($_POST['my_csrf'])</code></pre>

    <h2>AJAX / SPA Usage</h2>
    <p>For frontend applications, expose an endpoint that returns a fresh token:</p>
    <pre><code>// ajax_token.php
require_once 'AntiCSRF.php';
$csrf = new AntiCSRF();
header('Content-Type: application/json');
echo json_encode(['token' => $csrf->generate()]);</code></pre>
    <p>Then send this token with each AJAX request (e.g., in a custom header or POST field).</p>

    <h2>Security Notes</h2>
    <div class="note">
        <strong>Important:</strong> Always use HTTPS in production. The class relies on PHP sessions – make sure session cookies are marked <code>HttpOnly</code> and <code>Secure</code>. For high‑security applications, consider additional checks (origin/referer).
    </div>

    <h2>License</h2>
    <p>BSD 3‑Clause – free to use, modify, and redistribute.</p>
</body>
</html>
