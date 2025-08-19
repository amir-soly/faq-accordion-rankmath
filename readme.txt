=== Easy FAQ Accordion for Rank Math ===
Contributors: amirsoli
Tags: faq, accordion, rank math, seo, schema, easy, simple
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The easiest way to display your Rank Math FAQ schema in a beautiful, responsive accordion.

== Description ==

Effortlessly transform the FAQ data you've already created in Rank Math into a stylish and responsive accordion.

`Easy FAQ Accordion for Rank Math` automatically finds the FAQ schema on your posts and pages and displays it in a clean, collapsible format. Simply add the `[faq_accordion]` shortcode to show your FAQs anywhere.

This plugin requires the Rank Math SEO plugin to be active, as it reads the FAQ data saved by its schema feature.

**Features:**
* **Automatic Display:** Converts Rank Math FAQs into an accordion UI.
* **Simple Shortcode:** Use `[faq_accordion]` to place the accordion in any post or page.
* **Fully Customizable:** A simple settings page lets you control colors, fonts, and sizes to match your theme.
* **Lightweight & Fast:** Optimized for performance without unnecessary bloat.

== Installation ==

1.  Upload the `easy-faq-accordion-for-rank-math` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Go to **Settings > FAQ Accordion** to adjust styles and options.
4.  Use the shortcode `[faq_accordion]` in your posts or pages to display the FAQ accordion.

== Frequently Asked Questions ==

= Does this plugin require Rank Math? =
Yes, this plugin is designed to work with Rank Math. It displays the FAQ data that you have saved using Rank Math's Schema feature.

= How do I add FAQs? =
While editing a post or page, go to the Rank Math SEO meta box, navigate to the 'Schema' tab, and add your FAQs there. Then, insert the `[faq_accordion]` shortcode in your content to display them.

== Changelog ==

= 1.1 =
* Complete code refactor to meet WordPress.org plugin review standards.
* Corrected text domain and internationalization.
* Fixed script and style enqueueing methods.
* Added proper escaping for all outputs.
* Renamed plugin for clarity and better branding.

= 1.0 =
* Initial release.