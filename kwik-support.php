<?php
/*
Plugin Name: Kwik Support
Plugin URI: http://kevin-chappell.com/kwik-support
Description: Kwik Support is a widget for the admin dashboard that lets you communicate directly with customers. They ask a question, you answer it via email or the dashboard.
Version: .9.4
Author: Kevin Chappell
Author URI: http://kevin-chappell.com
*/

/*
  TODO
	Add Branding
	Ability to delete entries
*/

// Include Settings
include("kwik-support-settings.php");


// Add Kwik Support Javascript
function add_kwik_support_style_n_scripts($hook) {
    // Check screen hook and current post type
    if ( 'index.php' == $hook ){
		wp_enqueue_script( 'kwik-support_admin_script', plugins_url('/kwik-support.js', __FILE__));
		wp_register_style( 'kwik-support_admin_css', plugins_url('/kwik-support.css', __FILE__)  );
		wp_register_style( 'kwik-support_admin_css-ie6', plugins_url('/kwik-support_ie6.css', __FILE__));
		wp_register_style( 'kwik-support_admin_css-ie7', plugins_url('/kwik-support_ie7.css', __FILE__));
		wp_enqueue_style( 'kwik-support_admin_css' );
		wp_enqueue_style( 'kwik-support_admin_css-ie6' );
		wp_enqueue_style( 'kwik-support_admin_css-ie7' );
		wp_enqueue_style('thickbox');
    }
}
add_action( 'admin_enqueue_scripts', 'add_kwik_support_style_n_scripts' ,10,1);


add_action("admin_head","kwik_support_load_tiny_mce");
function kwik_support_load_tiny_mce() {
wp_tiny_mce( true ); // true gives you a stripped down version of the editor
}




// Elapsed time
function elapsed_time($start){
  $end = time(); //we assume you're comparing to now.
  $diff = $end - $start;
  $days = floor ( $diff/86400 ); //calculate the days
  $diff = $diff - ($days*86400); // subtract the days

  $hours = floor ( $diff/3600 ); // calculate the hours
  $diff = $diff - ($hours*3600); // subtract the hours

  $mins = floor ( $diff/60 ); // calculate the minutes
  $diff = $diff - ($mins*60); // subtract the mins

  $secs = $diff; // what's left is the seconds;
  if ($secs > 0) { $returnval = "$secs second".(($secs>1) ? "s":"")." ago";}
  if ($mins > 0) { $returnval = "$mins minute".(($mins>1) ? "s":"")." ago";}
  if ($hours > 0) { $returnval = "$hours hour".(($hours>1) ? "s":"")." ago";}
  if ($days > 0) { $returnval = "$days day".(($days>1) ? "s":"")." ago";}
  $return = $label;
  return $return . $returnval;
}

function my_wp_dashboard_setup(){
  wp_add_dashboard_widget( 'kwik_support', __( 'Kwik Support' ), 'kwik_support' );
}

add_action('wp_dashboard_setup', 'my_wp_dashboard_setup');

/* Setup Database */
function kwik_support_install () {
  global $wpdb;

  $table_name = $wpdb->prefix . "kwik_support";
  if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

    $sql = "CREATE TABLE " . $table_name . " (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
	  message_ID bigint(20) unsigned NOT NULL default '0',
      text text NOT NULL,
      time bigint(11) DEFAULT '0' NOT NULL,
	  comment_date datetime NOT NULL default '0000-00-00 00:00:00',
      user_id tinytext NOT NULL,
	  user_email varchar(100) NOT NULL default '',
	  is_reply tinyint(1) NOT NULL default '0',
      PRIMARY KEY  (id),
	  KEY message_ID (message_ID)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $welcome_user_id = "1";
    $welcome_text = "Congratulations, you just completed the installation!";
    $now = date('Y-m-d H:i:s');

    get_currentuserinfo();

	$wpdb->insert( $table_name, array(
								'id' => 1,
								'message_ID' => 1,
								'text' => $welcome_text,
								'time' => time(),
								'comment_date' => $now,
								'user_id' => '1',
								'user_email' => $user_email,
								'is_reply' => '0'
								));



  }
}

