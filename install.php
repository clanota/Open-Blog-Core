<?php
if (file_exists(__DIR__ . '/config.json')) {
    $config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
    if ($config['installed'] ?? false) {
        die('系统已安装，请勿重复执行安装程序！');
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    if (empty($_POST['password'])) {
        die('密码不能为空');
    }
    $config = [
        'site_name' => $_POST['site_name'],
        'site_description' => $_POST['site_description'],
        'db_path' => $dataDir . '/posts.json',
        'installed' => true,
        'admin_password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
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
    <title>博客安装向导</title>
</head>
<body>
    <h2>博客安装向导</h2>
    <form method="post">
        <p>站点名称：</p>
        <input type="text" name="site_name" required>
        <p>站点描述：</p>
        <textarea name="site_description" required></textarea>
        <p>设置管理员密码：</p>
        <input type="password" name="password" required>
        <button type="submit">立即安装</button>
    </form>
</body>
</html>