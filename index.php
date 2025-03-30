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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title><?= htmlspecialchars($config['site_name']) ?></title>
</head>
<body>
    <div class="mdui-container">
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title"><?= $config['site_name'] ?></div>
    <div class="mdui-card-primary-subtitle"><?= $config['site_description'] ?></div>
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
    <?= $post['content'] ?>
    </div>
    </div>
    <br>
    <?php endforeach; ?>
</body>
</html>