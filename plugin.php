<?

/*
Plugin Name: Kloudstores
Plugin URI:
Description: Kloudstores page plugin.
Version: 1.0
Author:
Author URI:
License:
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
        die;
} // end if

require_once(plugin_dir_path( __FILE__ ) . "settings.php");
require_once(plugin_dir_path( __FILE__ ) . "iframe.php");
require_once(plugin_dir_path( __FILE__ ) . 'page_template_plugin.php' );

//register_activation_hook( __FILE__, 'kloudstores_install');
register_deactivation_hook( __FILE__, 'kloudstores_uninstall');

add_action( 'plugins_loaded', array( 'Page_Template_Plugin', 'get_instance' ) );
add_action( 'admin_menu', 'kloudstores_create_menu' );
add_action( 'admin_notices', 'activation_message' );

//function kloudstores_install() {}

function kloudstores_uninstall()
{
    $page = get_page(get_option('kldstrs_page'));
    wp_delete_post($page->ID);
    delete_option('kldstrs_url');
    delete_option('kldstrs_title');
    delete_option('kldstrs_page');
    delete_option('kldstrs_update');
    delete_option('kldstrs_activate');
}

function activation_message()
{
    if(isset($_GET['page']) && $_GET['page'] == 'kloudstores/settings.php' && isset($_GET['settings-updated']) && $_GET['settings-updated'])
    {
        $html = '<div class="updated">';
        $html .= '<p>';
        $html .= get_option('kldstrs_update');
        $html .= '</p>';
        $html .= '</div>';
        echo $html;
    }
    elseif(is_plugin_active('kloudstores/plugin.php') && in_array($GLOBALS['pagenow'], array('plugins.php')) && get_option('kldstrs_activate') != '1')
    {
        $html = '<div class="updated">';
        $html .= '<p>';
        $html .= 'Kloudstores plugin has been activated! Further setting up on the <a style="text-decoration:underline" href="'. admin_url('admin.php?page=kloudstores/settings.php') .'">Kloudstores settings page</a>.';
        $html .= '</p>';
        $html .= '</div>';
        echo $html;
    }
}
?>