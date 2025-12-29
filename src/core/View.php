<?php
/**
 * Core View Renderer
 */

namespace Core;

class View
{
    private static ?string $layout = 'main';
    private static array $sections = [];
    private static ?string $currentSection = null;

    /**
     * Render a view
     */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        // Reset sections for fresh render
        self::$sections = [];
        
        // Store data for use in view and partials
        $GLOBALS['_view_data'] = $data;
        
        // Extract data to local scope
        extract($data, EXTR_SKIP);
        
        // Start output buffering
        ob_start();
        
        // Include view file
        $viewPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        include $viewPath;
        
        $content = ob_get_clean();
        
        // Render with layout
        $layoutName = $layout ?? self::$layout;
        if ($layoutName) {
            // Only set content if not already set by section()
            if (!isset(self::$sections['content'])) {
                self::$sections['content'] = $content;
            }
            // Re-extract data for layout - important!
            extract($data, EXTR_SKIP);
            $layoutPath = BASE_PATH . '/app/views/layouts/' . $layoutName . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo self::$sections['content'];
            }
        } else {
            echo $content;
        }
        
        // Clean up
        unset($GLOBALS['_view_data']);
    }

    /**
     * Set layout
     */
    public static function setLayout(?string $layout): void
    {
        self::$layout = $layout;
    }

    /**
     * Start a section
     */
    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    /**
     * End current section
     */
    public static function endSection(): void
    {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    /**
     * Yield a section
     */
    public static function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    /**
     * Include a partial
     */
    public static function partial(string $partial, array $data = []): void
    {
        extract($data);
        $partialPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $partial) . '.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        }
    }

    /**
     * Escape HTML
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
