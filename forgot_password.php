<?php
add_action('rest_api_init', 'wp_user_forgot_password');
function wp_user_forgot_password() {
	register_rest_route('wp/v3', 'user/forgot-password', array(
		'methods' => 'POST',
		'callback' => 'forgot_password',
	));
}
function forgot_password($request = null){
	$parameters = $request->get_json_params();
	$login = $parameters['login'];

	if ( empty( $login ) ) {
		$json = array( 'code' => '0', 'msg' => 'Please enter login user detail' );
		echo json_encode( $json );
		exit;
	}

	$userdata = get_user_by( 'email', $login);

	if ( empty( $userdata ) ) {
		$userdata = get_user_by( 'login', $login );
	}

	if ( empty( $userdata ) ) {
		$json = array( 'code' => '101', 'msg' => 'User not found' );
		echo json_encode( $json );
		exit;
	}

	$user = new WP_User( intval( $userdata->ID ) );
	$reset_key = get_password_reset_key( $user );
	$wc_emails = WC()->mailer()->get_emails();
	$wc_emails['WC_Email_Customer_Reset_Password']->trigger( $user->user_login, $reset_key );

	$json = array( 'code' => '200', 'msg' => 'Password reset link has been sent to your registered email' );
	echo json_encode( $json );
	exit;
}