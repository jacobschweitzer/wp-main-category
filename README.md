# WP Main Category
Allows the selection of a main category for post types that use the category taxonomy. A dropdown box will appear under the category selection to allow choosing a main category. As of version 0.1 the plugin only supports Gutenberg. 

## Developer install
Run `npm install` which installs gulp and dependencies.
Run `gulp scripts` to compile the main admin JS file.

## Useful functions
`wpmc_get_posts_by_main_category( $category )` 
Gets all the posts the have the category slug or ID passed.

`wpmc_get_post_ids_by_main_category( $category )` 
Does the same as the function above but only gets the post IDs.

`wpmc_get_main_category()`
Gets the main category for the current post.

## Changelog
January 29, 2019: Version 0.1 released.