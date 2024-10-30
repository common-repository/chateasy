<?php
// create a php class with a function to get woocommerce order data

class Chateasy_Get_Data
{
    //to get order data
    public function get_order_data($order_id)
    {
        $order = wc_get_order($order_id);

        if (!$order) return array(
            'error' => 'Order not found'
        );

        $items = $order->get_items();

        $items_with_data = array();

        foreach ($items as $item_id => $item) {
            $item_string = $item->__toString();
            $parsed_item = json_decode($item_string, true);

            if (!isset($parsed_item['product_id'])) continue; // skip if product id is not set

            $more_data =
                $this->get_product_data($parsed_item['product_id']);

            $items_with_data[] = array_merge($parsed_item, $more_data);
        }

        $user_id = $order->get_user_id();

        $user_data = $this->get_user_data($user_id);
        $additional_order_data = $order->get_data();

        $data = array(
            'products' => $items_with_data,
            'order_id' => $order_id,
            'order_number' => $order->get_order_number(),
            'order_total' => $order->get_total(),
            'order_status' => $order->get_status(),
            'order_date' => $order->get_date_created()->date('d-m-Y'),
            'order_time' => $order->get_date_created()->date('h:i:s A'),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'customer_email' => $order->get_billing_email(),
            'customer_mobile' => $order->get_billing_phone(),
            'customer_address' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
            'customer_city' => $order->get_billing_city(),
            'customer_state' => $order->get_billing_state(),
            'customer_pincode' => $order->get_billing_postcode(),
            'customer_country' => $order->get_billing_country(),
            'customer_note' => $order->get_customer_note(),
            'user' => $user_data,
            'additional_data' => $additional_order_data
        );

        return $data;
    }


    //to get product data
    public function get_product_data($product_id)
    {
        $product = wc_get_product($product_id);

        if (!$product) return array(
            'error' => 'Product not found',
            'product_id' => $product_id
        );

        $all_data = $product->get_data();

        $data = array(
            'product_id' => $product_id,
        );

        $merged_array = array_merge($data, $all_data);
        return $merged_array;
    }

    //to get user data
    public function get_user_data($user_id)
    {
        $user = get_user_by('id', $user_id);
        $data = array(
            'user_id' => $user_id,
            'user_name' => $user->display_name,
            'user_email' => $user->user_email,
            'user_mobile' => $user->user_mobile,
            'user_address' => $user->user_address,
            'user_city' => $user->user_city,
            'user_state' => $user->user_state,
            'user_pincode' => $user->user_pincode,
            'user_country' => $user->user_country,
            'user_roles' => $user->roles,
        );

        return $data;
    }
}
