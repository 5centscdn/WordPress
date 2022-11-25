function tab_event(tab) {
  if (tab === 'tab_1') {

    jQuery('.tab-1-section').show();
    jQuery('.tab-2-section').hide();
    jQuery('#tab_1').attr('class', 'fivecent-nav-tabs-active');
    jQuery('#tab_2').attr('class', 'fivecent-nav-tabs-inactive');
  } else {

    jQuery('.tab-2-section').show();
    jQuery('.tab-1-section').hide();
    jQuery('#tab_1').attr('class', 'fivecent-nav-tabs-inactive');
    jQuery('#tab_2').attr('class', 'fivecent-nav-tabs-active');
  }
}


jQuery('#wp-admin-bar-delete-cache-completly').click(function () {
  purgecache();
});


/* acceleration tabs */

function acceleration(data) {

  if (data == 'tab1') {

    var cdn_check = false;
    if (jQuery('#wp_disble_cdn_backup').val() == 1) {
      cdn_check = true;
    };

    jQuery('#cdn_status_change').prop('checked', cdn_check)

    jQuery('.http2_section').css('pointer-events', 'unset');
    jQuery('.http2_section').css('opacity', 1);
    jQuery("#http2").prop('disabled', false);


    jQuery('.https_section').css('pointer-events', 'unset');
    jQuery('.https_section').css('opacity', 1);
    jQuery("#https").prop('disabled', false);

    jQuery('.https_redirect_section').css('pointer-events', 'unset');
    jQuery('.https_redirect_section').css('opacity', 1);
    jQuery("#redirect").prop('disabled', false);
    jQuery('#asset_acceleration').val(0);

    var cdn_backup_value = jQuery('#wp_disble_cdn_backup').val();

    jQuery('#wp_disble_cdn').val(cdn_backup_value);


    jQuery('#tab_2').prop('pointer-events', 'unset');
    jQuery('#tab_2').prop('opacity', 1);

  } else {

    jQuery('#cdn_status_change').prop('checked', false)

    jQuery('.http2_section').css('pointer-events', 'none');
    jQuery('.http2_section').css('opacity', 0.4);
    jQuery("#http2").prop('disabled', true);


    jQuery('.https_section').css('pointer-events', 'none');
    jQuery('.https_section').css('opacity', 0.4);
    jQuery("#https").prop('disabled', true);

    jQuery('.https_redirect_section').css('pointer-events', 'none');
    jQuery('.https_redirect_section').css('opacity', 0.4);
    jQuery("#redirect").prop('disabled', true);
    jQuery('#asset_acceleration').val(1);

    jQuery('#wp_disble_cdn').val(0);


    jQuery('#tab_2').css('pointer-events', 'none');
    jQuery('#tab_2').css('opacity', 0.4);

  }
}

/* end section */

/* auto service id change */

