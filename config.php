<?php
// ============================================================
// config.php — Edit DB credentials here, rest leave as is
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow');
define('DB_USER', 'root');        // ← apla MySQL username
define('DB_PASS', '');            // ← apla MySQL password (XAMPP madhe riku asel)
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'TaskFlow Pro');
define('APP_URL', 'http://localhost/taskflow');

// Gmail SMTP
define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'your@gmail.com');      // ← change
define('MAIL_PASSWORD',   'your_app_password');   // ← Gmail App Password
define('MAIL_FROM_NAME',  'TaskFlow Pro');
define('MAIL_FROM_EMAIL', 'your@gmail.com');       // ← change

// Twilio WhatsApp
define('TWILIO_SID',      'ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // ← change
define('TWILIO_TOKEN',    'your_auth_token');                    // ← change
define('TWILIO_WHATSAPP', 'whatsapp:+14155238886');

// Push Notifications VAPID (https://vapidkeys.com)
define('VAPID_PUBLIC_KEY',  'your_vapid_public_key');
define('VAPID_PRIVATE_KEY', 'your_vapid_private_key');
define('VAPID_SUBJECT',     'mailto:your@gmail.com');

// ============================================================
// Functions — don't edit below
// ============================================================

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
        } catch (PDOException $e) {
            die('<div style="font-family:Arial;padding:2rem;background:#0d0d0d;color:#ff4757;">
                <h2>⚠️ Database Error</h2>
                <p>'.$e->getMessage().'</p>
                <p>config.php madhe DB_USER aur DB_PASS check kara.</p>
                <a href="login.php" style="color:#00e5ff;">← Login</a>
                </div>');
        }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool  { return isset($_SESSION['user_id']); }
function uid(): int          { return (int)($_SESSION['user_id'] ?? 0); }
function currentUser(): array { return $_SESSION['user'] ?? []; }

function requireLogin(): void {
    if (!isLoggedIn()) { header('Location: '.APP_URL.'/login.php'); exit; }
}
function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: '.APP_URL.'/pages/dashboard.php'); exit;
    }
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
function getFlash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function e(mixed $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function sanitize(string $s): string {
    return trim(strip_tags($s));
}

function csrfToken(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verifyCsrf(): void {
    if (!isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf_token'] ?? '')) {
        die('Security check failed. Please go back and try again.');
    }
}
