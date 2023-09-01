<?php
/*
Plugin Name: 5centsCDN
Text Domain: 5centscdn
Description: Speed up your website with 5centsCDN Content Delivery Network. This plugin allows you to easily enable 5centsCDN on your WordPress website and enjoy greatly improved loading times around the world. Even better, it takes just a minute to set up. To Enable CDN web acceleration on your WordPress website using 5centsCDN Content Delivery Network. Simply enable the plugin and select the pull zone created on the CDN control panel. Enjoy world-class acceleration with 5centsCDN powered by Akamai (Enterprise plan)!
Author: 5centsCDN
Author URI: https://5centscdn.net
License: GPLv2 or later
Version: 23.9.1
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
define('FIVECENTSCDN_PULLZONEDOMAIN', "5centscdn.net");
if (!defined('FIVECENTSCDN_DOMAIN'))
define('FIVECENTSCDN_DOMAIN', "https://cp.5centscdn.net/");
if (!defined('FIVECENTSCDN_DEFAULT_DIRECTORIES'))
define('FIVECENTSCDN_DEFAULT_DIRECTORIES', "wp-content,wp-includes");
if (!defined('FIVECENTSCDN_DEFAULT_EXCLUDED'))
define('FIVECENTSCDN_DEFAULT_EXCLUDED', ".php");

// Load everything
spl_autoload_register('fivecentscdn_load_page');
function fivecentscdn_load_page($class) {
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnSettings.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnFilter.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/vendor/autoload.php');
  require_once(FIVECENTSCDN_PLUGIN_DIR.'/inc/fivecentscdnApi.php');
}

// Register the settings page and menu
add_action('admin_enqueue_scripts', 'fivecentscdn_add_theme_scripts' );
add_action('admin_bar_menu', 'fivecentscdn_add_toolbar_items', 100);
add_action('admin_menu', array("FivecentsCDNSettings", "initialize"));
add_action('admin_init', 'only_show_option_if_fivecentscdn_cache_is_active');

add_action('wp_head', "fivecentscdn_dnsPrefetch", 0);
add_action('wp_ajax_fivecentscdn_purge', "fivecentscdn_purge", 0);
add_action('wp_ajax_fivecentscdn_zone', "fivecentscdn_zone", 0);
add_action('wp_ajax_fivecentscdn_all_zones', "fivecentscdn_all_zones", 0);
add_action('wp_ajax_fivecentscdn_update_zone_ssl', "fivecentscdn_update_zone_ssl", 0);
add_action('wp_ajax_fivecentscdn_purge_file', "fivecentscdn_purge_file", 0);
add_action('wp_ajax_fivecentscdn_cname_update', "fivecentscdn_cname_update", 0);

add_action('enqueue_block_editor_assets', 'fivecentscdn_disable_editor_fullscreen_by_default' );
add_action('post_submitbox_misc_actions', 'fivecentscdn_wpdocs_post_submitbox_misc_actions' );
add_action('template_redirect', "fivecentscdn_do_rewrite");
add_action('admin_notices', 'fivecentscdn_promotional_banner');

register_uninstall_hook( __FILE__, 'fivecentscdn_deleteoption' );
register_deactivation_hook( __FILE__, 'fivecentscdn_deleteoption' );

function fivecentscdn_add_theme_scripts() {
  wp_enqueue_style( 'mytheme-options-style', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' );
  wp_enqueue_style( 'mytheme-bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css' );
  wp_enqueue_style( 'mytheme-roboto-fonts', 'https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap' );
  wp_enqueue_style( 'mytheme-fivecentcdn-style','/wp-content/plugins/5centscdn/assets/css/5centcdn.css?v=2.0' );
}

function fivecentscdn_wpdocs_post_submitbox_misc_actions( $post ) {
  echo "
    <div class=\"misc-pub-section my-options\">
      <label for=\"my_custom_post_action\">My Option</label><br/>
      <select id=\"my_custom_post_action\" name=\"my_custom_post_action\">
        <option value=\"1\">First Option goes here</option>
        <option value=\"2\">Second Option goes here</option>
      </select>
    </div>
  ";
}

function only_show_option_if_fivecentscdn_cache_is_active() {
  if (is_plugin_active('5centscdn/5centscdn.php')) {
    function clear_all_cached_files_fivecentscdncache() {
      global $wp_admin_bar;

      $options = FivecentsCDN::getOptions();
      if ($options['wp_disble_cdn'] == '1' || $options['asset_acceleration'] == '1') {
        $head_cdn_status_text="Disable CDN";
        $cdn_switch_status=0;
      } else {
        $head_cdn_status_text="Enable CDN";
        $cdn_switch_status=1;
      }

      $wp_admin_bar->add_menu([
        'id' => 'disable-fivecentscdn',
        'title' => __(($head_cdn_status_text)),
        'href' => wp_nonce_url(admin_url('admin.php?page=5centscdn&wp_disble_cdn='.$cdn_switch_status))
      ]);
    }
  }
}

function fivecentscdn_add_toolbar_items($admin_bar) {
  $current_page = admin_url(sprintf('admin.php?%s', http_build_query($_GET)));

  if (strpos($current_page, 'action=edit') !== false) {
    $url_components = parse_url($current_page);
    parse_str($url_components['query'], $params);
    $post_id = $params['post'];

    $post_data = (array) get_post($post_id);
    $post_slug_name = $post_data['post_name'];
    $page_url = esc_url(get_post_type_archive_link('post')).'/'.$post_slug_name;

    $admin_bar->add_menu( array(
      'id'    => 'fivecentscdn-sub-item-purge-single',
      'parent' => 'fivecentscdn',
      'title' => 'Post/Page Purge',
      'href'  =>admin_url('admin.php?page=5centscdn&purge="file"&file_url='.$page_url),
      'meta'  => array(
        'title' => __('Post/Page Purge'),
        'target' => '_blank',
        'class' => 'wp_rocket_item'
      ),
    ));
  }

  $admin_bar->add_menu( array(
    'id'    => 'fivecentscdn',
    'title' => '5centscdn',
    'href'  => '#',
    'meta'  => array(
      'title' => __('My Item'),
    ),
  ));
  $admin_bar->add_menu( array(
    'id'    => 'fivecentscdn-sub-item-setting',
    'parent' => 'fivecentscdn',
    'title' => 'Setting',
    'href'  =>admin_url('admin.php?page=5centscdn'),
    'meta'  => array(
      'title' => __('Setting'),
      'target' => '',
      'class' => 'wp_rocket_item'
    ),
  ));
  $admin_bar->add_menu( array(
    'id'    => 'fivecentscdn-sub-item-purge-all',
    'parent' => 'fivecentscdn',
    'title' => 'Purge All',
    'href'  => admin_url('admin.php?page=5centscdn&purge="all"'),
    'meta'  => array(
      'title' => __('Purge All'),
      'target' => '_blank',
      'class' => 'wp_rocket_item'
    ),
  ));

  $admin_bar->add_menu( array(
    'id'    => 'fivecentscdn-sub-faq',
    'parent' => 'fivecentscdn',
    'title' => 'FAQ',
    'href'  =>"https://www.5centscdn.net/help/?s=wordpress",
    'meta'  => array(
      'title' => __('FAQ'),
      'target' => '_blank',
      'class' => 'wp_rocket_item'
    ),
  ));
}

function fivecentscdn_disable_editor_fullscreen_by_default() {
  $script = "jQuery( window ).load(function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } });";
  wp_add_inline_script( 'wp-blocks', $script );
}

function fivecentscdn_cname_update() {
  $api_key=$_POST['apikey'];
  $zoneid=$_POST['zoneid'];
  $orgin=$_POST['orgin'];
  $cnames=$_POST['cnames'];

  if (isset($_POST['apikey'])) {
    $api = new FivecentsCDNApi();
    $zoneArr = $api->updatePullZoneCname($zoneid,sanitize_text_field($api_key),$orgin,$cnames);
    echo json_encode($zoneArr);
  }

  wp_die();
}

function fivecentscdn_do_rewrite() {
  $options = FivecentsCDN::getOptions();
  if (strlen(trim($options["cdn_domain_name"])) > 0) {
    $modified_site_url = $options["site_url"];

    if($options["wp_disble_cdn"]){
      $https_status = 'https://';
    }else{
     $https_status = $options['https'] ? 'https://' : 'http://';
    }


    if (!str_contains($options["site_url"], 'https') && isset($_SERVER['HTTPS'])) {
      $modified_site_url = str_replace("http", "https", $modified_site_url);
    }

    $rewriter = new FivecentsCDNFilter(
      $modified_site_url,
      ($https_status) . $options["cdn_domain_name"],
      $options["directories"],
      $options["excluded"],
      $options["disable_admin"]
    );
    $rewriter->startRewrite();
  }
}

function fivecentscdn_update_zone_ssl() {
  if (isset($_POST['apikey'])) {
    $zoneid = $_POST['zoneid'];
    $api_key = $_POST['apikey'];
    $http2 = $_POST['http2'];
    $redirect = $_POST['redirect'];
    $mode = $_POST['mode'];
    $enabled = $_POST['enabled'];

    $options = FivecentsCDN::getOptions();
    $options['https'] = ($enabled == 'Y');
    update_option('5centscdn', $options);

    $api = new FivecentsCDNApi();
    $zonessl = $api->updatePullZoneSsl($zoneid,sanitize_text_field($api_key),$http2,$redirect,$mode,$enabled);
    echo json_encode($zonessl);
  }

  wp_die();
}

function fivecentscdn_dnsPrefetch()  {
  $options = FivecentsCDN::getOptions();
  if(strlen(trim($options["cdn_domain_name"])) > 0) {
    echo "<link rel='dns-prefetch' href='//{$options["cdn_domain_name"]}' />";
  }
}

function fivecentscdn_purge() {
  $options = FivecentsCDN::getOptions();
  $api = new FivecentsCDNApi();
  if (strlen(trim($options["cdn_domain_name"])) > 0) {
    $purge_res = $api->purgePullZone($options["pull_zone"], $options["api_key"]);
    echo json_encode($purge_res);
  }

  wp_die();
}

function fivecentscdn_purge_file() {
  $options = FivecentsCDN::getOptions();
  $api = new FivecentsCDNApi();
  if(strlen(trim($options["cdn_domain_name"])) > 0) {
    $post_url = $_POST['post_url'];
    $purge_res = $api->purgePullZoneFile($options["pull_zone"], $options["api_key"], $post_url);
    echo json_encode($purge_res);
  }

  wp_die();
}

function fivecentscdn_zone() {
  $api = new FivecentsCDNApi();
  if (isset($_POST['zone_id'])  && isset($_POST['apikey'])) {
    $zoneArr = $api->getPullZones((int)($_POST['zone_id']), sanitize_text_field($_POST["apikey"]));
    $data = [
      'http' => $zoneArr['zone']['ssl']['http2'],
      'serviceid' => $zoneArr['zone']['serviceid'],
      'enabled' => $zoneArr['zone']['ssl']['enabled'],
      'cnames' => $zoneArr['zone']['cnames'],
      'fqdn' => $zoneArr['zone']['fqdn'],
      'warning' => $zoneArr['zone']['ssl']['warning'],
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
        if ($value['status'] != "Deleted" && $value['optimize'] == 'http') {
          $data[] = [
            "id" => $value['id'],
            "name" => "HTTP Pull #{$value['id']}".($value['alias'] ? " ({$value['alias']})" : ''),
            "serviceid" => $value['serviceid'],
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

function fivecentscdn_promotional_banner() {
    if (!isset($_COOKIE['fivecentscdn_banner_closed'])) {
        $banner_image_url =  plugins_url('assets/5centscdn.svg', __FILE__ );
  		echo
  			'<div id="fivecentscdn-promotional-banner" class="fivecentscdn notice notice-info">
          <span class="notice-dismiss" id="fivecentscdn-promotional-banner-close-button"></span>
          <div class="notice-right-container w-20 ">
            <a target="_blank" href="https://5centscdn.net">
            <img class="notice-logo" src="' . esc_url($banner_image_url) . '" alt="5centscdn logo">
            </a>
          </div>
          <div class=" w-80">
            <div class="notice-message">
               Hey! You have been using 5centsCDN Plugin for a few days and we hope 5centsCDN is able to help
               you speed up your Assets & Website Delivery. If you like our plugin would you please show some
               love by doing actions like:
               <div class="button-container notice-vert-space">
                 <a id="5centscdn" target="_blank" href="https://wordpress.org/support/plugin/5centscdn/reviews/" class="review-btn">Rate it 5 Stars</a>
                 <a id="5centscdn_btn_already_did" target="_blank" href="https://www.linkedin.com/company/5centscdn" class="wpmet-notice-button linked-btn">Follow us on LinkedIn</a>
                 <a id="#" target="_blank" href="https://twitter.com/5centscdn" class="wpmet-notice-button twitter-btn"> Tweet about 5centsCDN</a>
                 <a id="elementskit-lite_btn_not_good" target="_blank" href="https://www.g2.com/products/5centscdn/reviews#reviews" class="g2-btn button-default">Share a Review on G2</a>
               </div>
               <div style="clear:both"></div>
            </div>
          </div>
        </div>';
    }
}

function fivecentscdn_deleteoption() {
  delete_option('5centscdn');
}
?>
