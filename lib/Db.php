<?php

namespace CptTables\Lib;

class Db
{
    /**
     * @var object wpdb
     */
    private $db;

    /**
     * Grabs the Wordpress WPDB class so we can use it to run queries
     */
    public function __construct()
    {
        global $wpdb;

        $this->db = $wpdb;
    }

    /**
     * Returns a single column value from the query
     *
     * @param  string $query
     * @param  any $args
     * @return any
     */
    public function value(string $query, ...$args)
    {
        $results = $this->query($query, $args);

        if (empty($results[0]) || !is_array($results[0])) {
            return;
        }

        return array_shift($results[0]);
    }

    /**
     * Executes the query and returns the result from it as an array
     *
     * @param  string $query
     * @param  array  $args
     * @return array
     */
    public function query(string $query, $args = [])
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
