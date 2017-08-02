<?php
/**
* Plugin Name: Secure token
* Plugin URI: http://aprwebdesign.com
* Description: Generate secure login tokens by email
* Version: 1.0
* Author: APR Webdesign
* Author URI: aprwebdesign.com
* License: GPLv2
*/

function secure_token() {
  // Get user id from url
  $id = $_GET['id'];
  //get token from url
  $token = $_GET['token'];

  // Get secure token of user id
$get_token = get_user_meta( $id, '_secure_token', true );

// If no match or token doesn't exist show Secure Token form
  if($get_token != $token || $get_token == false)
{
  // Add plugin style to head and add proper html tags
  echo'<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="'. plugins_url( 'style.css', __FILE__ ).'">
  </head><body>';

// If user exists create secure token for user else alert user does not exists
if($_POST['email']){
  $mail = $_POST['email'];
  $user = get_user_by( 'email', $mail );
  if($user){

$stoken = bin2hex(random_bytes(20));
update_user_meta( $user->id, '_secure_token', $stoken);

$turl =  wp_login_url( get_permalink() );
$turl .= '?id='.$user->id.'&token='.$stoken;

// Send the login url with token to user
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

if (!empty($_SERVER["HTTP_CLIENT_IP"]))
{
 //check for ip from share internet
 $ip = $_SERVER["HTTP_CLIENT_IP"];
}
elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
{
 // Check for the Proxy User
 $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
}
else
{
 $ip = $_SERVER["REMOTE_ADDR"];
}

$message = '<div style="max-width:400px;height:auto;padding:0px 20px 15px 20px;background:#ecf0f1;text-align:center;border:1px solid #7f8c8d;margin:10px auto;"><h1 style="font-weight:400;
  font-size:3.5em;"><span style="color:#2980b9;font-weight:600;">Secure</span>token</h1><p>
  <strong>Your secure login url:</strong></p><p><a href="'.$turl.'">Click here to login</a></p><p><small>Not working? copy and past the follow link '.$turl.'</small></p><p><small>Token requested from: '.$ip.'</small></p> </div>';

wp_mail( $user->user_email, 'Your secure login token', $message , $headers );

header("Location: " . wp_login_url( get_permalink())."?send=token");

 }

 else{
   echo "<script>alert('user does not exist');</script>";
 }


 }

// Display the form for retrieving token
    ?>

<div class="stoken-form">
  <h4>This page is secured by</h4>
    <h1><span class="blue">Secure</span>token</h1>
<?php
if($_GET['loggedout']=='true'){
echo '<span class="logged-out">You are successfully logged out, to login request another secure token</span>';
}

if($_GET['send'] == 'token'){
  echo '<span class="token-send">You secure token is send, please check your e-mail</span>';

}
?>
    <form action="" method="post">

    <input type="email" name="email" placeholder="Your e-mail adres"><br>

    <input type="submit" value="Request secure token">
  </form>
  <div class="dbapr"><small>*Designed by APR Webdesign</small></div>
</div>
</body></html>
  <?php
  // Break the wordpress login form if secure token does not match
   break;

}
else{
//Show the login form
}

}
add_action('login_head', 'secure_token');


// Delete the token after login to ensure one time use only
function delete_token() {
  $user_id = get_current_user_id();
  delete_user_meta($user_id, '_secure_token');
}
add_action( 'admin_init', 'delete_token', 1 );
