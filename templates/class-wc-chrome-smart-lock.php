<?php
/**
*
* @package     WooCommerce Google Smart Lock
* @copyright   Copyright (c) 2018, Webkul
*/

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
/**
*
*/
class WK_WC_SmartLockTemplate
{
  public $menuitems = array();
  public function __construct()
  {
    $this->init();
  }
  public function init()
  {
    $menu_name = 'primary'; //Location
    $locations = get_nav_menu_locations();

    if( !empty( $locations[ $menu_name ] ) ) {
      $menu =  wp_get_nav_menu_object($locations[ $menu_name ]) ;
      $this->menuitems = wp_get_nav_menu_items($menu->term_id, array('order' => 'DESC'));
    }
    ?>
    <h3><?php esc_html_e( 'Google Smart Lock settings', 'wc_chrome_auto_login' ) ?></h3>
    <?php settings_errors(); ?>
    <form class="smart-lock-form" id="smart_lock_form" method="post" action="options.php" >
      <?php settings_fields('wk-wc-cal-smart-lock-settings-group'); ?>
      <?php do_settings_sections('wk-wc-cal-smart-lock-settings-group'); ?>
      <table width="1000" class="smart-lock-setting">

        <tr>
          <th width="250" scope="row"><?php esc_html_e( 'API Key', 'wc_chrome_auto_login' );?></th>
          <td width="500">
            <input name="wk_wc_cal_google_api_key" type="text" id="wk_wc_cal_google_api_key" value="<?php echo get_option('wk_wc_cal_google_api_key'); ?>" />
          </td>
        </tr>
        <tr>
          <th width="250" scope="row"><?php esc_html_e( 'Client ID', 'wc_chrome_auto_login' );?></th>
          <td width="500">
            <input name="wk_wc_cal_google_client_id" type="text" id="wk_wc_cal_google_client_id" value="<?php echo get_option('wk_wc_cal_google_client_id'); ?>" />
          </td>
        </tr>

        <tr>
          <th width="250" scope="row"><?php esc_html_e( 'Menu Configuration', 'wc_chrome_auto_login' );?></th>
          <td width="500">
            <select name="wk_wc_cal_google_chrome_menu" id="wk_wc_cal_google_chrome_menu" value="<?php echo get_option('wk_wc_cal_google_chrome_menu'); ?>">
              <?php
              $saved_option=get_option('wk_wc_cal_google_chrome_menu');

              if( !empty( $this->menuitems ) ) {

                foreach ($this->menuitems as $key => $value) {
                  if ($value->ID == $saved_option) {
                    ?>
                    <option value="<?php echo esc_html( $value->ID ); ?>" selected><?php echo esc_html__( $value->title, 'wc_chrome_auto_login' ); ?></option>
                    <?php
                  } else {
                    ?>
                    <option value="<?php echo esc_html( $value->ID ); ?>"><?php echo esc_html__( $value->title, 'wc_chrome_auto_login' ); ?></option>
                    <?php
                  }
                }

              }
              ?>
            </select>
          </td>
        </tr>

        <tr>
          <th><label for="status"><?php esc_html_e( 'Enable/Disable', 'wc_chrome_auto_login' ) ?></label></th>
          <td>
            <div class="switch-field">
              <?php $enable_disable=get_option('wk_wc_cal_google_enable_disable');
              if (empty($enable_disable)) {
                $enable_disable['options']='disable';
              }
              ?>
              <input type="radio" id="switch_left" name="wk_wc_cal_google_enable_disable[options]" value="enable" <?php checked('enable' == $enable_disable['options']); ?>/>
              <label for="switch_left"><?php esc_html_e( 'Enable', 'wc_chrome_auto_login' ) ?></label>
              <input type="radio" id="switch_right" name="wk_wc_cal_google_enable_disable[options]" value="disable" <?php checked('disable' == $enable_disable['options']); ?>/>
              <label for="switch_right"><?php esc_html_e( 'Disable', 'wc_chrome_auto_login' ) ?></label>
            </div>
          </td>
        </tr>
        <tr>

        </table>
        <p>
          <?php submit_button(); ?>
        </p>
      </form>
      <?php
    }
  }
  new WK_WC_SmartLockTemplate();
