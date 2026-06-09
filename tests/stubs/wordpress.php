<?php

// Plugin constants
if (!defined('FIVECENTSCDN_DEFAULT_DIRECTORIES')) {
    define('FIVECENTSCDN_DEFAULT_DIRECTORIES', 'wp-content,wp-includes');
}
if (!defined('FIVECENTSCDN_DEFAULT_EXCLUDED')) {
    define('FIVECENTSCDN_DEFAULT_EXCLUDED', '.php');
}
if (!defined('ABSPATH')) {
    define('ABSPATH', '/fake/wp/');
}

// Controlled by tests via $GLOBALS['_test_admin_bar_showing']
if (!function_exists('is_admin_bar_showing')) {
    function is_admin_bar_showing(): bool {
        return $GLOBALS['_test_admin_bar_showing'] ?? false;
    }
}

if (!function_exists('get_option')) {
    function get_option(string $key, mixed $default = false): mixed {
        return $GLOBALS['_test_options'][$key] ?? $default;
    }
}

if (!function_exists('wp_parse_args')) {
    function wp_parse_args(mixed $args, array $defaults = []): array {
        if (is_array($args)) {
            return array_merge($defaults, $args);
        }
        return $defaults;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr(string $text): string {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}