var existingapikey = jQuery("#fivecentscdn_api_key").val();
if (jQuery.trim(existingapikey)) {

  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    method: 'POST',
    data: {
      action: 'fivecentscdn_all_zones',
      apikey: existingapikey
    },
    success: function (res) {


      if (res.length !== 0) {

        var zoneurl = 'https://cp.5centscdn.net/dashboard/' + res[0]['serviceid'] + '/zones/http/pull/new';

        jQuery("#serviceid").val(res[0]['serviceid']);
        jQuery('.zoneurl_website_url').attr('href', zoneurl);
      }


    }

  });
  var acceleration_status = jQuery('#asset_acceleration').val();

  if (acceleration_status == 1) {

    jQuery('.http2_section').css('pointer-events', 'none');
    jQuery('.http2_section').css('opacity', 0.4);
    jQuery("#http2").prop('disabled', true);


    jQuery('.https_section').css('pointer-events', 'none');
    jQuery('.https_section').css('opacity', 0.4);
    jQuery("#https").prop('disabled', true);

    jQuery('.https_redirect_section').css('pointer-events', 'none');
    jQuery('.https_redirect_section').css('opacity', 0.4);
    jQuery("#redirect").prop('disabled', true);
    jQuery('#asset_acceleration').val(1);

  }


  /* check ssl warning exist */

  var fivecentscdn_pull_zone = jQuery('#fivecentscdn_pull_zone').val();
  if (jQuery.trim(fivecentscdn_pull_zone)) {

    var pull_zone_id = jQuery.trim(fivecentscdn_pull_zone);
    pull_zone_id = pull_zone_id.replace("pull-", "")

    jQuery.ajax({
      url: ajaxurl,
      dataType: 'json',
      method: 'POST',
      data: {
        action: 'fivecentscdn_zone',
        zone_id: pull_zone_id,
        apikey: existingapikey
      },
      success: function (res) {

        if (res.warning !== false) {

          let result = res.warning.replace('/dashboard/', "https://cp.5centscdn.net/dashboard/");
          result = result.replace('<a', "<a target='_blank' style='color:#59A52C;'");

          jQuery('.ssl-update-error-error').show();
          jQuery('.ssl-update-error-error').html('<p class="error-text"  >' + result + '</p>');
        }

        if (res.warning == false) {
          jQuery('#temp_ssl_warning_holder').val(0);
        } else {

          jQuery('#temp_ssl_warning_holder').val(res.warning);
        }


      }
    });

  }
  /* end section */

}
/* end section */


function field_pusher() {
  var uniq = (new Date()).getTime();

  var html = `<div id="field_` + uniq + `" style="margin:5px; position:relative; user-select: none;">
                             <input id = "` + uniq + `"type = "text"class = "form-control"placeholder = "Enter page/post url for purge" >
                             <div onClick ="remove_field('` + uniq + `')"
                             style = "background: #E0EDDB; cursor:pointer; width: 33px; height: 33px; position: absolute; top: 6px; right: 8px; border-radius: 6px;     justify-content: center; align-items: center; text-align: center;display: flex;" >
                                <span style="font-size: 27px;font-weight: 700; margin-top: -4px; color:#59A52C">-</span>
                             </div>
                              <div class = "invalid-feedback error_notification_web_site_url pusher_field_alert_` + uniq + `"
                              style = "display: none;" > Field cannot be empty </div>
                          </div>`;

  jQuery("#page_url_pusher").append(html);
}

function remove_field(id) {

  jQuery("#field_" + id).remove();
}


/* pure particular post or page */

function purgecacheFile() {

  var pageurlFiles = [];


  var inputs = jQuery("#page_url_pusher :input").each(function (e) {
    id = this.id;

    var page = jQuery.trim(this.value);

    if (page) {

      pageurlFiles.push(page);
      jQuery('.pusher_field_alert_' + id).hide();

    } else {

      jQuery('.pusher_field_alert_' + id).show();

    }

  });
  if (jQuery("#page_url_pusher :input").length === pageurlFiles.length) {
    fivecentscdn_showPopupMessage("Clearing Cache ...");

    jQuery.ajax({
      url: ajaxurl,
      dataType: 'json',
      method: 'POST',
      data: {
        action: 'fivecentscdn_purge_file',
        post_url: pageurlFiles
      },
      success: function (res) {

        if (res.result == "success") {
          setTimeout(function () {
            fivecentscdn_hidePopupMessage();
          }, 300);
        } else {
          fivecentscdn_hidePopupMessage();

          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Clearing cache failed. Please check your API key.',

          })
        }
      }
    });

  }

}

/* end section */


/* start admin menu purge catch */

