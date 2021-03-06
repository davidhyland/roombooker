=== JHub RoomBooker ===

Contributors: David Hyland
Author: http://dhyland.com/
Tags: calendar, scheduler
Requires at least: WordPress 4.1
Tested up to: 5.2.2
Stable tag: 4.9.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Room booking plugin developed for JHub.

== Description ==

This is a room booking plugin specifically developed for JHub. 

By default all users can create bookings and can edit their own bookings.

Administrator and Editor admin users have the ability to edit all bookings.

== Installation ==

1. Upload `roombooker` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a page to contain the calendar
3. Add `[jhub_roombooker]` shortcode to the page or add `echo do_shortcode('[jhub_roombooker]');` to your template




== Changelog ==

= 1.5.2 =
* Updated plugin-update-checker to v 4.7

= 1.5.1 =
* Updated WP version tested up to

= 1.5.0 =
* Bug fix for stats where booking did not have an end time specified

= 1.4.4 =
* Added further bug fix for event time clash detection
* Updated plugin-update-checker to v 4.5

= 1.4.3 =
* Fixed NumPeople not being saved on event update

= 1.4.2 =
* Added bug fix for event time clash detection

= 1.4.0 =
* Added date to confirmation email subject and body

= 1.3.9 =
* Bar graph bug fix

= 1.3.8 =
* Updated plugin version number

= 1.3.7 =
* Layout tweaks to admin stats page

= 1.3.6 =
* Added message for non-JS browsers

= 1.3.5 =
* Moved JS files to head

= 1.3.2 =
* Added auto plugin update functionality (plugin-update-checker v4.4)

= 1.3.1 =
* Fixed error and re-enabled javascript *strict* mode

= 1.3.0 =
* Removed Javascript *strict* mode for IE11 usage
* Updated FullCalendar to v3.9.0
* Updated Scheduler to v1.9.3

= 1.2.7 =
* Added database logs for save/update/delete and event clashes

= 1.2.6 =
* Bug fix where event clash check was also checking against deleted events

= 1.2.5 =
* Added tooltip to events to show all data easily
* Fix for events potentially clashing in times for same day same room

= 1.2.4 =
* Fix for event created in Month view not appearing in other views

= 1.2.3 =
* Bug fix for js errors shown on non-plugin pages

= 1.2.2 =
* Bug fix to time issues in calendar

= 1.2.1 =
* Added bug fix to fullcalendar library

= 1.2 =
* Added admin stats

= 1.1 =
* Bug fixes on edit event from URL

= 1.0 =
* Initial release


== Upgrade Notice ==

