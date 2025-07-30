<?php
class LayoutManager {
    private $layoutsDir = 'layouts/';
    private $layoutConfig = 'data/layouts.json';

    public function __construct() {
        if (!is_dir('data')) mkdir('data', 0755, true);
        if (!file_exists($this->layoutConfig)) {
            file_put_contents($this->layoutConfig, json_encode([]));
        }
    }

    public function getAvailableLayouts() {
        $layouts = [];
        $files = glob($this->layoutsDir . '*.php');
        
        foreach ($files as $file) {
            $layoutName = basename($file, '.php');
            $layouts[] = [
                'name' => $layoutName,
                'display_name' => ucfirst($layoutName),
                'created' => date('Y-m-d', filemtime($file))
            ];
        }
        
        return $layouts;
    }

    public function uploadLayout($file) {
        $filename = $file['name'];
        $tempPath = $file['tmp_name'];
        
        // 确保是PHP文件
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'php') {
            return ['success' => false, 'message' => '仅支持PHP文件'];
        }
        
        // 直接保存到layouts目录
        $targetPath = $this->layoutsDir . $filename;
        
        if (move_uploaded_file($tempPath, $targetPath)) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => '上传失败'];
    }

    public function deleteLayout($layoutName) {
        $filePath = $this->layoutsDir . $layoutName . '.php';
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    public function generateRandomLayout() {
        $layoutName = 'layout_random_' . uniqid();
        $layoutFile = $this->layoutsDir . $layoutName . '.php';
        
        $phpContent = <<<PHP
<?php
echo '<div class="random-layout glass-card">';
echo '  <h2>随机布局</h2>';
echo '  <p>此布局于' . date('Y-m-d') . '自动生成</p>';
echo '</div>';
?>
PHP;
        file_put_contents($layoutFile, $phpContent);
        
        return $layoutName;
    }
}
?>