var window_url = new URL(window.location.href);
if (window_url.searchParams.get('purge')) {
  var purge_type = window_url.searchParams.get('purge');
  if (purge_type == 'all') {

    fivecentscdn_showPopupMessage("Clearing Cache ...");
    jQuery.ajax({
      url: ajaxurl,
      dataType: 'json',
      method: 'POST',
      data: {
        action: 'fivecentscdn_purge'
      },
      success: function (res) {

        if (res.result == "success") {
          setTimeout(function () {
            fivecentscdn_hidePopupMessage();
          }, 300);
        } else {
          fivecentscdn_hidePopupMessage();
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Clearing cache failed. Please check your API key.',

          })
        }
      }
    });

    var url = window.location.href;
    url = url.split('?')[0] + '?page=5centscdn';

    history.pushState({}, null, url);

  } else if (purge_type == 'file') {
    var post_url = window_url.searchParams.get('file_url');
    if (post_url) {
      fivecentscdn_showPopupMessage("Clearing Cache ...");
      var files = [];
      files.push(post_url);


      jQuery.ajax({
        url: ajaxurl,
        dataType: 'json',
        method: 'POST',
        data: {
          action: 'fivecentscdn_purge_file',
          post_url: files
        },
        success: function (res) {

          if (res.result == "success") {
            setTimeout(function () {
              fivecentscdn_hidePopupMessage();
            }, 300);
          } else {
            fivecentscdn_hidePopupMessage();
            Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Clearing cache failed. Please check your API key.',

            })
          }
        }
      });

      var url = window.location.href;
      url = url.split('?')[0] + '?page=5centscdn';

      history.pushState({}, null, url);
    }
  }
}

/* end admin menu purge catch */

function purgecache() {
  fivecentscdn_showPopupMessage("Clearing Cache ...");
  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    method: 'POST',
    data: {
      action: 'fivecentscdn_purge'
    },
    success: function (res) {

      if (res.result == "success") {
        setTimeout(function () {
          fivecentscdn_hidePopupMessage();
        }, 300);
      } else {
        fivecentscdn_hidePopupMessage();
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Clearing cache failed. Please check your API key.',

        })
      }

    }
  });
}

function purgetab(tab) {
  if (tab == 1) {

    jQuery("#purge_tab1").addClass("fivecent-choose-button-active")
    // remove a class
    jQuery("#purge_tab1").removeClass("fivecent-choose-button-inactive")

    jQuery("#purge_tab2").addClass("fivecent-choose-button-inactive")
    // remove a class
    jQuery("#purge_tab2").removeClass("fivecent-choose-button-active")
    jQuery(".purge_tab_1_section").show();
    jQuery(".purge_tab_2_section").hide();

  } else {
    jQuery("#purge_tab1").addClass("fivecent-choose-button-inactive")
    // remove a class
    jQuery("#purge_tab1").removeClass("fivecent-choose-button-active")

    jQuery("#purge_tab2").addClass("fivecent-choose-button-active")
    // remove a class
    jQuery("#purge_tab2").removeClass("fivecent-choose-button-inactive")

    jQuery(".purge_tab_1_section").hide();
    jQuery(".purge_tab_2_section").show();
  }
}

