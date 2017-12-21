# Custom Post Tables

Wordpress stores custom post types in the default posts table (typically `wp_posts`), which is fine for most setups. In the majority of use cases, Wordpress sites are not used to store in excess of thousands of posts, and so this sort of setup doesn't place much additional load on servers.

In cases where the site generates a significant amount of posts across multiple post types though, queries can become very expensive - especially where meta generating plugins such as Advanced Custom Fields are involved. Where a Wordpress site is expected to generate thousands of posts (and subsequently, many thousands of rows of post meta) queries can be sped up significantly by splitting out data into separate tables. This plugin splits out data by post type, creating additional tables for each custom post type used. A 'product' custom post type for example will have its posts stored in `product` and its meta in `product_meta`.

## Implementation
Each new post and meta table is created to the same structure as the Wordpress default post and meta tables. This streamlines the storage process and means that Wordpress is capable of interpreting the data wherever it would normally use a `wp_posts` row, e.g. on the admin edit post pages, admin post listing pages, and in the `wp-posts` functions (e.g. `get_post()`).

When new posts are created, a row is inserted into the `wp_posts` table (as normal) and an automatic MySQL trigger is used to copy this data into the new custom table. Queries to the wp_posts and wp_postmeta table are then rewritten to use the custom table, so that all future lookups and updates made by Wordpress and its plugins are made to the new tables. The original `wp_posts` row is retained for lookup purposes, so that we can determine the post type (and therefore custom table) when there is only a post ID available to work with. Since these lookups are (usually) only necessary in the Wordpress admin and exclusively use the primary key, they do not significantly increase the load of the request. Additionally, each ID lookup is made a maximum of once per request and the result is cached on a per-request basis.

To minimise unecessary lookups when writing your own queries, specify the post type you are looking for whenever possible. This will allow the plugin to simply parse the table from the query without having to lookup the post type in the `wp_posts` table.

## Filter Hooks
### custom_post_type_tables:settings_capability:
Customise what capability the settings page should be limited to. Default is 'manage_options'.
