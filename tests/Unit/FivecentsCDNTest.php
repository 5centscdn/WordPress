<?php

// ─── cleanHostname ───────────────────────────────────────────────────────────

describe('FivecentsCDN::cleanHostname', function () {

    it('strips http://', function () {
        expect(FivecentsCDN::cleanHostname('http://cdn.example.com'))->toBe('cdn.example.com');
    });

    it('strips https://', function () {
        expect(FivecentsCDN::cleanHostname('https://cdn.example.com'))->toBe('cdn.example.com');
    });

    it('strips trailing slash', function () {
        expect(FivecentsCDN::cleanHostname('cdn.example.com/'))->toBe('cdn.example.com');
    });

    it('strips https:// and trailing slash together', function () {
        expect(FivecentsCDN::cleanHostname('https://cdn.example.com/'))->toBe('cdn.example.com');
    });

    it('returns plain hostname unchanged', function () {
        expect(FivecentsCDN::cleanHostname('cdn.example.com'))->toBe('cdn.example.com');
    });

    it('returns empty string for empty input', function () {
        expect(FivecentsCDN::cleanHostname(''))->toBe('');
    });

});

// ─── startsWith ──────────────────────────────────────────────────────────────

describe('FivecentsCDN::startsWith', function () {

    it('returns true when haystack starts with needle', function () {
        expect(FivecentsCDN::startsWith('https://example.com', 'https://'))->toBeTrue();
    });

    it('returns false when haystack does not start with needle', function () {
        expect(FivecentsCDN::startsWith('http://example.com', 'https://'))->toBeFalse();
    });

    it('returns true for empty needle', function () {
        expect(FivecentsCDN::startsWith('anything', ''))->toBeTrue();
    });

    it('returns false when needle is longer than haystack', function () {
        expect(FivecentsCDN::startsWith('hi', 'hello'))->toBeFalse();
    });

});

// ─── endsWith ────────────────────────────────────────────────────────────────

describe('FivecentsCDN::endsWith', function () {

    it('returns true when haystack ends with needle', function () {
        expect(FivecentsCDN::endsWith('style.css', '.css'))->toBeTrue();
    });

    it('returns false when haystack does not end with needle', function () {
        expect(FivecentsCDN::endsWith('style.js', '.css'))->toBeFalse();
    });

    it('returns true for empty needle', function () {
        expect(FivecentsCDN::endsWith('anything', ''))->toBeTrue();
    });

    it('returns false when needle is longer than haystack', function () {
        expect(FivecentsCDN::endsWith('hi', 'hello'))->toBeFalse();
    });

});

// ─── validateSettings ────────────────────────────────────────────────────────

describe('FivecentsCDN::validateSettings', function () {

    beforeEach(function () {
        $GLOBALS['_test_options']['home'] = 'https://example.com';
    });

    it('casts wp_disble_cdn to int', function () {
        $result = FivecentsCDN::validateSettings([
            'cdn_domain_name'    => 'cdn.example.com',
            'pull_zone'          => '42',
            'excluded'           => '.php',
            'directories'        => 'wp-content',
            'web_site_url'       => 'https://example.com',
            'serviceid'          => '7',
            'api_key'            => 'abc123',
            'wp_disble_cdn'      => '1',
            'disable_admin'      => '0',
            'asset_acceleration' => '0',
            'https'              => true,
        ]);
        expect($result['wp_disble_cdn'])->toBe(1);
    });

    it('casts serviceid to int', function () {
        $result = FivecentsCDN::validateSettings([
            'cdn_domain_name'    => 'cdn.example.com',
            'pull_zone'          => '42',
            'excluded'           => '.php',
            'directories'        => 'wp-content',
            'web_site_url'       => 'https://example.com',
            'serviceid'          => '99',
            'api_key'            => 'abc123',
            'wp_disble_cdn'      => '0',
            'disable_admin'      => '0',
            'asset_acceleration' => '0',
            'https'              => false,
        ]);
        expect($result['serviceid'])->toBe(99);
    });

    it('appends site path to cdn_domain_name when site is in a subdirectory', function () {
        $GLOBALS['_test_options']['home'] = 'https://example.com/blog';
        $result = FivecentsCDN::validateSettings([
            'cdn_domain_name'    => 'cdn.example.com',
            'pull_zone'          => '1',
            'excluded'           => '.php',
            'directories'        => 'wp-content',
            'web_site_url'       => 'https://example.com/blog',
            'serviceid'          => '1',
            'api_key'            => 'key',
            'wp_disble_cdn'      => '0',
            'disable_admin'      => '0',
            'asset_acceleration' => '0',
            'https'              => false,
        ]);
        expect($result['cdn_domain_name'])->toBe('cdn.example.com/blog');
    });

    it('strips trailing slashes from site_url', function () {
        $GLOBALS['_test_options']['home'] = 'https://example.com///';
        $result = FivecentsCDN::validateSettings([
            'cdn_domain_name'    => '',
            'pull_zone'          => '1',
            'excluded'           => '.php',
            'directories'        => 'wp-content',
            'web_site_url'       => 'https://example.com',
            'serviceid'          => '1',
            'api_key'            => 'key',
            'wp_disble_cdn'      => '0',
            'disable_admin'      => '0',
            'asset_acceleration' => '0',
            'https'              => false,
        ]);
        expect($result['site_url'])->toBe('https://example.com');
    });

    it('casts https to bool', function () {
        $result = FivecentsCDN::validateSettings([
            'cdn_domain_name'    => '',
            'pull_zone'          => '1',
            'excluded'           => '.php',
            'directories'        => 'wp-content',
            'web_site_url'       => 'https://example.com',
            'serviceid'          => '1',
            'api_key'            => 'key',
            'wp_disble_cdn'      => '0',
            'disable_admin'      => '0',
            'asset_acceleration' => '0',
            'https'              => '1',
        ]);
        expect($result['https'])->toBeBool()->toBeTrue();
    });

});