function update_zone_ssl(data) {
  /* $domain_status = 'http';

  if (isset($_SERVER['HTTPS'])) {
      $domain_status = 'https';
  } */
  if (data) {
    if (jQuery('#https').is(':checked')) {

    } else {
      if (location.protocol === 'https:') {

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Dont turn off https its may break your website',

        })
        return false;
      }
    }

  }
  var apikey = jQuery("#fivecentscdn_api_key").val();
  var zoneid = jQuery("#fivecentscdn_pull_zone").val();

  if (!jQuery.trim(apikey)) {
    jQuery('.class_fivecentscdn_api_key').focus();
    jQuery('.error_notification_api_key').show();
    jQuery(".error_notification_api_key").html('api key cannot be empty');
    return false;
  }

  if (jQuery.trim(zoneid) == '0') {
    jQuery('#fivecentscdn_pull_zone_notice').focus();
    jQuery('.error_notification_pull_zone').show();
    jQuery(".error_notification_pull_zone").html('pull zone cannot be empty');
    return false;
  }

  if (jQuery('#fivecentscdn_cdn_domain_name').val() == '0') {


    jQuery('#fivecentscdn_pull_zone_notice').focus();
    jQuery('.error_notification_cdn_domain_name').show();
    jQuery(".error_notification_cdn_domain_name").html('CNAME cannot be empty');
    return false;
  }
  var cdn_domain_name = jQuery('#fivecentscdn_cdn_domain_name').val();


  var is_5centscdn_domain = cdn_domain_name.includes('5centscdn.com');
  var mode = "L";
  var http2 = "N";
  var https = "N";
  var redirect = "N";
  if (is_5centscdn_domain) {
    mode = "L";
  }

  if (jQuery('#http2').is(':checked')) {
    http2 = "Y";
  }

  if (jQuery('#https').is(':checked')) {
    https = "Y";

    jQuery('.lock_icon').css("color", "#59A52C");
  } else {
    mode = "N";
    jQuery('.lock_icon').css("color", "red");
  }
  if (jQuery('#redirect').is(':checked')) {
    redirect = "Y";
  }


  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    method: 'POST',
    data: {
      zoneid: zoneid,
      action: 'fivecentscdn_update_zone_ssl',
      apikey: apikey,
      http2: http2,
      redirect: redirect,
      mode: mode,
      enabled: https
    },
    success: function (res) {
      if (res.zone.ssl.warning !== false) {

        let result = res.zone.ssl.warning.replace('/dashboard/', "https://cp.5centscdn.net/dashboard/");
        result = result.replace('<a', "<a target='_blank'  style='color:#59A52C;'");

        jQuery('.ssl-update-error-error').show();
        jQuery('.ssl-update-error-error').html('<p class="error-text"  >' + result + '</p>');


      } else {

        jQuery('.ssl-update-error-error').hide();
      }

      if (res.zone.ssl.warning == false) {
        jQuery('#temp_ssl_warning_holder').val("0");
      } else {

        jQuery('#temp_ssl_warning_holder').val(res.zone.ssl.warning);
      }

      if (res.zone.ssl.enabled == "Y") {
        jQuery('.http2_section').css('pointer-events', 'unset');
        jQuery('.http2_section').css('opacity', 1);
        jQuery("#http2").prop('disabled', false);

        jQuery('.https_redirect_section').css('pointer-events', 'unset');
        jQuery('.https_redirect_section').css('opacity', 1);
        jQuery("#redirect").prop('disabled', false);


      } else if (res.zone.ssl.enabled == "N") {

        jQuery('.http2_section').css('pointer-events', 'none');
        jQuery('.http2_section').css('opacity', 0.4);
        jQuery("#http2").prop('disabled', true);


        jQuery('.https_redirect_section').css('pointer-events', 'none');
        jQuery('.https_redirect_section').css('opacity', 0.4);
        jQuery("#redirect").prop('disabled', true);

      }
    }
  });

}

function disablecdn(val) {
  jQuery('#wp_disble_cdn').val(val);
  jQuery('#fivecentscdn_options_form').submit();
}

