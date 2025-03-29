<?php
if (!file_exists(__DIR__ . '/config.json')) {
    header('Location: install.php');
    exit;
}
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
if (!isset($config['installed']) || !$config['installed']) {
    header('Location: install.php');
    exit;
}
$posts = json_decode(file_get_contents($config['db_path']), true) ?? [];
if (isset($_GET['config']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $config['site_name'] = $_POST['site_name'];
    $config['site_description'] = $_POST['site_description'];
    file_put_contents(__DIR__.'/config.json', json_encode($config, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
if (isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!password_verify($oldPassword, $config['admin_password'])) {
        die('旧密码错误');
    }

    if ($newPassword !== $confirmPassword) {
        die('两次输入的密码不一致');
    }

    $config['admin_password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    file_put_contents(__DIR__.'/config.json', json_encode($config, JSON_PRETTY_PRINT));
    
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPost = [
        'id' => time(),
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'date' => date('Y-m-d H:i:s')
    ];
    array_push($posts, $newPost);
    file_put_contents($config['db_path'], json_encode($posts));
    header('Location: admin.php');
    exit;
}
if (isset($_GET['delete'])) {
    $posts = array_filter($posts, function($post) {
        return $post['id'] != $_GET['delete'];
    });
    file_put_contents($config['db_path'], json_encode(array_values($posts)));
    header('Location: admin.php');
    exit;
}
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>管理员面板</title>
</head>
<body>
        <h2>网站配置</h2>
    <form method="post" action="?config=1">
        <p>站点名称：</p><input type="text" name="site_name" value="<?= $config['site_name'] ?? '' ?>" required>
        <p>站点描述：</p><textarea name="site_description" required><?= $config['site_description'] ?? '' ?></textarea><br>
        <button type="submit">保存配置</button>
    </form>

    <h2>修改密码</h2>
    <form method="post">
        <input type="password" name="old_password" placeholder="旧密码" required><br>
        <input type="password" name="new_password" placeholder="新密码" required><br>
        <input type="password" name="confirm_password" placeholder="确认新密码" required><br>
        <button type="submit" name="change_password">修改密码</button>
    </form>

    <h2>文章管理</h2>
    <a href="admin.php?logout=1">退出登录</a>
    <form method="post">
        <input type="text" name="title" placeholder="标题" required><br>
        <textarea name="content" placeholder="内容" required></textarea><br>
        <button type="submit">发布文章</button>
    </form>

    <h3>现有文章</h3>
    <?php foreach ($posts as $post): ?>
        <div>
            <h4><?= $post['title'] ?></h4>
            <p><?= $post['date'] ?></p>
            <p><?= $post['content'] ?></p>
            <a href="?delete=<?= $post['id'] ?>">删除</a>
        </div>
    <?php endforeach; ?>
</body>
</html>