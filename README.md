# WP Main Category
Allows the selection of a main category for post types that use the category taxonomy. When using Gutenberg a dropdown box will appear under the category selection to allow choosing a main category. In classic editor mode a radio button appears to the right of each selected category allowing one category to be chosen as the main category. Supports both Gutenberg and classic editor mode as of version 0.2. 

## Developer install
Run `npm install` which installs gulp and dependencies.
Run `gulp scripts` to compile the main admin JS files.

## Useful functions
`wpmc_get_posts_by_main_category( $category )` 
Gets all the posts the have the category slug or ID passed.

`wpmc_get_post_ids_by_main_category( $category )` 
Does the same as the function above but only gets the post IDs.

`wpmc_get_main_category()`
Gets the main category for the current post.

## Changelog

### Version 0.2
Release Date: February 2, 2019

**Enhancements**
1. Allow selection of main category in classic editor mode.
2. Improve Gutenberg performance by only allowing main category to be saved when update button is clicked.

### Version 0.1
Release Date: January 29, 2019

**Enhancements**
1. Allows selection of main category via dropdown
2. Gutenberg support
