<?php
session_start();
require_once 'data.php';
require_once 'ThemeManager.php';
require_once 'LayoutManager.php';

$themeManager = new ThemeManager();
$layoutManager = new LayoutManager();

$dataHandler = new DataHandler();
$data = $dataHandler->getData();

// 检查管理员登录状态
$isAdmin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// 访客统计
$visitorStats = new VisitorStats();
$visitorStats->recordVisit();

// 获取当前主题
$currentTheme = $data['settings']['theme'] ?? 'theme-purple';

// 获取当前全局特效
$globalEffect = $data['settings']['global_effect'] ?? 'themes';

// 获取所有可用主题
$themes = $themeManager->getAvailableThemes();

// 获取当前布局
$layout = $_GET['layout'] ?? $data['settings']['layout'] ?? 'default';
$layoutFile = "layouts/{$layout}.php";

// 检查布局是否存在
if (!file_exists($layoutFile)) {
    $layoutFile = "layouts/默认布局.php"; // 使用默认布局
}

// 获取每日名言
function getDailyQuote() {
    $quotes = [
        "代码如诗，简洁即美。",
        "技术不是目的，而是解决问题的工具。",
        "优秀程序员写出人类能理解的代码，伟大程序员写出机器能执行的代码。",
        "编程是创造世界的第二法则。"
    ];
    return $quotes[array_rand($quotes)];
}

$dailyQuote = getDailyQuote();

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['profile']['name']) ?> | 个人主页</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
       <link href="css/<?= $globalEffect ?>.css" rel="stylesheet" id="global-effect-css">
    <link href="themes/main.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://map.qq.com/api/gljs?v=1.exp&key=<?= $data['settings']['qqmap_key'] ?? 'P6OBZ-2RCKJ-2T4F5-FHRTH-WKKXF-URF72' ?>"></script>
