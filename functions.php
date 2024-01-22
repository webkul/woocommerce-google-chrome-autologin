<?php
/**
* Plugin Name: WooCommerce Chrome Login
* Plugin URI: https://webkul.com
* Description: Wordpress WooCommerce Chrome Login Plugin customers to remember their login credentials and allow auto login.
* Version: 1.0.0
* Author: Webkul
* Text Domain: wc_chrome_auto_login
* Author URI: https://webkul.com
**/

/*----------*/ /*---------->>> Exit if Accessed Directly <<<----------*/ /*----------*/

if (!defined('ABSPATH')) {
  exit;
}

! defined('WK_WC_CHROME_PLUGIN') && define('WK_WC_CHROME_PLUGIN', plugin_dir_url(__FILE__));
! defined('WK_WC_CHROME_DIR_FILE') && define('WK_WC_CHROME_DIR_FILE', plugin_dir_path(__FILE__));

if( !function_exists( 'wk_cal_checkWoocommerceIsInstalled' ) ) {

  function wk_cal_checkWoocommerceIsInstalled()
  {
    ob_start();
    if ( ! class_exists( 'WooCommerce' ) ) {
      do_action('admin_notices_woocommerce');
    }
    else {
      require_once( WK_WC_CHROME_DIR_FILE . 'includes/woocommerce-wc-chrome-login-file-handler.php' );
      new WK_WC_ConfigHandler();
    }
  }

  add_action( 'plugins_loaded', 'wk_cal_checkWoocommerceIsInstalled' );

}
add_action( 'admin_notices_woocommerce', 'wk_wc_cal_mtsnWoocommerceMissingNotice' );
function wk_wc_cal_mtsnWoocommerceMissingNotice()
{
  echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Chrome Login depends on the last version of %s or later to work!' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . esc_html__( 'WooCommerce', 'wc_chrome_auto_login' ) . '</a>' ) . '</p></div>';
}
