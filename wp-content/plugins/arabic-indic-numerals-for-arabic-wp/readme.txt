=== Arabic-Indic Numerals for Arabic Wordpress ===
Contributors: jvarn13
Tags: arabic, numbers, numerals, أرقام، عربية، هندية
Requires at least: 1.5
Tested up to: 5.7.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Replaces "English" numbers with "Arabic" numbers in the Wordpress post date.

== Description ==

This plugin simply replaces Arabic numerals (a.k.a. Hindu-Arabic numerals or Indo-Arabic numerals) with Eastern Arabic numerals (a.k.a. Arabic–Indic numerals or Arabic Eastern numerals) in the date of posts or comments on WordPress. This is useful on sites using the Arabic version of WordPress, which translates month names but leaves the numbers unchanged.

== Installation ==

1. Install via the plugin directory, or upload plugin files to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==
= How do I insert the Arabic date? =
It is recommended to set the date format in the Wordpress settings menu to j F Y in order to format every date in an Arabic-friendly order (day month year), or set the format in specific places in your theme by placing `<?php echo get_the_time('j F Y'); ?>` in your templates. Alternatively, you can use the shortcode `[Arabic_Date]` to insert the current post date in Arabic on any page or post.

= Does this plugin translate the names of months into Arabic? =
No, you will need to install the Arabic version of Wordpress.

= Will all dates on my site be written in Arabic-Indic numbers? =
No, the dates will only be modified on parts of your site where the blog language is set to Arabic.

== Screenshots ==

1. Example of date on Twenty Fourteen theme without the plugin.
2. Example of date on Twenty Fourteen theme with the plugin.

== Changelog ==
= 1.0.3 =
* Added Arabic-style comma substitution (kudos https://github.com/aitohamy)

= 1.0.2 =
* Added POT file

= 1.0.0 =
* Added shortcode to display the post date in Arabic
* Load plugin textdomain
* Checked compatibility with Wordpress 5.0
* Clearer documentation
* Removed Arabic text from English readme so translation can be done via GlotPress

= 0.16 =
* Added filter to get_the_date in addition to the get_the_time filter.

= 0.15 =
* Initial public release.


== Upgrade Notice ==
Nothing
= 0.15 =