</head>
<body class="<?= $currentTheme ?>">
    <!-- 加载指示器 -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- 管理按钮 -->
    <a href="/admin.php" class="admin-btn">
        <i class="fas fa-cog"></i>
    </a>
    
    <!-- 模块2：布局模板容器 -->
    <div id="layout-container" class="glass-card neon-border">
        <?php include $layoutFile; ?>
    </div>
    
    <!-- 模块1：控制面板 (管理员可见) -->
    <?php if ($isAdmin): ?>
    <div class="control-panel glass-card neon-border">
        <!-- 主题选择器模块 -->
        <div class="control-panel-module">
            <h3><i class="fas fa-palette me-2"></i>主题选择</h3>
            <div class="theme-selector">
                <?php foreach ($themes as $theme): ?>
                    <div class="theme-btn <?= $theme['name'] === $currentTheme ? 'active' : '' ?>" 
                         style="background-color: <?= $theme['color'] ?>"
                         data-theme="<?= $theme['name'] ?>"
                         title="<?= $theme['display_name'] ?>">
                        <span class="theme-initial"><?= mb_substr($theme['display_name'], 0, 1, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="theme-name-display">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                    // 显示当前主题的中文名称
                    $currentThemeName = $currentTheme;
                    foreach ($themes as $theme) {
                        if ($theme['name'] === $currentTheme) {
                            $currentThemeName = $theme['display_name'];
                            break;
                        }
                    }
                    echo $currentThemeName;
                ?>
            </div>
        </div>

        <!-- 布局选择器模块 -->   
        <div class="control-panel-module">
            <h3><i class="fas fa-layer-group me-2"></i>布局设置</h3>
            <select id="layout-select" class="form-select mt-2">
                <?php 
                $layouts = $layoutManager->getAvailableLayouts();
                foreach ($layouts as $layoutOption): 
                    $selected = ($layout === $layoutOption['name']) ? 'selected' : '';
                ?>
                <option value="<?= $layoutOption['name'] ?>" <?= $selected ?>>
                    <?= $layoutOption['display_name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- 全局特效选择器模块 -->
        <div class="control-panel-module">
            <h3><i class="fas fa-magic me-2"></i>全局特效</h3>
            <select id="global-effect-select" class="form-select mt-2">
                <option value="themes" <?= ($globalEffect === 'themes') ? 'selected' : '' ?>>基础特效</option>
                <option value="xali" <?= ($globalEffect === 'xali') ? 'selected' : '' ?>>超级特效</option>
                <option value="alinb" <?= ($globalEffect === 'alinb') ? 'selected' : '' ?>>高级特效</option>
                <option value="aliyyds" <?= ($globalEffect === 'aliyyds') ? 'selected' : '' ?>>顶级特效</option>
            </select>
        </div>
        
        <!-- 下载简历模块 -->
        <div class="control-panel-module">
            <h3><i class="fas fa-download me-2"></i>简历操作</h3>
            <button id="download-resume" class="btn btn-primary w-100 mt-3">
                <i class="fas fa-download me-2"></i>下载简历
            </button>
            <div id="resume-download-status" class="mt-3 small text-center"></div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- 确保以下元素在布局中存在 -->
    <div id="daily-quote-container" class="d-none">
        <div id="daily-quote"></div>
    </div>
    <div id="blog-posts-container" class="d-none">
        <div id="blog-posts" class="row"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>    
    // 主题切换功能
    function applyTheme(theme) {
        // 更新body类名
        document.body.className = document.body.className
            .split(' ')
            .filter(cls => !cls.startsWith('theme-'))
            .join(' ') + ' ' + theme;
        
        // 更新按钮状态
        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === theme);
        });
        
        // 更新主题名称显示
        const themeNameDisplay = document.querySelector('.theme-name-display');
        if (themeNameDisplay) {
            const activeThemeBtn = document.querySelector(`.theme-btn[data-theme="${theme}"]`);
            if (activeThemeBtn) {
                themeNameDisplay.innerHTML = `<i class="fas fa-check-circle me-2"></i>${activeThemeBtn.title}`;
            }
        }
    }
    
    // 布局切换功能
    async function loadLayout(layoutName) {
        const overlay = document.getElementById('loading-overlay');
        
        try {
            // 显示加载指示器
            overlay.classList.add('active');
            
            // 保存设置到服务器
            await saveSettingToServer('layout', layoutName);
            
            // 直接重新加载整个页面
            window.location.href = `index.php?layout=${layoutName}`;
            
        } catch (error) {
            console.error('布局切换失败:', error);
            alert('布局切换失败: ' + error.message);
        } finally {
            // 隐藏加载指示器
            overlay.classList.remove('active');
        }
    }
    
    // 全局特效切换
    async function changeGlobalEffect(effect) {
        try {
            await saveSettingToServer('global_effect', effect);
            // 动态替换CSS链接
            const link = document.getElementById('global-effect-css');
            if (link) {
                link.href = `css/${effect}.css?t=${new Date().getTime()}`;
            }
        } catch (error) {
            console.error('全局特效保存失败:', error);
        }
    }
    
    // 处理联系表单提交
    function handleContactForm(form) {
        const formData = new FormData(form);
        const responseContainer = document.getElementById('contact-response');
        
        fetch('api.php?action=contact', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                form.reset();
            } else {
                responseContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            responseContainer.innerHTML = `<div class="alert alert-danger">请求失败，请稍后再试</div>`;
        });
    }
    
    // 统一设置保存
    function saveSettingToServer(setting, value) {
        return new Promise((resolve, reject) => {
            fetch('api.php?action=update-setting', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ setting, value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(`${setting} 更新成功`);
                    resolve();
                } else {
                    console.error(`${setting} 更新失败`);
                    reject(new Error(`${setting} 更新失败`));
                }
            })
            .catch(error => {
                console.error('保存设置失败:', error);
                reject(error);
            });
        });
    }
    
    // 简历下载功能
    document.getElementById('download-resume')?.addEventListener('click', function() {
        const btn = this;
        const status = document.getElementById('resume-download-status');
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>生成中...';
        btn.disabled = true;
        status.textContent = '正在生成简历截图，请稍候...';
        status.className = 'mt-2 small text-info';
        
        // 获取截图模式设置
        const screenshotMode = '<?= $data['settings']['screenshot_mode'] ?? 'full' ?>';
        const targetElement = screenshotMode === 'container' ? document.querySelector('.container') : document.body;
        
        // 使用html2canvas生成截图
        html2canvas(targetElement, {
            scale: 2, // 提高分辨率
            useCORS: true, // 允许跨域图片
            logging: false,
            backgroundColor: null
        }).then(canvas => {
            // 将canvas转换为图片
            const imgData = canvas.toDataURL('image/png');
            
            // 创建下载链接
            const link = document.createElement('a');
            link.download = '<?= $data['profile']['name'] ?>_简历.png';
            link.href = imgData;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            btn.innerHTML = '<i class="fas fa-download me-2"></i>下载简历';
            btn.disabled = false;
            status.textContent = '简历下载完成！';
            status.className = 'mt-2 small text-success';
        }).catch(error => {
            console.error('简历生成失败:', error);
            btn.innerHTML = '<i class="fas fa-download me-2"></i>下载简历';
            btn.disabled = false;
            status.textContent = '简历生成失败，请重试';
            status.className = 'mt-2 small text-danger';
        });
    });

    // 加载每日名言
    async function loadDailyQuote() {
        try {
            const response = await fetch('api.php?action=get-quote');
            if (!response.ok) {
                throw new Error('获取名言失败: ' + response.status);
            }
            const data = await response.json();
            const quoteElement = document.getElementById('daily-quote');
            if (quoteElement) {
                quoteElement.textContent = data.quote;
            }
        } catch (error) {
            console.error('加载名言失败:', error);
            const quoteElement = document.getElementById('daily-quote');
            if (quoteElement) {
                quoteElement.textContent = '保持热情，持续学习！';
            }
        }
    }

    // 加载博客文章
    async function loadBlogPosts() {
        try {
            const response = await fetch('api.php?action=get-blog-posts');
            if (!response.ok) {
                throw new Error('获取博客失败: ' + response.status);
            }
            const posts = await response.json();
            const blogPostsElement = document.getElementById('blog-posts');
            if (blogPostsElement) {
                let html = '';
                
                posts.forEach(post => {
                    html += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 glass-card">
                                <div class="card-body">
                                    <h5 class="card-title">${post.title}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">${post.date}</h6>
                                    <p class="card-text">${post.excerpt}</p>
                                    <a href="${post.link}" class="btn btn-sm btn-outline-primary">阅读全文</a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                blogPostsElement.innerHTML = html;
            }
        } catch (error) {
            console.error('加载博客失败:', error);
            const blogPostsElement = document.getElementById('blog-posts');
            if (blogPostsElement) {
                blogPostsElement.innerHTML = '<p class="text-center">暂时无法加载技术文章</p>';
            }
        }
    }

    // 页面加载时初始化
    document.addEventListener('DOMContentLoaded', () => {
        // 应用服务器设置的主题
        const phpTheme = '<?= $currentTheme ?>';
        applyTheme(phpTheme);
        
        // 设置布局选择框的值
        const layout = '<?= $layout ?>';
        if (document.getElementById('layout-select')) {
            document.getElementById('layout-select').value = layout;
        }
        
        // 设置全局特效选择框的值
        const globalEffect = '<?= $globalEffect ?>';
        if (document.getElementById('global-effect-select')) {
            document.getElementById('global-effect-select').value = globalEffect;
        }
        
        // 更新本地存储保持同步
        localStorage.setItem('selectedTheme', phpTheme);
        
        // 绑定主题按钮事件（仅管理员）
        <?php if ($isAdmin): ?>
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const theme = this.dataset.theme;
                    applyTheme(theme);
                    localStorage.setItem('selectedTheme', theme);
                    saveSettingToServer('theme', theme)
                        .catch(error => console.error('主题保存失败:', error));
                });
            });
            
            // 布局切换事件
            if (document.getElementById('layout-select')) {
                document.getElementById('layout-select').addEventListener('change', function() {
                    loadLayout(this.value);
                });
            }
            
            // 全局特效切换事件
            if (document.getElementById('global-effect-select')) {
                document.getElementById('global-effect-select').addEventListener('change', function() {
                    changeGlobalEffect(this.value);
                });
            }
        <?php endif; ?>
        
        // 绑定联系表单
        const contactForm = document.getElementById('contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                handleContactForm(this);
            });
        }

        // 初始化技能条动画
        document.querySelectorAll('.skill-progress').forEach(bar => {
            const width = bar.style.width;
            bar.style.setProperty('--target-width', width);
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 300);
        });
        
        // 加载数据
        loadBlogPosts();
        loadDailyQuote();
    });
    </script>
</body>
</html>