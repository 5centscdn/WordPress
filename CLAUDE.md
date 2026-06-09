# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What This Plugin Does

5centsCDN is a WordPress plugin that integrates 5centsCDN's global CDN infrastructure. It rewrites static asset URLs via PHP output buffering, replacing them with CDN domain equivalents. Two acceleration modes exist: **Asset Acceleration** (rewrites only assets in specified directories like `wp-content`, `wp-includes`) and **Whole Website Acceleration** (rewrites all content, requires a custom CNAME).

## Development Commands

No build system exists. PHP and JS are edited directly — no compilation or transpilation step.

**PHP dependencies (Guzzle HTTP client):**
```
composer install
composer update
```

**Testing the plugin:** Install it in a local WordPress environment (e.g., Local by Flywheel, XAMPP, or wp-env). There are no automated tests.

## Architecture

### Entry Point

[5centscdn.php](5centscdn.php) is the WordPress plugin entry point. It:
- Defines constants (`FIVECENTSCDN_PLUGIN_DIR`, `FIVECENTSCDN_PULLZONEDOMAIN`, `FIVECENTSCDN_DEFAULT_DIRECTORIES`, etc.)
- Registers the custom `spl_autoload_register()` to load classes from `/inc/`
- Hooks all WordPress actions and filters (admin menu, AJAX endpoints, frontend URL rewriting, admin bar, deactivation/uninstall cleanup)

### Core Classes

| File | Class | Responsibility |
|------|-------|---------------|
| [inc/fivecentscdnSettings.php](inc/fivecentscdnSettings.php) | `FivecentsCDN` | Options management (`getOptions`, `validateSettings`, `cleanHostname`) |
| [inc/fivecentscdnSettings.php](inc/fivecentscdnSettings.php) | `FivecentsCDNSettings` | Admin UI rendering, settings page with two tabs |
| [inc/fivecentscdnFilter.php](inc/fivecentscdnFilter.php) | `FivecentsCDNFilter` | Output buffer URL rewriting via regex |
| [inc/fivecentscdnApi.php](inc/fivecentscdnApi.php) | `FivecentsCDNApi` | GuzzleHTTP client for `https://api.5centscdn.com/v2/` |

### Data Flow

1. Admin saves API key → plugin fetches zones from 5centsCDN API (`/zones/http/pull`)
2. Admin selects zone → plugin fetches zone details (SSL, HTTP2, CNAMEs via `FivecentsCDNApi`)
3. Settings stored in WordPress Options API under key `'5centscdn'` (serialized array)
4. On each frontend page load: `template_redirect` activates `FivecentsCDNFilter::startRewrite()`, which wraps PHP output in a buffer
5. Before output is sent, regex replaces matching asset URLs with the CDN domain prefix

### AJAX Endpoints

All endpoints use `wp_ajax_` hooks (admin-only). Defined in [5centscdn.php](5centscdn.php), handlers inline:

- `fivecentscdn_purge` — purge entire zone cache
- `fivecentscdn_zone` — fetch single zone details
- `fivecentscdn_all_zones` — list all zones for the API key
- `fivecentscdn_update_zone_ssl` — update SSL/HTTP2/redirect settings
- `fivecentscdn_purge_file` — purge specific file URLs
- `fivecentscdn_cname_update` — add CNAME to zone

### Frontend JavaScript

[assets/js/5centscdn-backend.js](assets/js/5centscdn-backend.js) handles the entire admin settings page UI. It is not minified or bundled. Key areas:

- `setzone()` — populates pull zone dropdown via AJAX
- `update_zone_ssl()` — sends SSL/HTTP2 config to WordPress AJAX
- `purgecache()` / `purgecacheFile()` — triggers cache purge operations
- `submitForm()` — validates all fields before settings form submission
- `acceleration()` — toggles form state between Asset vs Whole Site modes

### API Client

[inc/fivecentscdnApi.php](inc/fivecentscdnApi.php) uses GuzzleHTTP 6 (`vendor/`). All requests send `x-api-key` header. Responses are parsed with `json_decode()`. Guzzle is configured with `'http_errors' => false` so 4xx/5xx responses don't throw — callers must check the response body manually.

### Versioning

Version numbers follow `YY.MM.DD` format (e.g., `25.12.18`). Update in both [5centscdn.php](5centscdn.php) header and [readme.txt](readme.txt).

## Key Constants

Defined in [5centscdn.php](5centscdn.php):

- `FIVECENTSCDN_PULLZONEDOMAIN` = `"5centscdn.net"`
- `FIVECENTSCDN_DOMAIN` = `"https://www.5centscdn.net/dashboard"`
- `FIVECENTSCDN_DEFAULT_DIRECTORIES` = `"wp-content,wp-includes"`
- `FIVECENTSCDN_DEFAULT_EXCLUDED` = `".php"`
