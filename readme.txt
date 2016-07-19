=== Xllentech English Islamic Calendar ===
Contributors: xllentech
Donate link: http://xllentech.com/
Tags: calendar, islamic calendar, hijri calendar, english islamic calendar, gregorian hijri calendar, xllentech calendar
Requires at least: 3.0
Tested up to: 4.5.3
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Best English Islamic Calendar plugin on Wordpress. It shows calendar with English(gregorian) and Islamic(hijri) dates. No maintenace year to year.

== Description ==

Xllentech English Islamic Calendar plugin shows calendar with english and islamic dates by the current english month. This calendar plugin is fully programmed in php and requires no maintenance for Islamic dates year to year.

Islamic month names is customizable from Settings page. You need to update number of days of the Islamic month only if the number of days for the month is different than the default shown in the calendar.

The plugin comes with first islamic date for the current english month, if it doesn't match with your area islamic date, you can change it from settings page. Create a support ticket for guidance. After that, This plugin calculates and keeps record of islamic dates of all the following months lifetime.

In addition to the calendar widget, this plugin also adds widget to show today's english date and islamic date. See Screnshots.

The Pro Version of the calendar provides next month and previous month links, so that viewers can go back or forward for any past or future months with no limit. Also show Today's Date as colored box. It also shows Islamic events on Calendar inside the day box. It uses Green text for Wiladat events, Red for Martyrdom Events and Black for Wafat Events. Events are shown only if the required file has data in it. See screenshots.

* Resposive CSS, Fully compatible with all screen sizes.
* Be worry free for Islamic dates, Requires no maintenance year to year
* Show Today's Dates with separate widget
* Supports shortcode, use [xcalendar] to display the widget anywhere
* Supports Timezones for Worldwide use
* Email Feature for easy reminder and updating of Islamic Month Days

See demo at: http://xllentech.com/web-development/wordpress-plugins/xllentech-calendar/


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/xllentech-calendar` directory, or install the plugin through the WordPress plugins screen directly.

2. Activate the plugin through the 'Plugins' screen in WordPress

3. Place the plugin using Widgets section in your Wordpress admin login, Give it the title you like.




== Frequently Asked Questions ==



= How do I override number of days for the islamic month? =

You have 2 options, Login to Wordpress Admin panel, and add month days override entry from Xllentech Calendar Settings page. 2nd option, you can use a dedicated private page to override.

= How to use the private page to update islamic month days? =
The plugin contains islamic-month-days.php file that can be used to update islamic month days easily. use this file into wordpress page with insert php plugin and following line into your wordpress page:
[insert_php] get_template_part('inc/islamic-month-days'); [/insert_php]

Folder path used here is Active theme folder/inc folder/this file name.

Name the page 'Days' in wordpress. I recommend making the page private or password protected to make 2 stage authentication. 1st your password and 2nd authentication is the pin used in the file, it's 1234 by default, Go to Settings to change it to whatever you like.


== Screenshots ==


1. This is how the plugin appears in the widget area. The plugin show dates for the current english month. Users are required to update the islamic month days 29 or 30. Other than that no other maintenance is required.

2. Xllentech Today widget, how it displays Today's english and islamic date every day.

3. Full version of Xllentech English Islamic Calendar. It shows Today as grayed box.

4. Full version shows Islamic event description on Calendar inside the box in small letters. It uses Green text for Wiladat events, Red for Martyrdom Events and Black for Wafat Events.


== Changelog ==

= 2.0.1: 5th July 2016 =
* Fixed XllenTech Today plugin doesn't reflect timezone

= 2.0.0: 15th June 2016 =
* Added Email Feature, Go to settings page for more information
* Added Timezone to support worldwide use
* Improved/Added number of Setting options
* Minor Date formula tweaks
* Easy Troubleshooting from settings page, if needed
* Improved islamic-month-days.php
* Minor bug fixes

= 1.2.2: 7th May 2016 =
*Minor bug fix

= 1.2.1: 19th April, 2016 =

* Verified and added Wordpress 4.5 compatibility

= 1.2.0: 20th March, 2016 =

* Added Settings page
* Added Feature to Customize islamic month names
* Added Feature to update month days from Settings page
* Settings page shows existing month days override entries

= 1.1.1: 11th March, 2016 =

Fixed minor bug, improved islamic-month-days.php

= 1.1: 10th March, 2016 =

1. Added support of shortcode [xcalendar]

= 1.0: 27th Feb, 2016 =

1. First release of the plugin

== Upgrade Notice ==

= 2.0.1: 5th July 2016 =
* Fixed XllenTech Today plugin doesn't reflect timezone

= 2.0.0: 15th June 2016 =
Upgrade this to resolve number of minor issues. Make sure to change Timezone from settings to match your area.
* Added Email Feature, Go to settings page for more information
* Added Timezone to support worldwide use
* Improved/Added number of Setting options
* Minor Date formula tweaks
* Easy Troubleshooting from settings page, if needed on support ticket
* Improved islamic-month-days.php
* Minor bug fixes

= 1.2.2: 7th May 2016 =
*Minor bug fix

= 1.2.1: 19th April, 2016 =

* Verified and added Wordpress 4.5 compatibility, No User action required

= 1.2.0: 20th March, 2016 =

* Added Settings page
* Added Feature to Customize islamic month names
* Added Feature to update month days from Settings page
* Settings page shows existing month days override entries

= 1.1.1: 11th March, 2016 =

Fixed minor bug, improved islamic-month-days.php

= 1.1: 10th March, 2016 =

Added support of shortcode [xcalendar]

= 1.0: 27th Feb, 2016 =

First release of the plugin