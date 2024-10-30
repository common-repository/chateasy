<?php

add_action('admin_menu', 'chateasy_add_admin_menu');
add_action('admin_init', 'chateasy_settings_init');

function chateasy_add_admin_menu()
{
    add_options_page('ChatEasy WooCommerce', 'ChatEasy WooCommerce', 'manage_options', 'chateasy', 'chateasy_settings_page', plugins_url('images/icon.png', __FILE__));
}

function chateasy_settings_init()
{
    register_setting('chateasy', 'chateasy_api_endpoint');
    register_setting('chateasy', 'chateasy_api_username');
    register_setting('chateasy', 'chateasy_api_secure_code');
    register_setting('chateasy', 'chateasy_admin_country_code');
    register_setting('chateasy', 'chateasy_admin_mobile_number');
    register_setting('chateasy', 'chateasy_sending_message_enabled');

    register_setting('chateasy', 'chateasy_plugin_active_id');
}

function validate_chateasy_fields()
{
    $chateasy_api_endpoint = get_option('chateasy_api_endpoint');
    $chateasy_api_username = get_option('chateasy_api_username');
    $chateasy_api_secure_code = get_option('chateasy_api_secure_code');
    $chateasy_admin_country_code = get_option('chateasy_admin_country_code');
    $chateasy_admin_mobile_number = get_option('chateasy_admin_mobile_number');
    $chateasy_sending_message_enabled = get_option('chateasy_sending_message_enabled');

    if (empty($chateasy_api_endpoint) || empty($chateasy_api_username) || empty($chateasy_api_secure_code) || empty($chateasy_admin_country_code) || empty($chateasy_admin_mobile_number) || empty($chateasy_sending_message_enabled)) {
        return false;
    };

    return true;
}

function chateasy_settings_page()
{
    $chateasy_activate = null;

    if (isset($_POST['chateasy_submit'])) {
        $api_endpoint = $_POST['chateasy_api_endpoint'];

        //remove '/' from the end of the url
        if (substr($api_endpoint, -1) === '/') {
            $api_endpoint = substr($api_endpoint, 0, -1);
        }

        if (!filter_var($api_endpoint, FILTER_VALIDATE_URL)) {
            echo '<div class="notice notice-error"><p>Invalid API Endpoint</p></div>';
            return;
        }

        update_option('chateasy_api_endpoint', sanitize_url(preg_replace('/\s+/', '', $api_endpoint)));
        update_option('chateasy_api_username', sanitize_text_field(preg_replace('/\s+/', '', $_POST['chateasy_api_username'])));
        update_option('chateasy_api_secure_code', sanitize_text_field(preg_replace('/\s+/', '', $_POST['chateasy_api_secure_code'])));
        update_option('chateasy_admin_country_code', sanitize_text_field(preg_replace('/\s+/', '', $_POST['chateasy_admin_country_code'])));
        update_option('chateasy_admin_mobile_number', sanitize_text_field(preg_replace('/\s+/', '', $_POST['chateasy_admin_mobile_number'])));
        update_option('chateasy_sending_message_enabled', isset($_POST['chateasy_sending_message_enabled']));
    }

    if (isset($_POST['chateasy_activate'])) {

        if (validate_chateasy_fields() == false) {
            echo '<div class="notice notice-error"><p>Invalid Fields</p></div>';
            return;
        }

        $api = new ChateasyAPI();
        $chateasy_activate = $api->activate();

        $response_code = wp_remote_retrieve_response_code($chateasy_activate);
        $response_body = wp_remote_retrieve_body($chateasy_activate);
        $response_body = json_decode($response_body, true);

        $active_id = isset($response_body['id']) ? $response_body['id'] : null;

        if (isset($response_body['message'])) {
            $message = $response_body['message'];
        } else if (isset($response_body['error'])) {
            $message = $response_body['error'];
        } else {
            $message = '<a
            style="color: red; font-weight: bold;"
            >' . __('Unknown Error: Verify the api endpoint field', 'chateasy') . '</a>';
        }

        if ($response_code == 200 && $active_id != null) {

            update_option('chateasy_plugin_active_id', $active_id);

            echo '<div class="notice notice-success"><p>Plugin activated successfully ' .
                $message
                . '</p>
                </div>';
        } else {
            echo '<div class="notice notice-error"><p>Activation Failed: ' . $message
                . '</p>
                
                    <p
                        style="color: #0073aa; font-weight: bold;">
                    
                        Check all the fields are correct and save before activating.
                    </p>
                </div>';
        }
    }

    $api_endpoint = get_option('chateasy_api_endpoint', 'https://api.chateasy.in');
    $api_username = get_option('chateasy_api_username');
    $api_secure_code = get_option('chateasy_api_secure_code');
    $country_code = get_option('chateasy_admin_country_code', 91);
    $admin_mobile_number = get_option('chateasy_admin_mobile_number');
    $sending_message_enabled = get_option('chateasy_sending_message_enabled', true);

    $plugin_active_id = get_option('chateasy_plugin_active_id', null);

    $buttonValue = $plugin_active_id == null ? 'Activate' : 'Re Activate';
?>
    <div class="wrap">
        <h2>ChatEasy WooCommerce Settings</h2>

        <h4>
            Please enter your ChatEasy API credentials below. You can get your API credentials from your ChatEasy settings section.
            <a>https://app.chateasy.in</a>
        </h4>

        <form method="POST">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="chateasy_api_endpoint">API Endpoint</label></th>
                        <td>
                            <input type="text" name="chateasy_api_endpoint" id="chateasy_api_endpoint" value="<?php echo esc_attr($api_endpoint); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="chateasy_api_username">API Username</label></th>
                        <td>
                            <input type="text" name="chateasy_api_username" id="chateasy_api_username" value="<?php echo esc_attr($api_username); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="chateasy_api_secure_code">API Secure Code</label></th>
                        <td>
                            <input type="text" name="chateasy_api_secure_code" id="chateasy_api_secure_code" value="<?php echo esc_attr($api_secure_code); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="chateasy_admin_country_code">Country Code</label></th>
                        <td>
                            <input type="text" name="chateasy_admin_country_code" id="chateasy_admin_country_code" value="<?php echo esc_attr($country_code); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="chateasy_admin_mobile_number">Admin Mobile Number</label></th>
                        <td>
                            <input type="text" name="chateasy_admin_mobile_number" id="chateasy_admin_mobile_number" value="<?php echo esc_attr($admin_mobile_number); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="chateasy_sending_message_enabled">Sending Message Enabled</label></th>
                        <td>
                            <input type="checkbox" name="chateasy_sending_message_enabled" id="chateasy_sending_message_enabled" <?php checked($sending_message_enabled); ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"></th>
                        <td>
                            <input type="submit" class="button button-primary" name="chateasy_submit" value="Save Changes">
                            <input type="submit" class="button" name="chateasy_activate" value="<?php echo esc_attr($buttonValue); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <?php if (isset($chateasy_activate)) : ?>
            <h3>Test Message Response</h3>
            <pre><?php print_r($response_body); ?></pre>
        <?php endif; ?>
    </div>
<?php

}
