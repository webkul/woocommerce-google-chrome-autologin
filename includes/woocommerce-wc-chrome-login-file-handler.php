<?php
class WK_WC_ConfigHandler {

  public $client_id_val = '';

  public function __construct() {

    add_action( 'wp_enqueue_scripts', array( $this, 'wk_wc_cal_add_scripts_frontend' ) );

    add_filter( 'wk_wc_cal_script_loader_tag', array( $this, 'wk_WooSmartLock_add_defer_attribute' ), 10, 2 );

    add_filter( 'wk_wc_cal_script_loader_tag', array( $this, 'wk_WooSmartLock_add_async_attribute' ), 10, 2 );

    add_action( 'wp_ajax_nopriv_wk_woo_user_login', array( $this, 'wk_woo_user_login' ) );

    add_action( 'wp_ajax_wk_woo_user_login', array( $this, 'wk_woo_user_login' ) );

    add_action( 'wp_ajax_wk_woo_user_logged_in', array( $this, 'wk_woo_user_logged_in' ) );

    add_action( 'wp_ajax_nopriv_wk_woo_user_logged_in', array( $this, 'wk_woo_user_logged_in' ) );

    add_action( 'admin_menu', array( $this, 'wk_register_menu_page' ) );

    add_action( 'init', array( $this, 'wk_google_smart_lock_registration' ) );

    $this->client_id_val = get_option( 'wk_wc_cal_google_api_key' );

    add_action( 'admin_enqueue_scripts', array( $this, 'wk_register_scripts_admin' ) );

    add_filter( 'nav_menu_css_class', array( $this, 'wk_special_login_class' ), 10, 2 );

  }

  public function wk_special_login_class($classes, $item) {

    $menu_chrome_login = get_option('wk_wc_cal_google_chrome_menu');

    if ( isset( $menu_chrome_login ) && !empty( $menu_chrome_login ) ) {
      if ( $item->ID == $menu_chrome_login ) {
        $classes['chrome-login-class'] = 'wk-wc-chrome-login';
      } else {
        unset( $classes['chrome-login-class'] );
      }
    }
    return $classes;
  }

  public function wk_register_scripts_admin() {

    wp_register_style( 'wk_wc_cal_admin_backend_style', WK_WC_CHROME_PLUGIN.'assets/css/style.css', array(), '1.0.5' );
    wp_enqueue_style( 'wk_wc_cal_admin_backend_style' );

  }

  public function wk_wc_cal_add_scripts_frontend() {

    wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'google-location-frontend-one', '//smartlock.google.com/client' );

    // apply_filters( 'wk_wc_cal_script_loader_tag', '<script>', 'google-location-frontend' );

    wp_enqueue_script( 'wk-wc-cal-chrome-login-js', WK_WC_CHROME_PLUGIN . 'assets/js/chrome-credentials.js', array(), '5.3.6' );

    $client_id_ajax = get_option( 'wk_wc_cal_google_client_id' );

    $redirect_val = wc_get_page_permalink( 'myaccount' );

    $enable_disable = get_option( 'wk_wc_cal_google_enable_disable' );

    if ( !empty( $enable_disable ) ) {
      $val_enable = $enable_disable['options'];
    } else {
      $val_enable = '';
    }

    wp_localize_script( 'wk-wc-cal-chrome-login-js', 'wk_cal_ajax_var', array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'ajaxnonce' ), 'client_id' => $client_id_ajax, 'redirect_val' => $redirect_val, 'enable_disable' => $val_enable ) );
  }

  public function wk_woo_user_logged_in() {

    $redirect = wc_get_page_permalink( 'myaccount' );

    echo is_user_logged_in() ? $redirect : 'no';
    die;
  }

  public function wk_register_menu_page() {

    add_menu_page( esc_html__( "Chrome Smart Lock", 'wc_chrome_auto_login' ), esc_html__( "Chrome Smart", 'wc_chrome_auto_login' ) . '<br>' . esc_html__( 'Lock', 'wc_chrome_auto_login' ) , "manage_options", "lock_settings", array( $this, "wk_google_smart_lock_settings" ), "", 10 );

  }

  public function wk_google_smart_lock_settings() {
    require_once( WK_WC_CHROME_DIR_FILE . 'templates/class-wc-chrome-smart-lock.php' );
  }

  public function wk_woo_user_login() {

    $user_name = $_POST['username'];
    $user_name = sanitize_text_field( $user_name );
    $user_password = $_POST['password'];
    $user_password = sanitize_text_field( $user_password );

    if ( !empty( $user_name ) && !empty( $user_password ) ) {
      $creds = array(
        'user_login'    => trim( $user_name ),
        'user_password' => $user_password,
      );

      $redirect = wc_get_page_permalink( 'myaccount' );
      $user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );
      if ( $user ) {
        echo $redirect;
      }
      die;
    }
  }

  public function wk_WooSmartLock_add_defer_attribute( $tag, $handle ) {

    $scripts_to_defer = array( 'google-location-frontend' );
    foreach ( $scripts_to_defer as $defer_script ) {
      if ( $defer_script === $handle ) {
        return str_replace( ' src', ' defer="defer" src', $tag );
      }
    }
    return $tag;

  }

  public function wk_WooSmartLock_add_async_attribute( $tag, $handle ) {

    $scripts_to_async = array( 'google-location-frontend' );
    foreach ( $scripts_to_async as $async_script ) {
      if ( $async_script === $handle ) {
        return str_replace( ' src', ' async="async" src', $tag );
      }
    }

    return $tag;

  }

  public function wk_google_smart_lock_registration() {

    register_setting( 'wk-wc-cal-smart-lock-settings-group', 'wk_wc_cal_google_api_key' );
    register_setting( 'wk-wc-cal-smart-lock-settings-group', 'wk_wc_cal_google_client_id' );
    register_setting( 'wk-wc-cal-smart-lock-settings-group', 'wk_wc_cal_google_chrome_menu' );
    register_setting( 'wk-wc-cal-smart-lock-settings-group', 'wk_wc_cal_google_enable_disable' );

  }

}
