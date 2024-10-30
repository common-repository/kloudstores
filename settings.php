<?php

define('KS_DOMAIN', 'kloudstores.com');

function ks_plugin_admin_resources($hook) {
    wp_enqueue_script( 'ks_resources', plugin_dir_url( __FILE__ ) . 'js/ks_plugin.js', null, null, true);
}

function kloudstores_create_menu()
{
    //create new top-level menu
    $page = add_menu_page('Kloudstores Plugin Settings', 'Kloudstores', 'administrator', __FILE__, 'kloudstores_settings_page');

    add_action('admin_init', 'ks_admin_init');
    add_action('load-' . $page, 'update_post');
}

function ks_admin_init()
{
    //register our settings
    register_setting('kloudstores-settings-group', 'kldstrs_url');
    register_setting('kloudstores-settings-group', 'kldstrs_title');
    register_setting('kloudstores-settings-group', 'kldstrs_page');
    register_setting('kloudstores-settings-group', 'kldstrs_activate');
    register_setting('kloudstores-settings-group', 'kldstrs_update');

    add_action( 'admin_enqueue_scripts', 'ks_plugin_admin_resources' );
}

function update_post()
{
    if (isset($_GET['settings-updated'])) {
        $existing_page = get_option('kldstrs_page');

        if ((int) $existing_page) {
            $post = array(
                'ID' => $existing_page,
                'post_title' => get_option('kldstrs_title'),
                'post_name' => get_option('kldstrs_title'),
                'post_content' => '[iframe src="http://' . get_option('kldstrs_url') . '.' . KS_DOMAIN . '?container=wp"]'
            );

            wp_update_post($post, false);
        } else {
            $post = array(
                'comment_status' => 'closed',
                'ping_status' =>  'closed' ,
                'post_author' => 1,
                'post_date' => date('Y-m-d H:i:s'),
                'post_name' => get_option('kldstrs_title'),
                'post_status' => 'publish' ,
                'post_type' => 'page',
                'post_title' => get_option('kldstrs_title'),
                'post_content' => '[iframe src="http://' . get_option('kldstrs_url') . '.' . KS_DOMAIN . '?container=wp"]'
            );

            $new_id = wp_insert_post($post, false);

            update_option('kldstrs_page', $new_id);
            update_option('kldstrs_activate', time());
            update_post_meta( $new_id, '_wp_page_template', 'kloud_tpl.php' );
        }
    }
}

function kloudstores_settings_page() {
?>
<div class="wrap">
    <h2>Kloudstores</h2>

    <? if ((int) get_option('kldstrs_activate')): ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'kloudstores-settings-group' ); ?>
            <?php do_settings_sections( 'kloudstores-settings-group' ); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        Page name
                    </th>
                    <td>
                        <input type="text" name="kldstrs_title" value="<?= get_option('kldstrs_title'); ?>" class="regular-text" /><br />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Store url
                    </th>
                    <td>
                        <input type="text" name="kldstrs_url" value="<?= get_option('kldstrs_url'); ?>" class="regular-text" style="width:235px;" />.<?= KS_DOMAIN ?> <br />
                    </td>
                </tr>
            </table>

            <input type="hidden" name="kldstrs_update" value="The settings were successfully updated!" />
            <input type="hidden" name="kldstrs_activate" value="1" />
            <input type="hidden" name="kldstrs_page" value="<?= get_option('kldstrs_page'); ?>" />

            <?php submit_button(); ?>
        </form>
    <? else: ?>
        <p>This plugin will create a store on <a href="http://www.kloudstores.com?ref=wp-plugin" target="_blank">kloudstores.com</a> for you and automatically link it in your blog.</p>

        <form method="post" action="options.php">
            <?php settings_fields( 'kloudstores-settings-group' ); ?>
            <?php do_settings_sections( 'kloudstores-settings-group' ); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        Store name
                    </th>
                    <td>
                        <input type="text" name="kldstrs_blogname" value="<?= get_option('blogname'); ?>" class="regular-text" /><br />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        Administration email
                    </th>
                    <td>
                        <input type="text" name="kldstrs_admin_email" value="<?= get_option('admin_email'); ?>" class="regular-text" /><br />
                        <p class="description">We will send you the password to the store administration to this email address.</p>
                    </td>
                </tr>
            </table>

            <input type="hidden" name="kldstrs_title" value="Store" />
            <input type="hidden" name="kldstrs_url" value="<?= get_option('kldstrs_url'); ?>" />
            <input type="hidden" name="kldstrs_update" value="The store was successfully created!" />
            <input type="hidden" name="kldstrs_activate" value="<?= time() ?>" />
            <input type="hidden" name="kldstrs_page" value="0" />

            <?php submit_button(); ?>
        </form>
    <? endif; ?>
</div>

<script type="text/javascript">
    var config = {
        "admin_url": "https://admin.<?= KS_DOMAIN ?>",
        "themes_url": "http://themes.<?= KS_DOMAIN ?>",
        "cookie_domain": ".<?= KS_DOMAIN ?>"
    };
</script>
<?php } ?>