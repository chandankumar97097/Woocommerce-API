<?php
add_action('rest_api_init', 'wp_user_login');
function wp_user_login() {
    register_rest_route('wc/v3', 'user/login', array(
        'methods' => 'POST',
        'callback' => 'user_login',
    ));
}
function user_login($request = null) {
	$data = array();
	$data['user_login'] = $request["username"];
	$data['user_password'] =  $request["password"];
	$data['remember'] = $request["remember"];

	if ($data['user_login']) {
		if ($data['user_password']) {
			$user = wp_authenticate($data['user_login'], $data['user_password']);
			if (is_wp_error($user)) {
				$result['status'] = false;
				$result['details'] = 'Username or password is incorrect.';
				echo json_encode($result);
			} else {
				$expiration = time() + apply_filters('auth_cookie_expiration', 1209600, $user->ID, true);
				$cookie = wp_generate_auth_cookie($user->ID, $expiration, 'logged_in');
				$cookie_admin = wp_generate_auth_cookie($user->ID, $expiration, ‘secure_auth’);
				echo json_encode(array(
					"status" => true,
					"cookie" => $cookie,
					"cookie_admin" => $cookie_admin,
					"cookie_name" => LOGGED_IN_COOKIE,
					"user" => array(
						"id" => $user->ID,
						"username" => $user->user_login,
						"nicename" => $user->user_nicename,
						"email" => $user->user_email,
						"url" => $user->user_url,
						"registered" => $user->user_registered,
						"displayname" => $user->display_name,
						"firstname" => $user->user_firstname,
						"lastname" => $user->last_name,
						"nickname" => $user->nickname,
						"description" => $user->user_description,
						"capabilities" => $user->wp_capabilities,
						"avatar" => $avatar[1]
					)
				));
			}
		} else {
			$result['status'] = false;
			$result['details'] = 'Password is empty.';
			echo json_encode($result);
		}
	} else {
		$result['status'] = false;
		$result['details'] = 'Username Or Email is empty.';
		echo json_encode($result);
	}
	die();
}