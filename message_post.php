<?
// Includes
include_once('../../../wp-load.php');
// Get values from js and kwik-support.php
$message_id = Trim(stripslashes($_POST['message_ID']));
if ($message_id): echo $message_id; endif;
$comment_date = $_POST['comment_date'];
$is_reply = $_POST['is_reply'];
$message = stripslashes($_POST['message']);

$user_reply_email = stripslashes($_POST['user_reply_email']);

$curpage = $_POST['return_link'];
$current_user = Trim(stripslashes($_POST['current_user']));
$time = Trim(stripslashes($_POST['time']));
$table_name = $wpdb->prefix . "kwik_support";
global $user_login , $user_email, $blogname ;
get_currentuserinfo();
$blogname = get_bloginfo('blogname');
$siteurl = get_bloginfo('url');



$settings = get_option('kwik_support_settings');

// Insert query
$wpdb->insert( $table_name, array( 'message_ID' => $message_id, 'user_id' => $current_user, 'text' => $message, 'time' => $time, 'comment_date' => $comment_date, 'user_email' => $user_email, 'is_reply' => $is_reply), array( '%s', '%s' ) );
// Send an email notification to the support provider
if ($settings['send_email']) {	

if ($is_reply == 1){
	
			$headers = "From: " . $user_login . "<" . $user_email . ">\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$body = "<html><body style='font-size:12px; font-family:Arial,Helvetica,sans-serif; color:#444444;'>";
			$body .= "<h4 style='color:#444444;font-size:16px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif;'>New message posted to the kwik_support dashboard widget at <span style='color:#ff0000;'>$blogname</span></h4>";
			$body .= "<p style='color:#444444;'> <a href='mailto:$user_email?subject=Site%20support%20request'>$user_login</a> replies:<br/>";
			$body .= "$message </p><hr/>";
			$body .= "<p style='color:#444444;'> Respond to this request here:";
			$body .= "<br/><a href='$siteurl/wp-admin/index.php' title='$blogname Dashboard'>$blogname Dashboard</a></p>";
			$body .= "</body></html>";
			$success = mail ($user_email,  "Re: $blogname Website Question", $body, $headers);
			die();
	
	} else{
		
			$headers = "From: " . $user_login . "<" . $user_email . ">\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$body = "<html><body style='font-size:12px; font-family:Arial,Helvetica,sans-serif; color:#444444;'>";
			$body .= "<img src='http://kcdesignlab.com/wp-content/themes/display/files/website_support_request.gif' alt='Website support request' /><hr/>";
			$body .= "<h4 style='color:#444444;font-size:16px; font-family:'Trebuchet MS',Arial,Helvetica,sans-serif;'>New Message posted to the kwik_support dashboard widget at <span style='color:#ff0000;'>$blogname</span></h4>";
			$body .= "<p style='color:#444444;'> <a href='mailto:$user_email?subject=Site%20support%20request'>$user_login</a> writes:<br/>";
			$body .= "$message </p><hr/>";
			$body .= "<p style='color:#444444;'> Respond to this request here:";
			$body .= "<br/><a href='$siteurl/wp-admin/index.php' title='$blogname Dashboard'>$blogname Dashboard</a></p>";
			$body .= "</body></html>";
			$success = mail ($settings['support_address'],  "$blogname Website Question", $body, $headers);
			die();
		
		}



			
			
			
 }


?>