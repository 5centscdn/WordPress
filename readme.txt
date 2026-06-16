=== 5centsCDN - WordPress CDN Plugin ===
Contributors: 5centsCDN
Tags: optimize, cdn, content delivery network, performance, caching
Requires at least: 3.8
Tested up to: 7.0
Stable tag: trunk
Version: 26.06.09
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Optimize WordPress speed and performance with 5centsCDN plugin. Get advanced caching, CDN, and seamless optimization today!

== Description ==

Optimize WordPress speed and performance with 5centsCDN plugin. Get advanced caching, CDN, and seamless optimization today!

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= How does it work? =
The plugin will automatically configure your website to replace existing static content links with the CDN links, greatly speeding up your content.

= Features =
* Enter API key get pull zone from API
* Replace static links with CDN links
* Automatic HTTPS configuration
* Include or exclude specific directories or phrases
* Set a custom CDN hostname
* Show HTTP2 status and HTTP status
* CDN Cache Purging

= System Requirements =
* PHP >=5.3
* WordPress >=3.8

= Author =
* [5centsCDN](https://5centscdn.net "5centsCDN")

== FAQ ==

= Q: How can I obtain the API key for 5centsCDN? =
A: To get your API key, follow these steps:
1. Log into your 5centsCDN Dashboard.
2. Navigate to the sidebar and select Account > API.
3. Click the + button to generate a new API key.
4. Assign all the necessary permissions and save it.

Once you have your API key, go back to your WordPress site, paste the API key, and click “Connect” to complete the setup.

= Q: What is the difference between Asset Acceleration and Whole Site Acceleration? =
A: Asset Acceleration delivers specific content types like images (JPEG, PNG, AVIF), documents (PDF), and similar files using the CDN. Whole Site Acceleration, on the other hand, delivers the entire website including HTML, CSS, JS, fonts, and more. For optimal performance, we recommend using Whole Site Acceleration.

== Changelog ==

= 26.06.09 =
* Plugin Supports WordPress Version 7.0
* Added built-in debug mode for URL rewrite tracing
* Fixed PHP 8 compatibility: array_push on null excludedPhrases
* Fixed PHP 8 compatibility: count() on undefined variable in zone list
* Fixed str_replace("http") mangling paths containing "http" substrings
* Improved error messages for better clarity and debugging
* Fixed issues with enabling and disabling the CDN
* Fixed zone list loading and display issues
* Fixed URL rewrite regex to match both http:// and https:// asset URLs for protocol-agnostic CDN replacement

= 25.4.15 =
* Plugin Supports WordPress Version 6.8

= 24.8.16 =
* Plugin Supports WordPress Version 6.6.1
* FAQs Added 

= 24.8.12 =
* Plugin Supports WordPress Version 6.6.1
* Code Improvements 
* Improved Whole Website Acceleration 

= 23.11.20 =
* Plugin Supports WordPress Version 6.4.1
* Code Improvements

= 23.10.9 =
* Plugin Supports WordPress Version 6.3.1
* Code Improvements
* Improved UI/UX

= 23.9.1 =
* Plugin Supports WordPress Version 6.3.1
* Fixed Bugs that prevented Asset Acceleration to connect to CDN for some users
* Code Improvements

= 23.6.23 =
* Plugin Supports WordPress Version 6.2.2
* Fixed text issues in the description
* Improved UI/UX
* Code Improvements
* Documentation Improvements

= 22.11.21 =
* Plugin Supports WordPress Version 6.1.1
* Bug Fix: Asset Delivery from HTTP > HTTPS
* Code Improvements

= 22.7.27 =
* Bug Fix: api call made on each page load
* Code Improvements
= 22.7.08 =
* Improved Documentation
* Showing zone alias names
= 22.3.09 =
* Improved UI/UX
* Added Asset Acceleration / Wholesite Acceleration
* Option to enable or disable SSL, HTTP/2 from within the plugin
* SSL warnings & only allow plugin to be enabled when SSL is activated in CDN
* Improved Purge Animations
= 21.6.11 =
* trim trailing white spaces from the php files
= 21.6 =
* added register_deactivation_hook to delete the plugin options set by the module
* changed 5centsCDN API version from v1 => v2
= 21.1 =
* Change button text for saving options to 'Save Settings'
* GuzzleClient disable throwing errors on http_errors
* added register_uninstall_hook to delete the option set by the module
= 20.1 =
* BrandKit Update
= 20.0 =
* Initial release

== Screenshots ==

1. Connect with CDN
2. Asset Acceleration: API & SSL Configuration
3. Asset Acceleration: Cache Settings
4. Asset Acceleration: Plugin Enabled
5. Whole Website Acceleration: with proper CNAME configured
6. Cache Purging: Purge All / Selected files / Post / Page

== Release Notes ==

= 26.06.09 (JUNE 09TH, 2026) =
* Plugin Supports WordPress Version 7.0
* Added built-in debug mode for URL rewrite tracing (define FIVECENTSCDN_DEBUG to enable)
* Fixed PHP 8 compatibility: array_push on null excludedPhrases
* Fixed PHP 8 compatibility: count() on undefined variable in zone list
* Fixed str_replace("http") mangling paths containing "http" substrings
* Improved error messages for better clarity and debugging
* Fixed issues with enabling and disabling the CDN
* Fixed zone list loading and display issues
* Fixed URL rewrite regex to match both http:// and https:// asset URLs for protocol-agnostic CDN replacement

= 25.4.15 (APRIL 15TH, 2025) =
* Plugin Supports WordPress Version 6.8

= 24.8.16 (AUGUST 16TH, 2024) =
* Plugin Supports WordPress Version 6.6.1
* FAQs Added

= 24.8.12 (AUGUST 12TH, 2024) =
* Plugin Supports WordPress Version 6.6.1
* Code Improvements 
* Improved Whole Website Acceleration 

= 23.11.20 (NOVEMBER 20TH, 2023) =
* Plugin Supports WordPress Version 6.4.1
* Code Improvements 

= 23.10.9 (OCTOBER 9TH, 2023) =
* Plugin Supports WordPress Version 6.3.1
* Code Improvements
* Improved UI/UX

= 23.9.1 (SEPTEMBER 1ST, 2023) =
* Plugin Supports WordPress Version 6.3.1
* Fixed Bugs that prevented Asset Acceleration to connect to CDN for some users
* Code Improvements

= 23.6.23 (JUNE 23RD, 2023) =
* Plugin Supports WordPress Version 6.2.2
* Fixed text issues in the description
* Improved UI/UX
* Code Improvements
* Documentation Improvements

= 22.11.21 (NOVEMBER 21th, 2022) =
* Plugin Supports WordPress Version 6.1.1
* Bug Fix: Asset Delivery from HTTP > HTTPS
* Code Improvements

= 22.7.27 (July 27th, 2022) =
* Bug Fix: api call made on each page load
* Code Improvements
= 22.7.08 (July 8th, 2022) =
* Improved Documentation
* Showing zone alias names
= 22.3.09 (March 9th, 2022) =
* Improved UI/UX
* Added Asset Acceleration / Wholesite Acceleration
* Option to enable or disable SSL, HTTP/2 from within the plugin
* SSL warnings & only allow plugin to be enabled when SSL is activated in CDN
* Improved Purge Animations

= 21.6.11 (June 11th, 2021) =
* trim trailing white spaces from the php files

= 21.6 (June 3rd, 2021) =
* added register_deactivation_hook to delete the option set by the module
* changed 5centsCDN API version from v1 => v2

= 21.1 (January 29th, 2021) =
* Change button text for saving options to 'Save Settings'
* GuzzleClient disable throwing errors on http_errors
* added register_uninstall_hook to delete the option set by the module

= 20.1 (January 8th, 2021) =
* 5centsCDN BrandKit Update to v3

= 20.0 (January 22th, 2020) =
* Initial release.
* Enter API key get pull zone from API
* Replace static links with CDN links
* Automatic HTTPS configuration
* Include or exclude specific directories or phrases
* Set a custom CDN hostname
* Show HTTP2 status and HTTP status
* Purge cache enabled
* CDN enable and disable option added

## Upgrade Notice ##
UI/UX improvements
