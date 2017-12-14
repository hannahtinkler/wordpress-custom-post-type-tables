<?php

namespace CptTables\Lib;

class Table
{
    private $db;
    private $table;

    public function __construct(Db $db, array $config, string $table)
    {
        $this->db = $db;
        $this->table = $table;
        $this->config = $config;

        $this->createPostTable();
        $this->createMetaTable();
    }

    private function createPostTable()
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $this->table,
                $this->config['default_post_table']
            )
        );
    }

    private function createMetaTable()
    {
        $this->db->query(
            sprintf(
                "CREATE TABLE IF NOT EXISTS %s LIKE %s",
                $this->table . '_meta',
                $this->config['default_meta_table']
            )
        );
    }
}
