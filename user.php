<?php
session_start();
ob_start(); // 添加输出缓冲

require_once 'data.php';
require_once 'ThemeManager.php';
require_once 'LayoutManager.php';

$themeManager = new ThemeManager();
$layoutManager = new LayoutManager();

// 检查登录状态
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$dataHandler = new DataHandler();
$data = $dataHandler->getData();

// 获取当前编辑的部分
$section = isset($_GET['section']) ? $_GET['section'] : 'profile';

// 处理主题CSS保存
if (isset($_POST['css_content']) && $section === 'theme-manager') {
    $themesFile = 'themes/main.css';
    if (file_put_contents($themesFile, $_POST['css_content'])) {
        // 记录活动日志
        $logEntry = "update|更新了主题CSS|" . date('Y-m-d H:i:s');
        file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
        header('Location: user.php?section=theme-manager&success=1');
        exit;
    } else {
        $errorMsg = 'CSS保存失败';
    }
}

// 处理布局上传
if (isset($_FILES['layout_upload']) && $section === 'layout-manager') {
    $layoutManager = new LayoutManager();
    $result = $layoutManager->uploadLayout($_FILES['layout_upload']);
    if ($result['success']) {
        // 记录活动日志
        $logEntry = "update|上传了新布局: {$_FILES['layout_upload']['name']}|" . date('Y-m-d H:i:s');
        file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
        header('Location: user.php?section=layout-manager&success=1');
        exit;
    } else {
        $errorMsg = $result['message'];
    }
}

// 处理生成随机布局
if (isset($_POST['generate_layout']) && $section === 'layout-manager') {
    $layoutManager = new LayoutManager();
    $layoutName = $layoutManager->generateRandomLayout();
    // 记录活动日志
    $logEntry = "update|生成了随机布局: $layoutName|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
    header('Location: user.php?section=layout-manager&success=1');
    exit;
}

// 处理删除布局
if (isset($_POST['delete_layout']) && $section === 'layout-manager') {
    $layoutName = $_POST['delete_layout'];
    $layoutManager = new LayoutManager();
    if ($layoutManager->deleteLayout($layoutName)) {
        // 记录活动日志
        $logEntry = "update|删除了布局: $layoutName|" . date('Y-m-d H:i:s');
        file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
        header('Location: user.php?section=layout-manager&success=1');
        exit;
    } else {
        $errorMsg = '删除布局失败';
    }
}

