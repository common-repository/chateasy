<?php

class Chateasy_DB
{

    private $chateasy_plugin_table_suffix = 'chateasy';

    public function create_db()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;

        $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            method VARCHAR(6) NOT NULL,
            response_body TEXT,
            response_header TEXT NOT NULL,
            response_code INT(11) NOT NULL DEFAULT 200,
            response_message TEXT,
            post_data TEXT,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
            ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $create = dbDelta($sql);

        return $create;
    }

    public function drop_db()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;

        $sql = "DROP TABLE IF EXISTS $table_name";
        return $wpdb->query($sql);
    }

    public function save_log($response, $post_data)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;

        $wpdb->insert(
            $table_name,
            array(
                'method' => $response['request']['method'] ?? 'GET',
                'response_body' => $response['body'],
                'response_header' => json_encode($response['headers']),
                'response_code' => $response['response']['code'],
                'response_message' => $response['response']['message'],
                'post_data' => json_encode($post_data),
            )
        );

        return $wpdb;
    }

    public function get_logs($limit, $offset)
    {
        $limit = intval($limit) ? intval($limit) : 10;
        $offset = intval($offset) ? intval($offset) : 0;

        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT $limit OFFSET $offset";
        $results = $wpdb->get_results($sql);

        return $results;
    }

    public function get_logs_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT COUNT(*) FROM $table_name";
        $results = $wpdb->get_var($sql);

        return $results;
    }

    public function get_logs_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT * FROM $table_name WHERE id = $id";
        $results = $wpdb->get_results($sql);

        return $results;
    }

    public function delete_log_by_id($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "DELETE FROM $table_name WHERE id = $id";
        $results = $wpdb->query($sql);

        return $results;
    }

    public function delete_all_logs()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "TRUNCATE TABLE $table_name";
        $results = $wpdb->query($sql);

        return $results;
    }

    public function get_logs_by_date($date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT * FROM $table_name WHERE time LIKE '$date%'";
        $results = $wpdb->get_results($sql);

        return $results;
    }

    public function get_logs_by_date_range($start_date, $end_date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT * FROM $table_name WHERE time BETWEEN '$start_date' AND '$end_date'";
        $results = $wpdb->get_results($sql);

        return $results;
    }

    public function get_logs_by_response_code($response_code)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->chateasy_plugin_table_suffix;


        $sql = "SELECT * FROM $table_name WHERE response_code = $response_code";
        $results = $wpdb->get_results($sql);

        return $results;
    }
}
