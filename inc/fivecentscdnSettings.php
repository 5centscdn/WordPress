<?php
class FivecentsCDN
{
   public static function getOptions() {
      $wp_filtered_site_url = get_option('home'); 
      if (isset($_SERVER['HTTPS']) && strpos($wp_filtered_site_url, 'https://') === false) {
         $wp_filtered_site_url = str_replace("http://", "https://", $wp_filtered_site_url);
      }
      return wp_parse_args( get_option('5centscdn'), [
         "pull_zone" => "",
         "cdn_domain_name" => "",
         "excluded" => FIVECENTSCDN_DEFAULT_EXCLUDED,
         "directories" => FIVECENTSCDN_DEFAULT_DIRECTORIES,
         "site_url" => get_option('home'),
         "web_site_url"=> $wp_filtered_site_url,
         "serviceid" => "",
         "api_key" => "",
         "wp_disble_cdn" => 0,
         "disable_admin" => 0,
         "asset_acceleration"=>0,
         "https" => false,
      ]);
   }

   public static function getOption($option) {
      $options = FivecentsCDN::getOptions();
      return $options[$option];
   }
   
   public static function validateSettings($data) {
      $cdn_domain_name = FivecentsCDN::cleanHostname($data['cdn_domain_name']);
      $pull_zone = $data['pull_zone'];

      $siteUrl = get_option('home');
      while (substr($siteUrl, -1) == '/') {
         $siteUrl = substr($siteUrl, 0, -1);
      }

      if ($data['wp_disble_cdn'] == "0") {
         $siteUrl = "";
      }

      if ($cdn_domain_name) {
         $cdn_domain_name = $cdn_domain_name.parse_url($siteUrl, PHP_URL_PATH);
      }
 
      return [
         "pull_zone" => $pull_zone,
         "cdn_domain_name" => $cdn_domain_name,
         "excluded" => esc_attr($data['excluded']),
         "directories" => esc_attr($data['directories']),
         "site_url" => $siteUrl,
         "web_site_url" => esc_attr($data['web_site_url']),
         "serviceid" => (int)($data['serviceid']),
         "api_key" => $data['api_key'],
         "wp_disble_cdn" => (int)($data['wp_disble_cdn']),
         "disable_admin" =>    (int)($data['disable_admin']),
         "asset_acceleration" =>    (int)($data['asset_acceleration']),
         "https" => (bool)($data['https']),
      ];
   }
   
   public static function cleanHostname($hostname) {
      $hostname = str_replace("http://", "", $hostname);
      $hostname = str_replace("https://", "", $hostname);
      return str_replace("/", "", $hostname);
   }
   
   public static function startsWith($haystack, $needle) {
      $length = strlen($needle);
      return (substr($haystack, 0, $length) === $needle);
   }
   
   public static function endsWith($haystack, $needle) {
      $length = strlen($needle);
      if ($length == 0) {
         return true;
      }
      return (substr($haystack, -$length) === $needle);
   }
}
   
class FivecentsCDNSettings
{
   public static function initialize() {
      $myicon = plugins_url('/menu-icon.png', __FILE__ );  
      add_menu_page(
         "5centsCDN",
         "5centsCDN",
         "manage_options",
         "5centscdn",
         array(
           'FivecentsCDNSettings',
           'outputSettingsPage'
         ), 
         $myicon
      );
      add_filter(
         'plugin_action_links_' .FIVECENTSCDN_PLUGIN_BASE,
         array(
           __CLASS__,
           'add_action_link'
         )
      );

      wp_enqueue_script('5centscdn-backend', plugins_url('assets/js/5centscdn-backend.js?v=2.0', dirname(__FILE__)), array('jquery'), '1.1.3', true);
      wp_register_script('5centscdn_lottieanimation', 'https://unpkg.com/@lottiefiles/lottie-player@0.4.0/dist/lottie-player.js');
      wp_enqueue_script('5centscdn_lottieanimation');
               
      wp_register_script('5centscdn_sweetalert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11');
      wp_enqueue_script('5centscdn_sweetalert');
      register_setting('5centscdn', '5centscdn', array("FivecentsCDN", "validateSettings"));
   }
   
