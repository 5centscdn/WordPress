<?php

require_once __DIR__ . '/stubs/wordpress.php';
require_once __DIR__ . '/../inc/fivecentscdnSettings.php';
require_once __DIR__ . '/../inc/fivecentscdnFilter.php';

pest()->extend(Tests\TestCase::class)->in('Feature');

// Returns a FivecentsCDNFilter subclass with rewrite() and rewriteUrl() made public
function makeFilter(
    string $baseUrl,
    string $cdnUrl,
    string $directories = 'wp-content,wp-includes',
    string $excludedPhrases = '.php',
    bool   $disableForAdmin = false
): FivecentsCDNFilter {
    return new class($baseUrl, $cdnUrl, $directories, $excludedPhrases, $disableForAdmin) extends FivecentsCDNFilter {
        public function rewrite($html)   { return parent::rewrite($html); }
        public function rewriteUrl($asset) { return parent::rewriteUrl($asset); }
    };
}
