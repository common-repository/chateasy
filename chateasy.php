<?php
/*
Plugin Name: Woo ChatEasy
Plugin URI: https://chateasy.in/
Description: This plugin will send API based POST request to ChatEasy instance to send message to client's whatsapp number on wooommerce order and payments. This is a simple plugin to connect your woocommerce store with ChatEasy instance.
Version: 1.0.6
Author: ChatEasy
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

defined('ABSPATH') or die('No script kiddies please!');
if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/chateasy-db.php';
require_once plugin_dir_path(__FILE__) . 'includes/api/construct_request.php';

require_once plugin_dir_path(__FILE__) . 'includes/chateasy-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/chateasy-admin-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/chateasy-get-order.php';
require_once plugin_dir_path(__FILE__) . 'includes/chateasy-woocommerce-hooks.php';

require_once plugin_dir_path(__FILE__) . 'includes/api/rest_api_init.php';

// how to use the class
// $get_data = new chateasy_get_data();

add_action('woocommerce_thankyou', 'chateasy_handle_woocommerce_thankyou', 10, 1);

// on order note added
add_action('woocommerce_order_note_added', 'chateasy_handle_woocommerce_order_note_added', 10, 2);

// send WhatsApp messages when a customer places an order.
add_action('woocommerce_new_order', 'chateasy_handle_woocommerce_new_order', 10, 1);

// This code will send an notification to the customer's
// mobile number when the order status is changed to "completed"
add_action('woocommerce_order_status_changed', 'chateasy_handle_woocommerce_order_status_changed', 10, 4);






// run this function only on plugin activation
register_activation_hook(__FILE__, 'chateasy_handle_plugin_activation');
function chateasy_handle_plugin_activation()
{
  require_once plugin_dir_path(__FILE__) . 'includes/chateasy-db.php';

  $chateasy_db = new Chateasy_DB();
  $chateasy_db->create_db();
}




// // run this function only on plugin deactivation
// register_deactivation_hook(__FILE__, 'chateasy_handle_plugin_deactivation');
// function chateasy_handle_plugin_deactivation()
// {
//   $chateasy_db = new Chateasy_DB();
//   $chateasy_db->;
// }