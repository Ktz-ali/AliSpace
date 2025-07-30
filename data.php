<?php
ob_start();

// 设置禁止缓存
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

class DataHandler {
    private $dataFile = 'data/content.json';
    
    public function __construct() {
        // 确保data目录存在
        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }
        
        // 如果JSON文件不存在则创建
        if (!file_exists($this->dataFile)) {
            $initialData = [
                'profile' => [
                    'name' => '阿力',
                    'title' => '高级全栈工程师 & 安卓逆向工程师',
                    'bio' => '我是一名充满激情的开发者，专注于创建高效、美观且用户友好的web应用。拥有8年全栈开发经验，精通现代JavaScript框架和云原生技术。',
                    'image' => 'http://q2.qlogo.cn/headimg_dl?dst_uin=1728031575&spec=100',
                    'social' => [
                        'github' => '#',
                        'linkedin' => '#',
                        'twitter' => '#',
                        'instagram' => '#'
                    ]
                ],
                'skills' => [
                    ['name' => 'JavaScript/TypeScript', 'level' => 95],
                    ['name' => 'React & Vue', 'level' => 90],
                    ['name' => 'Node.js & PHP', 'level' => 85],
                    ['name' => 'UI/UX设计', 'level' => 80]
                ],
                'projects' => [
                    [
                        'title' => '企业级CRM系统',
                        'description' => '基于React和Node.js构建的客户关系管理系统，支持多平台访问。',
                        'image' => '/images/xyh1.jpg',
                        'link' => '#'
                    ],
                    [
                        'title' => '电商平台重构',
                        'description' => '使用Vue.js和微服务架构重构传统电商平台，性能提升300%。',
                        'image' => '/images/xyh2.jpg',
                        'link' => '#'
                    ],
                    [
                        'title' => 'AI数据分析工具',
                        'description' => '结合机器学习的数据可视化工具，支持实时数据分析和预测。',
                        'image' => '/images/xyh3.jpg',
                        'link' => '#'
                    ]
                ],
                'experience' => [
                    [
                        'period' => '2020 - 至今',
                        'position' => '高级全栈工程师',
                        'company' => '科技创新有限公司',
                        'description' => '负责公司核心产品架构设计和开发，带领10人团队完成多个重要项目，优化系统性能。'
                    ],
                    [
                        'period' => '2017 - 2020',
                        'position' => '前端开发主管',
                        'company' => '互联网科技有限公司',
                        'description' => '主导公司前端技术栈升级，从jQuery迁移到Vue.js，提升开发效率和用户体验。'
                    ]
                ],
                'education' => [
                    [
                        'period' => '2013 - 2017',
                        'degree' => '计算机科学学士',
                        'school' => '科技大学',
                        'description' => '主修软件开发与系统设计，获得优秀毕业生称号。'
                    ]
                ],
                'certifications' => [
                    [
                        'name' => 'AWS 认证解决方案架构师',
                        'issuer' => '亚马逊网络服务',
                        'date' => '2021-06-01',
                        'url' => '#'
                    ]
                ],
                'blogPosts' => [
                    [
                        'title' => 'React性能优化实战',
                        'excerpt' => '探索React应用性能瓶颈及优化策略...',
                        'date' => '2023-06-15',
                        'link' => '#'
                    ],
                    [
                        'title' => '云原生架构设计原则',
                        'excerpt' => '如何设计高可用、可扩展的云原生应用...',
                        'date' => '2023-05-22',
                        'link' => '#'
                    ],
                    [
                        'title' => '微服务通信模式解析',
                        'excerpt' => '深入探讨gRPC与REST在微服务架构中的应用...',
                        'date' => '2023-07-10',
                        'link' => '#'
                    ]
                ],
                'software' => [
                    [
                        'name' => '智能笔记助手',
                        'description' => '基于AI的智能笔记整理工具，支持语音输入和自动分类',
                        'icon' => '/images/app1.png',
                        'download' => '#'
                    ],
                    [
                        'name' => '代码优化工具',
                        'description' => '自动检测代码问题并提供优化建议的开发助手',
                        'icon' => '/images/app2.png',
                        'download' => '#'
                    ]
                ],
                'websites' => [
                    [
                        'name' => 'GitHub',
                        'description' => '代码托管平台',
                        'icon' => 'https://github.githubassets.com/favicons/favicon.png',
                        'link' => 'https://github.com'
                    ],
                    [
                        'name' => 'Stack Overflow',
                        'description' => '开发者问答社区',
                        'icon' => 'https://cdn.sstatic.net/Sites/stackoverflow/Img/favicon.ico',
                        'link' => 'https://stackoverflow.com'
                    ],
                    [
                        'name' => 'MDN Web Docs',
                        'description' => 'Web开发文档',
                        'icon' => 'https://developer.mozilla.org/favicon-48x48.cbbd161b.png',
                        'link' => 'https://developer.mozilla.org'
                    ],
                    [
                        'name' => 'CodePen',
                        'description' => '前端代码示例',
                        'icon' => 'https://cpwebassets.codepen.io/assets/favicon/favicon-touch-de50acbf5d634ec6791894eba4ba9cf490f709b3d742597c6fc4b734e6492a5a.png',
                        'link' => 'https://codepen.io'
                    ],
                    [
                        'name' => 'LeetCode',
                        'description' => '编程题库',
                        'icon' => 'https://leetcode.com/favicon.ico',
                        'link' => 'https://leetcode.com'
                    ],
                    [
                        'name' => 'Dev.to',
                        'description' => '开发者社区',
                        'icon' => 'https://dev-to-uploads.s3.amazonaws.com/uploads/logos/resized_logo_UQww2soKuUsjaOGNB38o.png',
                        'link' => 'https://dev.to'
                    ],
                    [
                        'name' => 'CSS-Tricks',
                        'description' => 'CSS技巧',
                        'icon' => 'https://css-tricks.com/apple-touch-icon.png',
                        'link' => 'https://css-tricks.com'
                    ],
                    [
                        'name' => 'Smashing Magazine',
                        'description' => '设计资源',
                        'icon' => 'https://www.smashingmagazine.com/images/favicon/apple-touch-icon.png',
                        'link' => 'https://www.smashingmagazine.com'
                    ],
                    [
                        'name' => 'FreeCodeCamp',
                        'description' => '编程学习',
                        'icon' => 'https://www.freecodecamp.org/favicon-32x32.png',
                        'link' => 'https://www.freecodecamp.org'
                    ],
                    [
                        'name' => 'Hacker News',
                        'description' => '科技新闻',
                        'icon' => 'https://news.ycombinator.com/favicon.ico',
                        'link' => 'https://news.ycombinator.com'
                    ],
                    [
                        'name' => 'Dribbble',
                        'description' => '设计灵感',
                        'icon' => 'https://cdn.dribbble.com/assets/favicon-b38525134603b9513174ec887944bde1a869eb6cd414f4d640ee48ab2a15a26b.ico',
                        'link' => 'https://dribbble.com'
                    ],
                    [
                        'name' => 'Behance',
                        'description' => '创意作品',
                        'icon' => 'https://a5.behance.net/7d3b9a8d7f3c4e6b8d5a8d5b8d5a8d5b8d5a8d5b8d5a8d5b8d5a8d5b8d5a8d5b.png',
                        'link' => 'https://behance.net'
                    ]
                ],
                'settings' => [
                    'theme' => 'theme-purple',
                    'layout' => 'default',
                    'qqmap_key' => 'P6OBZ-2RCKJ-2T4F5-FHRTH-WKKXF-URF72',
                    'screenshot_mode' => 'full'
                ],
                'version' => 1,
                'last_updated' => time()
            ];
            
            file_put_contents($this->dataFile, json_encode($initialData, JSON_PRETTY_PRINT));
        }
    }
    
    public function getData() {
        $json = file_get_contents($this->dataFile);
        return json_decode($json, true);
    }
    
    public function getDataVersion() {
        $data = $this->getData();
        return $data['version'] ?? 0;
    }
    
    public function saveData($data) {
        $currentVersion = $data['version'] ?? 0;
        $data['version'] = $currentVersion + 1;
        $data['last_updated'] = time();
        
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->dataFile, $json);
        
        $this->clearFrontendCache();
        return true;
    }
    
    private function clearFrontendCache() {
        $cacheFiles = [
            'index.php',
            'api.php',
            'layouts/layout_*.php'
        ];
        
        foreach ($cacheFiles as $pattern) {
            foreach (glob($pattern) as $file) {
                if (file_exists($file)) {
                    // 添加版本号到文件
                    $version = time();
                    $content = file_get_contents($file);
                    $content = preg_replace('/(\.js\?v=)(\d+)/', '$1'.$version, $content);
                    $content = preg_replace('/(\.css\?v=)(\d+)/', '$1'.$version, $content);
                    file_put_contents($file, $content);
                    touch($file);
                }
            }
        }
        
        // 清除前端资源缓存
        $this->clearAssetCache();
    }
    
    private function clearAssetCache() {
        $assets = ['css/main.css', 'js/main.js'];
        foreach ($assets as $file) {
            if (file_exists($file)) {
                $version = time();
                $newFile = preg_replace('/(\.\w+)$/', '.v'.$version.'$1', $file);
                copy($file, $newFile);
            }
        }
    }
}