function submitForm() {

  /* url filter after redirect */

  var url = window.location.href;
  url = url.split('?')[0] + '?page=5centscdn';

  history.pushState({}, null, url);

  /* end section */

  var site_url = jQuery('.class_fivecentscdn_site_url').val();
  var api_key = jQuery('.class_fivecentscdn_api_key').val();


  if (!jQuery.trim(site_url)) {
    jQuery('.class_fivecentscdn_site_url').focus();
    jQuery('.error_notification_web_site_url').show();
    jQuery(".error_notification_web_site_url").html('wp site url cannot be empty');
    return false;
  }
  jQuery('.error_notification_web_site_url').hide();

  if (!jQuery.trim(api_key)) {
    jQuery('.class_fivecentscdn_api_key').focus();
    jQuery('.error_notification_api_key').show();
    jQuery(".error_notification_api_key").html('api key cannot be empty');
    return false;
  }
  jQuery('.error_notification_api_key').hide();


  if (jQuery('#fivecentscdn_pull_zone').val() == '0') {
    jQuery('#fivecentscdn_pull_zone_notice').focus();
    jQuery('.error_notification_pull_zone').show();
    jQuery(".error_notification_pull_zone").html('pull zone cannot be empty');
    return false;
  }
  jQuery('.error_notification_pull_zone').hide();

  if (jQuery('#fivecentscdn_cdn_domain_name').val() == '0') {
    jQuery('#fivecentscdn_pull_zone_notice').focus();
    jQuery('.error_notification_cdn_domain_name').show();
    jQuery(".error_notification_cdn_domain_name").html('CNAME cannot be empty');
    return false;
  }
  jQuery('.error_notification_cdn_domain_name').hide();

  var asset_acceleration_value = jQuery("#asset_acceleration").val();

  if (asset_acceleration_value == 1) {

    var cdn_domain_name_is = jQuery('#fivecentscdn_cdn_domain_name').val();
    if (cdn_domain_name_is.includes('5centscdn') == false) {

      if (cdn_domain_name_is.includes(window.location.hostname) == false) {

        jQuery('.cname_add_warning_message').css('display', 'block');
        jQuery('#cname_add_field').focus();

        return false;

      }


    }

  }


  jQuery('#fivecentscdn_options_form').submit();


}


jQuery('#cname_add_button').click(function (event) {

  event.preventDefault();
  var cname_add_field = jQuery('#cname_add_field').val();
  cname_add_field = cname_add_field.replace(/(^\w+:|^)\/\//, '');
  if (jQuery.trim(cname_add_field)) {
    if (validURL(jQuery.trim(cname_add_field)) == true) {
      var l = getLocation(window.location.href);
      var adding_cname;

      if (jQuery.trim(cname_add_field).includes(l.hostname) == true) {
        var temp_cname = jQuery('#temp_cnames_add_to_input').val();
        if (temp_cname == 0) {
          adding_cname = jQuery.trim(cname_add_field);
        } else {
          adding_cname = temp_cname + ',' + jQuery.trim(cname_add_field);
        }
        var apikey1 = jQuery("#fivecentscdn_api_key").val();

        var fivecentscdn_pull_zone = jQuery('#fivecentscdn_pull_zone').val();
        var pull_zone_id = jQuery.trim(fivecentscdn_pull_zone);
        pull_zone_id = pull_zone_id.replace("pull-", "")

        jQuery.ajax({
          url: ajaxurl,
          dataType: 'json',
          method: 'POST',
          data: {
            action: 'fivecentscdn_cname_update',
            apikey: apikey1,
            zoneid: pull_zone_id,
            orgin: window.location.origin,
            cnames: adding_cname
          },
          success: function (res) {
            if (res.result == 'success' && res.warnings == null) {
              jQuery('.cname_add_warning_message').css('display', 'none')
              jQuery('.cname_added_success_message').cdd('display', 'block')
            } else {

              jQuery('.cname_add_warning_message').css('display', 'none')

              var i;
              jQuery('.cname_added_warning_messages').html('');

              for (i = 0; i < res.warnings.length; i++) {

                jQuery(".cname_added_warning_messages").append('<p class="error-text" style="line-height:5px!important; ">' + res.warnings[i] + ' </p> ');
              }

              jQuery('.cname_added_warning_messages').css('display', 'block')


            }
          }
        });
      } else {

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'invalid cname',

        })
      }
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'invalid cname',

      })
    }
  } else {

    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Field cannot be empty',

    })
  }

});

var getLocation = function (href) {
  var l = document.createElement("a");
  l.href = href;
  return l;
};

function validURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
    '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
  return !!pattern.test(str);
}

