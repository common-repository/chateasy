<?php

// add a global variable to store the plugin table 
global $chateasy_plugin_table_name;
$chateasy_plugin_table_name = 'chateasy';

function chateasy_create_option_for_request($data = array())
{
    $api_username = get_option('chateasy_api_username');
    $api_secure_code = get_option('chateasy_api_secure_code');

    $options = array(
        'method' => 'POST',
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($api_username . ':' . $api_secure_code)
        ),
        'body' => json_encode($data)
    );

    return $options;
}

// function chateasy_send_message($phone_number, $message)
// {
//     if (empty($api_username) || empty($api_secure_code)) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'API Credentials are invalid.'
//         ));
//     }

//     if (!$sending_message_enabled) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'Sending message is disabled.'
//         ));
//     }

//     if (empty($message)) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'Message is empty.'
//         ));
//     }

//     $phone_number = str_replace('+', '', $phone_number);
//     $phone_number = str_replace(' ', '', $phone_number);
//     $phone_number = str_replace('-', '', $phone_number);

//     if (strlen($phone_number) === 12 && substr($phone_number, 0, 2) === $country_code) {
//         $phone_number = substr($phone_number, 2);
//     }

//     if (strlen($phone_number) !== 10) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'Phone number is invalid. It should be 10 digits.'
//         ));
//     }

//     if (strlen($country_code) !== 2) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'Country code is invalid. It should be 2 digits.'
//         ));
//     }

//     if (empty($api_endpoint)) {
//         return json_encode(array(
//             'status' => 'error',
//             'message' => 'API Endpoint is empty.'
//         ));
//     }

//     $url = $api_endpoint . "/message/sendText";
//     $data = array(
//         'to' => $phone_number,
//         'text' => $message,
//         'countryCode' => $country_code,
//         'priority' => 'high',
//     );
//     $options = chateasy_create_option_for_request($data);
//     $response = wp_remote_request($url, $options);
//     $response_code = wp_remote_retrieve_response_code($response);
//     $response_message = wp_remote_retrieve_response_message($response);
//     $response_body = wp_remote_retrieve_body($response);
//     error_log('ChatEasy API Response Body: ' . $response_body);

//     return $response_body;
// }

function chateasy_check_status()
{
    $api_endpoint = get_option('chateasy_api_endpoint', 'https://api.chateasy.in');
    $country_code = get_option('chateasy_admin_country_code', '91');
    $url = $api_endpoint . "/instance/status";

    $options = chateasy_create_option_for_request();
    $response = wp_remote_request($url, $options);
    // $response_code = wp_remote_retrieve_response_code( $response );
    // $response_message = wp_remote_retrieve_response_message( $response );
    $response_body = wp_remote_retrieve_body($response);

    return $response_body;
}

// ** The below code was only added for debugging on local. 
// ** It is not required for the plugin to work.

function debug_to_console($data, $text = null)
{
    $api = new ChateasyAPI();

    $data = array(
        $data, 'action' => $text
    );

    $api->post($data, 'debug_to_console');
}
