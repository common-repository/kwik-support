<?php
add_action('admin_init', 'kwik_support_settings_init' );
add_action('admin_menu', 'kwik_support_settings_add_page');

// Init plugin options to white list our options
function kwik_support_settings_init(){
  register_setting('kwik_support_settings_settings', 'kwik_support_settings', 'kwik_support_settings_validate');
}

// Add menu page
function kwik_support_settings_add_page() {
  add_options_page('Kwik Support Settings', 'Kwik Support', 'manage_options', 'kwik_support-settings', 'kwik_support_settings_page');
}



// Output
function kwik_support_settings_page() {
  ?>
  <div class="wrap">
    <div id="icon-options-general" class="icon32"><br/></div>
    <h2>Kwik Support Settings</h2>
    <h3>General</h3>
    <form method="post" action="options.php">
      <?php
	  $admin_email = get_option('admin_email');
      settings_fields('kwik_support_settings_settings');
      $settings = get_option('kwik_support_settings');
      // Default settings
      if (!is_array($settings)){
        $settings = array(
          'number_of_messages' => '5',
          'view_newer_messages' => __('View newer messages', 'kwik_support'),
          'view_older_messages' => __('View older messages', 'kwik_support'),
          'send_message' => __('Send Message', 'kwik_support'),
		  'reply_link' => __('reply', 'kwik_support'),
		  'support_address' => __($admin_email, 'kwik_support'),
          'loading' => __('Loading...', 'kwik_support'),
        );
      }
      ?>
      <table class="form-table">
        <tr valign="top"><th scope="row">Number of messages displayed</th>
          <td><input type="text" name="kwik_support_settings[number_of_messages]" value="<?php echo $settings['number_of_messages']; ?>" /></td>
        </tr>
      </table>
      <h3>Text on buttons</h3>
      <table class="form-table">
        <tr valign="top"><th scope="row">View newer messages</th>
          <td><input type="text" name="kwik_support_settings[view_newer_messages]" value="<?php echo $settings['view_newer_messages']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">View older messages</th>
          <td><input type="text" name="kwik_support_settings[view_older_messages]" value="<?php echo $settings['view_older_messages']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Send message</th>
          <td><input type="text" name="kwik_support_settings[send_message]" value="<?php echo $settings['send_message']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Reply Link</th>
          <td><input type="text" name="kwik_support_settings[reply_link]" value="<?php echo $settings['reply_link']; ?>" /></td>
        </tr>

        <tr valign="top"><th scope="row">Loading message</th>
          <td><input type="text" name="kwik_support_settings[loading]" value="<?php echo $settings['loading']; ?>" /></td>
        </tr>
      </table>
      
      
      
      
      
      
      
      <h3>Notifications</h3>
            <table class="form-table">
                <tr valign="top"><th scope="row">Send email</th>
          <td><input  name="kwik_support_settings[send_email]" type="checkbox" style="margin-left:30px;" id="send_email" <?php if(isset($settings['send_email'])) {?> value="true" checked="true"<?php } ?>/>
          </td>
        </tr>
         <tr valign="top"><th scope="row"></th>
          <td>Support provider's email address: <input type="text" name="kwik_support_settings[support_address]" value="<?php echo $settings['support_address']; ?>" /></td>
        </tr>
      </table>
      
      
      
      
      
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
    </form>
  </div>
  <?php	
}










// Sanitize and validate input. Accepts an array, return a sanitized array.
function kwik_support_settings_validate($input) {
  
  // Our option must be safe text with no HTML tags
  $input['number_of_messages'] =  wp_filter_nohtml_kses($input['number_of_messages']);
  
  return $input;
}
?>