function setzone() {

  var apikey = jQuery("#fivecentscdn_api_key").val();
  jQuery.ajax({
    url: ajaxurl,
    dataType: 'json',
    method: 'POST',
    data: {
      action: 'fivecentscdn_all_zones',
      apikey: apikey
    },
    success: function (res) {
      var json_obj = res;
      var output = [];
      output.push('<option value="0">Select pull zone</option>');
      for (var i in json_obj) {
        if (json_obj[i].status !== 'Deleted') {
          output.push('<option value="' + json_obj[i].id + '">' + json_obj[i].name + '</option>');
        }

      }
      jQuery('#fivecentscdn_pull_zone').html(output.join(''));
    }
  });
}
/* jQuery(".class_fivecentscdn_api_key").keyup(function (e) {



    
}); */

jQuery(".class_fivecentscdn_api_key").on("change paste keyup", function () {
  var api_key = jQuery(".class_fivecentscdn_api_key").val();

  jQuery(".tab-1-section-1").show();

  jQuery(".tab-1-section-2").hide();
  jQuery(".fivecent-button-main").show();
  jQuery(".fivecentscdn_api_key_initial").val(api_key);
  jQuery('.fivecent_save_settings_button').hide();
  jQuery("#fivecentscdn-connect-button").html('Connect');
});

jQuery("#cdn_status_change").change(function (event) {


  if (jQuery('#cdn_status_change').is(':checked')) {


    var https_status = false;


    var asset_acceleration_value = jQuery("#asset_acceleration").val();

    if (asset_acceleration_value != 1) {

      if (jQuery('#https').is(':checked')) {
        https_status = true;

        if (jQuery('#temp_ssl_warning_holder').val() != 0) {

          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'please wait till ssl warning is gone!',

          })
          jQuery('#cdn_status_change').prop('checked', false)
          return false;
        }
      }


      if (window.location.protocol != "http:" && https_status == false) {
        jQuery('#cdn_status_change').prop('checked', false)

        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'turn on https',

        })
        return false;
      }

    }


  }


  event.preventDefault();
  var site_url = jQuery('.class_fivecentscdn_site_url').val();
  var api_key = jQuery('.class_fivecentscdn_api_key').val();

  var cdn_status = 0;
  var cdn_status_checked = false;
  if (jQuery('#cdn_status_change').is(':checked')) {
    cdn_status = 1;
    cdn_status_checked = true;
  } else {
    cdn_status_checked = false;
  }


  if (cdn_status == 1) {

    var asset_acceleration_value = jQuery("#asset_acceleration").val();

    if (asset_acceleration_value == 1) {

      cdn_status = 0;

      var cdn_domain_name_is = jQuery('#fivecentscdn_cdn_domain_name').val();
      if (cdn_domain_name_is.includes('5centscdn') == false) {

        if (cdn_domain_name_is.includes(window.location.hostname) == false) {

          jQuery('.cname_add_warning_message').css('display', 'block');
          jQuery('#cname_add_field').focus();
          jQuery('#cdn_status_change').prop("checked", false)

          return false;

        }


      }

    }

    if (!jQuery.trim(site_url)) {

      jQuery('#cdn_status_change').prop("checked", cdn_status_checked)
      jQuery('.class_fivecentscdn_site_url').focus();
      jQuery('.error_notification_web_site_url').show();
      jQuery(".error_notification_web_site_url").html('wp site url cannot be empty');
      jQuery('#cdn_status_change').prop('checked', false)
      return false;
    }
    jQuery('.error_notification_web_site_url').hide();

    if (!jQuery.trim(api_key)) {
      jQuery('#cdn_status_change').prop("checked", cdn_status_checked)
      jQuery('.class_fivecentscdn_api_key').focus();
      jQuery('.error_notification_api_key').show();
      jQuery(".error_notification_api_key").html('api key cannot be empty');
      jQuery('#cdn_status_change').prop('checked', false)
      return false;
    }
    jQuery('.error_notification_api_key').hide();


    if (jQuery('#fivecentscdn_pull_zone').val() == '0') {

      jQuery('#cdn_status_change').prop("checked", cdn_status_checked)
      jQuery('#fivecentscdn_pull_zone_notice').focus();
      jQuery('.error_notification_pull_zone').show();
      jQuery(".error_notification_pull_zone").html('pull zone cannot be empty');
      jQuery('#cdn_status_change').prop('checked', false)
      return false;
    }
    jQuery('.error_notification_pull_zone').hide();

    if (jQuery('#fivecentscdn_cdn_domain_name').val() == '0') {

      jQuery('#cdn_status_change').prop("checked", cdn_status_checked)
      jQuery('#fivecentscdn_pull_zone_notice').focus();
      jQuery('.error_notification_cdn_domain_name').show();
      jQuery(".error_notification_cdn_domain_name").html('CNAME cannot be empty');
      jQuery('#cdn_status_change').prop('checked', false)
      return false;
    }
    jQuery('.error_notification_cdn_domain_name').hide();
  } else {

    var asset_acceleration_value = jQuery("#asset_acceleration").val();

    if (asset_acceleration_value == 1) {
      jQuery('#asset_acceleration').val(0);
    }
  }


  jQuery('#wp_disble_cdn').val(cdn_status);

  jQuery('#fivecentscdn_options_form').submit();
});


