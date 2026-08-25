=== BetterBlocks ===

Contributors: deanoakley
Donate link: https://thriveweb.com.au/
Tags: betterblocks, block editor, optimisation, usability
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 7.1
Stable tag: 1.0.19
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Handy improvements for the Wordpress block editor interface such as post type support, hiding blocks, adjustable sidebar, and more.

== Description ==

BetterBlocks keeps your block editor tidy, simple, and organised, allowing you to focus on what matters — your content. With a handful of impactful tweaks to the block editor, this plugin helps you maintain an efficient editing space and smoother workflow.

Features

+ Extra Margins for Blocks: Adds customizable margins around blocks for a clean, uncluttered editing space.
+ Enable/Disable Block Editor by Post Type: Choose where to use the block editor, toggling it on or off for specific post types.
+ Dynamic Sidebar Resizing: Adjust the block editor sidebar width to your liking, making editing smoother and more flexible.
+ Hide Blocks from Frontend: Temporarily remove active blocks from the frontend, letting you work in private without deleting content.
+ ACF fields in sidebar: ACF block fields are always available in the editor sidebar.
+ Hide Block Directory: Keep your block search focused by hiding the block directory and reducing clutter.

== Installation ==

Use the Wordpress "Add New Plugin" feature and search for "BetterBlocks" or download the ZIP file and:

1. Upload the `betterblocks` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Adjust the plugin settings as needed from the plugin settings page.

== Frequently Asked Questions ==

= Can I enable/disable the block editor for specific post types? =  
Yes! BetterBlocks lets you choose which of the registered post types on your website will use the block editor.

= Will this plugin add new blocks to my editor? =  
No, BetterBlocks doesn’t add new blocks, but rather focuses on enhancing the existing Wordpress editor experience.

== Changelog ==

= 1.0.19 =
* Adjusted the full-screen ACF block form modal dimensions in the WordPress 7.1 editor.
* Removed the ACF sidebar visibility setting; ACF block fields now always remain available in the editor sidebar.
* Removed the ACF Force Preview Mode setting and force-preview functionality.
* Improved Hide Block editor rendering by using the native block wrapper and only loading controls for the selected block.
* Throttled sidebar resize initialization and made saved sidebar width storage fail-safe.
* Added plugin compatibility metadata for WordPress, PHP, and private update protection.

= 1.0.18 =
* Updated block editor asset loading for the always-iframed editor in WordPress 7.1.
* Improved sidebar resizing compatibility and removed fragile editor UI selectors.
* Updated WordPress script dependencies and settings sanitization callback.
* Fixed plugin version consistency and editor-only asset loading.

= 1.0.17 =
* Version bump

= 1.0.15 =
* Update Wordpress plugin banner and icon assets.

= 1.0.4 =
* Initial release of BetterBlocks with improvements to the block editor such as post type support, hiding blocks, adjustable sidebar, and more.
