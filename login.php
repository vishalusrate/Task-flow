<?php
require_once 'config.php';
if (isLoggedIn()) { header('Location: pages/dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email = sanitize($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $db    = getDB();
    $stmt  = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user  = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
        if (!$user['is_verified']) {
            $error = 'Pehle email verify kara. Inbox check kara.';
        } else {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user']      = $user;
            flash('success', 'Welcome, ' . $user['name'] . '! 👋');
            header('Location: pages/dashboard.php');
            exit;
        }
    } else {
        $error = 'Email ya password chukiche ahe.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login — TaskFlow Pro</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#0b0b0f;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#13131a;border:1px solid #25252f;border-radius:16px;padding:2.5rem;width:100%;max-width:400px}
.logo{text-align:center;margin-bottom:2rem}
.logo-box{width:52px;height:52px;background:#ff6b35;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:900;color:#000;font-size:1rem;margin:0 auto .75rem}
.logo h1{color:#e8e8f0;font-size:1.4rem}.logo h1 span{color:#ff6b35}
.logo p{color:#5a5a72;font-size:.85rem;margin-top:.25rem}
label{display:block;color:#5a5a72;font-size:.8rem;font-weight:700;margin-bottom:.4rem}
input{width:100%;background:#0b0b0f;border:1px solid #25252f;border-radius:7px;color:#e8e8f0;padding:.65rem .9rem;font-size:.9rem;margin-bottom:1rem;outline:none}
input:focus{border-color:#ff6b35}
.btn{width:100%;background:#ff6b35;color:#000;border:none;border-radius:7px;padding:.75rem;font-size:.95rem;font-weight:700;cursor:pointer;margin-top:.25rem}
.btn:hover{background:#ff8a5b}
.links{text-align:center;margin-top:1.25rem;font-size:.85rem;color:#5a5a72;display:flex;justify-content:center;gap:.75rem}
.links a{color:#00e5ff}
.err{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.3);color:#ff4757;padding:.75rem 1rem;border-radius:7px;font-size:.85rem;margin-bottom:1rem}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-box">TF</div>
    <h1>TaskFlow <span>Pro</span></h1>
    <p>Apla account madhe sign in kara</p>
  </div>
  <?php if ($error): ?><div class="err">❌ <?= e($error) ?></div><?php endif; ?>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <label>Email</label>
    <input type="email" name="email" placeholder="you@example.com" required autocomplete="email">
    <label>Password</label>
    <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
    <button type="submit" class="btn">Sign In →</button>
  </form>
  <div class="links">
    <a href="forgot.php">Forgot password?</a>
    <span>·</span>
    <a href="register.php">Register</a>
  </div>
</div>
</body>
</html>
