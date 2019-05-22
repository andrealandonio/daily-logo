=== Daily logo ===
Contributors: lando1982
Tags: logo, daily, doodle, switch, organize, manage
Requires at least: 3.5
Tested up to: 5.2
Stable tag: 2.1.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Daily logo is a simple and flexible plugin which allow users to display a different header/logo in their site every day.

== Description ==

Daily logo is a simple and flexible plugin which allow users to display a different header/logo in their site every day. If you need to have a different logo every day or during a particular event with this plugin you can completely customize your site header/logo management.

When you install and activate the plugin, an admin section is added. The section is composed by 2 pages, in the first one are automatically listed all your configured daily logos, in the second one, you can see a small legend and you can setup default look & feel templates.
You can provide a starting time and an ending time for every logo. By default the start time is 00:00 and the end time is 23:59.

You can provide 2 different templates for the header/logo HTML snippet. Imagine having different layout for desktop or mobile. The algorithm for choosing the templates is demanded to you.

There is 2 different actions that you can use:

* daily_logo_show_today / daily_logo_show_today_alternative (display daily logo)
* daily_logo_show_date / daily_logo_show_date_alternative (display a custom date logo)

Here you can find and example of template detection in a PHP template file (e.g. header.php):

`
$is_mobile = false;
if ($is_mobile) do_action( 'daily_logo_show_today_alternative' );
else do_action( 'daily_logo_show_today' );
`

`
$is_mobile = false;
if ($is_mobile) do_action( 'daily_logo_show_date_alternative', 2015, 5, 12 );
else do_action( 'daily_logo_show_date', 2015, 5, 12 );
`

The action with the "alternative" suffix is used for the second template layout.

= Usage =

1. Go to `WP-Admin -> Daily Logo`.
2. Add, modify, clone or delete a daily logo or configure the default templates.
3. Replace in your the logo HTML snippet with the following code `do_action( 'daily_logo_show_today' )`.

Links: [Author's Site](http://www.andrealandonio.it)

== Installation ==

1. Unzip the downloaded `daily-logo` zip file
2. Upload the `daily-logo` folder and its contents into the `wp-content/plugins/` directory of your WordPress installation
3. Activate `daily-logo` from Plugins page

== Frequently Asked Questions ==

= Works on multisite? =

No

= Can I use my custom image sizes on widget? =

No, it's better to upload for every daily logo an image with the right (or your desired) size.

= How works with different layout? =

The plugin provides 2 different fields to upload 2 different image, with different size. In this way you can use one layout for you first template (for example the desktop site) and the other layout for an alternative template (for example the mobile version of your site)

= How works with logos scheduled in the same day? =

With logos on the same day, the plugin uses the older and valid one and then first to expire (note that with overlapped dates the plugin may fail, it is advised not to use overlapped dates)

== Screenshots ==

1. Daily logo items management
2. Settings admin page

== Changelog ==

= 2.1.0 - 2017-01-02 =
* Added "HAS_IMAGE_ALTERNATIVE" and "IMAGE_ALTERNATIVE" tags management

= 2.0.1 - 2016-07-15 =
* Fixed JS date picker errors

= 2.0.0 - 2016-07-15 =
* Added start/end time management

= 1.2.0 - 2016-06-29 =
* Added "load more" in logos list

= 1.1.0 - 2015-10-25 =
* Added clone logo row option

= 1.0.0 - 2015-06-01 =
* First release

== Upgrade Notice ==

= 1.0.0 =
This version requires PHP 5.3.3+
