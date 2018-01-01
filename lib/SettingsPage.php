<?php

namespace CptTables\Lib;

class SettingsPage
{
    /**
     * The menu and page name for this settings page
     * @var string
     */
    private $name = 'Custom Post Type Tables';

    /**
     * The slug for the settings page menu item
     * @var string
     */
    private $slug = 'cpt_tables';

    /**
     * The option key that the enabled tables is stored in
     * @var string
     */
    private $enabledOption = 'ctp_tables:tables_enabled';

    /**
     * The filter that plugin users can hook into to customise capability
     * required to access this page
     *
     * @var string
     */
    private $filter = 'ctp_tables:settings_capability';

    /**
     * The public WP post types to exclude from the settings page CPT list
     * @var array
     */
    private $exclude = [
        'post',
        'page',
        'media',
        'attachment',
    ];

    /**
     * Add settings page. If form has been submitted, route to save method.
     *
     * @param Table   $table
     * @param Triggers $triggers
     */
    public function __construct(Table $table, Triggers $triggers)
    {
        $this->table = $table;
        $this->triggers = $triggers;

        if (isset($_POST['cpt_tables:submitted'])) {
            $this->saveSettings();
            exit;
        }

        add_filter('admin_menu', [$this, 'addSettingsPage']);
    }

    /**
     * Add settings page to admin settings menu
     */
    public function addSettingsPage()
    {
        $this->capability = apply_filters($this->filter, 'manage_options');

        add_options_page(
            $this->name,
            $this->name,
            $this->capability,
            $this->slug,
            [$this, 'showSettingsPage']
        );
    }

    /**
     * Shows the settings page or a 404 if the user does not have access
     *
     * @return void
     */
    public function showSettingsPage()
    {
        if (!current_user_can(apply_filters($this->capability, 'manage_options'))) {
            require_once __DIR__ . '/../templates/access-denied.php';
            wp_die();
        }

        $postTypes = $this->getAllPostTypes();
        $enabled = $this->getEnabledPostTypes();

        require_once __DIR__ . '/../templates/settings.php';
    }

    /**
     * Save the new settings to the options table and then update the db to add
     * new tables and rebuild the triggers
     *
     * @return void
     */
    public function saveSettings()
    {
        $oldPostTypes = $this->getEnabledPostTypes();
        $newPostTypes = $_POST['custom_post_type_tables_enable'] ?: [];

        if ($oldPostTypes != $newPostTypes) {
            $this->updateDatabaseSchema($newPostTypes);
            update_option($this->enabledOption, serialize($newPostTypes), null, true);
        }

        wp_redirect($_SERVER['REQUEST_URI'] . '&success=true');
    }

    /**
     * Create new tables and rebuild triggers
     *
     * @param  array  $postTypes
     * @return void
     */
    private function updateDatabaseSchema(array $postTypes)
    {
        foreach ($postTypes as $postType) {
            $this->table->create($postType);
        }

        $this->triggers->create($postTypes);
    }

    /**
     * Gets the option that stores enabled post type tables and unserializes it
     * @return array
     */
    public function getEnabledPostTypes() : array
    {
        return unserialize(get_option($this->enabledOption)) ?: [];
    }

    /**
     * Parses the public WP post types object into an array with the name
     * and the slug in. Then order alphabetically.
     *
     * @return array
     */
    public function getAllPostTypes() : array
    {
        $postTypes = array_map(
            function ($postType) {
                return ['name' => $postType['labels']['name'], 'slug' => $postType['name']];
            },
            json_decode(json_encode(get_post_types(['public' => true], 'object')), true)
        );

        $postTypes = array_filter($postTypes, function ($item) {
            return !in_array($item['slug'], $this->exclude);
        });

        usort($postTypes, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $postTypes;
    }
}
