<?php

namespace CptTables;

use CptTables\Lib\Db;
use CptTables\Lib\Table;
use CptTables\Lib\Triggers;
use CptTables\Lib\AdminFilters;
use CptTables\Lib\QueryFilters;
use CptTables\Lib\SettingsPage;

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
     * @return void
     */
    public function load()
    {
        $self = new self();

        $self->db = new Db;
        $self->config = require(__DIR__ . '/config.php');

        $self->setupAdminFilters();
        $self->setupQueryFilters();
        $self->setupSettingsPage();
    }

    /**
     * @return void
     */
    private function setupAdminFilters()
    {
        new AdminFilters;
    }

    /**
     * @return void
     */
    private function setupQueryFilters()
    {
        new QueryFilters($this->db, $this->config);
    }

    /**
     * @return void
     */
    private function setupSettingsPage()
    {
        new SettingsPage(
            new Table($this->db, $this->config),
            new Triggers($this->db, $this->config)
        );
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
