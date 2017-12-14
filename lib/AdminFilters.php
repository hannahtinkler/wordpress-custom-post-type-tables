<?php

namespace CptTables\Lib;

class AdminFilters
{
    /**
     * Bind the method that updates the admin redirect url to the admin_url
     * filter
     */
    public function __construct()
    {
        add_filter('admin_url', [$this, 'updateAdminUrl']);
    }

    /**
     * Adds post type from GET/POST request to the url if it is an admin page
     * @param  string $url
     * @return string
     */
    public function updateAdminUrl(string $url)
    {
        if ($this->isAdminPage($url)) {
            $url .= sprintf(
                '&post_type=%s',
                $_POST['post_type'] ?? $_GET['post_type'] ?? ''
            );
        }

        return $url;
    }

    /**
     * Returns true is the current page is in the Wordpress admin
     * @param  string  $url
     * @return boolean
     */
    public function isAdminPage(string $url)
    {
        $match = get_site_url(null, 'wp-admin/', 'admin') . 'post.php?';

        return strpos($url, $match) !== false;
    }
}
