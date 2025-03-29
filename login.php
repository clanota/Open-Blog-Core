<?php
session_start();
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!password_verify($_POST['password'], $config['admin_password'])) {
        die('密码错误');
    }
    $_SESSION['logged_in'] = true;
    $_SESSION['password_hash'] = $config['admin_password'];
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>管理员登录</title>
</head>
<body>
    <h2>管理员登录</h2>
    <form method="post">
        <input type="password" name="password" placeholder="管理员密码" required>
        <button type="submit">登录</button>
    </form>
</body>
</html>