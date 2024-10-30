<?php

// this is a php class to create a request options to the chateasy api

class ChateasyAPI
{
    private $api_username;
    private $api_secure_code;
    private $api_endpoint;
    private $sending_message_enabled;
    private $admin_country_code;
    private $admin_mobile_number;
    public $options;

    private function create_option_for_request($api_username, $api_secure_code)
    {
        return array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($api_username . ':' . $api_secure_code),
                'User-Agent' => 'Chateasy WordPress Plugin'
            )
        );
    }

    public function __construct()
    {
        $this->api_username = get_option('chateasy_api_username');
        $this->api_secure_code = get_option('chateasy_api_secure_code');
        $this->api_endpoint = get_option('chateasy_api_endpoint');
        $this->sending_message_enabled = get_option('chateasy_sending_message_enabled') == "1" ? true : false;
        $this->admin_country_code = get_option('chateasy_admin_country_code');
        $this->admin_mobile_number = get_option('chateasy_admin_mobile_number');

        $this->options = $this->create_option_for_request(
            $this->api_username,
            $this->api_secure_code
        );
    }

    public function post($data, $endpoint = '/woo/post')
    {
        $options = $this->options;
        $options['method'] = 'POST';

        $options['body'] = json_encode($data);

        $endpoint = str_starts_with($endpoint, '/') ? $endpoint : ('/' . $endpoint);
        $endpoint = $this->api_endpoint . $endpoint;

        $response = wp_remote_post($endpoint, $options);

        $db = new Chateasy_DB();
        $db->save_log($response, $data);

        return $response;
    }

    public function get($endpoint = '/woo/get')
    {
        $options = $this->options;
        $options['method'] = 'GET';

        $endpoint = str_starts_with($endpoint, '/') ? $endpoint : ('/' . $endpoint);
        $endpoint = $this->api_endpoint . $endpoint;

        $response = wp_remote_get($endpoint, $options);
        return $response;
    }

    public function test()
    {
        $data = array();
        $response = $this->post($data, '/woo/test');
        return $response;
    }

    public function activate()
    {
        //path is 2 level up from this file
        $pluginPath = dirname(dirname(__FILE__, 2))
            . DIRECTORY_SEPARATOR . 'chateasy.php';

        $custom_logo_id = get_theme_mod('custom_logo');
        $image = wp_get_attachment_image_src($custom_logo_id, 'full') ? wp_get_attachment_image_src($custom_logo_id, 'full')[0] : null;

        $current_user = array(
            'id' => get_current_user_id(),
            'user_login' => wp_get_current_user()->user_login,
            'user_email' => wp_get_current_user()->user_email,

            'user_registered' => wp_get_current_user()->user_registered,
            'user_avatar' => get_avatar_url(get_current_user_id()),

            'first_name' => wp_get_current_user()->first_name,
            'last_name' => wp_get_current_user()->last_name,
            'display_name' => wp_get_current_user()->display_name,

            'is_admin' => current_user_can('administrator') ? true : false,
            'is_editor' => current_user_can('editor') ? true : false,
            'is_author' => current_user_can('author') ? true : false,
            'is_contributor' => current_user_can('contributor') ? true : false,
            'is_subscriber' => current_user_can('subscriber') ? true : false,

            'roles' => wp_get_current_user()->roles,
        );

        $data = array(
            'wordpress' => array(
                'version' => get_bloginfo('version'),
                'url' => get_bloginfo('url'),
                'blogdescription' => get_option('blogdescription'),
                'name' => get_bloginfo('name'),
                'language' => get_bloginfo('language'),
                'charset' => get_bloginfo('charset'),
                'admin_email' => get_bloginfo('admin_email'),
                'php_version' => phpversion(),
                'theme' => wp_get_theme(),
                'timezone' => get_option('timezone_string'),
                'gmt_offset' => get_option('gmt_offset'),
                'date_format' => get_option('date_format'),
                'time_format' => get_option('time_format'),
                'active_plugins' => get_option('active_plugins'),
                'template' => get_option('template'),
                'stylesheet' => get_option('stylesheet'),
                'multisite' => is_multisite(),
                'locale' => get_locale(),
                'admin_url' => admin_url(),
                'logo' => $image,
                'site_icon' => get_site_icon_url(),

                'current_user' => $current_user
            ),

            'chateasy' => array(
                'api_username' => $this->api_username,
                'api_secure_code' => $this->api_secure_code,
                'api_endpoint' => $this->api_endpoint,
                'sending_message_enabled' => $this->sending_message_enabled,
                'admin_country_code' => $this->admin_country_code,
                'admin_mobile_number' => $this->admin_mobile_number,

                'version' => get_plugin_data($pluginPath),
                'plugin_path' => $pluginPath,
            )
        );

        $response = $this->post($data, '/woo/activate');

        $db = new Chateasy_DB();

        $db->save_log($response, $data);

        // $response_code = wp_remote_retrieve_response_code($response);
        // $response_body = wp_remote_retrieve_body($response);
        // $response_body = json_decode($response_body, true);

        return $response;
    }
}
