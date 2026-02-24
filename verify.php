<?php
require_once 'config.php';
$token = sanitize($_GET['token'] ?? '');
$msg = $type = '';
if ($token) {
    $db   = getDB();
    $stmt = $db->prepare("SELECT id FROM users WHERE verify_token=? AND is_verified=0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    if ($user) {
        $db->prepare("UPDATE users SET is_verified=1, verify_token=NULL WHERE id=?")->execute([$user['id']]);
        $msg = '✅ Email verified! Aata login kara.'; $type = 'ok';
    } else {
        $msg = '❌ Invalid ya already used verification link.'; $type = 'err';
    }
} else {
    $msg = '❌ Token missing.'; $type = 'err';
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Verify</title>
<style>body{font-family:Arial;background:#0b0b0f;color:#e8e8f0;display:flex;align-items:center;justify-content:center;min-height:100vh}
.card{background:#13131a;border:1px solid #25252f;padding:2rem;border-radius:12px;text-align:center;max-width:400px}
.ok{color:#00c48c} .err{color:#ff4757}
a{color:#00e5ff;display:block;margin-top:1rem}</style></head>
<body><div class="card">
<div class="<?= $type ?>"><?= e($msg) ?></div>
<a href="login.php">← Login kara</a>
</div></body></html>
