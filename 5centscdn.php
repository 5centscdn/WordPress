<?php
/*
Plugin Name: 5centsCDN
Text Domain: 5centscdn
Description: Speed up your website with 5centsCDN Content Delivery Network. This plugin allows you to easily enable 5centsCDN on your WordPress website and enjoy greatly improved loading times around the world. Even better, it takes just a minute to set up. To Enable CDN web acceleration on your WordPress website using 5centsCDN Content Delivery Network. Simply enable the plugin and select the pull zone created on the CDN control panel. Enjoy world-class acceleration with 5centsCDN powered by Akamai (Enterprise plan)!
Author: 5centsCDN
Author URI: https://5centscdn.net
License: GPLv2 or later
Version: 22.11.21
*/

/*
Copyright (C)  2020 5centsCDN

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
defined('ABSPATH') OR die();

// Load the paths
if (!defined('FIVECENTSCDN_PLUGIN_FILE'))
define('FIVECENTSCDN_PLUGIN_FILE', __FILE__);
if (!defined('FIVECENTSCDN_PLUGIN_DIR'))
define('FIVECENTSCDN_PLUGIN_DIR', dirname(__FILE__));
if (!defined('FIVECENTSCDN_PLUGIN_BASE'))
define('FIVECENTSCDN_PLUGIN_BASE', plugin_basename(__FILE__));
if (!defined('FIVECENTSCDN_PULLZONEDOMAIN'))
define('FIVECENTSCDN_PULLZONEDOMAIN', "5centscdn.com");
if (!defined('FIVECENTSCDN_DOMAIN'))
define('FIVECENTSCDN_DOMAIN', "https://cp.5centscdn.com/");
if (!defined('FIVECENTSCDN_DEFAULT_DIRECTORIES'))
define('FIVECENTSCDN_DEFAULT_DIRECTORIES', "wp-content,wp-includes");
if (!defined('FIVECENTSCDN_DEFAULT_EXCLUDED'))
define('FIVECENTSCDN_DEFAULT_EXCLUDED', ".php");

// Load everything
spl_autoload_register('fivecentscdn_load_page');
function fivecentscdn_load_page($class)
{
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnSettings.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnFilter.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/vendor/autoload.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnApi.php');
}

function add_theme_scripts() {
  wp_enqueue_style( 'mytheme-options-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
}
add_action( 'admin_enqueue_scripts', 'add_theme_scripts' );


function only_show_option_if_fivecentscdn_cache_is_active() {
  if (is_plugin_active('5centscdn/5centscdn.php')) {
    function clear_all_cached_files_fivecentscdncache() {
      global $wp_admin_bar;
      if (!is_super_admin() || !is_admin_bar_showing()) return;
      $args = [
        'id' => 'delete-cache-completly',
        'title' => 'Purge Cache',
        'href' => wp_nonce_url(admin_url('admin.php?page=5centscdn&wp_delete_cache=1&tab=tab_one'), 'wp-cache'),
        'parent' => '',
        'meta' => [
          'title' => 'Clear all cached files of WP 5centsCDN Cache'
        ]
      ];
      $options = FivecentsCDN::getOptions();

      $wp_admin_bar->add_menu($args);
      $wp_admin_bar->add_menu([
      'id' => 'disable-fivecentscdn',
      'title' => __(($options['wp_disble_cdn'] == "1" ? "Disable CDN": "Enable CDN")),
      'href' => wp_nonce_url(admin_url('admin.php?page=5centscdn&wp_disble_cdn='.($options['wp_disble_cdn'] == "1" ? "0" : "1").'&tab=tab_one'), 'disable-cdn')
      ]);
    }
    add_action('wp_before_admin_bar_render', 'clear_all_cached_files_fivecentscdncache', 999);
  }
}

// Register the settings page and menu
add_action("admin_menu", array("FivecentsCDNSettings", "initialize"));
add_action('admin_init', 'only_show_option_if_fivecentscdn_cache_is_active');
add_action("template_redirect", "doRewriteFivecentsCDN");
add_action("wp_head", "fivecentscdn_dnsPrefetch", 0);
add_action("wp_ajax_fivecentscdn_purge", "fivecentscdn_purge", 0);
add_action("wp_ajax_fivecentscdn_zone", "five5centscdn_zone", 0);
add_action("wp_ajax_fivecentscdn_all_zones", "fivecentscdn_all_zones", 0);

function doRewriteFivecentsCDN()
{
  $options = FivecentsCDN::getOptions();
  if(strlen(trim($options["cdn_domain_name"])) > 0)
  {
	$rewriter = new FivecentsCDNFilter($options["site_url"], (is_ssl() ? 'https://' : 'http://') . $options["cdn_domain_name"], $options["directories"], $options["excluded"], $options["disable_admin"]);
	$rewriter->startRewrite();
  }
}

function fivecentscdn_dnsPrefetch()
{
  $options = FivecentsCDN::getOptions();
  if(strlen(trim($options["cdn_domain_name"])) > 0)
  {
	echo "<link rel='dns-prefetch' href='//{$options["cdn_domain_name"]}' />";
  }
}

function fivecentscdn_purge() {

  $options = FivecentsCDN::getOptions();
  $api = new FivecentsCDNApi();
  if(strlen(trim($options["cdn_domain_name"])) > 0){
    $api->purgePullZone($options["pull_zone"], $options["api_key"]);
    echo "1";
  }
  wp_die();

}

function five5centscdn_zone() {

  $options = FivecentsCDN::getOptions();
  $api = new FivecentsCDNApi();

  if (isset($_POST['zone_id'])  && isset($_POST['apikey']))  {
    $zoneArr = $api->getPullZones((int)($_POST['zone_id']), sanitize_text_field($_POST["apikey"]));
    $data = [
      'http' => $zoneArr['zone']['ssl']['http2'],
      'serviceid' => $zoneArr['zone']['serviceid'],
      'enabled' => $zoneArr['zone']['ssl']['enabled'],
      'cnames' => $zoneArr['zone']['cnames'],
      'fqdn' => $zoneArr['zone']['fqdn']
    ];
    echo json_encode($data);
  }
  wp_die();
}

function fivecentscdn_all_zones() {

  if (isset($_POST['apikey'])) {
    $api = new FivecentsCDNApi();
    $zoneArr = $api->listPullZones(sanitize_text_field($_POST['apikey']));
    if (count($zoneArr['zones']) > 0) {
      foreach ($zoneArr['zones'] as $key => $value) {
        if ($value['status'] != "Deleted") {
          $data[] = [
            "id" => $value['id'],
            "name" => $value['name']
          ];
        }
      }
      if (count($data) > 0) {
        echo json_encode($data);
      }
    }
  }
  wp_die();
}

register_uninstall_hook( __FILE__, 'fivecentscdn_deleteoption' );
register_deactivation_hook( __FILE__, 'fivecentscdn_deleteoption' );
function fivecentscdn_deleteoption() {
  delete_option('5centscdn');
}
