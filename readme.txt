=== 5centsCDN - WordPress CDN Plugin ===
Contributors: 5centsCDN
Tags: cdn, content delivery network, performance, bandwidth
Requires at least: 3.8
Tested up to: 5.7.2
Stable tag: trunk
Version: 21.6
Requires PHP: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

**Speed up your website with 5centsCDN** Content Delivery Network. This plugin allows you to easily enable 5centsCDN on your WordPress website and enjoy greatly improved loading times around the world. Even better, it takes just a minute to set up. To Enable CDN web acceleration on your WordPress website using 5centsCDN Content Delivery Network. Simply enable the plugin and select the pull zone created on the CDN control panel. Enjoy world-class acceleration with 5centsCDN powered by Akamai (Enterprise plan)!

== Description ==

**Speed up your website with 5centsCDN** Content Delivery Network. This plugin allows you to easily **enable 5centsCDN on your WordPress** 
website and enjoy greatly improved loading times around the world. Even better, it takes just a minute to set up. To Enable CDN web acceleration on your WordPress website using 5centsCDN Content Delivery Network. Simply enable the plugin and select the pull zone created on the CDN control panel. Enjoy world-class acceleration with 5centsCDN powered by Akamai (Enterprise plan)!

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
* Purge cache enabled
* CDN enable and disable option added

= System Requirements =
* PHP >=5.3
* WordPress >=3.8

= Author =
* [5centsCDN](https://5centscdn.net "5centsCDN")

== Frequently Asked Questions ==

== Changelog ==

= 21.6 =
* added register_deactivation_hook to delete the option set by the module
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

1. 5centsCDN settings page
2. Cache settings page
3. Purge cache page

== Releasenotes ==

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
Bugfix release