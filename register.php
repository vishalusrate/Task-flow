<?php
require_once 'config.php';
if (isLoggedIn()) { header('Location: pages/dashboard.php'); exit; }

$error = $success = '';
$currencies = ['INR'=>'₹','USD'=>'$','EUR'=>'€','GBP'=>'£','AED'=>'د.إ','JPY'=>'¥'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $whatsapp = sanitize($_POST['whatsapp'] ?? '');
    $currency = $_POST['currency'] ?? 'INR';
    $symbol   = $currencies[$currency] ?? '₹';
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password2'] ?? '';

    if (!$name || !$email || !$pass) {
        $error = 'Name, email aur password required ahe.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif (strlen($pass) < 6) {
        $error = 'Password minimum 6 characters pahije.';
    } elseif ($pass !== $pass2) {
        $error = 'Passwords match hot nahi.';
    } else {
        $db    = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'He email already registered ahe.';
        } else {
            $token  = bin2hex(random_bytes(32));
            $hashed = password_hash($pass, PASSWORD_BCRYPT);
            $db->prepare("INSERT INTO users (name,email,password,phone,whatsapp,currency,currency_symbol,is_verified,verify_token)
                          VALUES (?,?,?,?,?,?,?,?,?)")
               ->execute([$name,$email,$hashed,$phone,$whatsapp,$currency,$symbol,0,$token]);

            // Try send verification mail — if fails, auto-verify for easy setup
            $mailSent = false;
            if (file_exists('vendor/autoload.php')) {
                require_once 'includes/mailer.php';
                $mailSent = sendVerificationEmail($email, $name, $token);
            }
            if (!$mailSent) {
                // Auto-verify if mail not configured
                $db->prepare("UPDATE users SET is_verified=1, verify_token=NULL WHERE email=?")->execute([$email]);
                $success = '✅ Account tayaar! Mail configure nahi ahe, tyamule auto-verified kele. Login kara.';
            } else {
                $success = '✅ Account tayaar! Tumchya email var verification link pathavla ahe.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register — TaskFlow Pro</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#0b0b0f;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.card{background:#13131a;border:1px solid #25252f;border-radius:16px;padding:2.5rem;width:100%;max-width:500px}
.logo{text-align:center;margin-bottom:2rem}
.logo-box{width:52px;height:52px;background:#ff6b35;border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:900;color:#000;font-size:1rem;margin:0 auto .75rem}
.logo h1{color:#e8e8f0;font-size:1.4rem}.logo h1 span{color:#ff6b35}
.row{display:flex;gap:1rem}
.group{display:flex;flex-direction:column;flex:1;margin-bottom:.875rem}
label{color:#5a5a72;font-size:.78rem;font-weight:700;margin-bottom:.35rem}
input,select{background:#0b0b0f;border:1px solid #25252f;border-radius:7px;color:#e8e8f0;padding:.6rem .85rem;font-size:.88rem;width:100%;outline:none}
input:focus,select:focus{border-color:#ff6b35}
select option{background:#13131a}
.btn{width:100%;background:#ff6b35;color:#000;border:none;border-radius:7px;padding:.75rem;font-size:.95rem;font-weight:700;cursor:pointer;margin-top:.5rem}
.btn:hover{background:#ff8a5b}
.links{text-align:center;margin-top:1rem;font-size:.85rem;color:#5a5a72}
.links a{color:#00e5ff}
.err{background:rgba(255,71,87,.12);border:1px solid rgba(255,71,87,.3);color:#ff4757;padding:.75rem 1rem;border-radius:7px;font-size:.85rem;margin-bottom:1rem}
.ok{background:rgba(0,196,140,.12);border:1px solid rgba(0,196,140,.3);color:#00c48c;padding:.75rem 1rem;border-radius:7px;font-size:.85rem;margin-bottom:1rem}
@media(max-width:480px){.row{flex-direction:column}}
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-box">TF</div>
    <h1>TaskFlow <span>Pro</span></h1>
    <p>Navin account banava</p>
  </div>
  <?php if ($error): ?><div class="err">❌ <?= e($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="ok"><?= e($success) ?></div><div class="links"><a href="login.php">Login kara →</a></div>
  <?php else: ?>
  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
    <div class="row">
      <div class="group"><label>Purn Nav *</label><input type="text" name="name" placeholder="Ramesh Patil" required></div>
      <div class="group"><label>Email *</label><input type="email" name="email" placeholder="you@email.com" required></div>
    </div>
    <div class="row">
      <div class="group"><label>Phone</label><input type="text" name="phone" placeholder="+91 9876543210"></div>
      <div class="group"><label>WhatsApp Number</label><input type="text" name="whatsapp" placeholder="+91 9876543210"></div>
    </div>
    <div class="group">
      <label>Currency</label>
      <select name="currency">
        <?php foreach ($currencies as $code => $sym): ?>
        <option value="<?= $code ?>"><?= $code ?> (<?= $sym ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="row">
      <div class="group"><label>Password *</label><input type="password" name="password" placeholder="Min 6 chars" required></div>
      <div class="group"><label>Confirm Password *</label><input type="password" name="password2" placeholder="Repeat" required></div>
    </div>
    <button type="submit" class="btn">Account Banava →</button>
  </form>
  <?php endif; ?>
  <div class="links" style="margin-top:1rem">Already account ahe? <a href="login.php">Login kara</a></div>
</div>
</body>
</html>
