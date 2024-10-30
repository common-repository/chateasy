<?php

function chateasy_create_db()
{
    $chateasy_db = new Chateasy_DB();
    $settings = $chateasy_db->create_db();

    return $settings;
}

function chateasy_create_db_api_handler()
{
    register_rest_route('chateasy/v1', '/createDb', array(
        'methods' =>
        WP_REST_Server::READABLE,
        'callback' => 'chateasy_create_db',
    ));
}

function chateasy_get_logs()
{
    $chateasy_db = new Chateasy_DB();
    $logs = $chateasy_db->get_logs(
        100,
        null
    );

    return $logs;
}

function chateasy_get_logs_api_handler()
{
    register_rest_route('chateasy/v1', '/getLogs', array(
        'methods' =>
        WP_REST_Server::READABLE,
        'callback' => 'chateasy_get_logs',
    ));
}


function chateasy_create_log($request)
{
    $chateasy_db = new Chateasy_DB();

    $response = array(
        'body' => $request['body'],
        'headers' => $request['headers'],
        'response' => array(
            'code' => 200,
            'message' => 'OK',
        ),
        'request' => array(
            'method' => 'POST',
        ),
    );

    $log = $chateasy_db->save_log(
        $response,
        $request['headers']
    );

    return $log;
}

function chateasy_create_log_api_handler()
{
    register_rest_route('chateasy/v1', '/createLog', array(
        'methods' =>
        WP_REST_Server::READABLE,
        'callback' => 'chateasy_create_log',
    ));
}

//add an api route to receive the status of the settings
add_action('rest_api_init', 'chateasy_create_db_api_handler');

//add a log to db via api
add_action('rest_api_init', 'chateasy_get_logs_api_handler');

//add a api to create a log
add_action('rest_api_init', 'chateasy_create_log_api_handler');
