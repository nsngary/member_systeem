<?php
require_once 'auth_check.php';
require_role(['admin','user']);
require_once 'csrf.php';
require_once 'util.php';
?>
<!doctype html><html lang="zh-Hant-TW"><head>
<meta charset="utf-8"><title>新增會員</title>
<link rel="stylesheet" href="style.css">
<?php flash_js(); ?>
</head><body>
<a href="logout.php" class="btn btn-logout" style="float:right">登出</a>
<h2>新增會員</h2>

<form method="POST" action="save_member.php" class="form">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
  <label>姓名：
    <input required name="name" placeholder="請輸入姓名">
  </label><br>
  <label>Email：
    <input type="email" required name="email" placeholder="sample@mail.com">
  </label><br>
  <label>會員類型：
    <select name="member_type">
      <option value="VIP">VIP</option>
      <option value="Regular">Regular</option>
    </select>
  </label><br>

  <button type="submit" class="btn">送出</button>
  <a href="admin_dashboard.php" class="btn btn-danger">取消</a>
</form>

<script src="flash.js"></script>
</body></html>
