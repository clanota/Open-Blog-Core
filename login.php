<?php
session_start();
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!password_verify($_POST['password'], $config['admin_password'])) {
        die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>Wrong password</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        管理员密码错误
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        好好想想看是不是记错了
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
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
    <div class="mdui-card-primary-subtitle">请输入管理员密码</div>
    </div>
    <div class="mdui-card-content">
    <form method="post">
    <div class="mdui-textfield">
    <label class="mdui-textfield-label">管理员密码</label>
    <input type="password" name="password" class="mdui-textfield-input" placeholder="管理员密码" required>
    </div>
    <div class="mdui-card-actions">
        <div class="mdui-row-xs-2">
            <div class="mdui-col">
                <button type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">登录后台</button>
            </div>
            <div class="mdui-col">
                <a href="index.php" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">返回首页</a>
            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div>
</body>
</html>