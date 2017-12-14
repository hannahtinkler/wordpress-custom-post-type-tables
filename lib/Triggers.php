<?php

namespace CptTables\Lib;

class Triggers
{
    private $db;
    private $config;
    private $postsTrigger = 'custom_post_copy';
    private $metaTrigger = 'custom_meta_copy';

    public function __construct(Db $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;

        $this->createPostTrigger();
        $this->createMetaTrigger();
    }

    private function createPostTrigger()
    {
        $query = sprintf(
            "CREATE TRIGGER %s
            AFTER INSERT ON %s FOR EACH ROW BEGIN ",
            $this->postsTrigger,
            $this->config['default_post_table']
        );

        foreach ($this->config['post_types'] as $i => $table) {
            if ($i) {
                $query .= 'ELSE';
            }

            $query .= sprintf(
                "IF (NEW.post_type = '%s') THEN
                    REPLACE INTO %s (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt, post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_content_filtered, post_parent, guid, menu_order, post_type, post_mime_type, comment_count)
                    VALUES (NEW.ID, NEW.post_author, NEW.post_date, NEW.post_date_gmt, NEW.post_content, NEW.post_title, NEW.post_excerpt, NEW.post_status, NEW.comment_status, NEW.ping_status, NEW.post_password, NEW.post_name, NEW.to_ping, NEW.pinged, NEW.post_modified, NEW.post_modified_gmt, NEW.post_content_filtered, NEW.post_parent, NEW.guid, NEW.menu_order, NEW.post_type, NEW.post_mime_type, NEW.comment_count);
                ",
                $table,
                $table
            );
        }

        $query .= "END IF; END";

        $this->db->query($query);
    }

    private function createMetaTrigger()
    {
        $query = sprintf(
            "CREATE TRIGGER %s
            AFTER INSERT ON %s FOR EACH ROW BEGIN ",
            $this->metaTrigger,
            $this->config['default_meta_table']
        );

        foreach ($this->config['post_types'] as $i => $table) {
            if ($i) {
                $query .= 'ELSE';
            }

            $query .= sprintf(
                "IF ((SELECT post_type FROM %s WHERE ID = NEW.post_id) = '%s') THEN
                    REPLACE INTO %s (meta_id, post_id, meta_key, meta_value)
                    VALUES (NEW.meta_id, NEW.post_id, NEW.meta_key, NEW.meta_value);
                ",
                $this->config['default_post_table'],
                $table,
                $table . '_meta'
            );
        }

        $query .= "END IF; END";

        $this->db->query($query);
    }
}
