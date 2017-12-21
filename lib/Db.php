<?php

namespace CptTables\Lib;

use Exception;

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
    public function query(string $query, $args = []) : ?array
    {
        $query = str_replace(["'?'", '?'], "'%s'", $query);

        if ($args) {
            $results = $this->db->get_results(
                $this->db->prepare($query, $args)
            );
        } else {
            $results = $this->db->get_results($query);
        }

        if ($this->db->last_error) {
            throw new Exception($this->db->last_error);
        }

        return json_decode(json_encode($results), true);
    }

    /**
     * Escapes variables for inclusion in queries where they are not values
     *
     * @param  any $var
     * @return any
     */
    public function escape($var)
    {
        return esc_sql($var);
    }
}
