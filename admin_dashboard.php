<?php
require_once 'auth_check.php';
require_role(['admin','user']);
require_once 'csrf.php';
require_once 'db_config.php';
require_once 'util.php';

$uid  = $_SESSION['uid'];
$role = $_SESSION['role'];

if ($role==='admin') {
    $stmt=$conn->prepare(
      'SELECT id,name,email,member_type,member_id FROM members
         ORDER BY member_type,seq'
    );
} else {
    $stmt=$conn->prepare(
      'SELECT id,name,email,member_type,member_id FROM members
         WHERE user_id = ? ORDER BY member_type,seq'
    );
    $stmt->bind_param('i',$uid);
}
$stmt->execute();
$rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);          // :contentReference[oaicite:4]{index=4}:contentReference[oaicite:5]{index=5}
?>
<!DOCTYPE html><html lang="zh-Hant-TW"><head>
<meta charset="utf-8"><title>會員列表</title>
<link rel="stylesheet" href="style.css?v=<?= htmlspecialchars(filemtime(__DIR__.'/style.css')) ?>">
<?php flash_js(); ?>
</head><body>

<a href="logout.php" class="btn btn-logout" style="float:right">登出</a>
<h2>會員列表</h2>
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
      <form method="POST" action="delete_member.php"
            data-confirm="確定要刪除『<?= htmlspecialchars($r['name']) ?>』？"
            style="display:inline">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
        <input type="hidden" name="member_id" value="<?= $r['id'] ?>">
        <button type="submit" class="btn btn-danger">刪除</button>
      </form>
    </td>
  </tr>
<?php endforeach ?>
  </tbody>
</table>

<script src="confirm.js"></script>
<script src="flash.js"></script>
</body></html>