   public static function add_action_link($data) {
      // check permission
      if ( ! current_user_can('manage_options') ) {
         return $data;
      }
      return array_merge(
         $data,
         array(
           sprintf(
             '<a href="%s">%s</a>',
             add_query_arg(['page' => '5centscdn'], admin_url('admin.php')),
             __("Settings")
           )
         )
      );
   }

   public static function outputSettingsPage() {
      $fivecent_logo=plugins_url( 'assets/fivecent_logo.png', dirname(__FILE__) );

      $info_img=plugins_url( 'assets/info.svg', dirname(__FILE__) );

      $clean_img=plugins_url( 'assets/clean.svg', dirname(__FILE__) );
   
   
   
      $options = FivecentsCDN::getOptions();
      $api = new FivecentsCDNApi();
      if ($options['pull_zone']) {
         $zone_id = $options['pull_zone'];
         $zone = $api->getPullZones($zone_id, $options["api_key"]);
         $http2 = $zone['zone']['ssl']['http2'];
         $http = $zone['zone']['ssl']['enabled'];
         $redirect = $zone['zone']['ssl']['redirect'];
         $warning=$zone['warnings'];
       
         if ($zone['zone']['ssl']['warning']==false) {
            $ssl_warning=0;
         } else {
            $ssl_warning=$zone['zone']['ssl']['warning'];
         }

         $cnames = $zone['zone']['cnames'].",".$zone['zone']['fqdn'];
         $zone_name = $zone['zone']['name'];
         $zoneArr = $api->listPullZones($options["api_key"]);
      }
   
      $trimed_apikey=trim($options['api_key']);

      
     
       ?> 

     <?php 

          $zoneurl = (trim($options["pull_zone"])) ? FIVECENTSCDN_DOMAIN.'dashboard/'.$options['serviceid'].'/zones/http/pull/new' : FIVECENTSCDN_DOMAIN.'clientarea.php';
     
     ?>
<!-- html section -->
<div class="container" style="background:#F4F5F7">
   <!-- header -->
   <div class="fivecent-header">
      <img src="<?=  $fivecent_logo ?>" />
   </div>
   <!-- end header -->
   <br />
   <!-- tab button -->

   
   <ul class="fivecent-nav-tabs ">
      <li class="fivecent-nav-tabs-active " id="tab_1">
         <a onclick="tab_event('tab_1');" class="">CDN Settings</a>
      </li>
      <li class="fivecent-nav-tabs-inactive" style="pointer-events:<?php if($options['asset_acceleration']==1){echo 'none';}else{echo 'unset';} ?>; opacity:<?php if($options['asset_acceleration']==1){echo 0.4;}else{echo 1;} ?>" id="tab_2">
         <a onclick="tab_event('tab_2');" class="" >Cache Settings</a>
      </li>
   </ul>
   <!-- tab button end -->
   <form id="fivecentscdn_options_form" method="post" action="options.php" >
      <?php settings_fields('5centscdn') ?>
      <!-- tab-1 -->
      <div class="tab-1-section" style="display:block;" >
         <!--  section -1  -->
         <div class="tab-1-section-1" style="display:<?php if(trim($options['api_key'])){ echo 'none';}else{echo 'block';} ?>">
         
       
            <div class="fivecent-card">
               <p class="fivecent-title">Follow these simple steps to <span class="fivecent-sub-color">Connect Your
                  CDN</span> Account
               </p>
               <ul class="fivecent-sub-points">
                  <li><span><i class="fa fa-arrow-right"></i></span><a style="color:#59A52C;" href="https://cp.5centscdn.net/clientarea.php">Login</a> to your 5centsCDN account. Scroll to account in
                     the sidebar and select API
                  </li>
                  <li><span><i class="fa fa-arrow-right"></i></span>Click on manage and copy the API key.</li>
                  <li><span><i class="fa fa-arrow-right"></i></span>Paste the copied key in the required field to connect
                     your CDN Network.
                  </li>
               </ul>
            </div>
            <br />
            <div class="fivecent-title-card">
               <p class="fivecent-sub-title">Connect Your CDN</p>
            </div>
            <br />
            <div class="warning-message-box invalid-apikey-error" style="display:none;">
               <p class="error-text">Invalid api key</p>
            </div>
            <div class="fivecent-card">
               <div class="row" style="margin: 55px;">
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;">WP Site URL</label>
                     <input type="text" class="form-control" name="5centscdn[web_site_url]" id="fivecentscdn_site_url" value="<?php echo $options['web_site_url']; ?>"  placeholder="Enter wp site url">
                     <div class="fivecent-info-card">
                        <span class="fivecent-icon-round">
                        <i style="font-size: 8px; color: #59A52C; width: 16px;" class="fa fa-info"></i>
                        </span>
                        <div>
                           <p style="color:#8D8D8D; font-size: 11px;">Your WP site is accessible using the following URL <a href="<?php echo get_option('home'); ?>" style="color:#59A52C;font-weight: 500;"><?php echo get_option('home'); ?></a>. Create a pull zone under <a style="color:#59A52C;font-weight: 500;" href="<?=$zoneurl?>" target="_blank">5centsCDN control panel</a> and point the origin URL to WP <?php echo parse_url($options['web_site_url'], PHP_URL_SCHEME)."://".parse_url($options['web_site_url'], PHP_URL_HOST); ?>!
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;" for="inputPassword4">API Key</label>
                     <input type="text" class="form-control fivecentscdn_api_key_initial" name="5centscdn[api_key]" id="fivecentscdn_api_key" value="<?php echo $options['api_key']; ?>" placeholder="Enter API key">
                     <div class="invalid-feedback error_notification_api_key"></div>
                     <div class="fivecent-info-card">
                        <span class="fivecent-icon-round">
                        <i style="font-size: 8px; color: #59A52C; width: 16px;" class="fa fa-info"></i>
                        </span>
                        <div>
                           <p style="color:#8D8D8D; font-size: 11px;">5centsCDN API key is required to pull the zone
                              details and Enable
                              features such as cache purging. Access your API key on the
                              <a style="color: #59A52C;" target="_blank" href="https://cp.5centscdn.net/clientarea.php">5centsCDN
                              Control Panel</a> 
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- end section-1 -->

         <!-- section -2 -->
         <div class="tab-1-section-2" style="display:<?php if(trim($options['api_key'])){ echo 'block';}else{echo 'none';} ?>">
            <div class="fivecent-card">
               <div class="fivecent-gray-card">
                  <span class="fivecent-switch-title">CDN</span>
                  <div class="custom-control custom-switch">
                     <input type="checkbox" class="custom-control-input" id="cdn_status_change" name="example" <?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "checked";}else{ echo "";} if($trimed_apikey){}else{ echo 'disabled';} ?> style="display:none" />
                     <label class="custom-control-label" for="cdn_status_change"></label>
                  </div>
                  