jQuery("#fivecentscdn-connect-button").click(function (e) {
  var apikey = jQuery("#fivecentscdn_api_key").val();
  e.preventDefault();


  if (apikey.length == 0) {


    jQuery(".error_notification_api_key").show();
    jQuery(".error_notification_api_key").html('please first set your API key');

    jQuery("#fivecentscdn_api_key").focus();
  } else {
    jQuery("#fivecentscdn-connect-button").html('Connecting.. <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    jQuery.ajax({
      url: ajaxurl,
      dataType: 'json',
      method: 'POST',
      data: {
        action: 'fivecentscdn_all_zones',
        apikey: apikey
      },
      success: function (res) {
        // jQuery(".tab-1-section-1").hide()
        jQuery(".tab-1-section-1").hide();
        jQuery(".tab-1-section-2").show();
        jQuery(".fivecent-button-main").hide();
        jQuery(".class_fivecentscdn_api_key").val(apikey);
        jQuery('.fivecent_save_settings_button').show();
        setzone();
        jQuery('#fivecentscdn_cdn_domain_name').val(0)

        jQuery('#http2').prop("checked", false);
        jQuery('#https').prop("checked", false);
        jQuery('#redirect').prop("checked", false)


        if (res.length !== 0) {

          var zoneurl = 'https://cp.5centscdn.net/dashboard/' + res[0]['serviceid'] + '/zones/http/pull/new';

          jQuery("#serviceid").val(res[0]['serviceid']);
          jQuery('.zoneurl_website_url').attr('href', zoneurl);
        }


      },
      error: function (xhr, status, error) {

        jQuery(".invalid-apikey-error").show();
        jQuery("#fivecentscdn-connect-button").html('Connect');
      }
    });

  }
  // alert(apikey);
});

jQuery("#fivecentscdn_pull_zone").keydown(function (e) {
  if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    (e.keyCode >= 35 && e.keyCode <= 40) ||
    (e.keyCode >= 65 && e.keyCode <= 90)) {
    return;
  }
  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
    e.preventDefault();
  }
});

jQuery("#fivecentscdn_cdn_domain_name").keydown(function (e) {
  if (jQuery.inArray(e.keyCode, [109, 189, 46, 8, 9, 27, 13, 110, 190]) !== -1 ||
    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    (e.keyCode >= 35 && e.keyCode <= 40) ||
    (e.keyCode >= 65 && e.keyCode <= 90)) {
    return;
  }
  if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
    e.preventDefault();
  }
});

