<?php

namespace CptTables;

use CptTables\Lib\Db;
use CptTables\Lib\Table;
use CptTables\Lib\Triggers;
use CptTables\Lib\AdminFilters;
use CptTables\Lib\QueryFilters;

class Core
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var string
     */
    public static $plugin_slug = 'custom-post-tables';

    /**
     * @var string
     */
    public $flags = [
        'CptTables\Core::setupCustomTables' => 'ctp_tables:custom_tables_added',
    ];

    /**
     * @return void
     */
    public function load()
    {
        $self = new self();

        $self->db = new Db;
        $self->config = require(__DIR__ . '/config.php');

        $self->setupCustomTables();
        $self->setupAdminFilters();
        $self->setupQueryFilters();
    }

    /**
     * Creates additional tables and sets up triggers to copy data over to them
     * @return void
     */
    private function setupCustomTables()
    {
        if (!get_option($this->flags[__METHOD__])) {
            foreach ($this->config['post_types'] as $postType) {
                new Table($this->db, $this->config, $postType);
            }

            new Triggers($this->db, $this->config);

            update_option($this->flags[__METHOD__], true, null, true);
        }
    }

    private function setupAdminFilters()
    {
        new AdminFilters;
    }

    private function setupQueryFilters()
    {
        new QueryFilters($this->db, $this->config);
    }

    /**
     * @return void
     */
    public function activate()
    {
        flush_rewrite_rules();
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        flush_rewrite_rules();
    }
}
