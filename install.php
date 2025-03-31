<?php
if (file_exists(__DIR__ . '/config.json')) {
    $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    if ($config['installed'] ?? false) {
        die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>Already installed</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        已经安装过了
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        如果需要再次安装就把config.json删了吧
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    if (empty($_POST['password'])) {
        die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>The password is empty</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        管理员密码为空
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        设一个密码吧...
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
    }
    $config = [
        'site_name' => $_POST['site_name'],
        'site_description' => $_POST['site_description'],
        'db_path' => $dataDir . '/posts.json',
        'installed' => true,
        'admin_password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'qq' => $_POST['qq']
    ];
    $jsonConfig = json_encode($config, JSON_PRETTY_PRINT);
    file_put_contents(__DIR__ . '/config.json', $jsonConfig);
    file_put_contents($config['db_path'], json_encode([], JSON_PRETTY_PRINT));
    header('Location: index.php');
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
    <title>安装向导</title>
</head>
<body>
    <div class="mdui-container">
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">安装向导</div>
    <div class="mdui-card-primary-subtitle">感谢安装Open-Blog-Core-GUI</div>
    </div>
    <div class="mdui-container">
    <form method="post">
        站点名称：
        <input type="text" name="site_name" placeholder="站点名称" class="mdui-textfield-input" required><br>
        站点描述：
        <textarea name="site_description" class="mdui-textfield-input" placeholder="站点描述" required style="min-height: 1em; overflow: hidden; resize: none;" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 500) + 'px'"></textarea><br>
        管理员密码：
        <input type="password" name="password" placeholder="管理员密码" class="mdui-textfield-input" required><br>
        QQ号：
        <input type="text" name="qq" placeholder="QQ号" class="mdui-textfield-input" required>
        <br>
        <button type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">立即安装</button>
        <br>
    </form>
    <div>
    </div>
</body>
</html>