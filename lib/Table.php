<?php

namespace CptTables\Lib;

class Table
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var string
     */
    private $table;

    /**
     * Triggers the create methods for tables
     * @param Db     $db
     * @param array  $config
     * @param string $table
     */
    public function __construct(Db $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Executes the methods required to add the necessary tables
     * @param  string $table
     * @return void
     */
    public function create(string $table)
    {
        $this->createPostTable($table);
        $this->createMetaTable($table);
    }

    /**
     * Creates the new post table for the custom post type, basing the structure
     * on wp_posts
     *
     * @param  array  $table
     * @return void
     */
    private function createPostTable(string $table)
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $this->db->escape($table),
                $this->db->escape($this->config['default_post_table'])
            )
        );
    }

    /**
     * Creates the new postmeta table for the custom post type, basing the
     * structure on wp_postmeta
     *
     * @param  array  $table
     * @return void
     */
    private function createMetaTable(string $table)
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $this->db->escape($table . '_meta'),
                $this->db->escape($this->config['default_meta_table'])
            )
        );
    }
}
