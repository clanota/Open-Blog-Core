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
    $config['qq'] = $_POST['qq'];
    $config['custom_css'] = $_POST['custom_css'] ?? '';
    $config['custom_js'] = $_POST['custom_js'] ?? '';
    file_put_contents(__DIR__.'/config.json', json_encode($config, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}
if (isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!password_verify($oldPassword, $config['admin_password'])) {
        die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>The old password is wrong</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        旧密码错误
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        如果真忘了就把config.json删了吧...
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
    }

    if ($newPassword !== $confirmPassword) {
        die('<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <meta name="renderer" content="webkit">
        <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
        <title>Passwords do not match</title>
    </head>
    <body>
        <div class="mdui-container"><br>
            <div class="mdui-card">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">
                        密码不一致
                    </div>
                    <div class="mdui-card-primary-subtitle">
                        仔细检查一下，别打错了
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script src="https://npm.elemecdn.com/mdui@1.0.2/dist/js/mdui.min.js"></script>
    </body>
</html>');
    }

    $config['admin_password'] = password_hash($newPassword, PASSWORD_DEFAULT);
    file_put_contents(__DIR__.'/config.json', json_encode($config, JSON_PRETTY_PRINT));
    
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['edit'])) {
        foreach ($posts as &$post) {
            if ($post['id'] == $_GET['edit']) {
                $post['title'] = $_POST['title'];
                $post['content'] = $_POST['content'];
                break;
            }
        }
    } else {
        $newPost = [
            'id' => time(),
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'date' => isset($_POST['date']) ? $_POST['date'] : date('Y-m-d H:i:s')
        ];
        array_push($posts, $newPost);
    }
    file_put_contents($config['db_path'], json_encode($posts));
    header('Location: admin.php');
    exit;
}

if (isset($_GET['edit'])) {
    $editPost = null;
    foreach ($posts as $post) {
        if ($post['id'] == $_GET['edit']) {
            $editPost = $post;
            break;
        }
    }
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="renderer" content="webkit">
    <link rel="stylesheet" href="https://npm.elemecdn.com/mdui@1.0.2/dist/css/mdui.min.css">
    <title>管理员面板</title>
</head>
<body>
    <div class="mdui-container">
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">网站配置</div>
    <div class="mdui-card-primary-subtitle">设置标题与描述</div>
    </div>
    <div class="mdui-card-content">
    <form method="post" action="?config=1">
    站点名称：
    <input type="text" name="site_name" value="<?= $config['site_name'] ?? '' ?>" class="mdui-textfield-input" placeholder="站点名称" required><br>
    站点描述：
    <textarea name="site_description" class="mdui-textfield-input" placeholder="站点描述" required style="min-height: 1em; overflow: hidden; resize: none;" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 500) + 'px'"><?= $config['site_description'] ?? '' ?></textarea>
    <br>
    QQ号：
    <input type="text" name="qq" placeholder="QQ号" class="mdui-textfield-input" value="<?= $config['qq'] ?? '' ?>" required><br>
    自定义CSS：
    <textarea name="custom_css" class="mdui-textfield-input" placeholder="自定义CSS代码" style="min-height: 1em; overflow: hidden; resize: none;" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 500) + 'px'"><?= $config['custom_css'] ?? '' ?></textarea><br>
    自定义JS：
    <textarea name="custom_js" class="mdui-textfield-input" placeholder="自定义JavaScript代码" style="min-height: 1em; overflow: hidden; resize: none;" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 500) + 'px'"><?= $config['custom_js'] ?? '' ?></textarea><br>
    <button type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">保存配置</button>
    </form>
    </div>
    </div>
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">登录管理</div>
    <div class="mdui-card-primary-subtitle">管理你的登录状态和密码</div>
    </div>
    <div class="mdui-card-content">
    <form method="post">
        旧密码：
        <input type="password" name="old_password" placeholder="旧密码" class="mdui-textfield-input" required><br>
        新密码：
        <input type="password" name="new_password" placeholder="新密码" class="mdui-textfield-input" required><br>
        确认新密码：
        <input type="password" name="confirm_password" placeholder="确认新密码" class="mdui-textfield-input" required>
        <br>
    <div class="mdui-row-xs-3">
        <div class="mdui-col">
        <button type="submit" name="change_password" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">修改密码</button>
        </div>
        <div class="mdui-col">
        <a href="index.php" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">前往首页</a>
        </div>
        <div class="mdui-col">
        <a href="admin.php?logout=1" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">退出登录</a>
        </div>
        </div>
    </form>
    </div>
    </div>
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">文章编辑</div>
    <div class="mdui-card-primary-subtitle">编辑与发布你的文章</div>
    </div>
    <div class="mdui-card-content">
    <form method="post">
       标题：
        <input type="text" name="title" placeholder="标题" class="mdui-textfield-input" value="<?= isset($editPost) ? $editPost['title'] : '' ?>" required><br>
        内容：
        <textarea name="content" placeholder="内容" class="mdui-textfield-input" required style="min-height: 1em; overflow: hidden; resize: none;" oninput="this.style.height = ''; this.style.height = Math.min(this.scrollHeight, 500) + 'px'"><?= isset($editPost) ? $editPost['content'] : '' ?></textarea>
        <?php if (isset($editPost)): ?>
        <input type="hidden" name="date" value="<?= $editPost['date'] ?>">
        <?php endif; ?>
        <br>
        <button type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">发布文章</button>
    </form>
   </div>
   </div>
   <br>
   <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">文章列表</div>
    <div class="mdui-card-primary-subtitle">管理你的文章</div>
    </div>
    <div class="mdui-card-content">
    <?php 
    foreach (array_reverse($posts) as $post): 
    ?>
        <div>
            <h4><?= $post['title'] ?></h4>
            <p><?= $post['date'] ?></p>
            <p><?= $post['content'] ?></p>
            <div class="mdui-row-xs-2">
            <div class="mdui-col">
            <a href="?delete=<?= $post['id'] ?>" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">删除</a>
            </div>
            <div class="mdui-col">
            <a href="?edit=<?= $post['id'] ?>" class="mdui-btn mdui-btn-raised mdui-ripple mdui-btn-block">编辑</a>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    </div>
    </div>
    <br>
    <div class="mdui-card">
    <div class="mdui-card-primary">
    <div class="mdui-card-primary-title">致谢</div>
    <div class="mdui-card-primary-subtitle">感谢那些为OBC开发作出贡献的个人/项目</div>
    </div>
    <div class="mdui-card-content">
     Markdown处理：Prasedown.php<br>
     前端框架：Mdui V1<br>
     前端编写：曦予<br>
     后端编写：Trae IDE & 曦予<br>
     头像API：Tencent QQ<br>
     当前版本：V25.3.31<br>
     『愿一生可爱，一生被爱』
    </div>
    </div>
    </div>
    <br>
</body>
</html>