                  <div class="<?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "fivecent-chips-active";}else{ echo "fivecent-chips-inactive";}  ?>">
                     <i style="font-size: 8px; width: 16px;" class="fa fa-circle"></i><span><?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "Enabled";}else{ echo "Disabled";}  ?></span>
                  </div>
               </div>
               <div class="container-fluid px-md-5  py-md-5">
                  <div class="row" style="">
                     <div class="col-md-6">
                        <label class="sr-only">wp site url</label>
                        <div class="input-group mb-2">
                           <div class="input-group-prepend">
                              <div class="input-group-text fivecent-group-card">WP Site URL
                                 <span class="fivecent-icon-round fivecent-tooltip"
                                    style="margin: 5px; border: 2px solid #ffffff;">
                                 <i style="font-size: 8px; color: #ffff; width: 16px;" class="fa fa-info"></i>
                                 <span style="top:-51px !important;" class="fivecent-tooltiptext ">Your WP site is accessible using the following URL <code style="color:#59A52C !important"><?php echo get_option('home'); ?></code>. Create a pull zone under <a class="zoneurl_website_url" style="color:#59A52C !important" href="<?=$zoneurl?>" target="_blank">5centsCDN control panel</a> and point the origin URL to WP <?php echo parse_url($options['web_site_url'], PHP_URL_SCHEME)."://".parse_url($options['web_site_url'], PHP_URL_HOST); ?>!</span>
                                 </span>
                              </div>
                           </div>
                           <input style="border-top-left-radius: 0px !important; border-bottom-left-radius: 0px !important;" type="text" name="5centscdn[web_site_url]" id="fivecentscdn_site_url" value="<?php echo $options['web_site_url']; ?>" class="form-control fivecent-group-card-input class_fivecentscdn_site_url"
                              placeholder="Enter wp site url">
                               <div class="invalid-feedback error_notification_web_site_url"></div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <label class="sr-only">API Key</label>
                        <div class="input-group mb-2">
                           <div class="input-group-text fivecent-group-card">API Key
                              <span class="fivecent-icon-round fivecent-tooltip"
                                 style="margin: 5px; border: 2px solid #ffffff;">
                              <i style="font-size: 8px; color: #ffff; width: 16px;" class="fa fa-info"></i>
                              <span class="fivecent-tooltiptext" style="top:-51px !important">5centsCDN API key is required to pull the zone details and will enable features such as cache purging. You can access your API key on the <a style="color:#59A52C !important" href="<?=$zoneurl?>" target="_blank">5centsCDN control panel!</a></span>
                              </span>
                           </div>
                           <input style="border-top-left-radius: 0px !important; border-bottom-left-radius: 0px !important;" type="text" name="5centscdn[api_key]" id="fivecentscdn_api_key"  value="<?php echo $options['api_key']; ?>" style="margin-left: -1px;" class="class_fivecentscdn_api_key form-control fivecent-group-card-input"
                              placeholder="Enter API Key" />
                               <div class="invalid-feedback error_notification_api_key"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <br />
            <div class="fivecent-title-card">
               <div class="form-check fivecent-form-check">
                  <input type="radio" onChange="acceleration('tab1')" class="form-check-input" id="radio1" name="optradio" value="option1" <?php if(isset($options['asset_acceleration'])){ if($options['asset_acceleration']==0){ echo 'checked';} }else{ echo 'checked'; } ?> >
                  <label class="form-check-label" for="radio1">Asset Acceleration</label>
               </div>
               <div class="form-check fivecent-form-check">
                  <input type="radio" onChange="acceleration('tab2')" class="form-check-input" id="radio2" name="optradio" value="option2" <?php if(isset($options['asset_acceleration'])){ if($options['asset_acceleration']==1){ echo 'checked';} } ?>>
                  <label class="form-check-label" for="radio2">Whole Website Acceleration</label>
               </div>
            </div>
       
            

            <div class="fivecent-card">

               <div class="warning-message-box cname_add_warning_message" style="display:none;">

                  <p class="error-text">CNAME is not found please add <span><input id="cname_add_field" placeholder="Enter CNAME" style="height: 10px;" type="text" /> <button id="cname_add_button" style="border: none; margin: 5px; background: #59a52c; color: white; border-radius: 7px;line-height: 25px;">Add</button></span></p>

               </div>

               <div class="success-message-box cname_added_success_message" style="display:none;">

                  <p class="success-text">Zone Updated!</span></p>

               </div>

               <div class="warning-message-box cname_added_warning_messages" style="display:none;">

                  
               </div>

               <div class="ssl-update-error-error warning-message-box">
               </div>
               <div class="row  px-md-5  py-md-5" style="margin: 5px;">
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;">Pull Zone <span class="fivecent-tooltip"> <img
                        style="    width: 16px;"
                        src="<?= $info_img  ?>" /> <span
                        class="fivecent-tooltiptext" style="top:-17px !important;">Select the pull zone to rewrite your WP URLs to serve via CDN.</span></span>
                     </label>
                     <select name="5centscdn[pull_zone]" id="fivecentscdn_pull_zone" class="custom-select">
                        <option value=0 selected>Select pull zone</option>
                         <?php 
                           if (trim($options['api_key'])) {
                             if (count($zoneArr['zones'])>0) {
                               foreach ($zoneArr['zones'] as $key => $value) {
                                  if ($value['status'] != "Deleted") {
                         ?>
                         <option value="<?=$value['id']?>"<?php echo ($options["pull_zone"] == $value['id'] ? "selected" : "")?>> <?=$value['name']?></option>
                         <?php  
                                   }
                               }
                           }
                          }
                       ?>
                     </select>
                      <div class="invalid-feedback error_notification_pull_zone"></div>
                  </div>
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;">CDN Resource <span class="fivecent-tooltip"> <img style="width: 16px;" src="<?= $info_img  ?>" /> <span style="top: -18px !important;" class="fivecent-tooltiptext">ou have chosen <span id = "txt_cdn_domain_name"><?php echo $cdn_domain_name['0']; ?></span> zone to rewrite your WP URLs to serve via CDN. </span></span></label>
                       <?php 
              	          $cdn_domain_nameArr = $options['cdn_domain_name'];
              	          if ($cdn_domain_nameArr) {
                         $cdn_domain_name = explode('/', $cdn_domain_nameArr);
              	         }
              	       ?>
                     <select name="5centscdn[cdn_domain_name]" id="fivecentscdn_cdn_domain_name" class="custom-select">
                        <option value=0 selected>Select CNAME</option>
                         <?php if($cnames){
                       $cnamesArr = explode(',', ltrim($cnames,','));
                      foreach ($cnamesArr as $value) {
                        echo '<option value="'.$value.'" '.($cdn_domain_name[0] == $value ? "selected" : "").'>'.$value.'</option>';
                      }
                    }?>
                     </select>
                      <div class="invalid-feedback error_notification_cdn_domain_name"></div>
                   
                  </div>
                 
                  <div class=" col-md-6 https_section">
                     <div class="fivecent-form-input-card ">
                        <span class="fivecent-switch-title" style="font-size: 11px;">HTTPS &nbsp; &nbsp;<i 
                           style="color:<?php echo ($http == "Y" ? " #59A52C" : "red"); ?>;" class="https_status_locker fa fa-lock lock_icon"></i></span>
                        <div class="custom-control custom-switch">
                           <input style="display:none;" type="checkbox" class="custom-control-input" id="https"onClick="return update_zone_ssl('https')" <?php echo ($http == "Y" ? "checked" : ""); ?> />
                           <label class="custom-control-label" for="https"></label>
                        </div>
                     </div>
                  </div>
                   <div class=" col-md-6 http2_section">
                     <div class="fivecent-form-input-card ">
                        <span class="fivecent-switch-title" style="font-size: 11px;">HTTP2</span>
                         <input type="hidden" id="serviceid" class="regular-text code"  name="5centscdn[serviceid]" value="<?php echo $options['serviceid']; ?>" /> 
                        <div class="custom-control custom-switch">
                           <input style="display:none;" type="checkbox" class="custom-control-input" id="http2" onClick="return update_zone_ssl()"  <?php echo ($http2 == "Y" ? "checked" : ""); ?> />
                           <label class="custom-control-label" for="http2"></label>
                        </div>
                     </div>
                  </div>
                   <div class=" col-md-6 https_redirect_section">
                     <div class="fivecent-form-input-card ">
                        <span class="fivecent-switch-title" style="font-size: 11px;">HTTPS Redirect </span>
                        <div class="custom-control custom-switch">
                           <input style="display:none;" type="checkbox" class="custom-control-input" id="redirect" onClick="return update_zone_ssl()" name="example" <?php echo ($redirect == "Y" ? "checked" : ""); ?> />
                           <label class="custom-control-label" for="redirect"></label>
                        </div>
                     </div>
                  </div>
               </div>
            
               
               <div class="row px-md-5  py-md-2" style="margin: 5px;">
                  <div class=" col-md-6">
                     <button type="button" id="purge_tab1" onClick="return purgetab(1)" class="btn btn-primary btn-lg btn-block fivecent-choose-button-active">Purge
                     All</button>
                  </div>
                  <div class=" col-md-6">
                     <button type="button" id="purge_tab2" onClick="return purgetab(2)" class="btn btn-primary btn-lg btn-block fivecent-choose-button-inactive">Purge
                     Files</button>
                  </div>
               </div>

               <!-- purge all tab 1 section -->
              <div class="purge_tab_1_section" style="display:block"> 
               <div class="fivecent-gray-card"
                  style="background: #EEF0F2; justify-content: center; font-family: Roboto; font-style: normal; font-weight: normal; font-size: 14px; line-height: 23px;  color: #858585;  align-items: center;  text-align: center; height: auto;">
                  <p style="margin-top: auto !important;">Purging clears the zone or file cache on the edge servers and
                     gets
                     rebuilt from the origin on the
                     next request.
                  </p>
               </div>
               <div class="row" style="justify-content: center; align-items: center; margin-left:0px !important; margin-right:0px !important">
                  <div class="col-md-6" style="justify-content: center; align-items: center; display: flex;">
                     <button style="margin: 20px; font-size: 16px;" onClick="return purgecache()" type="button"
                        class="btn btn-lg btn-primary fivecent-border-button">Purge All <img style="width: 13px;"
                        src="<?= $clean_img ?>" /></button>
                  </div>
               </div>
            </div>
               <!--end  purge all tab 1 section -->
               <!-- purge particular filr tab 2 section -->

               <div class="purge_tab_2_section" style="display:none"> 
                  <div class="row" style="margin: 55px;">
                  <div class="form-group col-md-12">
                     <label style="color: #252525;font-weight: 500;">File List (One file per line</label>
                     <div id="page_url_pusher">
                          <div id="field_8909" style="margin:5px; position:relative; ">
                             <input id="8909" type="text" class="form-control" placeholder="Enter page/post url for purge">
                             <div onClick="field_pusher()" style="background: #E0EDDB; cursor:pointer;user-select: none; width: 33px; height: 33px; position: absolute; top: 6px; right: 8px; border-radius: 6px;     justify-content: center; align-items: center; text-align: center;display: flex;">
                                <span style="font-size: 27px;font-weight: 700; margin-top: -4px; color:#59A52C">+</span>
                             </div>
                             <div class="invalid-feedback error_notification_web_site_url pusher_field_alert_8909" style="display: none;">Field cannot be empty</div>
                          </div>
                          

                     </div>
                    
                  </div>
               </div>

               <div class="row" style="justify-content: center; align-items: center; margin-left:0px !important; margin-right:0px !important">
                  <div class="col-md-6" style="justify-content: center; align-items: center; display: flex;">
                     <button style="margin: 20px; font-size: 16px;" onClick="return purgecacheFile()" type="button"
                        class="btn btn-lg btn-primary fivecent-border-button">Purge All <img style="width: 13px;"
                        src="<?= $clean_img ?>" /></button>
                  </div>
               </div>

               <!-- end purge particular filr tab 2 section -->
            </div>
         </div>
      </div>
         <!-- hiden input fields -->

            <input type="hidden" name="5centscdn[wp_disble_cdn]" id="wp_disble_cdn" value="<?php echo $options['wp_disble_cdn']; ?>" />
            <input type="hidden" name="5centscdn[disable_admin]" id="5centscdn_disable_admin" value="<?php echo $options['disable_admin']; ?>" />

            <input type="hidden" name="5centscdn[asset_acceleration]" id="asset_acceleration" value="<?php if(isset($options['asset_acceleration'])){ echo $options['asset_acceleration'];}else{ echo 0;} ?>" />
         
             <!-- backup disable cdn -->
               <input type="hidden"  id="wp_disble_cdn_backup" value="<?php echo $options['wp_disble_cdn']; ?>" />
             <!-- end -->

             <!-- tempararly store all zone cnames -->
                <input type="hidden" id="temp_cnames_add_to_input" type="text" value=<?php if(isset($zone['zone']['cnames'])){ echo $zone['zone']['cnames'];}else{ echo 0;}   ?> />
             <!-- end -->

             <!-- ssl warning holder -->

               <input type="hidden" id="temp_ssl_warning_holder" value="<?php if(isset($ssl_warning)){echo $ssl_warning;}else{ echo 0;} ?>" />
             <!-- end -->
      </div>
      <!-- end tab-1 -->
      <!-- tab-2 -->
      <div class="tab-2-section" style="display:none;">

     
         <div class="fivecent-card">
              <div class="fivecent-gray-card">
                  <span class="fivecent-switch-title">CDN</span>
                  <div class="custom-control custom-switch">
                     <input type="checkbox" class="custom-control-input" id="cdn_status_change" name="example" <?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "checked";}else{ echo "";} if($trimed_apikey){}else{ echo 'disabled';} ?> style="display:none"/>
                     <label class="custom-control-label" for="cdn_status_change"></label>
                  </div>
                  <div class="<?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "fivecent-chips-active";}else{ echo "fivecent-chips-inactive";}  ?>">
                     <i style="font-size: 8px; width: 16px;" class="fa fa-circle"></i><span><?php if($options['wp_disble_cdn']==1 or $options['asset_acceleration']==1){ echo "Enabled";}else{ echo "Disabled";}  ?></span>
                  </div>
               </div>  

               <div class="row" style="margin: 55px;">
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;">Excluded Extensions</label>
                     <input type="text" class="form-control" name="5centscdn[excluded]" id="fivecentscdn_excluded" value="<?php echo $options['excluded']; ?>" placeholder="Enter Excluded Extensions">
                     <div class="fivecent-info-card">
                        <span class="fivecent-icon-round">
                        <i style="font-size: 8px; color: #59A52C; width: 16px;" class="fa fa-info"></i>
                        </span>
                        <div>
                           <p style="color:#8D8D8D; font-size: 13px;">The links containing the listed phrases will be excluded from the CDN.
                             Enter a <code style="background: #59A52C; color: #ffffff; border-radius: 3px;" >,</code> separated list without spaces.</p>
                           
                              
                        </div>
                        
                     </div>
                     <div style="height: 39px;background: #EFF1F4; border-radius: 8px;margin-top: -25px;text-align: center; justify-content: center; align-items: center; display: flex; padding-top: 18px;">

                         <p style="height: 30px;font-family: Roboto;font-style: normal;font-weight: 500;font-size: 13px;line-height: 30px;color: #8D8D8D;">Default value <span class="fivecent-sub-color">.php</span></p>
                     </div>
                  </div>
                  <div class="form-group col-md-6">
                     <label style="color: #252525;font-weight: 500;" for="inputPassword4">Included Directories</label>
                     <input type="text" class="form-control fivecentscdn_api_key_initial" name="5centscdn[directories]" id="fivecentscdn_directories" value="<?php echo $options['directories']; ?>" placeholder="Enter Included Directories">
                 
                     <div class="fivecent-info-card">
                        <span class="fivecent-icon-round">
                        <i style="font-size: 8px; color: #59A52C; width: 16px;" class="fa fa-info"></i>
                        </span>
                        <div>
                           <p style="color:#8D8D8D; font-size: 13px;">Only the files linking inside of this directory will be pointed to their
                           CDN url. Enter a <code style="background: #59A52C; color: #ffffff; border-radius: 3px;" >,</code> separated list without spaces.
                           </p>
                        </div>
                     </div>
                      <div style="height: 39px;background: #EFF1F4; border-radius: 8px;margin-top: -25px;text-align: center; justify-content: center; align-items: center; display: flex; padding-top: 18px;">

                         <p style="height: 30px;font-family: Roboto;font-style: normal;font-weight: 500;font-size: 13px;line-height: 30px;color: #8D8D8D;">Default value <span class="fivecent-sub-color">wp-content,wp-includes</span></p>
                     </div>
                  </div>
               </div>


          </div>
       
      </div>
      <!-- end tab-2 -->
        <!-- connect button -->
      <div class="fivecent-button-main" style="display:<?php if(trim($options['api_key'])){ echo 'none';}else{echo 'flex';} ?>;">
         <button  id="fivecentscdn-connect-button">Connect</button>
      </div>
      <!-- end connect button -->

      <!-- save settings button -->

          <div class="row fivecent_save_settings_button" style="justify-content: center; align-items: center;     margin-left: 0px !important;margin-right: 0px !important; display:<?php if(trim($options['api_key'])){ echo 'flex';}else{echo 'none';} ?>;">
             <div class="col-md-6" style="justify-content: center; align-items: center; display: flex;">
                 <button name="fivecentscdn-save-button" id="fivecentscdn-save-button" onclick="submitForm()" style="margin: 40px; background: #59A52C;    border: none;padding-left: 27px;padding-right: 27px; font-family: Roboto; font-style: normal;font-size: 17px;line-height: 30px;color: #FFFFFF;" type="button"
                     class="btn btn-lg btn-primary">Save
                     Settings</button>
             </div>
         </div>

      <!-- end save settings button -->

      <div id="fivecentscdn_popupBackground" style="display: none;justify-content: center; align-items: center; flex-direction: column; z-index: 10; position: fixed; top: 0px; left: 0px; height: 100vh; width: 100%; background-color: #ffffff9e">
   
            <div id="fivecentscdn_popupBox" style="display:flex; z-index: 15; position: fixed; top: 0px;  height: 100%; width: 100%; justify-content: center; align-items: center; flex-direction: column;">
                <lottie-player
                   autoplay
                 
                   loop
                   mode="normal"
                   src="<?php echo plugins_url('purge_animation.json', __FILE__ ); ?>"
                   style=" width: 153px; height: 153px;"
                >
               </lottie-player>
            </div>
       </div>
           
    
   </form>

   
