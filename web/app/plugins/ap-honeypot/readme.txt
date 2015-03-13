=== AP HoneyPot WordPress Plugin ===
Contributors: v-media
Tags: comments, spam, http:BL, honeypot
Requires at least: 2.9
Tested up to: 3.7.1
Stable tag: trunk

AP HoneyPot WordPress Plugin allows you to verify IP addresses of clients
connecting to your blog against the Project Honey Pot database.

== Description ==

AP HoneyPot WordPress Plugin, based on Jan Stępień's http:BL, allows you
to verify IP addresses of clients connecting to your blog against the Project
Honey Pot database. Thanks to http:BL API you can quickly check whether your
visitor is an email harvester, a comment spammer or any other malicious
creature. Communication with verification server is done via DNS request
mechanism, which makes the query and response even quicker. Now, thanks
to AP HoneyPot WordPress Plugin any potentially harmful clients are denied
from accessing your blog and therefore abusing it.


= Your Feedback Matters =

Bugs to report? Feature requests? Criticism? New ideas? We want to hear from
you! Do not hesitate. Get in touch with us and share your views.

== Installation ==

1. Get an archive with the most recent version of AP HoneyPot WordPress Plugin.
1. Uncompress the `ap-honeypot` directory from the archive to your `wp-content/plugins` directory.
1. Activate the plugin in the administration panel.
1. Open the plugin’s configuration subpage and enter your Access Key and configure available options accordingly to your preferences.
1. Save settings and enjoy.

== Frequently Asked Questions ==

= Does AP HoneyPot WordPress Plugin work with WordPress MU? =

Actually, it does... But there's no proper way to deny your bloggers to
access the configuration page. Though you can change `APHP_PLUGIN_MENU_PARENT`
constant to `wpmu-admin.php`, it is not recommended to do this. WPMU security
enhancements are yet to be done. You can also provide us your own patch. :)

= Is there an easy way to contact the developer of this plugin? =

Of course there is. Visit [our website](http://artprima.eu/) in order
to find our e-mail address and other contacts.

== Screenshots ==

1. Settings page with a warning nag.
2. Dashboard widget.

== Changelog ==

= 1.4 =
* Nothing but supported WP version update

= 1.2 =
* New dashboard widget. Now you will be able to check manually any IP whether it is listed in the httpBL

= 1.1 =
* Added white-list option
* Changed options to look more like other Wordpress settings pages
* Fixed minor bugs in settings

= 1.0 (AP HoneyPot) =
* Rewriten to uses PHP classes
* Added Dashboard widget with log entries
* Added nag to warn users about an empty/dummy http:BL Access Key
* Added a quick link to settings from the plugins list
* Changed a parent menu for the settings link to `options-general.php`

= 1.8 (http:BL) =
* If a honey pot link is specified an invisible link will be inserted on every page automatically to help the project
* Fixed combinations of specific and generic threat types
* Added upgrade notice to documentation
* Added changelog to documentation

= 1.7 (http:BL) =
* Added options to specify threat level per threat type
