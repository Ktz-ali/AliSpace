<?php
ob_start();
session_start();

// 设置禁止缓存
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 包含数据模型
require_once 'data.php';

$action = $_GET['action'] ?? '';
$dataHandler = new DataHandler();
$visitorStats = new VisitorStats();

switch ($action) {
    case 'update-setting':
        handleUpdateSetting($dataHandler);
        break;
        
    case 'get-quote':
        getDailyQuote();
        break;
        
    case 'get-resume':
        downloadResume();
        break;
        
    case 'contact':
        handleContactForm();
        break;
        
    case 'visitor-stats':
        getVisitorStats($visitorStats);
        break;
        
    case 'guestbook':
        handleGuestbook();
        break;
        
    case 'delete-msg':
        deleteGuestbookMessage();
        break;
        
    case 'get-blog-posts':
        getBlogPosts($dataHandler);
        break;
        
        case 'save-themes':
    saveThemesCSS();
    break;
    
    
    default:
        sendErrorResponse('Invalid action');
}




// 保存CSS文件
function saveThemesCSS() {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        sendErrorResponse('Unauthorized');
    }
    
    if (!isset($_POST['css_content'])) {
        sendErrorResponse('Missing CSS content');
    }
    
    $themeManager = new ThemeManager();
    if (file_put_contents($themeManager->themesFile, $_POST['css_content'])) {
        // 记录活动日志
        $logEntry = "update|更新了主题CSS|" . date('Y-m-d H:i:s');
        file_put_contents('data/activity.log', $logEntry . PHP_EOL, FILE_APPEND);
        sendSuccessResponse(['message' => 'CSS保存成功']);
    } else {
        sendErrorResponse('CSS保存失败');
    }
}

// 处理设置更新
function handleUpdateSetting($dataHandler) {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        sendErrorResponse('Unauthorized');
    }
    
    $json = file_get_contents('php://input');
    $request = json_decode($json, true);
    
    // 新增：支持保存全局特效设置
    if (isset($request['setting'], $request['value'])) {
        $setting = $request['setting'];
        $value = $request['value'];
        $data = $dataHandler->getData();
        
        // 新增特效设置保存
        if ($setting === 'global_effect') {
            // 验证特效值有效性
            $validEffects = ['themes', 'xali', 'alinb', 'aliyyds'];
            if (!in_array($value, $validEffects)) {
                sendErrorResponse('无效的特效选择');
            }
        }
        
        $data['settings'][$setting] = $value;
        $dataHandler->saveData($data);
        sendSuccessResponse();
    } else {
        sendErrorResponse('Missing parameters');
    }
}

// 获取每日名言
function getDailyQuote() {
    $quotes = [
        "代码如诗，简洁即美。",
        "技术不是目的，而是解决问题的工具。",
        "优秀程序员写出人类能理解的代码，伟大程序员写出机器能执行的代码。",
        "编程是创造世界的第二法则。"
    ];
    header('Content-Type: application/json');
    echo json_encode(['quote' => $quotes[array_rand($quotes)]]);
    exit;
}

// 下载简历
function downloadResume() {
    $resumeFile = 'resume.pdf';
    if (file_exists($resumeFile)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.basename($resumeFile).'"');
        readfile($resumeFile);
        exit;
    } else {
        http_response_code(404);
        echo '简历文件不存在';
        exit;
    }
}

// 处理联系表单
function handleContactForm() {
    $success = false;
    $error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = htmlspecialchars($_POST['name'] ?? '');
        $email = htmlspecialchars($_POST['email'] ?? '');
        $message = htmlspecialchars($_POST['message'] ?? '');
        
        if (empty($name) || empty($email) || empty($message)) {
            $error = '请填写所有必填字段';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = '邮箱格式不正确';
        } else {
            if (sendContactEmail($name, $email, $message)) {
                $success = true;
            } else {
                $error = '邮件发送失败，请稍后再试';
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $success ? '留言已发送' : $error
    ]);
    exit;
}

// 获取访客统计
function getVisitorStats($visitorStats) {
    $stats = $visitorStats->getStats();
    header('Content-Type: application/json');
    echo json_encode([
        'total' => $stats['total_visits'],
        'today' => $stats['daily_visits'][date('Y-m-d')] ?? 0,
        'top_referrers' => $visitorStats->getTopReferrers(5)
    ]);
    exit;
}

// 处理留言板
function handleGuestbook() {
    $guestbookFile = 'data/guestbook.json';
    
    if (!is_dir('data')) {
        mkdir('data', 0755, true);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (file_exists($guestbookFile)) {
            header('Content-Type: application/json');
            echo file_get_contents($guestbookFile);
            exit;
        } else {
            file_put_contents($guestbookFile, json_encode(['messages' => []]));
            header('Content-Type: application/json');
            echo json_encode(['messages' => []]);
            exit;
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $messages = [];
        
        if (file_exists($guestbookFile)) {
            $messages = json_decode(file_get_contents($guestbookFile), true)['messages'];
        }
        
        $newMessage = [
            'id' => uniqid(),
            'name' => htmlspecialchars($data['name'] ?? '访客'),
            'message' => htmlspecialchars($data['message']),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        array_unshift($messages, $newMessage);
        file_put_contents($guestbookFile, json_encode(['messages' => $messages]));
        
        header('Content-Type: application/json');
        echo json_encode($newMessage);
        exit;
    }
}

// 删除留言
function deleteGuestbookMessage() {
    $id = $_GET['id'] ?? '';
    if ($id) {
        $guestbookFile = 'data/guestbook.json';
        if (file_exists($guestbookFile)) {
            $data = json_decode(file_get_contents($guestbookFile), true);
            $data['messages'] = array_filter($data['messages'], fn($msg) => $msg['id'] !== $id);
            file_put_contents($guestbookFile, json_encode($data));
            sendSuccessResponse();
        }
    }
    sendErrorResponse('删除失败');
}

// 获取博客文章
function getBlogPosts($dataHandler) {
    $data = $dataHandler->getData();
    $blogPosts = $data['blogPosts'] ?? [];
    header('Content-Type: application/json');
    echo json_encode($blogPosts);
    exit;
}



// 辅助函数：发送成功响应
function sendSuccessResponse($data = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}

// 辅助函数：发送错误响应
function sendErrorResponse($message) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

ob_end_flush();