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
    <title><?= htmlspecialchars($config['site_name']) ?></title>
</head>
<body>
    <h1><?= $config['site_name'] ?></h1>
    <p><?= $config['site_description'] ?></p>
    <h2>博客文章</h2>
    <?php 
    foreach (array_reverse($posts) as $post): 
    ?>
        <div>
            <h3><?= $post['title'] ?></h3>
            <p><?= $post['date'] ?></p>
            <p><?= $post['content'] ?></p>
        </div>
    <?php endforeach; ?>
</body>
</html>