<?php
require_once 'config.php';
$msg = $type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = sanitize($_POST['email'] ?? '');
    $db    = getDB();
    $stmt  = $db->prepare("SELECT id, name FROM users WHERE email=? AND is_verified=1");
    $stmt->execute([$email]);
    $user  = $stmt->fetch();
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $db->prepare("UPDATE users SET reset_token=?, reset_expires=DATE_ADD(NOW(),INTERVAL 1 HOUR) WHERE id=?")
           ->execute([$token, $user['id']]);
        $link = APP_URL . '/reset.php?token=' . urlencode($token);
        if (file_exists('vendor/autoload.php')) {
            require_once 'includes/mailer.php';
            $body = '<p>Hi '.$user['name'].',</p><p>Password reset link:</p><a href="'.$link.'" style="color:#ff6b35">'.$link.'</a><p>1 tasyadhik valid ahe.</p>';
            sendMail($email, $user['name'], 'TaskFlow Pro — Password Reset', $body);
        }
        $msg = '✅ Reset link pathavla (mail configure asel tar). Link: <a href="'.$link.'" style="color:#00e5ff">Click here</a>';
        $type = 'ok';
    } else {
        $msg = '❌ He email registered nahi ya verified nahi.'; $type = 'err';
    }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Forgot Password</title>
<style>*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial;background:#0b0b0f;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#13131a;border:1px solid #25252f;border-radius:16px;padding:2rem;width:100%;max-width:400px}
h2{color:#e8e8f0;margin-bottom:1.5rem}
label{display:block;color:#5a5a72;font-size:.8rem;font-weight:700;margin-bottom:.4rem}
input{width:100%;background:#0b0b0f;border:1px solid #25252f;border-radius:7px;color:#e8e8f0;padding:.65rem .9rem;font-size:.9rem;margin-bottom:1rem;outline:none}
input:focus{border-color:#ff6b35}
.btn{width:100%;background:#ff6b35;color:#000;border:none;border-radius:7px;padding:.7rem;font-weight:700;cursor:pointer}
.ok{background:rgba(0,196,140,.12);border:1px solid rgba(0,196,140,.3);color:#00c48c;padding:.75rem;border-radius:7px;margin-bottom:1rem;font-size:.85rem}
.err{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.3);color:#ff4757;padding:.75rem;border-radius:7px;margin-bottom:1rem;font-size:.85rem}
a{color:#00e5ff;font-size:.85rem;display:block;text-align:center;margin-top:1rem}</style></head>
<body><div class="card">
  <h2>🔑 Password Reset</h2>
  <?php if ($msg): ?><div class="<?= $type ?>"><?= $msg ?></div><?php endif; ?>
  <?php if (!$msg): ?>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <label>Tumcha registered email</label>
    <input type="email" name="email" placeholder="you@email.com" required>
    <button type="submit" class="btn">Reset Link Pathava →</button>
  </form>
  <?php endif; ?>
  <a href="login.php">← Login var para ja</a>
</div></body></html>
