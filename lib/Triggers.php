<?php

namespace CptTables\Lib;

class Triggers
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $postsTrigger = 'custom_post_copy';

    /**
     * @var string
     */
    private $metaTrigger = 'custom_meta_copy';

    /**
     * Triggers the methods that create the post and post meta triggers
     *
     * @param Db    $db
     * @param array $config
     */
    public function __construct(Db $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    /**
     * Executes the methods required to set up all necessary triggers
     * @param  array  $tables
     * @return void
     */
    public function create(array $tables)
    {
        $this->createPostTrigger($tables);
        $this->createMetaTrigger($tables);
    }

    /**
     * Creates a trigger on the new custom post type table that copies each new
     * row of data from the posts table to the custom table
     *
     * @param  array  $tables
     * @return void
     */
    private function createPostTrigger(array $tables)
    {
        $this->db->value("DROP TRIGGER IF EXISTS " . $this->db->escape($this->postsTrigger));

        $params = [];
        $query = sprintf(
            "CREATE TRIGGER %s
            AFTER INSERT ON %s FOR EACH ROW BEGIN ",
            $this->db->escape($this->postsTrigger),
            $this->db->escape($this->config['default_post_table'])
        );

        foreach ($tables as $i => $table) {
            if ($i) {
                $query .= 'ELSE';
            }

            $query .= sprintf(
                "IF (NEW.post_type = ?) THEN
                    REPLACE INTO %s (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
                    VALUES (NEW.ID, NEW.post_author, NEW.post_date, NEW.post_date_gmt, NEW.post_content, NEW.post_title, NEW.post_excerpt, NEW.post_status, NEW.comment_status, NEW.ping_status, NEW.post_password, NEW.post_name, NEW.to_ping, NEW.pinged, NEW.post_modified, NEW.post_modified_gmt, NEW.post_content_filtered, NEW.post_parent, NEW.guid, NEW.menu_order, NEW.post_type, NEW.post_mime_type, NEW.comment_count);
                ",
                $this->db->escape($table)
            );

            $params[] = $table;
        }

        $query .= "END IF; END";

        $this->db->query($query, $params);
    }

    /**
     * Creates a trigger on the new custom post type meta table that copies each
     * new row of data from the post meta table to the custom meta table
     *
     * @param  array  $tables
     * @return void
     */
    private function createMetaTrigger(array $tables)
    {
        $this->db->value("DROP TRIGGER IF EXISTS " . $this->db->escape($this->metaTrigger));

        $params = [];
        $query = sprintf(
            "CREATE TRIGGER %s
            AFTER INSERT ON %s FOR EACH ROW BEGIN ",
            $this->db->escape($this->metaTrigger),
            $this->db->escape($this->config['default_meta_table'])
        );

        foreach ($tables as $i => $table) {
            if ($i) {
                $query .= 'ELSE';
            }

            $query .= sprintf(
                "IF ((SELECT post_type FROM %s WHERE ID = NEW.post_id) = ?) THEN
                    REPLACE INTO %s (meta_id, post_id, meta_key, meta_value)
                    VALUES (NEW.meta_id, NEW.post_id, NEW.meta_key, NEW.meta_value);
                ",
                $this->db->escape($this->config['default_post_table']),
                $this->db->escape($table . '_meta')
            );

            $params[] = $table;
        }

        $query .= "END IF; END";

        $this->db->query($query, $params);
    }
}
