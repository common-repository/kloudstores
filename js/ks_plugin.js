/**
 * We use the jQuery Cookie plugin to automatically set the login cookie for the
 * kloudstores administration area after the store was successfully created.
 */

/*!
 * jQuery Cookie Plugin
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 */
(function($) {
    $.cookie = function(key, value, options) {

        // key and at least value given, set cookie...
        if (arguments.length > 1 && (!/Object/.test(Object.prototype.toString.call(value)) || value === null || value === undefined)) {
            options = $.extend({}, options);

            if (value === null || value === undefined) {
                options.expires = -1;
            }

            if (typeof options.expires === 'number') {
                var days = options.expires,
                    t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = String(value);

            return (document.cookie = [
                encodeURIComponent(key), '=', options.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join(''));
        }

        // key and possibly options given, get cookie...
        options = value || {};
        var decode = options.raw ? function(s) {
                return s;
            } : decodeURIComponent;

        var pairs = document.cookie.split('; ');
        for (var i = 0, pair; pair = pairs[i] && pairs[i].split('='); i++) {
            if (decode(pair[0]) === key) return decode(pair[1] || ''); // IE saves cookies with empty string as "c; ", e.g. without "=" as opposed to EOMB, thus pair[1] may be undefined
        }
        return null;
    };
})(jQuery);

jQuery(document).ready(function(){
    if (! jQuery('[name=kldstrs_url]').length || jQuery('[name=kldstrs_url]').val() == '') {
        // The store does not exist and in this case we change the caption of the
        // submit button from "save settings" to "create my store"
        jQuery('#submit').val('Create my store');

        jQuery('#submit').closest('form').submit(function(){
            var email_field = jQuery(this).find('input[name=kldstrs_admin_email]');
            var name_field = jQuery(this).find('input[name=kldstrs_blogname]');
            var email = email_field.val();
            var name = name_field.val();
            var option_form = jQuery(this);

            if (option_form.find('[name=kldstrs_url]').val() != '') {
                // the store was already created, exit
                return true;
            }

            // Check if the store name was entered
            if (name.trim() == '') {
                alert('Please enter a store name');
                return false;
            }

            // Check if the email address was entered
            if (email.trim() == '') {
                alert('Please enter an email address');
                return false;
            }

            // Send the information to the secure endpoint on kloudstores to try and create the new store
            jQuery.getJSON(config['admin_url'] + '/signup?jsonp=?', {
                email          : email,
                name           : name,
                theme          : typeof selected_theme != 'undefined' && selected_theme ? selected_theme : 'default',
                method         : 'post',
                admin_language : 'en'
            }, function(response) {
                if (typeof response.error != 'undefined') {
                    alert(response.error);
                    return true;
                }

                if (typeof response['sessid'] != 'undefined') {
                    // Set the administration area cookie to basically login the user
                    jQuery.cookie('k_admin', response['sessid'], {
                        expires: 365,
                        path: '/',
                        domain: config['cookie_domain']
                    });

                    option_form.find('[name=kldstrs_url]').val(response['user']['key']);

                    // Submit the settings form to save the store url and any other needed settings
                    option_form.find('#submit').click();
                } else {
                    alert('There was an error while trying to create your store. Please try again.');
                }
            });

            return false;
        });
    } else {
    }
});