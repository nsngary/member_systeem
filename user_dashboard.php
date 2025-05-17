<?php
require_once 'auth_check.php';
require_role(['user']);
require_once 'csrf.php';
require_once 'db_config.php';
require_once 'util.php';

$stmt=$conn->prepare(
  'SELECT id,name,email,member_type,member_id FROM members
     WHERE user_id = ? ORDER BY member_type,seq'
);
$stmt->bind_param('i',$_SESSION['uid']);
$stmt->execute();
$rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html lang="zh-Hant-TW"><head>
<meta charset="utf-8"><title>我的會員資料</title>
<link rel="stylesheet" href="style.css">
<?php flash_js(); ?>
</head><body>
<a href="logout.php" class="btn btn-logout" style="float:right">登出</a>
<h2>我的會員資料</h2>

<a class="btn" href="add_member.php">➕ 新增資料</a><br><br>

<table class="table">
  <thead><tr><th>#</th><th>姓名</th><th>Email</th><th>類型</th><th>操作</th></tr></thead>
  <tbody>
<?php foreach($rows as $r): ?>
  <tr>
    <td><?= $r['member_id'] ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['email']) ?></td>
    <td><?= $r['member_type'] ?></td>
    <td>
      <a class="btn" href="edit_member.php?id=<?= $r['id'] ?>">編輯</a>
    </td>
  </tr>
<?php endforeach ?>
  </tbody>
</table>

<script src="flash.js"></script>
</body></html>
