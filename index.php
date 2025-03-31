<?php
if (!file_exists(__DIR__ . '/config.json')) {
    header('Location: install.php');
    exit;
}
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
if (!file_exists($config['db_path'])) {
    file_put_contents($config['db_path'], json_encode([], JSON_PRETTY_PRINT));
}
$posts = json_decode(file_get_contents($config['db_path']), true) ?? [];
require_once __DIR__ . '/Parsedown.php';
$parsedown = new Parsedown();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <?php if (!empty($config['custom_css'])): ?>
    <style><?= $config['custom_css'] ?></style>
    <?php endif; ?>
    <title><?= htmlspecialchars($config['site_name']) ?></title>
</head>
<body>
    <div class="mdui-container">
    <br>
    <div class="mdui-card">
    <div class="mdui-card-header">
    <img class="mdui-card-header-avatar" src="http://q.qlogo.cn/headimg_dl?dst_uin=<?= $config['qq'] ?? '000000' ?>&spec=640&img_type=jpg" onclick="window.location.href='admin.php'"/>
    <div class="mdui-card-header-title"><?= $config['site_name'] ?></div>
    <div class="mdui-card-header-subtitle"><?= $config['site_description'] ?></div>
    </div>
    </div>
    <br>
    <?php 
    foreach (array_reverse($posts) as $post): 
    ?>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title"><?= $post['title'] ?></div>
    <div class="mdui-card-primary-subtitle"><?= $post['date'] ?></div>
    </div>
    <div class="mdui-card-content">
    <?php echo $parsedown->text($post['content']); ?>
    </div>
    </div>
    <br>
    <?php endforeach; ?>
    <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    <?php if (!empty($config['custom_js'])): ?>
    <script><?= $config['custom_js'] ?></script>
    <?php endif; ?>
</body>
</html>