// CSRF保护
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF验证失败');
    }

    // 初始化新数据
    $newData = $data;

    // 处理表单提交
    switch ($section) {
        case 'profile':
            $newData['profile'] = [
                'name' => $_POST['name'],
                'title' => $_POST['title'],
                'bio' => $_POST['bio'],
                'image' => $_POST['image'],
                'social' => [
                    'github' => $_POST['github'],
                    'linkedin' => $_POST['linkedin'],
                    'twitter' => $_POST['twitter'],
                    'instagram' => $_POST['instagram']
                ]
            ];
            break;
            
        case 'skills':
            $newData['skills'] = [];
            foreach ($_POST['skill_name'] as $index => $name) {
                if (!empty($name)) {
                    $newData['skills'][] = [
                        'name' => $name,
                        'level' => $_POST['skill_level'][$index]
                    ];
                }
            }
            break;
            
        case 'projects':
            $newData['projects'] = [];
            foreach ($_POST['project_title'] as $index => $title) {
                if (!empty($title)) {
                    $newData['projects'][] = [
                        'title' => $title,
                        'description' => $_POST['project_description'][$index],
                        'image' => $_POST['project_image'][$index],
                        'link' => $_POST['project_link'][$index]
                    ];
                }
            }
            break;
            
        case 'experience':
            $newData['experience'] = [];
            foreach ($_POST['exp_period'] as $index => $period) {
                if (!empty($period)) {
                    $newData['experience'][] = [
                        'period' => $period,
                        'position' => $_POST['exp_position'][$index],
                        'company' => $_POST['exp_company'][$index],
                        'description' => $_POST['exp_description'][$index]
                    ];
                }
            }
            break;
            
        case 'education':
            $newData['education'] = [];
            foreach ($_POST['edu_period'] as $index => $period) {
                if (!empty($period)) {
                    $newData['education'][] = [
                        'period' => $period,
                        'degree' => $_POST['edu_degree'][$index],
                        'school' => $_POST['edu_school'][$index],
                        'description' => $_POST['edu_description'][$index]
                    ];
                }
            }
            break;
            
        case 'certifications':
            $newData['certifications'] = [];
            foreach ($_POST['cert_name'] as $index => $name) {
                if (!empty($name)) {
                    $newData['certifications'][] = [
                        'name' => $name,
                        'issuer' => $_POST['cert_issuer'][$index],
                        'date' => $_POST['cert_date'][$index],
                        'url' => $_POST['cert_url'][$index]
                    ];
                }
            }
            break;
            
        case 'blog':
            $newData['blogPosts'] = [];
            foreach ($_POST['blog_title'] as $index => $title) {
                if (!empty($title)) {
                    $newData['blogPosts'][] = [
                        'title' => $title,
                        'excerpt' => $_POST['blog_excerpt'][$index],
                        'date' => $_POST['blog_date'][$index],
                        'link' => $_POST['blog_link'][$index]
                    ];
                }
            }
            break;
            
        case 'software':
            $newData['software'] = [];
            foreach ($_POST['software_name'] as $index => $name) {
                if (!empty($name)) {
                    $newData['software'][] = [
                        'name' => $name,
                        'description' => $_POST['software_description'][$index],
                        'icon' => $_POST['software_icon'][$index],
                        'download' => $_POST['software_download'][$index]
                    ];
                }
            }
            break;
            
        case 'websites':
            $newData['websites'] = [];
            foreach ($_POST['website_name'] as $index => $name) {
                if (!empty($name)) {
                    $newData['websites'][] = [
                        'name' => $name,
                        'description' => $_POST['website_description'][$index],
                        'icon' => $_POST['website_icon'][$index],
                        'link' => $_POST['website_link'][$index]
                    ];
                }
            }
            break;
            
        case 'theme-manager':
            if (isset($_POST['theme'])) {
                $newData['settings']['theme'] = $_POST['theme'];
                $_SESSION['current_theme'] = $_POST['theme'];
            }
            break;
            
        case 'layout-manager':
            if (isset($_POST['layout'])) {
                $newData['settings']['layout'] = $_POST['layout'];
            }
            break;
            
        case 'settings':
            $newData['settings'] = [
                'qqmap_key' => $_POST['qqmap_key'],
                'screenshot_mode' => $_POST['screenshot_mode'],
                'global_effect' => $_POST['global_effect'] // 新增全局特效设置
            ];
            break;
    }

    $dataHandler->saveData($newData);
    
    // 记录活动日志
    $activityMessage = [
        'profile' => '更新了个人信息',
        'skills' => '更新了技能列表',
        'projects' => '更新了项目列表',
        'experience' => '更新了工作经历',
        'education' => '更新了教育经历',
        'certifications' => '更新了证书信息',
        'blog' => '更新了博客文章',
        'software' => '更新了软件作品',
        'websites' => '更新了网站导航',
        'theme-manager' => '更新了主题设置',
        'layout-manager' => '更新了布局设置',
        'settings' => '更新了系统设置'
    ][$section] ?? '更新了数据';
    
    $logEntry = "update|{$activityMessage}|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
    
    // 修复：重定向时保留当前section
    header('Location: user.php?section=' . urlencode($section) . '&success=1');
    exit;
}

// 生成CSRF令牌
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

// 加载留言数据
$guestbookFile = 'data/guestbook.json';
$guestbook = ['messages' => []];
if (file_exists($guestbookFile)) {
    $guestbook = json_decode(file_get_contents($guestbookFile), true);
}

