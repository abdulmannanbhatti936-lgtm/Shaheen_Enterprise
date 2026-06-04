<?php
/**
 * Image Helper - Phase 2
 * Standardizes image path resolution across Frontend and Backend.
 */
if (!function_exists('formatImgPath')) {
    function formatImgPath($path, $isBackend = false) {
        if (empty($path)) {
            return ($isBackend ? '../../Frontend/' : '') . 'images/placeholder.jpg';
        }
        
        // If it's already a full URL
        if (strpos($path, 'http') === 0) {
            return $path;
        }

        $prefix = $isBackend ? '../../Frontend/' : '';

        // Check if path already starts with uploads/ or images/
        if (strpos($path, 'uploads/') === 0 || strpos($path, 'images/') === 0) {
            return $prefix . $path;
        }

        // Default legacy fallback
        return $prefix . 'images/' . $path;
    }
}
?>
