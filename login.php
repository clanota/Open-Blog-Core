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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>管理员登录</title>
</head>
<body>
    <div class="mdui-container">
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">管理员登录</div>
    <div class="mdui-card-primary-subtitle">请输入密码</div>
    </div>
    <div class="mdui-container">
    <form method="post">
    <div class="mdui-textfield">
    <input type="password" name="password" placeholder="管理员密码" class="mdui-textfield-input" required>
    </div>
    <button type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">登录后台</button>
    <br>
    </form>
    </div>
    </div>
    </div>
</body>
</html>