class VisitorStats {
    private $statsFile = 'data/visitor_stats.json';
    
    public function __construct() {
        // 确保data目录存在
        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }
        
        // 初始化统计文件
        if (!file_exists($this->statsFile)) {
            file_put_contents($this->statsFile, json_encode([
                'total_visits' => 0,
                'daily_visits' => [],
                'referrers' => [],
                'last_ip' => ''
            ]));
        }
    }
    
    public function recordVisit() {
            // 添加位置记录
        $ip = $_SERVER['REMOTE_ADDR'];
        $location = $this->getLocationFromIP($ip);
        
        $stats = $this->getStats();
        $stats['total_visits']++;
        
        $today = date('Y-m-d');
        if (isset($stats['daily_visits'][$today])) {
            $stats['daily_visits'][$today]++;
        } else {
            $stats['daily_visits'][$today] = 1;
        }
        
        $referrer = $_SERVER['HTTP_REFERER'] ?? 'direct';
        $host = parse_url($referrer, PHP_URL_HOST);
        $source = $host ? $host : 'direct';
        
        // if (isset($stats['referrers'][$source])) {
            // $stats['referrers'][$source]++;
        // } else {
            // $stats['referrers'][$source] = 1;
        // }
        
        // file_put_contents($this->statsFile, json_encode($stats));
    // }
    
            // 添加位置记录
        if (!isset($stats['locations'][$location])) {
            $stats['locations'][$location] = 0;
        }
        $stats['locations'][$location]++;
        
        file_put_contents($this->statsFile, json_encode($stats));
    }
    
    
        private function getLocationFromIP($ip) {
        // 简单实现 - 实际项目应使用IP地理位置API
        $ipHash = md5($ip);
        $cities = ['北京', '上海', '广州', '深圳', '成都', '重庆', '武汉', '杭州'];
        $index = hexdec(substr($ipHash, 0, 1)) % count($cities);
        return $cities[$index];
    }
    
    public function getVisitorLocations() {
        $stats = $this->getStats();
        return $stats['locations'] ?? [];
    }
    
    
    
    public function getStats() {
        return json_decode(file_get_contents($this->statsFile), true);
    }
    
    public function getTotalVisits() {
        $stats = $this->getStats();
        return $stats['total_visits'];
    }
    
    public function getTodayVisits() {
        $stats = $this->getStats();
        $today = date('Y-m-d');
        return $stats['daily_visits'][$today] ?? 0;
    }
    
    public function getTopReferrers($limit = 5) {
        $stats = $this->getStats();
        arsort($stats['referrers']);
        return array_slice($stats['referrers'], 0, $limit, true);
    }
}

function sendContactEmail($name, $email, $message) {
    $to = '1728031575@qq.com'; // 替换为您的邮箱
    $subject = '来自个人主页的留言 - ' . $name;
    
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;'>您收到一条新留言</h2>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;'>
                    <p><strong style='color: #2c3e50;'>姓名：</strong> $name</p>
                    <p><strong style='color: #2c3e50;'>邮箱：</strong> <a href='mailto:$email'>$email</a></p>
                    <p><strong style='color: #2c3e50;'>留言内容：</strong></p>
                    <div style='background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #3498db; margin-top: 10px;'>
                        <p style='line-height: 1.6;'>".nl2br($message)."</p>
                    </div>
                </div>
                <p style='margin-top: 30px; text-align: center; color: #7f8c8d; font-size: 14px;'>
                    此邮件来自您的个人主页联系表单
                </p>
            </div>";
    
    return mail($to, $subject, $body, $headers);
}

// 记录登录活动
function logLoginActivity($username) {
    $logEntry = "login|{$username} 登录系统|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
}

// 记录登出活动
function logLogoutActivity($username) {
    $logEntry = "logout|{$username} 退出系统|" . date('Y-m-d H:i:s');
    file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
}

ob_end_flush();