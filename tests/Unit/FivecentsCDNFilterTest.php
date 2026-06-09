<?php

// ─── Constructor ────────────────────────────────────────────────────────────

describe('constructor', function () {

    it('initializes excludedPhrases as array when phrases are empty', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '');
        expect($f->excludedPhrases)->toBeArray();
    });

    it('always appends ] and ( regardless of user excludes', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '');
        expect($f->excludedPhrases)->toContain(']')->toContain('(');
    });

    it('parses comma-separated excluded phrases', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '.php,.xml');
        expect($f->excludedPhrases)->toContain('.php')->toContain('.xml');
    });

    it('trims whitespace from each excluded phrase', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', ' .php , .xml ');
        expect($f->excludedPhrases)->toContain('.php')->toContain('.xml');
    });

    it('falls back to default directories when directories is empty', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', '', '');
        expect($f->directories)->not->toBeEmpty();
    });

    it('applies quotemeta to directory names (hyphens are not special chars)', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content,wp-includes', '');
        // quotemeta does not escape hyphens; confirms directories are stored as-is
        expect($f->directories)->toContain('wp-content')->toContain('wp-includes');
    });

    it('strips blank entries from directories', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content,,wp-includes', '');
        expect(array_values($f->directories))->not->toContain('');
    });

});

// ─── Relative URL rewriting ─────────────────────────────────────────────────

describe('rewrite — relative URLs', function () {

    beforeEach(function () {
        $this->f = makeFilter('https://example.com', 'https://cdn.example.com');
    });

    it('rewrites relative URL in double-quoted src', function () {
        $html = '<script src="/wp-includes/js/jquery.min.js"></script>';
        expect($this->f->rewrite($html))
            ->toContain('https://cdn.example.com/wp-includes/js/jquery.min.js');
    });

    it('rewrites relative URL in single-quoted href', function () {
        $html = "<link href='/wp-content/themes/theme/style.css'>";
        expect($this->f->rewrite($html))
            ->toContain('https://cdn.example.com/wp-content/themes/theme/style.css');
    });

    it('rewrites root-level asset with extension (favicon, robots, etc.)', function () {
        $html = '<img src="/favicon.ico">';
        expect($this->f->rewrite($html))
            ->toContain('https://cdn.example.com/favicon.ico');
    });

    it('rewrites multiple URLs in a single HTML string', function () {
        $html = '<link href="/wp-content/style.css"><script src="/wp-includes/js/app.js"></script>';
        $result = $this->f->rewrite($html);
        expect($result)
            ->toContain('https://cdn.example.com/wp-content/style.css')
            ->toContain('https://cdn.example.com/wp-includes/js/app.js');
    });

    it('does not rewrite paths outside specified directories', function () {
        $html = '<link href="/custom-dir/style.css">';
        expect($this->f->rewrite($html))->not->toContain('cdn.example.com/custom-dir');
    });

});

// ─── Absolute URL rewriting ─────────────────────────────────────────────────

describe('rewrite — absolute URLs', function () {

    beforeEach(function () {
        $this->f = makeFilter('https://example.com', 'https://cdn.example.com');
    });

    it('replaces baseUrl with cdnUrl in absolute URL', function () {
        $html = '<img src="https://example.com/wp-content/uploads/photo.jpg">';
        $result = $this->f->rewrite($html);
        expect($result)
            ->toContain('https://cdn.example.com/wp-content/uploads/photo.jpg')
            ->not->toContain('https://example.com/wp-content');
    });

    it('does not double-rewrite a URL matched once', function () {
        $html = '<script src="/wp-content/themes/theme/script.js"></script>';
        $result = $this->f->rewrite($html);
        expect(substr_count($result, 'cdn.example.com'))->toBe(1);
    });

});

// ─── Exclusions ─────────────────────────────────────────────────────────────

describe('rewrite — exclusions', function () {

    it('skips .php files when .php is in excluded phrases', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content,wp-includes', '.php');
        $html = '<a href="/xmlrpc.php">';
        expect($f->rewrite($html))->not->toContain('cdn.example.com');
    });

    it('skips any URL containing a custom excluded phrase', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '.xml');
        $html = '<link href="/wp-content/feeds/feed.xml">';
        expect($f->rewrite($html))->not->toContain('cdn.example.com');
    });

    it('rewrites .php files when .php is NOT in excluded phrases', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content,wp-includes', '');
        $html = '<a href="/xmlrpc.php">';
        expect($f->rewrite($html))->toContain('cdn.example.com/xmlrpc.php');
    });

    it('skips URLs that contain ( character (hardcoded exclusion)', function () {
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '');
        // Simulate a URL fragment that contains a literal ( — the regex won't capture it
        // because [^"')] stops at ) and the lookbehind eats (, but if somehow matched,
        // the excludedPhrases check for "(" prevents rewriting.
        $html = '<a href="/wp-content/file(1).css">';
        expect($f->rewrite($html))->not->toContain('cdn.example.com');
    });

});

// ─── Admin bar skip ─────────────────────────────────────────────────────────

describe('rewrite — disableForAdmin', function () {

    afterEach(function () {
        $GLOBALS['_test_admin_bar_showing'] = false;
    });

    it('skips rewriting when admin bar is showing and disableForAdmin is true', function () {
        $GLOBALS['_test_admin_bar_showing'] = true;
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '', true);
        $html = '<link href="/wp-content/style.css">';
        expect($f->rewrite($html))->not->toContain('cdn.example.com');
    });

    it('rewrites normally when admin bar is showing but disableForAdmin is false', function () {
        $GLOBALS['_test_admin_bar_showing'] = true;
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '', false);
        $html = '<link href="/wp-content/style.css">';
        expect($f->rewrite($html))->toContain('cdn.example.com');
    });

    it('rewrites normally when admin bar is not showing', function () {
        $GLOBALS['_test_admin_bar_showing'] = false;
        $f = makeFilter('https://example.com', 'https://cdn.example.com', 'wp-content', '', true);
        $html = '<link href="/wp-content/style.css">';
        expect($f->rewrite($html))->toContain('cdn.example.com');
    });

});
