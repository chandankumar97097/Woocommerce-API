<?php
add_action('rest_api_init', 'wp_user_password_change');
function wp_user_password_change() {
	register_rest_route('wp/v2', 'user/change-password', array(
		'methods' => 'POST',
		'callback' => 'change_password',
	));
}
function change_password($request = null){
	$parameters = $request->get_json_params();
	$user_id = $parameters['user_id'];
	$user = get_user_by( 'id', $user_id );
	$password = $parameters['password'];
	$new_password = $parameters['new_password'];

	if(empty($user_id)){
		$json = array('code'=>'0','msg'=>'Please enter user id');
		echo json_encode($json);
		exit;
	}
	if(empty($password)){
		$json = array('code'=>'0','msg'=>'Please enter old password');
		echo json_encode($json);
		exit;
	}
	if(empty($new_password)){
		$json = array('code'=>'0','msg'=>'Please enter new password');
		echo json_encode($json);
		exit;
	}
	$hash = $user->data->user_pass;
	$code = 500; $status = false;
	if (wp_check_password( $password, $hash ) ){
		$msg = 'Password updated successfully';
		$code = 200; $status = true;
		wp_set_password($new_password , $user_id);
	}else{
		$msg = 'Current password does not match.';
	}
	
	$json = array('code'=>$code,'status'=>$status,'msg'=>$msg);
	echo json_encode($json);
	exit;
}