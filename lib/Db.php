<?php

namespace CptTables\Lib;

class Db
{
    /**
     * @var object wpdb
     */
    private $db;

    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
    }

    public function value($query, ...$args)
    {
        $results = $this->query($query, $args);

        if (empty($results[0]) || !is_array($results[0])) {
            return;
        }

        return array_shift($results[0]);
    }

    public function query($query, $args = [])
    {
        $query = str_replace(["'?'", '?'], "'%s'", $query);

        if ($args) {
            $results = $this->db->get_results(
                $this->db->prepare($query, $args)
            );
        } else {
            $results = $this->db->get_results($query);
        }


        return json_decode(json_encode($results), true);
    }
}