</div>
</div>

<!-- end html section -->

<?php

if($options['pull_zone']){
  if($http=="N"){

  ?>

   <script>
        jQuery('.http2_section').css('pointer-event', 'none');
        jQuery('.http2_section').css('opacity', 0.4);
        jQuery("#http2").prop('disabled', true);



        jQuery('.https_redirect_section').css('pointer-event', 'none');
        jQuery('.https_redirect_section').css('opacity', 0.4);
        jQuery("#redirect").prop('disabled', true);
     
   </script>
  
  <?php
  }
}
?>

<?php 
  if(isset($_GET['wp_disble_cdn']) && $_GET['wp_disble_cdn']==0){

   ?>

   <script>

      jQuery('#asset_acceleration').val(0)
   </script>

   <?php
  }
?>

  <?php if(isset($_GET['wp_disble_cdn']) && $_GET['wp_disble_cdn']!= $options['wp_disble_cdn']){ ?>
        <script>
                   
                /* url filter after redirect */

            var url = window.location.href;
            url = url.split('?')[0] + '?page=5centscdn';

            history.pushState({}, null, url);

            /* end section */
           /*  var header_cdn_status=<?php echo $_GET['wp_disble_cdn'];  ?>

            if(header_cdn_status==0){
               alert(1)
                
            } */
            jQuery('#wp_disble_cdn').val('<?php echo sanitize_text_field($_GET['wp_disble_cdn']);?>');
                  
            jQuery('#fivecentscdn_options_form').submit();  
      </script>  
      <?php
      }
   }
}
?>