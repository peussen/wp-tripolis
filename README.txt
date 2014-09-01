=== WP-Tripolis ===
Contributors: peter.eussen
Tags: forms, tripolis, shortcode
Requires at least: 3.9.0
Tested up to: 3.9.2
Stable Tag: 1.0
License: GPLv3
Author URI: http://harperjones.nl
Plugin URL: https://github.com/HarperJones/wp-tripolis
License URI: http://www.gnu.org/copyleft/gpl.html

Create subscribe forms for Tripolis Dialogue without having to implement your own Tripolis API client.

== Description ==

= Introduction =
This is a Wordpress plugin which will allow you to add (only at the moment) users to the [Tripolis](http://www.tripolis.com) database
without having to do much programming. This plugin has a partial implementation of the Tripolis API (version
2.0).

= Prerequisits =
To use this plugin you need to have an active Tripolis Dialogue license, and your environment should have
an API user who at the minimum has access to:

 - Modules
	 - Contact
 - At least one database
 - At least one workspace

Also make sure your use is a "Clientdomain Administrator" as that seems to be required in order to use the API. Don't
make the user an Interactive user as that will require the user to change his/her password every 60 days.

== Installation ==
= Setup =
Download and install the plugin in whatever way you are used to and activate the plugin from your plugins page.

After activation you should have a new Settings page where you need to fill in the API user credentials. The information
you need to enter you should have ready. If you do not know the server, check the URL of your license login. It should
match what you see there. If your environment is not in the option list, contact me.

Once the setup has been completed it will, you may start using the shortcode. To help you create a shortcode, use the
shortcode generator tool found under the "Tools" menu.

= Placing forms =
You can place a form anywhere you can put a shortcode. By default this plugin makes it so it is also allowed in the text
widget, which should ease the use of the plugin a bit.

You can also directly make a call to the shortcode from your template by adding the following code in your code

   `do_shortcode('<shortcode>');`

== Frequently Asked Questions ==

= The plugin is activated, but I don't see the settings page =
The plugin won't load completely, if you do not have PHP 5.4 or higher installed on your system. Please check your php
version first, if you are using an allowed version, but are still at a loss, let me know!

= Can i customize the HTML? =
You can override all templating done by creating a directory wp-tripolis in your theme directory and place all files there.
To give you a head start you can copy the templates from the templates directory.

= How can I alter fields in the form? =
There are a couple of filters you can use to manipulate the fields of the form. All filters will pass 2 parameters, the value
and a field definition.

* **wp-tripolis_classes**: allows you to add/remove classes from a field/label
* **wp-tripolis_label**: allows you to override the label with your own text
* **wp-tripolis_value**: allows you to set default values for fields (e.g. pre-fill with user info)
* **wp-tripolis_required**: modify which field are mandatory
* **wp-tripolis_submit-label**: modify which label of the submit button

= I need an unsubscribe page! =
We are working on it!

= Can i have a profile page, where people can edit their subscriptions? =
Although not currently planned, this may come in the future if enough people require it. You can always take the plugin
as a start point and develop from there.

== Screenshots ==

1. Main plugin configuration. Allows you to define your API login settings
2. Shortcode generator. This will help you generate the shortcode for your subscribe page, because you will probably need
some help figuring all the field codes out.

== Changelog ==

= 0.1 =
* Initial release, only supports subscribe form

