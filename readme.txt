=== GuildQuality Member Feedback Widget ===
Contributors: kevin@GuildQuality
Company Link: http://www.guildquality.com/
Tags: widget, feedback, homebuilder, remodeler, contractor
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple widget that displays a Guildmember's feedback. GuildQuality surveys on behalf of quality minded homebuilders, remodelers and contractors

== Description ==

GuildQuality's Member Feedback Widget plugin for WordPress allows you to display a feed of recent published comments and reviews from your GuildQuality account on your WordPress site or blog.

GuildQuality surveys on behalf of quality minded home builders, remodelers and contractors. You must have a member account with GuildQuality in order to utilize this plugin. Don't have an account? Visit GuildQuality.com to sign up for a free trial.

Style the widget to fit your site with an easy to use form on the widget administration panel.

This widget will help promote the quality of your services and express your commitment to quality to your site visitors.

Simply install this plugin and include the widget on one of your widget areas of your site to include your feedback on your site or blog.

== Installation ==

1. Upload the `GQ_member_feedback` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Goto the widget page [Appearance > Widgets] in your administration panel
4. Drag the `GuildQuality Member Feedback Widget` to a widget area
5. Questions? Check out [this post](http://www.guildquality.com/blog/2012/10/16/wordpress-widget/ "GuildQuality WordPress widget instructions") for further instructions.

== Frequently Asked Questions ==

= Can I use this plugin without a GuildQuality account? =

No, A GuildQuality account is required to use this plugin. If you don't have an account visit [GuilQuality to start a free trial!](http://www.guildquality.com/freetrial/?s=WPwidgetMarket "GuildQuality Free Trial Signup")

== Troubleshooting ==

= Having issues with the plugin, check these tips first =

Your GuildQuality account url should be included in the plugin configuration in order for your survey response data to show up on your wordpress site.
Your profile must have at least 1 published response in order to display the widget. (If you select to display only comments or only reviews, you must have at least 1 comment or review respectively.)

= The GQ widget looks different than before or simply doesn't look right =

You now have full control of styling the widget since version 1.3
There has been a slight styling change that centers the widget for certain width containers. If this doesn't work well for you continue with the instuctions below:

* In your css, you may specify styling to override the styling from the widget
* Look through the [DOM](http://www.w3.org/TR/DOM-Level-2-Core/introduction.html "Document Object Model") for the functioning widget
* You will see a number of .gq-* classes e.g. 'gq-review-title' will style the `<b>` wrapper around each Review's title.
* Add css styling to the class with `!important` attributes e.g.:
``
.gq-review-title {
	font-size: 15px !important;
}
``

If the widget is unable to display any responses, you may see an error message.

== Screenshots ==

1. The admin panel for the GuildQuality widget.
2. Here is a configured widget in a sidebar.
3. Here is another configured widget.

== Changelog ==

= 1.4 =
* Fixed to allow multiple instances of the widget on the same page

= 1.3 =
* fetch data via [WP's HTTP API](http://codex.wordpress.org/HTTP_API "WordPress HTTP API")
* Handles members with fewer than 20 published responses
* Includes flexibility in url formatting
* Specific classes (.gq-*) added to the html for custom styling
* Choose to display only reviews or only comments

= 1.2 =
* Corrected images for firefox

= 1.1 =
* Fixed broken links
* Included alias in alt text

= 1.0 =
* Initial version.

== Upgrade Notice ==

If you implemented fixes to handle widget styling, specifically for centering or left justifying the elements, you may see those changes ignored in version 1.3. You can now access the elements via specific css classes and re-implement these changes. Note that the widget should now center and be full width at any size.
