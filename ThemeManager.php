<?php
class ThemeManager {
    private $themesFile = 'themes/main.css';
    private $themeConfig = 'data/themes.json';

    public function __construct() {
        if (!is_dir('data')) mkdir('data', 0755, true);
        
        if (!file_exists($this->themeConfig)) {
            file_put_contents($this->themeConfig, json_encode([]));
        }
    }

    public function getAvailableThemes() {
        // 从CSS文件提取所有主题信息
        $cssContent = file_exists($this->themesFile) ? file_get_contents($this->themesFile) : '';
        
        // 主题名称映射
        $themeNameMap = [
            '.theme-purple' => '紫色科技',
            '.theme-green' => '绿色自然',
            '.theme-blue' => '蓝色海洋',
            '.theme-orange' => '橙色火焰',
            '.theme-pink' => '粉色梦幻',
            '.theme-dark' => '深色未来'
        ];
        
        // 从CSS内容中提取主题
        preg_match_all('/\.(theme-\w+)\s*\{([^\}]+)\}/', $cssContent, $matches, PREG_SET_ORDER);
        
        $themes = [];
        foreach ($matches as $match) {
            $className = $match[1];
            $cssContent = $match[2];
            
            // 提取颜色变量
            $primary = $this->extractCSSVariable($cssContent, '--primary') ?: '#6c5ce7';
            $secondary = $this->extractCSSVariable($cssContent, '--secondary') ?: '#a29bfe';
            $accent = $this->extractCSSVariable($cssContent, '--accent') ?: '#fd79a8';
            $text = $this->extractCSSVariable($cssContent, '--font-primary') ?: '#2d3436';
            $bg = $this->extractBackgroundColor($cssContent) ?: '#6c5ce7';
            
            $displayName = $themeNameMap['.'.$className] ?? ucfirst(str_replace('theme-', '', $className));
            
            $themes[] = [
                'name' => $className,
                'display_name' => $displayName,
                'color' => $primary,
                'secondary' => $secondary,
                'accent' => $accent,
                'text' => $text,
                'bg' => $bg
            ];
        }
        
        return $themes;
    }
    
    // 提取CSS变量值
    private function extractCSSVariable($cssContent, $variableName) {
        if (preg_match("/{$variableName}:\s*(#[0-9a-f]{3,6}|[a-z]+\([^)]+\));/i", $cssContent, $matches)) {
            return trim($matches[1], '; ');
        }
        return null;
    }
    
    // 提取背景色（渐变中的第一个颜色）
    private function extractBackgroundColor($cssContent) {
        if (preg_match('/background:\s*linear-gradient\([^,]+\s*,\s*([^,]+)/i', $cssContent, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
