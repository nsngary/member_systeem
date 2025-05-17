<?php
session_start();
require_once 'secure_headers.php';
require_once 'csrf.php';
require_once 'db_config.php';
require_once 'util.php';

if (isset($_SESSION['uid'])) {
    header('Location: user_dashboard.php'); exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf_token();

    $u   = trim($_POST['username'] ?? '');
    $p1  = $_POST['password']  ?? '';
    $p2  = $_POST['password2'] ?? '';
    $ans = intval($_POST['captcha'] ?? -1);

    if ($ans !== ($_SESSION['captcha_answer'] ?? -999)) {
        set_flash('error','驗證碼錯誤'); goto out;
    }
    if ($u==='' || $p1==='' || $p1!==$p2) {
        set_flash('error','資料不完整或密碼不一致'); goto out;
    }
    /* 使用者名稱唯一檢查 */
    $chk=$conn->prepare('SELECT 1 FROM users WHERE username=?');
    $chk->bind_param('s',$u); $chk->execute();
    if ($chk->get_result()->fetch_row()) {
        set_flash('error','帳號已存在'); goto out;
    }
    $hash=password_hash($p1,PASSWORD_DEFAULT);
    $ins=$conn->prepare('INSERT INTO users (username,password_hash,role) VALUES (?,?,?)');
    $role='user';
    $ins->bind_param('sss',$u,$hash,$role);
    $ins->execute();

    set_flash('success','註冊成功，請登入');
    header('Location: login.php'); exit;
}
out:
?>
<!doctype html><html lang="zh-Hant-TW"><head>
<meta charset="utf-8"><title>註冊</title>
<link rel="stylesheet" href="style.css">
<?php flash_js(); ?>
</head><body>
<h2>註冊帳號</h2>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
  帳號：<input name="username" placeholder="請輸入帳號" required><br>
  密碼：<input type="password" name="password" placeholder="需含有數字/英文" required><br>
  再次輸入密碼：<input type="password" name="password2" placeholder="需含有數字/英文" required><br>
  驗證碼：<img src="captcha.php?<?= time() ?>"> =
  <input name="captcha" size="2" required><br>
  <button type="submit" class="btn">送出</button>
</form>

<script src="flash.js"></script>
</body></html>
