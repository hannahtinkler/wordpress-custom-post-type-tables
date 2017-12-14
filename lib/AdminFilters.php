<?php

namespace CptTables\Lib;

class AdminFilters
{
    public function __construct()
    {
        add_filter('admin_url', [$this, 'updateAdminUrl']);
    }

    public function updateAdminUrl(string$url)
    {
        if ($this->isAdminPage($url)) {
            $url .= sprintf(
                '&post_type=%s',
                $_POST['post_type'] ?? $_GET['post_type'] ?? ''
            );
        }

        return $url;
    }

    public function isAdminPage(string $url)
    {
        $match = get_site_url(null, 'wp-admin/', 'admin') . 'post.php?';

        return strpos($url, $match) !== false;
    }
}
