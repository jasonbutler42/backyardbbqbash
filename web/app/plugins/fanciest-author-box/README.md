Fanciest Author Box
===================

The only author box plugin you'll ever need.

Fanciest Author Box is a WordPress plugin developed by ThematoSoup. Its long list of features gives identity to your single or multi-author WordPress blog. Social widgets are a nice asset that let you present authors in a more personal manner and are ideal for guest bloggers.

This premium author bio plugin is very easy to use and customize. Right after installation the author bio is, by default, enabled on all your posts, pages and custom post types. You can also implement it as a widget in your sidebars, as a template tag or manually using shortcode. It is translation ready and uses color pickers which allow you to set your own color scheme.

## Features

* Automatically added before or after (or both!) your posts, pages and custom post types (not on archive pages)
* Widget
* Shortcode
* Template tag
* Translation ready (see how you can translate it to your own language)
* Ability to set your own color scheme using color pickers
* Random author option for widgets
* Option to show it in your RSS feads

## Supports Major Social Networks

* Twitter bio, latest tweet
* Facebook like & follow
* Google+ add to circles button & rel=author verification (read FAQ section)
* LinkedIn widget
* Custom HTML tab

## Installation

First, you need to unpack the content of the folder you downloaded from CodeCanyon and open fanciest-author-box folder. After you do this there are two ways of installing the plugin:

1. FTP Upload: Using your FTP program, upload the non-zipped plugin folder fanciest-author-box into /wp-content/plugins folder on your server.
2. WordPress Upload: Navigate to Plugins > Add New > Upload and choose the zipped plugin folder fanciest-author-box.zip

## Updating

It’s the same as installing a plugin via FTP. Just overwrite the fanciest-author-box folder in your /wp-content/plugins directory. Your settings will remain unchanged.

## Changelog

**v2.0.8 (9/5/2014)**
* Removes custom update notifier
* CSS fixes

**v2.0.7 (8/19/2014)**
* Fixes Google authorship bug

**v2.0.6 (6/13/2014)**
* Fixes LinkedIn tab bug when switching from LinkedIn to Google+ back to LinkedIn again

**v2.0.5 (5/29/2014)**
* Improved Co-Authors Plus compatibility, latest posts tab now includes co-authored posts

**v2.0.4**
* Performance optimization, improves speed when some popular/related posts plugins are used

**v2.0.3**
* Fixes a multi-shortcode Javascript bug

**v2.0.2**
* Fixes difference between custom tab allowed tags in user vs. global options
* Improved default color settings
* Fixes Facebook and LinkedIn widgets not being loaded in second author box

**v2.0.1**
* Fixes minor JS bug
* Enhances default color settings

**v2.0**
* Uses Genericons icon for tabs
* CSS updates
* Improves method that adds author box to posts/pages automatically
* Pinterest widget
* YouTube widget

**v1.8 (3/13/2014)**
* Adds update notifier
* Moves Settings page to Settings > Fanciest Author Box
* Adds Co-Author Plus support for multi-author posts
* Adds filter hooks (ts_fab_load_css, ts_fab_load_js) that allows developers to disable CSS and JS from being loaded. If hooking into these hooks, you need to return false
* Adds CSS that hides author box added by several popular themes
* Changes list of allowed tags in Custom Tab to default WordPress allowed tags (like the ones used in comments)
* Skips expensive user queries in widget code if blog has more than 200 users

**v1.7.1 (3/3/2014)**
* Updated Google+ widget (more compact, no duplicate photo)
* Updated LinkedIn widget (more compact, no duplicate photo, details appear on mouseover)
* Updated LinkedIn field, full profile URL can now also be used


**v1.7 (3/12/2014)**
* Fixes https error in LinkedIn member profile
* Adds filter ('ts_fab_social_field') that allows you to modify which user meta fields are used for social tabs – Read more


**v1.6 (1/14/2014)**
* Not loading JS and CSS when not necessary
* CSS fixes
* note: If you’re using template tag to add Fanciest Author Box, since version 1.6 you need to include Fanciest Author Box JavaScript and CSS files manually. Read more at Fanciest Author Box documentation website.


**v1.5 (6/22/2013)**
* Updated Twitter API (refer to Fanciest Author Box > Tabs Settings to learn how to enable Twitter tab)


**v1.4.7 (6/12/2013)**
* Bug fix, where authors who opted not to show their author box were still shown in Fanciest Author Box widget


**v1.4.6 (6/9/2013)**
* Option to display author avatar above text in the author box (Avatar position: Floated left, Above text)
* Shortcode attribute avatar=”above” that places avatar above text
* Minor CSS fixes


**v1.4.4 (4/5/2013)**
* User count check added to FAB widget, so sites with huge number of users don’t cause it to break
CSS updates


**v1.4.3 (1/17/2013)**
* Facebook Widget fix


**v1.4.2 (1/10/2013)**
* Minor bug fixes


**v1.4.1 (1/5/2013)**
* Fixed Facebook widget not showing in Firefox


**v1.4 (11/20/2012)**
* Bug fixes
* Added option to manually change execution priority where Fanciest Author Box wouldn’t display, because of possible conflicts with other plugins


**v1.3.3 (10/16/2012)**
* Improved Twitter caching


**v1.3.2 (10/13/2012)**
* Updated Twitter API


**v1.3.1 (10/9/2012)**
* Minor bug fixes


**v1.3 (08/26/2012)**
* Added LinkedIn tab with Member Profile Plugin
* Added option to show icons only in tabs
* User profile fields now not shown for Subscribers
* Switched Facebook widgets from iframe to HTML5 – improves performance
* Added name linked to Facebook profile for Subscribe widget
* Improved custom tab, allowing admin to make it possible for users to override custom tab content only or both title and content (Thanks to Kimberly Castleberry)
* Added RTL support (credit: Metude)
* Added random author option to widget (credit: Indie-Film Moonworks)
* Added Latvian translation (Thanks to Klavs Petersons)
* Added simplified author box to RSS feeds (credit: Katie Orr)


**v1.2.1 (08/13/2012)**
* Improved Twitter fallback, when Twitter API returns nothing
* Fixed bug that caused javascript conflict when multiple shotcodes or template tags were used on the same page


**v1.2 (07/05/2012)**
* Bug fix – overridden photo (via uploader) was showing only in bio tab
* Added “+FirstName LastName” heading with rel=author to Google tab for author verification
* Added custom HTML tab. Thanks to http://codecanyon.net/user/oakland


**v1.1 (06/18/2012)**
* Added option to disable Fanciest Author Box for individual posts, pages and custom posts using ‘ts_fab_hide’ custom field. Credit goes to http://codecanyon.net/user/stscorer
* Added Photo URL field to user profiles, if it has value, that URL will be used to override Gravatar image, if left empty * Gravatar will be used. Suggested by http://codecanyon.net/user/candidhams
* Added option to choose between Subscribe (for profiles) and Like (for pages) Facebook button. Thanks http://codecanyon.net/user/Suicide
