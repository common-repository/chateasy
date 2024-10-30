<?php
function chateasy_handle_woocommerce_thankyou($order_id)
{
    $order = wc_get_order($order_id);

    $get_data = new Chateasy_Get_Data();

    $data = $get_data->get_order_data($order);
    debug_to_console($data, 'woocommerce_thankyou');
}

function chateasy_handle_woocommerce_order_note_added($order_note_id, $order)
{
    $get_data = new Chateasy_Get_Data();
    $order_id = $order->get_id();

    $note_data = wc_get_order_note($order_note_id);
    $data = array(
        'new_note' => $note_data,
        'order_id' => $order_id,

        'order' => $get_data->get_order_data($order_id),
    );
    debug_to_console($data, 'woocommerce_order_note_added');
}

function chateasy_handle_woocommerce_new_order($order_id)
{
    $order = wc_get_order($order_id);

    $get_data = new Chateasy_Get_Data();

    $data = $get_data->get_order_data($order);
    debug_to_console($data, 'woocommerce_new_order');
}

function chateasy_handle_woocommerce_order_status_changed($order_id, $old_status, $new_status, $order)
{
    $order = wc_get_order($order_id);

    $get_data = new Chateasy_Get_Data();

    $data = $get_data->get_order_data($order);

    $additional_fields = array(
        'old_status' => $old_status,
        'new_status' => $new_status,
    );

    $data = array_merge($data, $additional_fields);
    debug_to_console($data, 'woocommerce_order_status_changed');
}