register_activation_hook(__FILE__,'kwik_support_install');

// Output
function kwik_support(){
global $wpdb;
  $table_name = $wpdb->prefix . "kwik_support";
  $settings = get_option('kwik_support_settings');

  // Default settings
  if (!is_array($settings)){
    $settings = array(
      'number_of_messages' => '5',
      'view_newer_messages' => 'View newer messages',
      'view_older_messages' => 'View older messages',
      'send_message' => 'Send message',
	  'reply_link' => 'reply',
      'loading' => 'Loading...',
    );
  }

  // Settings
  $number_of_posts = $settings['number_of_messages'];
  $number_of_posts_plus_one = $number_of_posts + 1;
  $loopcounter = 0;

  // Query
  $messages = $wpdb->get_results("
  SELECT *
  FROM  ".$table_name."
  WHERE is_reply=0
  ORDER BY id
  DESC
  LIMIT $number_of_posts_plus_one");

foreach ($messages as $row) {
            if ($loopcounter == 1) {
              break;
            }
$message_id = $row->message_ID;
$loopcounter = 1;
}


$new_message_id = $message_id + 1;





  global $current_user;
  get_currentuserinfo();
  $current_logged_in_user = $current_user->ID;

  ?>

  <div id="kwik_support">
    <div id="kwik_support_new_message">
      <form action="">
        <fieldset>
         <?php the_editor('', 'kwik_support_new_message_text'); ?>

          <input type="submit" id="kwik_support_new_message_send" class="button-primary alignright" value="<?php echo $settings['send_message']; ?>"/>

          <?php
          // Pass the hidden stuff to the JavaScript
          ?>
          <input type="hidden" id="kwik_support_new_message_user" value="<?php echo $current_logged_in_user; ?>"/>
          <input type="hidden" id="kwik_support_new_message_time" value="<?php echo time(); ?>"/>
          <input type="hidden" id="kwik_support_message_id" value="<?php echo $new_message_id; ?>"/>
          <input type="hidden" id="kwik_support_number_of_posts" value="<?php echo $number_of_posts; ?>"/>
          <input type="hidden" id="kwik_support_comment_date" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
          <input type="hidden" class="kwik_support_is_reply" name="is_reply" value="0"/>
          <input type="hidden" id="kwik_support_setting_send_message" value="<?php echo $settings['send_message']; ?>"/>
          <input type="hidden" id="kwik_support_setting_view_older_messages" value="<?php echo $settings['view_older_messages']; ?>"/>
          <input type="hidden" id="kwik_support_setting_loading" value="<?php echo $settings['loading']; ?>"/>
        </fieldset>
      </form>
    </div>
    <div id="kwik_support_load_newer_messages">
      <form action="">
        <fieldset>
          <input type="button" id="kwik_support_load_newer_messages_button" class="button" value="<?php echo $settings['view_newer_messages']; ?>"/>
        </fieldset>
      </form>
    </div>
    <div id="kwik_support_messages_viewport">
      <div id="kwik_support_messages_wrapper">
        <div class="kwik_support_messages view0">
          <?php
          foreach ($messages as $row) {
            if ($loopcounter == $number_of_posts) {
              break;
            }
            $timestamp = $row->time;
            $user_info = get_userdata($row->user_id);
            $user_login = ($user_info->user_login);

            // Execute only when a new user post
            if ($row->message_ID != $message_id){
              if($loopcounter != 0){
                echo '<div class="kwik_support_round_bottom"><!-- ie6 fix --></div>';
              }
              ?>
              <div class="kwik_support_message <?php echo "question-".$row->message_ID; ?>">
                <div class="kwik_support_user <?php if ($current_logged_in_user === $row->user_id){echo 'current_user';} ?>">
                  <div class="kwik_support_user_login">
                    <?php echo $user_login;?> writes:
                  </div>
                </div>
              </div>
            <?php
            } else if ($loopcounter == 1){ ?>
                <div class="kwik_support_message <?php echo "question-".$row->message_ID; ?>">
                <div class="kwik_support_user <?php if ($current_logged_in_user === $row->user_id){echo 'current_user';} ?>">
                  <div class="kwik_support_user_login">
                    <?php echo $user_login;?> writes:
                  </div>
                </div>
              </div>
            <?php }


			$message_id = $row->message_ID;


            ?>

            <div class="kwik_support_separator"><!-- ie6 fix --></div>
            <div id="messageid=<?php echo $row->id; ?>" class="kwik_support_message_container">

              <span class="kwik_support_message_text"><?php echo $row->text; ?></span>

			  <?php if (current_user_can('manage_options')) { ?>
              <a class="kwik_support_reply_link"><?php echo $settings['reply_link']; ?></a><?php } ?>
              <span class="kwik_support_message_time"><?php echo elapsed_time($timestamp); ?></span>
       <form class="reply-<?php echo $message_id; ?> reply_form clear" action="" >
        <fieldset>
          <textarea class="kwik_support_new_reply_text" name="message"></textarea>
          <input type="submit" class="button-primary alignright kwik_support_new_reply_send" value="<?php _e( 'Submit Reply', 'kwik_support' ); ?>"/>

          <?php
          // Pass the hidden stuff to the JavaScript
		  $user_reply_email = ($user_info->user_email);
          ?>
          <input type="hidden" class="kwik_support_new_reply_user" value="<?php echo $current_logged_in_user; ?>"/>
          <input type="hidden" class="kwik_support_new_reply_time" value="<?php echo time(); ?>"/>
          <input type="hidden" class="kwik_support_message_id" value="<?php echo $message_id; ?>"/>
          <input type="hidden" class="kwik_support_comment_date" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
          <input type="hidden" class="kwik_support_user_reply_email" name="user_reply_email" value="<?php echo $user_reply_email; ?>"/>
          <input type="hidden" class="kwik_support_is_reply" name="is_reply" value="1"/>
          <input type="hidden" class="kwik_support_number_of_posts" value="<?php echo $number_of_posts; ?>"/>
        </fieldset>
      </form>
            </div>
            <?php


			  // Query
  $replies = $wpdb->get_results("
  SELECT *
  FROM  ".$table_name."
  WHERE is_reply=1
  AND message_ID=".$message_id."
  ORDER BY id
  DESC
  LIMIT $number_of_posts_plus_one");

		 foreach ($replies as $reply) { ?>
            <div class="kwik_support_separator"><!-- ie6 fix --></div>
            <div id="messageid=<?php echo $reply->id; ?>" class="kwik_support_message_container reply_message">

              <span class="kwik_support_message_text<?php if ($reply->message_ID == $message_id){?> reply_message<?php } ?>"><?php echo $reply->text; ?></span>
              <span class="kwik_support_message_time"><?php echo elapsed_time($timestamp); ?></span>
            </div>

			<?php
			 }
            $last_post_id = $row->user_id;
			$support_comment_id = $row->id;
            $loopcounter++;
          }
          ?>
          <div class="kwik_support_round_bottom"><!-- ie6 fix --></div>
        </div>
        <?php
          if(count($messages) != $number_of_posts_plus_one){
            echo '<input type="hidden" id="kwik_support_more" value="0">';
          }
        ?>
      </div>
    </div>
    <div id="kwik_support_load_older_messages">
      <form action="">
        <fieldset>
          <input type="button" id="kwik_support_load_older_messages_button" class="button" value="<?php echo $settings['view_older_messages']; ?>"/>
        </fieldset>
      </form>
    </div>
  </div>
  <?php
}
?>