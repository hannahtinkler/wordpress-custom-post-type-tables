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

    public function create(string $table)
    {
        $this->createPostTable($table);
        $this->createMetaTable($table);
    }

    /**
     * Creates the new post table for the custom post type, basing the structure
     * on wp_posts
     *
     * @return void
     */
    private function createPostTable($table)
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $table,
                $this->config['default_post_table']
            )
        );
    }

    /**
     * Creates the new postmeta table for the custom post type, basing the
     * structure on wp_postmeta
     *
     * @return void
     */
    private function createMetaTable($table)
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $table . '_meta',
                $this->config['default_meta_table']
            )
        );
    }
}
