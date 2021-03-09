<?php
add_action('rest_api_init', 'wp_rest_user_endpoints');
function wp_rest_user_endpoints($request) {
    register_rest_route('wc/v3', 'users/register', array(
        'methods' => 'POST',
        'callback' => 'register_user',
    ));
}
function register_user($request = null) {
    $parameters = $request->get_json_params();
    $fullname = sanitize_text_field($parameters['fullname']);
    $firstname = strtok($fullname, ' ');
    $lastname = strstr($fullname, ' ');
    $email = sanitize_text_field($parameters['username']);
    $phone = sanitize_text_field($parameters['phone']);
    $password = sanitize_text_field($parameters['password']);
    $nice_name = strtolower($email);
    $user_data = array(
        'user_login' => $email,
        'first_name' => $firstname,
        'last_name' => $lastname,
        'user_email' => $email,
        'user_pass' => $password,
        'nicename' => $nice_name,
        'display_name' => $fullname,
        'role' => 'customer'
    );
    $user_id = wp_insert_user($user_data);
    $data = array();
    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'billing_phone', $phone);
        $data['status'] = 'success';
        $data['user_id'] = $user_id;
        $data['user_login'] = $email;
        $data['first_name'] = $firstname;
        $data['last_name'] = $lastname;
        $data['user_email'] = $email;
        $data['user_phone'] = $phone;
    } else {
        if (isset($user_id->errors['empty_user_login'])) {
            $data['status'] = 'error';
            $data['message'] = 'User Name and Email are mandatory';
        } elseif (isset($user_id->errors['existing_user_login']) ) {
            $data['status'] = 'error';
            $data['message'] = 'User name/email already registered.';
        } elseif (isset($user_id->errors['existing_user_email']) ) {
            $data['status'] = 'error';
            $data['message'] = 'This email address is already registered!';
        } else {
            $data['status'] = 'error';
            $data['message'] = 'Error Occured please fill up the sign up form carefully.';
        }
    }
    echo wp_send_json($data);
    wp_die();
}