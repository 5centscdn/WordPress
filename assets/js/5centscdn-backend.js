jQuery('#wp-admin-bar-delete-cache-completly').click(function() {
    purgecache();
});

function purgecache() {
    fivecentscdn_showPopupMessage("Clearing Cache ...");
    jQuery.ajax({
        url: ajaxurl,
        dataType: 'json',
        method: 'POST',
        data: {
            action: 'fivecentscdn_purge'
        },
        success: function(res) {
          if (res == "1") {
              setTimeout(function() {
                  fivecentscdn_hidePopupMessage();
              }, 300);
          } else {
              fivecentscdn_hidePopupMessage();
              alert("Clearing cache failed. Please check your API key.");
          }
        }
    });
}

function disablecdn(val) {
    jQuery('#wp_disble_cdn').val(val);
    jQuery('#fivecentscdn_options_form').submit();
}

function submitForm() {
    var site_url = jQuery('#fivecentscdn_site_url').val();
    var res = site_url.split(":");
    if (jQuery('#fivecentscdn_pull_zone').val() == '0') {
        jQuery('#fivecentscdn_pull_zone_notice').focus();
        jQuery('#fivecentscdn_pull_zone_notice').show();
        return false;
    } else {
        jQuery('#fivecentscdn_pull_zone_notice').hide();
        jQuery('#fivecentscdn_options_form').submit();
        return true;
    }
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
        success: function(res) {
            var json_obj = res;
            var output = [];
            output.push('<option value="0">Select Zone</option>');
            for (var i in json_obj) {
                output.push('<option value="' + json_obj[i].id + '">' + json_obj[i].name + '</option>');
            }
            jQuery('#fivecentscdn_pull_zone').html(output.join(''));
        }
    });
}
jQuery("#fivecentscdn_api_key").keyup(function(e) {
    jQuery(".zonecdn").hide();
    jQuery(".connectsubmit").show();
    jQuery(".submit").hide();
    jQuery("#fivecentscdn-clear-cache-button").hide();
});

jQuery("#fivecentscdn-connect-button").click(function(e) {
    var apikey = jQuery("#fivecentscdn_api_key").val();
    if (apikey.length == 0) {
        jQuery("#fivecentscdn_api_key_notice").show();
        jQuery("#fivecentscdn_api_key_notice").html('please first set your API key');
        jQuery([document.documentElement, document.body]).animate({
            scrollTop: jQuery("#fivecentscdn_api_key_notice").offset().top
        }, 1000);
        jQuery("#fivecentscdn_api_key").focus();
    } else {
        setzone();
        jQuery("#fivecentscdn_api_key_notice").hide();
        jQuery("#fivecentscdn_api_key_notice").html('');
        jQuery(".zonecdn").show();
        jQuery(".connectsubmit").hide();
        jQuery(".submit").show();
        jQuery("#fivecentscdn-clear-cache-button").hide();
    }
});

jQuery("#fivecentscdn_pull_zone").keydown(function(e) {
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

jQuery("#fivecentscdn_cdn_domain_name").keydown(function(e) {
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
    jQuery([document.documentElement, document.body]).animate({
        scrollTop: 0
    }, 500);
}

function fivecentscdn_hidePopupMessage() {
    jQuery("#fivecentscdn_popupBackground").hide("fast");
    jQuery("#fivecentscdn_popupBox").hide("fast");
}

jQuery("#fivecentscdn_cdn_domain_name").change(function(event) {
    jQuery("#txt_cdn_domain_name").text(jQuery("#fivecentscdn_cdn_domain_name").val());
});
jQuery("#fivecentscdn_pull_zone").change(function(event) {
    var zone_id = jQuery("#fivecentscdn_pull_zone").val();
    var apikey = jQuery("#fivecentscdn_api_key").val();
    if (zone_id != 0) {
        var weburl = "<?php echo FIVECENTSCDN_DOMAIN; ?>";
        jQuery("#fivecentscdn_cdn_domain_name").empty();
        jQuery("#fivecentscdn_cdn_domain_name").empty();
        jQuery('#fivecentscdn_cdn_domain_name').append("<option value='0'> Select CDN Resource</option>");
        jQuery.ajax({
            url: ajaxurl,
            dataType: 'json',
            method: 'POST',
            data: {
                action: 'fivecentscdn_zone',
                zone_id: zone_id,
                apikey: apikey
            },
            success: function(response) {
                jQuery("#serviceid").val(response['serviceid']);
                jQuery(".zonecdn").show();
                jQuery('#fivecentscdn_cdn_domain_name').append("<option value=" + response['fqdn'] + "> " + response['fqdn'] + " </option>");
                jQuery("#txt_cdn_domain_name").text(response['fqdn']);
                if (response['cnames']) {
                    var cnames = response['cnames'].split(",");
                    jQuery.each(cnames, function(i) {
                        jQuery('#fivecentscdn_cdn_domain_name').append("<option value=" + cnames[i] + "> " + cnames[i] + " </option>");
                    });
                }
                if (response['http'] == "Y") {
                    jQuery("#fivecentscdn_http_notice").html("<span style='color: green'>Enabled</span><a href='" + weburl + "dashboard/" + response['serviceid'] + "/zones/http/pull/" + zone_id + "' target='_blank'>(To disable HTTPS2 click here!)</a>");
                } else {
                    jQuery("#fivecentscdn_http_notice").html("<span style='color: red'>Disabled</span><a href='" + weburl + "dashboard/" + response['serviceid'] + "/zones/http/pull/" + zone_id + "' target='_blank'>(To enable HTTPS2 click here!)</a>");
                }
                if (response['enabled'] == "Y") {
                    jQuery("#fivecentscdn_http_ennable").html("<i class='fa fa-lock' aria-hidden='true' style='font-size: 16px;color: green'></i>&nbsp;&nbsp;<a href='" + weburl + "dashboard/" + response['serviceid'] + "/zones/http/pull/" + zone_id + "' target='_blank'>(To disable HTTPS click here!)</a>");
                } else {
                    jQuery("#fivecentscdn_http_ennable").html("<i class='fa fa-lock' aria-hidden='true' style='font-size: 16px;color:gray '></i>&nbsp;&nbsp;<a href='" + weburl + "dashboard/" + response['serviceid'] + "/zones/http/pull/" + zone_id + "' target='_blank'>(To enable HTTPS click here!)</a>");
                }
            }
        });
    }
});

(function () {
    jQuery('.wp-menu-name:contains("5centsCDN")').prev().find('img').css("width", "60%").css("padding-top", "6px");
})()