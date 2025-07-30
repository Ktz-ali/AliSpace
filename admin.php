<?php
session_start();
require_once 'data.php';
// require_once 'visitor_stats.php';

// 检查登录状态
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$dataHandler = new DataHandler();
$data = $dataHandler->getData();
$visitorStats = new VisitorStats();


// 服务器信息
$serverInfo = [
    'PHP版本' => phpversion(),
    '服务器软件' => $_SERVER['SERVER_SOFTWARE'],
    '操作系统' => php_uname('s') . ' ' . php_uname('r'),
    '内存使用' => round(memory_get_usage(true)/1048576, 2) . ' MB',
    '磁盘空间' => function_exists('disk_free_space') ? 
        (round(disk_free_space(__DIR__)/1073741824, 2) . ' GB free') : 
        'N/A'
];

// 访客统计
$stats = [
    'total' => $visitorStats->getTotalVisits(),
    'today' => $visitorStats->getTodayVisits(),
    'top_referrers' => $visitorStats->getTopReferrers(5)
];

// 加载最近活动日志
$activityLog = [];
$logFile = 'data/activity.log';
if (file_exists($logFile)) {
    $activityLog = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    $activityLog = array_slice($activityLog, 0, 5); // 获取最近5条
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>后台管理系统</title>
  <link href="css/admin.css" rel="stylesheet">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://map.qq.com/api/gljs?v=1.exp&key=P6OBZ-2RCKJ-2T4F5-FHRTH-WKKXF-URF72"></script>
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
            <small>管理员</small>
          </div>

          <li class="nav-item">
            <a class="nav-link active" href="admin.php">
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
              <a class="nav-link" href="user.php?section=profile">
                <i class="fas fa-user"></i> 个人信息
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=skills">
                <i class="fas fa-laptop-code"></i> 技能管理
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=projects">
                <i class="fas fa-project-diagram"></i> 项目管理
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=experience">
                <i class="fas fa-briefcase"></i> 工作经历
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=education">
                <i class="fas fa-graduation-cap"></i> 教育经历
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=certifications">
                <i class="fas fa-award"></i> 证书管理
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=blog">
                <i class="fas fa-blog"></i> 博客管理
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=software">
                <i class="fas fa-mobile-alt"></i> 软件作品
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=websites">
                <i class="fas fa-sitemap"></i> 网站导航
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=guestbook">
                <i class="fas fa-comments"></i> 留言管理
              </a>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=theme-manager">
                <i class="fas fa-palette"></i> 主题管理
              </a>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=layout-manager">
                <i class="fas fa-layer-group"></i> 布局管理
              </a>
            <li class="nav-item">
              <a class="nav-link" href="user.php?section=settings">
                <i class="fas fa-cog"></i> 系统设置
              </a>
            </li>
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
        <div class="admin-header">
          <h1><i class="fas fa-tachometer-alt me-3"></i>后台控制面板</h1>
          <p class="mb-0">系统状态概览与快捷操作</p>
        </div>



        <div class="row">
          <!-- 统计卡片 -->
          <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
              <i class="fas fa-users"></i>
              <div class="number"><?= $stats['total'] ?></div>
              <div class="label">总访问量</div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
              <i class="fas fa-calendar-day"></i>
              <div class="number"><?= $stats['today'] ?></div>
              <div class="label">今日访问</div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card stat-card bg-info text-white">
              <i class="fas fa-server"></i>
              <div class="number"><?= round(memory_get_usage(true)/1048576, 2) ?> MB</div>
              <div class="label">内存使用</div>
            </div>
          </div>

          <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark">
              <i class="fas fa-hdd"></i>
              <div class="number">
                <?= function_exists('disk_free_space') ? 
                                    round(disk_free_space(__DIR__)/1073741824, 2) : 'N/A' ?> GB
              </div>
              <div class="label">磁盘空间</div>
            </div>
          </div>
        </div>

        <div class="row mt-4">
          <!-- 访客统计 -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-primary text-white">
                <i class="fas fa-chart-line me-2"></i>访客统计
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <h5>总访问量: <?= $stats['total'] ?></h5>
                  <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                  </div>
                </div>
                <div class="mb-3">
                  <h5>今日访问: <?= $stats['today'] ?></h5>
                  <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= min(100, ($stats['today'] / max(1, $stats['total'])) * 100) ?>%"></div>
                  </div>
                </div>
                <h5>主要来源:</h5>
                <ul class="list-group">
                  <?php foreach ($stats['top_referrers'] as $ref => $count): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= $ref ?>
                    <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>

          <!-- 服务器信息 -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header bg-info text-white">
                <i class="fas fa-server me-2"></i>服务器信息
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <?php foreach ($serverInfo as $key => $value): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= $key ?>
                    <span class="text-muted"><?= $value ?></span>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- 访客地图 -->
        <div class="row mt-4">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header bg-success text-white">
                <i class="fas fa-map-marked-alt me-2"></i>访客分布地图 (真实数据)
              </div>
              <div class="card-body">
                <div id="visitor-map" style="height: 400px;"></div>
                <div class="mt-3">
                  <h5>访客位置分布:</h5>
                  <ul class="list-group">
                    <?php foreach ($visitorStats->getVisitorLocations() as $location => $count): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <?= htmlspecialchars($location) ?>
                      <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                    </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <h3 class="section-title">快捷操作</h3>
        <div class="row quick-links">
          <div class="col-md-2 col-4 mb-3">
            <a href="index.php" target="_blank" class="card text-decoration-none">
              <i class="fas fa-external-link-alt"></i>
              <h5>前端首页</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=profile" class="card text-decoration-none">
              <i class="fas fa-user"></i>
              <h5>个人信息</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=skills" class="card text-decoration-none">
              <i class="fas fa-laptop-code"></i>
              <h5>技能管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=projects" class="card text-decoration-none">
              <i class="fas fa-project-diagram"></i>
              <h5>项目管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=experience" class="card text-decoration-none">
              <i class="fas fa-briefcase"></i>
              <h5>工作经历</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=education" class="card text-decoration-none">
              <i class="fas fa-graduation-cap"></i>
              <h5>教育经历</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=certifications" class="card text-decoration-none">
              <i class="fas fa-award"></i>
              <h5>证书管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=blog" class="card text-decoration-none">
              <i class="fas fa-blog"></i>
              <h5>博客管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=software" class="card text-decoration-none">
              <i class="fas fa-mobile-alt"></i>
              <h5>软件作品</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=websites" class="card text-decoration-none">
              <i class="fas fa-sitemap"></i>
              <h5>网站导航</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=guestbook" class="card text-decoration-none">
              <i class="fas fa-comments"></i>
              <h5>留言管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=theme-manager" class="card text-decoration-none">
              <i class="fas fa-palette"></i>
              <h5>主题管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=layout-manager" class="card text-decoration-none">
              <i class="fas fa-layer-group"></i>
              <h5>布局管理</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="user.php?section=settings" class="card text-decoration-none">
              <i class="fas fa-cog"></i>
              <h5>系统设置</h5>
            </a>
          </div>
          <div class="col-md-2 col-4 mb-3">
            <a href="logout.php" class="card text-decoration-none">
              <i class="fas fa-cog"></i>
              <h5>退出登录</h5>
            </a>
          </div>
        </div>

        <h3 class="section-title">最近活动</h3>
        <div class="card">
          <div class="card-body">
            <ul class="list-group">
              <?php if (!empty($activityLog)): ?>
              <?php foreach ($activityLog as $logEntry): 
                                    list($type, $message, $time) = explode('|', $logEntry); ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <?php 
                                                $iconClass = [
                                                    'login' => 'fas fa-sign-in-alt text-success',
                                                    'logout' => 'fas fa-sign-out-alt text-warning',
                                                    'update' => 'fas fa-edit text-primary',
                                                    'delete' => 'fas fa-trash-alt text-danger',
                                                    'create' => 'fas fa-plus-circle text-info'
                                                ][$type] ?? 'fas fa-info-circle text-secondary';
                                            ?>
                  <i class="<?= $iconClass ?> me-2"></i>
                  <?= htmlspecialchars($message) ?>
                  <small class="text-muted d-block"><?= $time ?></small>
                </div>
                <span class="badge bg-<?= $type === 'login' ? 'success' : ($type === 'delete' ? 'danger' : 'primary') ?>">
                  <?= [
                                                'login' => '登录',
                                                'logout' => '退出',
                                                'update' => '编辑',
                                                'delete' => '删除',
                                                'create' => '新建'
                                            ][$type] ?? '操作' ?>
                </span>
              </li>
              <?php endforeach; ?>
              <?php else: ?>
              <li class="list-group-item text-center">暂无活动记录</li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 初始化访客地图
    function initVisitorMap() {
        // 创建地图实例
        const map = new TMap.Map('visitor-map', {
            center: new TMap.LatLng(39.908823, 116.397470), // 北京中心坐标
            zoom: 4,
            viewMode: '2D'
        });
        
        // 添加标记
        const locations = [
            { pos: new TMap.LatLng(39.908823, 116.397470), name: '北京' },
            { pos: new TMap.LatLng(31.230416, 121.473701), name: '上海' },
            { pos: new TMap.LatLng(23.129163, 113.264435), name: '广州' },
            { pos: new TMap.LatLng(22.319303, 114.169361), name: '香港' },
            { pos: new TMap.LatLng(30.572815, 104.066801), name: '成都' },
            { pos: new TMap.LatLng(29.431586, 106.912251), name: '重庆' }
        ];
        
        // 创建标注物
        const marker = new TMap.MultiMarker({
            map: map,
            styles: {
                // 可以定义样式
            },
            geometries: locations.map(loc => ({
                position: loc.pos,
                properties: {
                    title: loc.name
                }
            }))
        });
    }
    
    // 页面加载完成后初始化地图
    document.addEventListener('DOMContentLoaded', initVisitorMap);
  </script>
</body>

</html>