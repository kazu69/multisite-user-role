<?php
/**
 * Plugin Name.
 *
 * @package   AddUserRoleMultisite
 * @author    kazu69
 * @license   GPL-2.0+
 * @link      https://github.com/kazu69
 */

/**
 * Plugin class.
 *
 *
 * @package AddUserRoleMultisite
 * @author  kazu69
 */
class AddUserRoleMultisite {

  /**
   * Plugin version, used for cache-busting of style and script file references.
   *
   * @since   0.0.1
   *
   * @var     string
   */
  protected $version = '0.0.5';

  /**
   * Unique identifier for your plugin.
   *
   * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
   * match the Text Domain file header in the main plugin file.
   *
   * @since    0.0.1
   *
   * @var      string
   */
  protected $plugin_slug = 'add-user-multisite';

  /**
   * Instance of this class.
   *
   * @since    0.0.1
   *
   * @var      object
   */
  protected static $instance = null;

  /**
   * Slug of the plugin screen.
   *
   * @since    0.0.1
   *
   * @var      string
   */
  protected $plugin_screen_hook_suffix = null;

  /**
   * Initialize the plugin by setting localization, filters, and administration functions.
   *
   * @since     0.0.1
   */
  private function __construct() {

    // Load plugin text domain
    add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

    // Add the options page and menu item.
    add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

    // Load admin style sheet and JavaScript.
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

    // Load public-facing style sheet and JavaScript.
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    // Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
    add_action( 'admin_init', array( $this, 'init_action_hook' ), 1 );

  }

  /**
   * Return an instance of this class.
   *
   * @since     0.0.1
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {

    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }

    return self::$instance;
  }

  /**
   * Fired when the plugin is activated.
   *
   * @since    0.0.1
   *
   * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
   */
  public static function activate( $network_wide ) {
      global $wpdb;
      global $wp_roles;
      $default_role = get_option( 'default_role' );
      $result = $wpdb->insert( $wpdb->sitemeta, array("meta_key" => "_add_multisite_exclude_role", "meta_value" => serialize( array() ), "site_id" => 1) );
  }

  /**
   * Fired when the plugin is deactivated.
   *
   * @since    0.0.1
   *
   * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
   */
  public static function deactivate( $network_wide ) {
    global $wpdb;
    //$query = "DELETE FROM $wpdb->sitemeta WHERE meta_key = '_add_multisite_role'";
    //$wpdb->query($query);

    $query = "DELETE FROM $wpdb->sitemeta WHERE meta_key = '_add_multisite_exclude_role'";
    $wpdb->query($query);
  }

  /**
   * Load the plugin text domain for translation.
   *
   * @since    0.0.1
   */
  public function load_plugin_textdomain() {

    $domain = $this->plugin_slug;
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
    load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
  }

  /**
   * Register and enqueue admin-specific style sheet.
   *
   * @since     0.0.1
   *
   * @return    null    Return early if no settings page is registered.
   */
  public function enqueue_admin_styles() {

    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
    }

  }

  /**
   * Register and enqueue admin-specific JavaScript.
   *
   * @since     0.0.1
   *
   * @return    null    Return early if no settings page is registered.
   */
  public function enqueue_admin_scripts() {

    if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
      return;
    }

    $screen = get_current_screen();
    if ( $screen->id == $this->plugin_screen_hook_suffix ) {
      wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
    }

  }

  /**
   * Register and enqueue public-facing style sheet.
   *
   * @since    0.0.1
   */
  public function enqueue_styles() {
    wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
  }

  /**
   * Register and enqueues public-facing JavaScript files.
   *
   * @since    0.0.1
   */
  public function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
  }

  /**
   * Register the administration menu for this plugin into the WordPress Dashboard menu.
   *
   * @since    0.0.1
   */
  public function add_plugin_admin_menu() {

    /*
     *
     * 'add_options_page': トップレベルメニュー「設定」用
     *
     */
    $this->plugin_screen_hook_suffix = add_options_page(
      __( 'Add User role Multisite option', $this->plugin_slug ),
      __( 'Add User role', $this->plugin_slug ),
      'activate_plugins',
      $this->plugin_slug,
      array( $this, 'display_plugin_admin_page' )
    );

  }

  /**
   * Render the settings page for this plugin.
   *
   * @since    0.0.1
   */
  public function display_plugin_admin_page() {
    include_once( 'views/admin.php' );
  }

  /**
   * NOTE:  Actions are points in the execution of a page or process
   *        lifecycle that WordPress fires.
   *
   *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
   *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
   *
   * @since    0.0.1
   */
  public function init_action_hook() {
    global $wpdb;
    global $user_ID;
    global $current_user;
    global $wp_roles;

    $wp_prefixes = $wpdb->base_prefix;

    // 設定するrole(default_role)
    $set_role = get_option( 'default_role' );

    // 除外リスト
    $query = "SELECT meta_value FROM $wpdb->sitemeta WHERE meta_key = '_add_multisite_exclude_role';";
    $exclude_roles = $wpdb->get_var($query);
    $exclude_roles = unserialize($exclude_roles);

    // blog_idを取得する
    $blog_ids = $wpdb->get_results( "SELECT blog_id FROM $wpdb->blogs" );

    $count = count($blog_ids);
    $data = array( $set_role => true );

    for( $i = 0; $i < $count; ++$i ):
      if ($i === 0):
        $meta_capability = $current_user->cap_key;
      else:
        $meta_capability = $wp_prefixes . $blog_ids[$i]->blog_id . "_capabilities";
      endif;

      $current_user_meta = get_user_meta( $user_ID );
      if ( isset($current_user_meta[$meta_capability]) ):
        $current_capability = $current_user_meta[$meta_capability];
      else:
        $current_capability = null;
      endif;

      if ( empty($current_capability) ):
        # データがない場合は追加する
        add_user_meta( $user_ID, $meta_capability, $data, true );
      else:
        # ある場合は内容をチェック
        if ( is_array($current_capability) ):
          foreach ($current_capability as $key => $capability):
            $capability = unserialize($capability);
            $role = array_keys($capability);
            $role = $role[0];

            // 除外リストのチェック
            // 除外リストの場合は更新しない
            if ( is_array( $exclude_roles )):
              if ( in_array($role, $exclude_roles) && $capability[$role] === true ) continue;
            endif;

            if ( !( $key === $set_role && $capability === true ) ) update_user_meta( $user_ID, $meta_capability, $data );
          endforeach;
        endif;
      endif;
    endfor;
  }

}