function fivecentscdn_showPopupMessage(message) {
  jQuery("#fivecentscdn_popupBackground").show("fast");
  jQuery("#fivecentscdn_popupBox").show("fast");
  jQuery("#fivecentscdn_popupMessage").text(message);
  /*  jQuery([document.documentElement, document.body]).animate({
       scrollTop: 0
   }, 500); */
}

function fivecentscdn_hidePopupMessage() {
  jQuery("#fivecentscdn_popupBackground").hide("fast");
  jQuery("#fivecentscdn_popupBox").hide("fast");
}

jQuery("#fivecentscdn_cdn_domain_name").change(function (event) {

  jQuery('.cname_add_warning_message').css('display', 'none')
  jQuery("#txt_cdn_domain_name").text(jQuery("#fivecentscdn_cdn_domain_name").val());
});
jQuery("#fivecentscdn_pull_zone").change(function (event) {
  var zone_id = jQuery("#fivecentscdn_pull_zone").val();
  var apikey = jQuery("#fivecentscdn_api_key").val();

  if (zone_id != 0) {
    var weburl = "<?php echo FIVECENTSCDN_DOMAIN; ?>";
    jQuery("#fivecentscdn_cdn_domain_name").empty();
    jQuery("#fivecentscdn_cdn_domain_name").empty();
    jQuery('#fivecentscdn_cdn_domain_name').append("<option value='0'>Select CNAME</option>");

    jQuery.ajax({
      url: ajaxurl,
      dataType: 'json',
      method: 'POST',
      data: {
        action: 'fivecentscdn_zone',
        zone_id: zone_id,
        apikey: apikey
      },
      success: function (response) {
        jQuery('.cname_add_warning_message').css('display', 'none');
        var zoneurl = 'https://cp.5centscdn.net/dashboard/' + response['serviceid'] + '/zones/http/pull/new';

        jQuery("#serviceid").val(response['serviceid']);
        jQuery('.zoneurl_website_url').attr('href', zoneurl);

        jQuery(".zonecdn").show();
        jQuery('#fivecentscdn_cdn_domain_name').append("<option value=" + response['fqdn'] + "> " + response['fqdn'] + " </option>");
        jQuery("#txt_cdn_domain_name").text(response['fqdn']);

        if (response['cnames']) {

          jQuery('#temp_cnames_add_to_input').val(response['cnames']);
          var cnames = response['cnames'].split(",");
          jQuery.each(cnames, function (i) {
            jQuery('#fivecentscdn_cdn_domain_name').append("<option value=" + cnames[i] + "> " + cnames[i] + " </option>");
          });
        }
        if (response['http'] == "Y") {
          jQuery("#http2").prop("checked", true)
        } else {
          jQuery("#http2").prop("checked", false)
        }
        if (response['enabled'] == "Y") {
          jQuery("#https").prop("checked", true)
          jQuery('.https_status_locker').css("color", "#59A52C");

          jQuery('.http2_section').css('pointer-events', 'unset');
          jQuery('.http2_section').css('opacity', 1);
          jQuery("#http2").prop('disabled', false);

          jQuery('.https_redirect_section').css('pointer-events', 'unset');
          jQuery('.https_redirect_section').css('opacity', 1);
          jQuery("#redirect").prop('disabled', false);

        } else {
          jQuery("#https").prop("checked", false)
          jQuery('.https_status_locker').css("color", "red");

          jQuery('.http2_section').css('pointer-events', 'none');
          jQuery('.http2_section').css('opacity', 0.4);
          jQuery("#http2").prop('disabled', true);


          jQuery('.https_redirect_section').css('pointer-events', 'none');
          jQuery('.https_redirect_section').css('opacity', 0.4);
          jQuery("#redirect").prop('disabled', true);
        }
      }
    });
  }
});

(function () {
  jQuery('.wp-menu-name:contains("5centsCDN")').prev().find('img').css("width", "60%").css("padding-top", "6px");
})()