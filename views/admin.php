<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   AddUserRoleMultisite
 * @author    kazu69
 * @license   GPL-2.0+
 * @link      ttps://github.com/kazu69
 */
?>
<div class="wrap">

<?php screen_icon(); ?>
  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

<?php
    global $wpdb;
    global $wp_roles;
    if ( isset($_POST["user_role"]) || isset( $_POST["exclude"]) ):

      /*
      * apply role
      */
      if( isset($_POST["user_role"] ) ):
        $result = update_option( 'default_role', $_POST["user_role"] );

      endif;

      /*
      * exclude role
      */
      if( isset( $_POST["exclude"] ) ):
        $query = "SELECT meta_id FROM $wpdb->sitemeta WHERE meta_key = '_add_multisite_exclude_role';";
        $meta_id = $wpdb->get_var($query);

        if($meta_id):
          if(empty($_POST["exclude"])):
            $serialize_data = "";
          else:
            $serialize_data = serialize( array_map( 'trim', array_map('strtolower', $_POST["exclude"]) ) );
          endif;
          $result = $wpdb->update( $wpdb->sitemeta, array("meta_key" => "_add_multisite_exclude_role", "meta_value" => $serialize_data), array('meta_id' => $meta_id) );
        endif;

      endif;

      if ($result):
        $done_message = __("Setting Done");
        echo "<div class='end-comment'>" . $done_message ."</div>";
      else:
        $error_message = __("Not update because there is no change, or an error occurred");
        echo "<div class='end-comment error'>" . $error_message . "</div>";
      endif;

      $message = __("Back");
      echo "<p><a href='" . $_SERVER['REQUEST_URI'] . "'>" . $message . "</a></p>";
        exit;

    else:
      $default_role = get_option( 'default_role' );
      $exclude_roles = unserialize( $wpdb->get_var("SELECT meta_value FROM $wpdb->sitemeta WHERE meta_key = '_add_multisite_exclude_role';") );
?>
      <div id="wp-add-role-multisite">
        <h3><?php echo "Setting role of the logged-in user"; ?></h3>
        <dl id="wp-add-role-multisite-settings">
          <dt><?php echo _e("Role it is applied to the current login (default_role)"); ?></dt>
          <dd><strong style="font-size: 1.2em;"><?php echo $default_role; ?></strong></dd>

          <dt><?php echo _e( "Set the Role to be applied on all networks" ); ?></dt>
          <dd>
              <form name="form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                <select name="user_role">
                <?php foreach ($wp_roles->get_names() as $key => $value):?>
                  <option value="<?php echo $value; ?>" <?php if( $value === $default_role ) echo "selected"; ?> ><?php echo $value; ?></option>
                <?php endforeach; ?>
                </select>
                <input type="submit" name="Submit" value="<?php echo __( "Change" ); ?>">
              </form>
          </dd>

          <dt><?php echo _e( "Not set up when the next Role (Exclusion list)" ); ?></dt>
          <dd>
            <form name="form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
              <input type="hidden" value="0" name="exclude">
              <ul class="exclude-list">
              <?php foreach ($wp_roles->get_names() as $key => $value):?>
                <li class="exclude-item">
                  <?php if(empty($exclude_roles)): ?>
                  <input type="checkbox" name="exclude[]" id="<?php echo $value; ?>" value="<?php echo strtolower($value); ?>" >
                  <label for="<?php echo $value; ?>"><?php echo strtolower($value); ?></label>
                  <?php else: ?>
                  <input type="checkbox" name="exclude[]" id="<?php echo $value; ?>" value="<?php echo strtolower($value); ?>" <?php if(in_array(strtolower($value), $exclude_roles)) echo "checked"; ?> >
                  <label for="<?php echo $value; ?>"><?php echo strtolower($value); ?></label>
                  <?php endif; ?>
                </li>
              <?php endforeach; ?>
              </ul>
              <input type="submit" name="Submit" value="<?php _e( "Change" ); ?>">
            </form>
          </dd>
        </dl>
      </div>

<?php endif; ?>

</div>
