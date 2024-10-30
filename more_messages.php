<?
// Includes
include_once('../../../wp-load.php');

// Variables
$start=$_POST['substrlastMessageId'];
$number_of_posts=$_POST['number_of_posts'];
$viewCounter=$_POST['viewCounter'];
$number_of_posts_plus_one = $number_of_posts + 1;
$loopcounter = 0;
$table_name = $wpdb->prefix . "kwik_support";
$more_messages = $wpdb->get_results("SELECT * FROM ".$table_name." WHERE id < $start ORDER BY id DESC LIMIT $number_of_posts_plus_one");

get_currentuserinfo();
$current_logged_in_user = $current_user->ID;

if($more_messages){ ?>
  <div class="kwik_support_messages view<?php echo $viewCounter;?>">
    <?php
    foreach ($more_messages as $row) {
      if ($loopcounter == $number_of_posts) {
        break;
      }
      $timestamp = $row->time;
      $user_info = get_userdata($row->user_id);
      $user_login = ($user_info->user_login);
      
            // Execute only when a different question
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
			

            ?>     <div class="kwik_support_separator"><!-- ie6 fix --></div>
            <div id="messageid=<?php echo $row->id; ?>" class="kwik_support_message_container">
          
              <span class="kwik_support_message_text"><?php echo $row->text; ?></span><?php if (current_user_can('manage_options')) { ?>
              <a class="kwik_support_reply_link"><?php _e( 'reply', 'kwik_support' ); ?></a><?php } ?>
              <span class="kwik_support_message_time"><?php echo elapsed_time($timestamp); ?></span>
       <form class="reply-<?php echo $message_id; ?> reply_form clear" action="" >
        <fieldset>
          <textarea rows="2" cols="20" class="kwik_support_new_reply_text input_field" name="message"></textarea>
          <input type="submit" class="button-primary alignright kwik_support_new_reply_send" value="<?php _e( 'Submit Reply', 'kwik_support' ); ?>"/>
          
          <?php
          // Pass the hidden stuff to the JavaScript
          ?>
          <input type="hidden" class="kwik_support_new_reply_user" value="<?php echo $current_logged_in_user; ?>"/>
          <input type="hidden" class="kwik_support_new_reply_time" value="<?php echo time(); ?>"/>
          <input type="hidden" class="kwik_support_message_id" value="<?php echo $message_id; ?>"/>
          <input type="hidden" class="kwik_support_is_reply" name="is_reply" value="true"/>
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
  if(count($more_messages) != $number_of_posts_plus_one){
    echo '<input type="hidden" id="kwik_support_more" value="0">';
  }
}
?>