<?php
/**
 *
 * Plugin Name: Change AMPHTML
 * Description: AMP sayfaları için amphtml etiketi domainini değiştirir. Ücretsiz bir yazılımdır. Güncel sürümünü github sayfamdan edinebilirsiniz.
 * Version:     1.0
 * Author:      Emre Nogay
 * Plugin URI:  https://github.com/emrenogay
 * Author URI:  https://emrenogay.com
 * License: GNU General Public License v3.0
 *
 */


add_filter( 'plugin_action_links_change-amphtml/change-amphtml.php', function ($links_array){
    array_unshift( $links_array, '<a href="'.get_admin_url().'options-general.php?page=emrenogay_amphtml_group">Ayarlar</a>' );
    return $links_array;
} );

add_action('wp_head', function () {
    ob_start();
}, 0);

add_action('wp_head', function () {
    $in = ob_get_clean();
    $http_version = is_ssl() ? 'https://' : 'http://';
    $addon = !empty(get_option('emrenogay__amphtml')) ? get_option('emrenogay__amphtml') : str_replace(['https://', 'http://'], null, get_site_url());
    $in = str_replace(
        '<link rel="amphtml" href="'.get_site_url(),
        '<link rel="amphtml" href="'.$http_version . $addon,
        $in);
    echo $in;
}, PHP_INT_MAX);


add_action( 'admin_init', function () {
    register_setting( 'emrenogay_amphtml_group', 'emrenogay__amphtml' );
} );

add_action('admin_menu', function(){

    add_options_page(
        'AMPHTML',
        'AMPHTML',
        'manage_options',
        'emrenogay_amphtml_group',
        function(){
            ?>
            <style>
                .submit{
                    padding: 0;
                }
                p.submit{
                    margin-top: 0 !important;
                }
            </style>
            <div style="width: fit-content; background: white; padding:15px; margin:10% auto 0; border-radius:7px;box-shadow: 1px 0 25px rgba(0, 0, 0, .1)">
                <div>
                    <form method="post" action="options.php">
                        <?php settings_fields( 'emrenogay_amphtml_group' ); ?>
                        <?php do_settings_sections( 'emrenogay_amphtml_group' ); ?>
                        <label>
                            <div style="margin-bottom:10px">
                                Kutuya <strong>sadece domain</strong> yazın. <br> Örneğin: sub.domain.com veya emrenogay.com gibi.
                            </div>
                            <input type="text" name="emrenogay__amphtml" placeholder="AMPHTML içeriği" value="<?php echo get_option('emrenogay__amphtml') ?>">
                        </label>
                        <?php submit_button(); ?>
                        <p>Bu eklenti <a href="https://emrenogay.com" rel="nofollow" target="_blank">emrenogay.com</a> tarafından <strong>ücretsiz</strong> promosyon olarak verilmektedir.</p>
                    </form>
                </div>
            </div>
            <?php
        },
    );
});
