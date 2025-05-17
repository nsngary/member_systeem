<?php
session_start();
require_once 'secure_headers.php';
require_once 'csrf.php';
require_once 'db_config.php';
require_once 'util.php';

if (isset($_SESSION['uid'])) {
    // 已登入直接導向
    header('Location: ' . ($_SESSION['role']==='admin'
            ? 'admin_dashboard.php' : 'user_dashboard.php'));
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $u   = trim($_POST['username'] ?? '');
    $p   = $_POST['password'] ?? '';
    $ans = intval($_POST['captcha'] ?? -1);

    if ($ans !== ($_SESSION['captcha_answer'] ?? -999)) {
        set_flash('error','驗證碼錯誤，請重試');
    } else {
        $stmt = $conn->prepare(
            'SELECT user_id, password_hash, role
               FROM users WHERE username = ?'
        );
        $stmt->bind_param('s', $u);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();              
        // :contentReference[oaicite:0]{index=0}:contentReference[oaicite:1]{index=1}

        if ($row && password_verify($p, $row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['uid']  = $row['user_id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['CREATED'] = $_SESSION['LAST_ACTIVITY'] = time();
            header('Location: '.($row['role']==='admin'
                                ? 'admin_dashboard.php'
                                : 'user_dashboard.php'));
            exit;
        }
        set_flash('error','帳號或密碼錯誤');
    }
    header('Location: login.php');
    exit;
}
?>
<!doctype html><html lang="zh-Hant-TW"><head>
<meta charset="utf-8"><title>登入</title>
<link rel="stylesheet" href="style.css">
<?php flash_js(); ?>
</head><body>
<h2>會員系統登入</h2>

<form method="POST">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
  帳號：<input name="username" placeholder="請輸入帳號" required autofocus><br>
  密碼：<input type="password" name="password" placeholder="需含有數字/英文" required><br>
  驗證碼：<img src="captcha.php?<?= time() ?>"> =
  <input name="captcha" size="2" required><br><br>&nbsp;
  <button type="submit" class="btn">登入</button>
  &nbsp;&nbsp;&nbsp;&nbsp;
  <a class="btn" href="register.php">註冊</a>
</form>

<script src="flash.js"></script>
</body></html>
