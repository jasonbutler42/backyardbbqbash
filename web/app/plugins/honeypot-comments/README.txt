=== Honeypot Comments ===
Contributors: gh0stpr3ss
Donate link: http://twitter.com/gh0stpr3ss
Tags: comments, spam
Requires at least: 3.5.1
Tested up to: 3.9.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin to stop most comment spam by adding in a simple honeypot for spam bots to get caught in.

== Description ==

No more need for captchas, making your readers jump through hoops in order to comment. Instead, activate this plugin and let a simple hidden honeypot input field catch spam bots, which will fill out all fields in the form (even our hidden honeypot field). If that honeypot field isn't empty, the comment won't process, effectively nuking most spam comments.

I also made sure that the **id** of the field is utilizing a random string of letters and numbers every time the page is loaded (ie: *34rscsDW37g8gf*) so that spam bots can't target a specific id name in order to bypass it, making it harder for the bots to win.

Installation is dead simple, just activate the plugin and you're done. No other steps required on your end, the plugin handles everything.

== Installation ==

1. Uzip the `honeypot-comments.zip` folder.
2. Upload the `honeypot-comments` folder to your `/wp-content/plugins` directory.
3. In your WordPress dashboard, head over to the *Plugins* section.
4. Activate *Honeypot Comments*.

== Frequently Asked Questions ==

= Why was this plugin created? =

Because I hate captchas and I believe there is an easier way to tackle the spam comment issue with WordPress blogs.

= What is the "honeypot" method? =

This plugin adds a hidden input field into the WordPress comment section for your blog posts which is hidden to humans viewing the page. The spambots, however, see it and add info to it and get tripped up because the plugin will stop any comment that has the honeypot field filled in.

This will not stop all spammers, but will catch a lot of the bots that are written to fill out forms automatically since they'll be filling out the honeypot field that this plugin adds in.

== Changelog ==

= 1.1.0 = 

* Changed the way the ID for the input field was created, as it was [pointed out to me](http://www.reddit.com/r/Wordpress/comments/283y6p/created_my_first_plugin_honeypot_comments_and/ci77rmf) that a sophisticated bot could figure out how I originally had the ID generated since it had *honeypot-comments-* in the ID name.

= 1.0.0 = 

* Initial release

== Additional info ==

The basic structure of this plugin was cloned from the [WordPress-Plugin-Boilerplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate) project.

This plugin supports the [GitHub Updater](https://github.com/afragen/github-updater) plugin, so if you install that, this plugin becomes automatically updateable direct from GitHub. Any submission to WP.org repo will make this redundant.
