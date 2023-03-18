<?php
/**
 *
 * Plugin Name: Change AMPHTML
 * Description: AMP sayfaları için amphtml etiketi domainini değiştirir. Ücretsiz bir yazılımdır. Güncel sürümünü github sayfamdan edinebilirsiniz.
 * Version:     1.6
 * Author:      Emre Nogay
 * Plugin URI:  https://github.com/emrenogay/change-amphtml
 * Author URI:  https://emrenogay.com
 * License: GNU General Public License v3.0
 *
 */


//Bu alan bunny.net kullanmak isteyenler için opsiyonel olarak eklenmiştir.
//Bunny.net sistemini AMP'de kullanmak için 16 ve satırları silin. 20. satırı kendinize göre düzenleyin.
/*
function buffer_bunny($buffer){

$domain = str_replace(['http://', 'https://', 'www.'], null, get_site_url());
$bunny_domain = 'socceramp.b-cdn.net';


return preg_replace(
    '/<amp-img(.*?)src="https?:\/\/'.$domain.'/',
    '<amp-img$1src="https://'.$bunny_domain, 
    $buffer);

}

function buffer_bunny_start()
    {
        if (function_exists('buffer_bunny')) {
            ob_start('buffer_bunny');
        }
    }

    function buffer_bunny_end()
    {
        if (function_exists('buffer_bunny') && ob_start('buffer_bunny') === true) {
            ob_end_flush();
        }
    }

add_action('after_setup_theme', 'buffer_bunny_start');
add_action('shutdown', 'buffer_bunny_end');
*/


//Bu alan resimlerde cdn.ampproject.org kullanmak isteyenler için opsiyonel olarak eklenmiştir.

function buffer_bunny( $finder ) {

    if ( strpos( $finder, 'https://cdn.ampproject.org/' ) !== false && ! is_admin() ) {
		
	
        $site = str_replace(['http://', 'https://'], '', rtrim(get_site_url(), '/'));

        $imgPattern = '@<amp-img(.*?)src="https?://'.$site.'@si';
        $imgReplace = '<amp-img$1src="https://'.str_replace(['.', ' '], '-', $site).'.cdn.ampproject.org'.'/i/s/'.$site;
        $finder = preg_replace( $imgPattern, $imgReplace, $finder );


        $bgPattern = "@background-image:(.*?)url\('?https?://" . $site.'@si';
        $bgReplace = 'background-image:$1url(http$2://'.str_replace(['.', ' '], '-', $site) . '.cdn.ampproject.org/i/s/'.$site;

        $finder = preg_replace($bgPattern, $bgReplace, $finder);
	
    }
	
	$http_version = is_ssl() ? 'https://' : 'http://';
	$addon = !empty(get_option('emrenogay__amphtml')) ? get_option('emrenogay__amphtml') : str_replace(['https://', 'http://'], null, get_site_url());
	
	$finder = str_replace(
        '<link rel="amphtml" href="'.get_site_url(),
        '<link rel="amphtml" href="'.$http_version . $addon,
        $finder);
	
	$finder = str_replace(
        "<link rel='amphtml' href='".get_site_url(),
        "<link rel='amphtml' href='".$http_version . $addon,
        $finder);
	
    return $finder;
}

function buffer_bunny_start()
{
    if (function_exists('buffer_bunny')) {
        ob_start('buffer_bunny');
    }
}

function buffer_bunny_end()
{
    if (function_exists('buffer_bunny') && ob_start('buffer_bunny') === true) {
        ob_end_flush();
    }
}

add_action('after_setup_theme', 'buffer_bunny_start');
add_action('shutdown', 'buffer_bunny_end');


add_filter( 'plugin_action_links_change-amphtml-main/change-amphtml.php', function ($links_array){
    array_unshift( $links_array, '<a href="'.get_admin_url().'options-general.php?page=emrenogay_amphtml_group">Ayarlar</a>' );
    return $links_array;
} );


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
        }
    );
});
