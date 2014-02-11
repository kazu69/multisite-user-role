<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that also follow
 * WordPress coding standards and PHP best practices.
 *
 * @package   AddUserRoleMultisite
 * @author    kazu69
 * @license   GPL-2.0+
 * @link      https://github.com/kazu69
 *
 * @wordpress-plugin
 * Plugin Name: Add-User-Role-Multisite
 * Plugin URI:  https://github.com/kazu69
 * Description: add user role all network
 * Version:     0.0.5
 * Author:      kazu69
 * Author URI:  https://github.com/kazu69
 * Text Domain: plugin-name-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'class-add-role-multisite.php' );


// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'AddUserRoleMultisite', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'AddUserRoleMultisite', 'deactivate' ) );

AddUserRoleMultisite::get_instance();

if (is_admin()) {
  include_once('updater.php');
  $config = array(
    'slug' => plugin_basename(__FILE__),
    'proper_folder_name' => 'Add-User-Role-Multisite',
    'api_url' => 'https://api.github.com/repos/raccoon2013/Add-User-Role-Multisite',
    'raw_url' => 'https://raw.github.com/raccoon2013/Add-User-Role-Multisite/master',
    'github_url' => 'https://github.com/raccoon2013/Add-User-Role-Multisite',
    'zip_url' => 'https://github.com/raccoon2013/Add-User-Role-Multisite/archive/master.zip',
    'sslverify' => true,
    'requires' => '3.0',
    'tested' => '3.3',
    'readme' => 'README.md',
    'access_token' => 'efc70d5ade2bdcaf01044864b1e633fcf850eb83',
  );
  //$github_updater = new wp_github_updater( $config );
}