// 定义部分标题
$sectionTitles = [
    'profile' => '个人信息',
    'skills' => '技能管理',
    'projects' => '项目管理',
    'experience' => '工作经历',
    'education' => '教育经历',
    'certifications' => '证书管理',
    'blog' => '博客管理',
    'software' => '软件作品',
    'websites' => '网站导航',
    'guestbook' => '留言管理',
    'theme-manager' => '主题管理',
    'layout-manager' => '布局管理',
    'settings' => '系统设置'
];

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>内容编辑 - <?= $sectionTitles[$section] ?></title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/user.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- 侧边栏导航 -->
      <div class="col-md-2 col-lg-2 d-md-block admin-sidebar collapse">
        <div class="pt-3">
          <div class="text-center mb-4">
            <img src="<?= htmlspecialchars($data['profile']['image']) ?>" class="rounded-circle mb-2" width="80" height="80" alt="管理员头像">
            <h5><?= htmlspecialchars($data['profile']['name']) ?></h5>
            <small>内容编辑</small>
          </div>
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="admin.php">
                <i class="fas fa-tachometer-alt"></i> 控制面板
              </a>
            </li>
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link" href="index.php" target="_blank">
                  <i class="fas fa-external-link-alt"></i> 前端首页
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link <?= $section === 'profile' ? 'active' : '' ?>" href="user.php?section=profile">
                  <i class="fas fa-user"></i> 个人信息
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'skills' ? 'active' : '' ?>" href="user.php?section=skills">
                  <i class="fas fa-laptop-code"></i> 技能管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'projects' ? 'active' : '' ?>" href="user.php?section=projects">
                  <i class="fas fa-project-diagram"></i> 项目管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'experience' ? 'active' : '' ?>" href="user.php?section=experience">
                  <i class="fas fa-briefcase"></i> 工作经历
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'education' ? 'active' : '' ?>" href="user.php?section=education">
                  <i class="fas fa-graduation-cap"></i> 教育经历
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'certifications' ? 'active' : '' ?>" href="user.php?section=certifications">
                  <i class="fas fa-award"></i> 证书管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'blog' ? 'active' : '' ?>" href="user.php?section=blog">
                  <i class="fas fa-blog"></i> 博客管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'software' ? 'active' : '' ?>" href="user.php?section=software">
                  <i class="fas fa-mobile-alt"></i> 软件作品
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'websites' ? 'active' : '' ?>" href="user.php?section=websites">
                  <i class="fas fa-sitemap"></i> 网站导航
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'guestbook' ? 'active' : '' ?>" href="user.php?section=guestbook">
                  <i class="fas fa-comments"></i> 留言管理
                </a>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'theme-manager' ? 'active' : '' ?>" href="user.php?section=theme-manager">
                  <i class="fas fa-palette"></i> 主题管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'layout-manager' ? 'active' : '' ?>" href="user.php?section=layout-manager">
                  <i class="fas fa-layer-group"></i> 布局管理
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= $section === 'settings' ? 'active' : '' ?>" href="user.php?section=settings">
                  <i class="fas fa-cog"></i> 系统设置
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">
                  <i class="fas fa-sign-out-alt"></i> 退出登录
                </a>
              </li>
            </ul>
        </div>
      </div>

      <!-- 主内容区 -->
      <div class="col-md-10 col-lg-10 admin-main">
        <div class="section-header">
          <h2><i class="fas fa-edit me-3"></i><?= $sectionTitles[$section] ?></h2>
        </div>
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          数据保存成功！
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <form method="POST">
          <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

          <?php if ($section === 'profile'): ?>
          <!-- 个人信息 -->
          <div class="form-section">
            <h3 class="mb-4">基本信息</h3>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">姓名</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($data['profile']['name']) ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">职位</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['profile']['title']) ?>">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">简介</label>
              <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($data['profile']['bio']) ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">照片URL</label>
              <input type="text" name="image" class="form-control" value="<?= htmlspecialchars($data['profile']['image']) ?>">
            </div>

            <h3 class="mt-5 mb-4">社交链接</h3>
            <div class="row">
              <div class="col-md-3 mb-3">
                <label class="form-label">GitHub</label>
                <input type="text" name="github" class="form-control" value="<?= htmlspecialchars($data['profile']['social']['github']) ?>">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($data['profile']['social']['linkedin']) ?>">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Twitter</label>
                <input type="text" name="twitter" class="form-control" value="<?= htmlspecialchars($data['profile']['social']['twitter']) ?>">
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">Instagram</label>
                <input type="text" name="instagram" class="form-control" value="<?= htmlspecialchars($data['profile']['social']['instagram']) ?>">
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($section === 'skills'): ?>
          <!-- 技能设置 -->
          <div class="form-section">
            <h3 class="mb-4">技能列表</h3>
            <div id="skills-container">
              <?php foreach ($data['skills'] as $index => $skill): ?>
              <div class="item-container skill-item">
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label">技能名称</label>
                    <input type="text" name="skill_name[]" class="form-control" value="<?= htmlspecialchars($skill['name']) ?>">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">技能水平 (0-100)</label>
                    <input type="number" name="skill_level[]" class="form-control" min="0" max="100" value="<?= $skill['level'] ?>">
                  </div>
                  <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-skill">删除</button>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-skill" class="btn btn-primary mt-3">添加技能</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'projects'): ?>
          <!-- 项目管理 -->
          <div class="form-section">
            <h3 class="mb-4">项目列表</h3>
            <div id="projects-container">
              <?php foreach ($data['projects'] as $index => $project): ?>
              <div class="item-container project-item">
                <div class="mb-3">
                  <label class="form-label">项目标题</label>
                  <input type="text" name="project_title[]" class="form-control" value="<?= htmlspecialchars($project['title']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">项目描述</label>
                  <textarea name="project_description[]" class="form-control" rows="2"><?= htmlspecialchars($project['description']) ?></textarea>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">图片URL</label>
                    <input type="text" name="project_image[]" class="form-control" value="<?= htmlspecialchars($project['image']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">项目链接</label>
                    <input type="text" name="project_link[]" class="form-control" value="<?= htmlspecialchars($project['link']) ?>">
                  </div>
                </div>
                <button type="button" class="btn btn-danger remove-project">删除项目</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-project" class="btn btn-primary mt-3">添加项目</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'experience'): ?>
          <!-- 工作经历 -->
          <div class="form-section">
            <h3 class="mb-4">工作经历</h3>
            <div id="experience-container">
              <?php foreach ($data['experience'] as $index => $exp): ?>
              <div class="item-container experience-item">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">时间段</label>
                    <input type="text" name="exp_period[]" class="form-control" value="<?= htmlspecialchars($exp['period']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">职位</label>
                    <input type="text" name="exp_position[]" class="form-control" value="<?= htmlspecialchars($exp['position']) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">公司名称</label>
                  <input type="text" name="exp_company[]" class="form-control" value="<?= htmlspecialchars($exp['company']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">工作描述</label>
                  <textarea name="exp_description[]" class="form-control" rows="3"><?= htmlspecialchars($exp['description']) ?></textarea>
                </div>
                <button type="button" class="btn btn-danger remove-experience">删除经历</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-experience" class="btn btn-primary mt-3">添加经历</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'education'): ?>
          <!-- 教育经历 -->
          <div class="form-section">
            <h3 class="mb-4">教育经历</h3>
            <div id="education-container">
              <?php foreach ($data['education'] as $index => $edu): ?>
              <div class="item-container education-item">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">时间段</label>
                    <input type="text" name="edu_period[]" class="form-control" value="<?= htmlspecialchars($edu['period']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">学位</label>
                    <input type="text" name="edu_degree[]" class="form-control" value="<?= htmlspecialchars($edu['degree']) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">学校名称</label>
                  <input type="text" name="edu_school[]" class="form-control" value="<?= htmlspecialchars($edu['school']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">描述</label>
                  <textarea name="edu_description[]" class="form-control" rows="2"><?= htmlspecialchars($edu['description']) ?></textarea>
                </div>
                <button type="button" class="btn btn-danger remove-education">删除</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-education" class="btn btn-primary mt-3">添加教育经历</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'certifications'): ?>
          <!-- 证书管理 -->
          <div class="form-section">
            <h3 class="mb-4">证书管理</h3>
            <div id="certifications-container">
              <?php foreach ($data['certifications'] as $index => $cert): ?>
              <div class="item-container certification-item">
                <div class="mb-3">
                  <label class="form-label">证书名称</label>
                  <input type="text" name="cert_name[]" class="form-control" value="<?= htmlspecialchars($cert['name']) ?>">
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">颁发机构</label>
                    <input type="text" name="cert_issuer[]" class="form-control" value="<?= htmlspecialchars($cert['issuer']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">获得日期</label>
                    <input type="date" name="cert_date[]" class="form-control" value="<?= htmlspecialchars($cert['date']) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">证书链接</label>
                  <input type="url" name="cert_url[]" class="form-control" value="<?= htmlspecialchars($cert['url']) ?>">
                </div>
                <button type="button" class="btn btn-danger remove-certification">删除</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-certification" class="btn btn-primary mt-3">添加证书</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'blog'): ?>
          <!-- 博客文章管理 -->
          <div class="form-section">
            <h3 class="mb-4">博客文章管理</h3>
            <div id="blog-container">
              <?php foreach ($data['blogPosts'] as $index => $post): ?>
              <div class="item-container blog-item">
                <div class="mb-3">
                  <label class="form-label">文章标题</label>
                  <input type="text" name="blog_title[]" class="form-control" value="<?= htmlspecialchars($post['title']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">文章摘要</label>
                  <textarea name="blog_excerpt[]" class="form-control" rows="15" style="height:400px"><?= htmlspecialchars($post['excerpt']) ?></textarea>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">发布日期</label>
                    <input type="date" name="blog_date[]" class="form-control" value="<?= htmlspecialchars($post['date']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">文章链接</label>
                    <input type="text" name="blog_link[]" class="form-control" value="<?= htmlspecialchars($post['link']) ?>">
                  </div>
                </div>
                <button type="button" class="btn btn-danger remove-blog">删除文章</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-blog" class="btn btn-primary mt-3">添加文章</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'software'): ?>
          <!-- 软件作品集管理 -->
          <div class="form-section">
            <h3 class="mb-4">软件作品管理</h3>
            <div id="software-container">
              <?php foreach ($data['software'] as $index => $software): ?>
              <div class="item-container software-item">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">软件名称</label>
                    <input type="text" name="software_name[]" class="form-control" value="<?= htmlspecialchars($software['name']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">图标URL</label>
                    <input type="text" name="software_icon[]" class="form-control" value="<?= htmlspecialchars($software['icon']) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">软件描述</label>
                  <textarea name="software_description[]" class="form-control" rows="3"><?= htmlspecialchars($software['description']) ?></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label">下载链接</label>
                  <input type="text" name="software_download[]" class="form-control" value="<?= htmlspecialchars($software['download']) ?>">
                </div>
                <button type="button" class="btn btn-danger remove-software">删除软件</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-software" class="btn btn-primary mt-3">添加软件</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'websites'): ?>
          <!-- 网站导航管理 -->
          <div class="form-section">
            <h3 class="mb-4">网站导航管理</h3>
            <div id="websites-container">
              <?php foreach ($data['websites'] as $index => $website): ?>
              <div class="item-container website-item">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label">网站名称</label>
                    <input type="text" name="website_name[]" class="form-control" value="<?= htmlspecialchars($website['name']) ?>">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">图标URL</label>
                    <input type="text" name="website_icon[]" class="form-control" value="<?= htmlspecialchars($website['icon']) ?>">
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">网站描述</label>
                  <input type="text" name="website_description[]" class="form-control" value="<?= htmlspecialchars($website['description']) ?>">
                </div>
                <div class="mb-3">
                  <label class="form-label">网站链接</label>
                  <input type="text" name="website_link[]" class="form-control" value="<?= htmlspecialchars($website['link']) ?>">
                </div>
                <button type="button" class="btn btn-danger remove-website">删除网站</button>
              </div>
              <?php endforeach; ?>
            </div>
            <button type="button" id="add-website" class="btn btn-primary mt-3">添加网站</button>
          </div>
          <?php endif; ?>

          <?php if ($section === 'guestbook'): ?>
          <!-- 留言管理 -->
          <div class="form-section">
            <h3 class="mb-4">留言管理</h3>
            <div class="scrollable-container">
              <table class="table">
                <thead>
                  <tr>
                    <th>姓名</th>
                    <th>留言内容</th>
                    <th>时间</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($guestbook['messages'] as $msg): ?>
                  <tr>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['message']) ?></td>
                    <td><?= $msg['timestamp'] ?></td>
                    <td>
                      <button class="btn btn-sm btn-danger delete-msg" data-id="<?= $msg['id'] ?>">删除</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($section === 'theme-manager'): ?>
          <!-- 主题样式管理 -->
          <div class="form-section">
            <h3 class="mb-4">主题样式管理</h3>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    操作成功！
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($errorMsg)): ?>
                <div class="alert alert-danger"><?= $errorMsg ?></div>
            <?php endif; ?>
            
            <!-- CSS编辑器 -->
            <div class="card glass-card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-code me-2"></i> CSS编辑器
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">主题CSS内容</label>
                            <textarea name="css_content" class="form-control" rows="15" style="font-family: monospace;"><?= 
                                file_exists('themes/main.css') ? 
                                htmlspecialchars(file_get_contents('themes/main.css')) : 
                                '/* 添加您的主题CSS */' 
                            ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>保存CSS
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- 主题选择器 -->
            <div class="card glass-card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-palette me-2"></i> 主题选择
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">当前主题</label>
                            <select name="theme" class="form-select">
                                <?php
                                $themes = $themeManager->getAvailableThemes();
                                $currentTheme = $data['settings']['theme'] ?? 'theme-purple';
                                foreach ($themes as $theme) {
                                    $selected = ($currentTheme === $theme['name']) ? 'selected' : '';
                                    echo "<option value=\"{$theme['name']}\" $selected>{$theme['display_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">保存主题设置</button>
                    </form>
                </div>
            </div>
            
            <!-- 主题预览 -->
            <div class="card glass-card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-eye me-2"></i> 主题预览
                </div>
                <div class="card-body">
      <style>
        /* 添加CSS变量作用域 */
        .theme-preview-container {
          --primary: <?= $theme['color'] ?>;
          --secondary: <?= $theme['secondary'] ?? '#6c757d' ?>;
          --accent: <?= $theme['accent'] ?? '#20c997' ?>;
          --text: <?= $theme['text'] ?? '#212529' ?>;
          --bg: <?= $theme['bg'] ?? '#f8f9fa' ?>;
        }
      </style>
      
                    <div class="row">
                        <?php foreach ($themeManager->getAvailableThemes() as $theme): ?>
                        <div class="col-md-4 mb-3">
          <!-- 添加主题变量作用域 -->
          <div class="theme-preview-container">
            <div class="preview-card p-3 rounded" 
                 style="background-color: var(--bg); color: var(--text);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?= $theme['display_name'] ?></h5>
                                    <?php if ($theme['name'] === $currentTheme): ?>
                                        <span class="badge bg-success">当前</span>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-3">
                                    <div class="d-flex mb-2">
                  <div class="color-box" style="background-color:var(--primary)"></div>
                  <div class="ms-2">
                    <small>主色</small>
                    <div><?= $theme['color'] ?></div>
                  </div>
                </div>
                <div class="d-flex mb-2">
                  <div class="color-box" style="background-color:var(--secondary)"></div>
                  <div class="ms-2">
                    <small>次色</small>
                    <div>--secondary</div>
                  </div>
                </div>
                <div class="d-flex">
                  <div class="color-box" style="background-color:var(--accent)"></div>
                                        <div class="ms-2">
                                            <small>强调色</small>
                                            <div>--accent</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
          
         <?php if ($section === 'layout-manager'): ?>
        <div class="form-section">
            <h3 class="mb-4">布局模板管理</h3>
            
            <div class="card glass-card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-layer-group me-2"></i> 布局设置
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">当前布局</label>
                            <select name="layout" class="form-select">
                                <?php
                                $layouts = $layoutManager->getAvailableLayouts();
                                $currentLayout = $data['settings']['layout'] ?? 'default';
                                foreach ($layouts as $layout): 
                                    $selected = ($currentLayout === $layout['name']) ? 'selected' : '';
                                ?>
                                <option value="<?= $layout['name'] ?>" <?= $selected ?>>
                                    <?= $layout['display_name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">保存布局设置</button>
                    </form>
                </div>
            </div>
            
            <div class="card glass-card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-upload me-2"></i> 上传新布局
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <div class="mb-3">
                            <label class="form-label">选择PHP布局文件</label>
                            <input type="file" name="layout_upload" class="form-control" accept=".php" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>上传布局
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card glass-card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-dice me-2"></i> 随机生成布局
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="generate_layout" value="1">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-dice me-2"></i>生成随机布局
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="card glass-card">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-list me-2"></i> 可用布局
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>布局名称</th>
                                    <th>显示名称</th>
                                    <th>创建日期</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($layoutManager->getAvailableLayouts() as $layout): ?>
                                <tr>
                                    <td><?= $layout['name'] ?></td>
                                    <td><?= $layout['display_name'] ?></td>
                                    <td><?= $layout['created'] ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                            <button type="submit" name="delete_layout" 
                                                    value="<?= $layout['name'] ?>" 
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('确定删除此布局？')">
                                                删除
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

          <?php if ($section === 'settings'): ?>
          <!-- 系统设置 -->
          <div class="form-section">
            <h3 class="mb-4">系统设置</h3>
          
            <!-- 全局特效设置 -->
            <div class="card glass-card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-magic me-2"></i> 全局特效设置
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">全局特效</label>
                        <select name="global_effect" class="form-select">
                            <option value="themes" <?= ($data['settings']['global_effect'] ?? 'themes') === 'themes' ? 'selected' : '' ?>>基础特效</option>
                            <option value="xali" <?= ($data['settings']['global_effect'] ?? '') === 'xali' ? 'selected' : '' ?>>超级特效</option>
                            <option value="alinb" <?= ($data['settings']['global_effect'] ?? '') === 'alinb' ? 'selected' : '' ?>>高级特效</option>
                            <option value="aliyyds" <?= ($data['settings']['global_effect'] ?? '') === 'aliyyds' ? 'selected' : '' ?>>顶级特效</option>
                        </select>
                    </div>
                </div>
            </div>
          
            <!-- 其他设置 -->
            <div class="card glass-card mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-cog me-2"></i> 其他设置
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">腾讯地图API密钥</label>
                        <input type="text" name="qqmap_key" class="form-control" 
                               value="<?= htmlspecialchars($data['settings']['qqmap_key'] ?? 'P6OBZ-2RCKJ-2T4F5-FHRTH-WKKXF-URF72') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">简历截图模式</label>
                        <select name="screenshot_mode" class="form-select">
                            <option value="full" <?= ($data['settings']['screenshot_mode'] ?? 'full') === 'full' ? 'selected' : '' ?>>完整页面</option>
                            <option value="container" <?= ($data['settings']['screenshot_mode'] ?? '') === 'container' ? 'selected' : '' ?>>主要内容区</option>
                        </select>
                        <div class="form-text">选择简历下载时的截图范围</div>
                    </div>
                </div>
            </div>
          <?php endif; ?>
            
            <!-- 保存按钮 -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">保存更改</button>
                <a href="admin.php" class="btn btn-secondary btn-lg ms-2">返回首页</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 动态添加项目逻辑
    <?php if ($section === 'skills'): ?>
        document.getElementById('add-skill').addEventListener('click', function() {
            const container = document.getElementById('skills-container');
            const newSkill = document.createElement('div');
            newSkill.className = 'item-container skill-item';
            newSkill.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">技能名称</label>
                        <input type="text" name="skill_name[]" class="form-control" placeholder="例如：JavaScript">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">技能水平 (0-100)</label>
                        <input type="number" name="skill_level[]" class="form-control" min="0" max="100" value="80">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-skill">删除</button>
                    </div>
                </div>
            `;
            container.appendChild(newSkill);
        });
    <?php endif; ?>
    
    <?php if ($section === 'projects'): ?>
        document.getElementById('add-project').addEventListener('click', function() {
            const container = document.getElementById('projects-container');
            const newProject = document.createElement('div');
            newProject.className = 'item-container project-item';
            newProject.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">项目标题</label>
                    <input type="text" name="project_title[]" class="form-control" placeholder="项目名称">
                </div>
                <div class="mb-3">
                    <label class="form-label">项目描述</label>
                    <textarea name="project_description[]" class="form-control" rows="2" placeholder="项目描述"></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">图片URL</label>
                        <input type="text" name="project_image[]" class="form-control" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">项目链接</label>
                        <input type="text" name="project_link[]" class="form-control" placeholder="https://example.com">
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-project">删除项目</button>
            `;
            container.appendChild(newProject);
        });
    <?php endif; ?>
    
    <?php if ($section === 'experience'): ?>
        document.getElementById('add-experience').addEventListener('click', function() {
            const container = document.getElementById('experience-container');
            const newExp = document.createElement('div');
            newExp.className = 'item-container experience-item';
            newExp.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">时间段</label>
                        <input type="text" name="exp_period[]" class="form-control" placeholder="例如：2020-2022">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">职位</label>
                        <input type="text" name="exp_position[]" class="form-control" placeholder="职位名称">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">公司名称</label>
                    <input type="text" name="exp_company[]" class="form-control" placeholder="公司名称">
                </div>
                <div class="mb-3">
                    <label class="form-label">工作描述</label>
                    <textarea name="exp_description[]" class="form-control" rows="3" placeholder="工作职责和成就"></textarea>
                </div>
                <button type="button" class="btn btn-danger remove-experience">删除经历</button>
            `;
            container.appendChild(newExp);
        });
    <?php endif; ?>
    
    <?php if ($section === 'education'): ?>
        document.getElementById('add-education').addEventListener('click', function() {
            const container = document.getElementById('education-container');
            const newEdu = document.createElement('div');
            newEdu.className = 'item-container education-item';
            newEdu.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">时间段</label>
                        <input type="text" name="edu_period[]" class="form-control" placeholder="例如：2016-2020">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">学位</label>
                        <input type="text" name="edu_degree[]" class="form-control" placeholder="例如：计算机科学学士">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">学校名称</label>
                    <input type="text" name="edu_school[]" class="form-control" placeholder="学校名称">
                </div>
                <div class="mb-3">
                    <label class="form-label">描述</label>
                    <textarea name="edu_description[]" class="form-control" rows="2" placeholder="学习经历和成就"></textarea>
                </div>
                <button type="button" class="btn btn-danger remove-education">删除</button>
            `;
            container.appendChild(newEdu);
        });
    <?php endif; ?>
    
    <?php if ($section === 'certifications'): ?>
        document.getElementById('add-certification').addEventListener('click', function() {
            const container = document.getElementById('certifications-container');
            const newCert = document.createElement('div');
            newCert.className = 'item-container certification-item';
            newCert.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">证书名称</label>
                    <input type="text" name="cert_name[]" class="form-control" placeholder="证书名称">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">颁发机构</label>
                        <input type="text" name="cert_issuer[]" class="form-control" placeholder="颁发机构">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">获得日期</label>
                        <input type="date" name="cert_date[]" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">证书链接</label>
                    <input type="url" name="cert_url[]" class="form-control" placeholder="https://example.com/certificate">
                </div>
                <button type="button" class="btn btn-danger remove-certification">删除</button>
            `;
            container.appendChild(newCert);
        });
    <?php endif; ?>
    
    <?php if ($section === 'blog'): ?>
        document.getElementById('add-blog').addEventListener('click', function() {
            const container = document.getElementById('blog-container');
            const newBlog = document.createElement('div');
            newBlog.className = 'item-container blog-item';
            newBlog.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">文章标题</label>
                    <input type="text" name="blog_title[]" class="form-control" placeholder="文章标题">
                </div>
                <div class="mb-3">
                    <label class="form-label">文章摘要</label>
                    <textarea name="blog_excerpt[]" class="form-control" rows="15" style="height:400px" placeholder="文章摘要"></textarea>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">发布日期</label>
                        <input type="date" name="blog_date[]" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">文章链接</label>
                        <input type="text" name="blog_link[]" class="form-control" placeholder="https://example.com/blog-post">
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-blog">删除文章</button>
            `;
            container.appendChild(newBlog);
        });
    <?php endif; ?>
    
    <?php if ($section === 'software'): ?>
        document.getElementById('add-software').addEventListener('click', function() {
            const container = document.getElementById('software-container');
            const newSoftware = document.createElement('div');
            newSoftware.className = 'item-container software-item';
            newSoftware.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">软件名称</label>
                        <input type="text" name="software_name[]" class="form-control" placeholder="软件名称">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">图标URL</label>
                        <input type="text" name="software_icon[]" class="form-control" placeholder="https://example.com/icon.png">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">软件描述</label>
                    <textarea name="software_description[]" class="form-control" rows="3" placeholder="软件功能和特点"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">下载链接</label>
                    <input type="text" name="software_download[]" class="form-control" placeholder="https://example.com/download">
                </div>
                <button type="button" class="btn btn-danger remove-software">删除软件</button>
            `;
            container.appendChild(newSoftware);
        });
    <?php endif; ?>
    
    <?php if ($section === 'websites'): ?>
        document.getElementById('add-website').addEventListener('click', function() {
            const container = document.getElementById('websites-container');
            const newWebsite = document.createElement('div');
            newWebsite.className = 'item-container website-item';
            newWebsite.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">网站名称</label>
                        <input type="text" name="website_name[]" class="form-control" placeholder="网站名称">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">图标URL</label>
                        <input type="text" name="website_icon[]" class="form-control" placeholder="https://example.com/favicon.ico">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">网站描述</label>
                    <input type="text" name="website_description[]" class="form-control" placeholder="网站简介">
                </div>
                <div class="mb-3">
                    <label class="form-label">网站链接</label>
                    <input type="text" name="website_link[]" class="form-control" placeholder="https://example.com">
                </div>
                <button type="button" class="btn btn-danger remove-website">删除网站</button>
            `;
            container.appendChild(newWebsite);
        });
    <?php endif; ?>
    
document.addEventListener('click', function(e) {
  // 技能删除
  if (e.target.classList.contains('remove-skill')) {
    e.target.closest('.skill-item').remove();
  }
  // 项目删除
  if (e.target.classList.contains('remove-project')) {
    e.target.closest('.project-item').remove();
  }
  // 经历删除
  if (e.target.classList.contains('remove-experience')) {
    e.target.closest('.experience-item').remove();
  }
  // 教育经历删除
  if (e.target.classList.contains('remove-education')) {
    e.target.closest('.education-item').remove();
  }
  // 证书删除
  if (e.target.classList.contains('remove-certification')) {
    e.target.closest('.certification-item').remove();
  }
  // 博客删除
  if (e.target.classList.contains('remove-blog')) {
    e.target.closest('.blog-item').remove();
  }
  // 软件删除
  if (e.target.classList.contains('remove-software')) {
    e.target.closest('.software-item').remove();
  }
  // 网站删除
  if (e.target.classList.contains('remove-website')) {
    e.target.closest('.website-item').remove();
  }
  
  // 留言删除（特殊处理）
  if (e.target.classList.contains('delete-msg')) {
    if (confirm('确定删除这条留言吗？')) {
      const id = e.target.dataset.id;
      fetch('api.php?action=delete-msg&id=' + id, {
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          e.target.closest('tr').remove();
        }
      });
    }
  }
});
  </script>
</body>
</html>
<?php ob_end_flush(); // 结束输出缓冲 ?>