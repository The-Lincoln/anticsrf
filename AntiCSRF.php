<?php
/**
 * AntiCSRF – One‑time CSRF token protection for forms.
 * 
 * Stores tokens in PHP session with expiration timestamps.
 * Generates SHA‑256 tokens, validates them, and automatically
 * regenerates after validation to enforce one‑time use.
 */
class AntiCSRF
{
    private string $sessionKey = '_csrf_tokens';
    private int $defaultTtl = 1800; // 30 minutes
    private bool $regenerateAfterValidation = true;

    /**
     * Constructor – starts session if not already active.
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
    }

    /**
     * Set the session key name (default '_csrf_tokens').
     * 
     * @param string $key
     * @return self
     */
    public function setSessionKey(string $key): self
    {
        $this->sessionKey = $key;
        if (!isset($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = [];
        }
        return $this;
    }

    /**
     * Set default token lifetime in seconds.
     * 
     * @param int $seconds
     * @return self
     */
    public function setDefaultTtl(int $seconds): self
    {
        $this->defaultTtl = $seconds;
        return $this;
    }

    /**
     * Enable/disable automatic token regeneration after validation.
     * Default is true (one‑time use).
     * 
     * @param bool $regenerate
     * @return self
     */
    public function setRegenerateAfterValidation(bool $regenerate): self
    {
        $this->regenerateAfterValidation = $regenerate;
        return $this;
    }

    /**
     * Generate a new CSRF token and store it in session.
     * 
     * @param int|null $ttl  Time‑to‑live in seconds (null = use default)
     * @return string  The raw token value
     */
    public function generate(?int $ttl = null): string
    {
        $token = bin2hex(random_bytes(32)); // 64 hex characters, SHA‑256 compatible
        $expires = time() + ($ttl ?? $this->defaultTtl);
        $_SESSION[$this->sessionKey][$token] = $expires;
        return $token;
    }

    /**
     * Output an HTML hidden input field with the CSRF token.
     * 
     * @param int|null $ttl  Optional TTL for this token
     * @return string  HTML: <input type="hidden" name="csrf_token" value="...">
     */
    public function field(?int $ttl = null): string
    {
        $token = $this->generate($ttl);
        return sprintf('<input type="hidden" name="csrf_token" value="%s">', htmlspecialchars($token));
    }

    /**
     * Validate a submitted CSRF token.
     * 
     * @param string $token  Usually from $_POST['csrf_token'] or custom header
     * @return bool  True if token exists, not expired, and (optionally) removed after check
     */
    public function validate(string $token): bool
    {
        $this->cleanExpiredTokens();

        if (!isset($_SESSION[$this->sessionKey][$token])) {
            return false;
        }

        $expires = $_SESSION[$this->sessionKey][$token];
        if ($expires < time()) {
            unset($_SESSION[$this->sessionKey][$token]);
            return false;
        }

        if ($this->regenerateAfterValidation) {
            unset($_SESSION[$this->sessionKey][$token]);
        }

        return true;
    }

    /**
     * Remove all expired tokens from session.
     * 
     * @return int  Number of tokens removed
     */
    public function cleanExpiredTokens(): int
    {
        $now = time();
        $removed = 0;
        foreach ($_SESSION[$this->sessionKey] as $token => $expires) {
            if ($expires < $now) {
                unset($_SESSION[$this->sessionKey][$token]);
                $removed++;
            }
        }
        return $removed;
    }

    /**
     * Get all currently valid tokens (for debugging).
     * 
     * @return array  Associative array token => expires timestamp
     */
    public function getAllValidTokens(): array
    {
        $this->cleanExpiredTokens();
        return $_SESSION[$this->sessionKey];
    }
}