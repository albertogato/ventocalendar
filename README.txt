=== VentoCalendar ===
Contributors: albertogato
Tags: events, calendar, event calendar, event map, openstreetmap
Requires at least: 5.0
Tested up to: 7.1
Stable tag: 1.3.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://ko-fi.com/albertogato

A lightweight and intuitive WordPress event calendar with interactive maps. No Google Maps API key needed.

== Description ==

# 📅 Simple & Lightweight WordPress Event Calendar

VentoCalendar is a powerful yet simple WordPress plugin for creating and managing events. It provides an intuitive interface for adding events with start and end dates, customizable colors, and an interactive calendar view.

Designed to focus on the essentials, VentoCalendar gives you a clean way to manage and display events without unnecessary complexity.

**Free and privacy-friendly plugin:** No ads, no tracking, and no mandatory external services for the core calendar functionality. Your event data stays on your server. Developed with WordPress coding standards and best practices in mind.

= 📅 Calendar Features =

* **Custom Event Post Type** - Dedicated post type for events with all WordPress features
* **Date & Time Management** - Easy-to-use datetime picker for start and end dates
* **Color Coding** - Assign custom colors to events for better visual organization
* **Interactive Calendar** - Beautiful calendar with selectable view type (monthly calendar or event list), with "Add to my calendar" buttons for Google Calendar and Apple Calendar
* **Layout Options** - Choose between basic, compact, or clean layouts to customize the calendar appearance
* **Gutenberg Blocks** - Two custom blocks for displaying calendars and event information
* **Shortcodes** - Flexible shortcodes for displaying calendars and event information anywhere
* **REST API** - Built-in REST API endpoints for custom integrations
* **Responsive Design** - Fully responsive calendar that works on all devices
* **Internationalization Ready** - Fully translatable with .pot file included

= 📍 Event Locations & Optional Maps =

Events can also have a physical location. Add a location or address to your events and optionally display an interactive map.

* **Event Locations** - Add location and address information to your events
* **Optional Interactive Maps** - Display a map for events that have a physical location
* **Map Location Picker** - Select the event location directly on the map
* **Powered by OpenFreeMap and MapLibre** - Open-source, community-driven map technology, with the MapLibre GL JS library bundled locally
* **No Google Maps API Key** - Maps do not require a Google Maps API key or Google account
* **No Geocoding Required** - Choose the map location manually without sending an address to a geocoding service

Maps are completely optional and do not change the way the core calendar works.

= Gutenberg Blocks =

1. **Calendar Block** - Show a monthly calendar view with all your events
2. **Event Info Block** - Display event date and time information using your WordPress date/time formats

= Shortcodes =

* `[ventocalendar-calendar]` - Display full calendar view
* `[ventocalendar-start-date]` - Display event start date
* `[ventocalendar-end-date]` - Display event end date
* `[ventocalendar-start-time]` - Display event start time
* `[ventocalendar-end-time]` - Display event end time
* `[ventocalendar-location]` - Display event location
* `[ventocalendar-address]` - Display event address
* `[ventocalendar-map]` - Display event map

All shortcodes use the date and time formats configured in Settings → General.

= Perfect For =

* Community organizations
* Event venues
* Churches and religious organizations
* Schools and universities
* Business event calendars
* Conference websites
* Workshop and class schedules

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins → Add New
3. Search for "VentoCalendar"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to Plugins → Add New → Upload Plugin
4. Choose the downloaded zip file and click "Install Now"
5. Activate the plugin through the 'Plugins' menu in WordPress

= After Activation =

1. Go to VentoCalendar → Add New Event to create your first event
2. Add event details including title, description, dates, and color
3. Use the Calendar block or shortcode to display events on any page
4. Visit Settings to configure automatic event info display

== Frequently Asked Questions ==

= How do I display the calendar on my website? =

You can display the calendar in three ways:
1. Use the Gutenberg block "VentoCalendar Calendar" in the block editor
2. Use the shortcode `[ventocalendar-calendar]` in any post or page
3. Add it directly to your theme template using `do_shortcode('[ventocalendar-calendar]')`

= Can I customize the date and time formats? =

Yes! The plugin uses the date and time formats configured in your WordPress Settings → General. All events, blocks, and shortcodes automatically use these formats for consistency across your site. To change the formats, simply update them in your WordPress general settings.

= Are the events shown in chronological order? =

Yes, events are automatically sorted by date in the calendar view and REST API responses.

= Can I have multi-day events? =

Absolutely! Events can span multiple days. Simply set different start and end dates, and the calendar will display them as horizontal bars spanning the date range.

= Can I add a location to an event? =

Yes! You can add a location and address to your events. If the event has a physical location, you can also optionally enable an interactive map and select the location directly on the map.

= Do I need a Google Maps API key? =

No. VentoCalendar's optional maps do not require a Google Maps API key or a Google account.

= Does VentoCalendar automatically convert addresses into map locations? =

No. VentoCalendar does not require automatic address geocoding. When using a map, you can select and position the event location directly on the map.

= Are maps required? =

No. Maps are completely optional. If an event does not need a map, simply leave the map disabled.

= Is the plugin translation ready? =

Yes! The plugin includes a .pot file for translations and follows WordPress internationalization best practices. Both PHP and JavaScript strings are translatable.

= Does it work with my theme? =

VentoCalendar is designed to work with any properly coded WordPress theme. The calendar uses minimal, theme-neutral styling that adapts to your site's design.

= Can I customize the event colors? =

Yes! Each event has a color picker that lets you choose any color. Events are displayed with their assigned colors in the calendar view.

= How do I show event information automatically? =

Go to VentoCalendar → Settings and check "Automatically display event information on single event pages". You can choose whether to display the start and end times. Date and time formats use the WordPress general settings.

= Is there an API for developers? =

Yes! The plugin includes REST API endpoints at `/wp-json/ventocalendar/v1/events` for retrieving events.

== Screenshots ==

1. Event edit screen with datetime picker and color selector
2. Monthly calendar view with color-coded events
3. Event modal showing event details
4. Gutenberg blocks in the editor
5. Plugin settings page
6. Mobile responsive calendar view
7. Event edit screen with location, address, and map location picker

== Changelog ==

= 1.3.0 =
* Feature: Added event locations, with location and address fields for each event.
* Feature: Added optional interactive maps for events, powered by OpenFreeMap and MapLibre.
* Feature: Added a map location picker to select the event location directly on the map.
* Feature: Maps do not require a Google Maps API key or an address geocoding service.
* Fix: Corrected a typo in a UI string.

= 1.2.0 =
* Feature: Added Custom CSS field in Settings to allow custom styles without modifying plugin files.
* Tweak: Minor CSS improvements.

= 1.1.4 =
* Improvement: modified algorithm that calculates event positions in the calendar

= 1.1.3 =
* Improvement: modified algorithm that calculates event positions in the calendar
* Tweak: Minor CSS improvements

= 1.1.2 =
* Fix: Resolved timezone-related date offset in Add to Calendar URL generation
* Tweak: Minor CSS improvements
* Tweak: Minor adjustment to calendar bars positioning

= 1.1.1 =
* Regenerated POT file.

= 1.1.0 =
* View type setting added in calendar
* Layout setting added in calendar
* Add to calendar buttons added in calendar
* Adjustments in UI and styles

= 1.0.2 =
* Corrected plugin version in main file and readme.

= 1.0.1 =
* Added Start and End Date columns in the Events admin list.
* Adjusted and improved several UI strings for clarity.

= 1.0.0 =
* Initial release
* Custom event post type with start/end dates
* Color coding for events
* Interactive Vue.js calendar
* Gutenberg blocks for event info and calendar
* Shortcodes for flexible display options
* REST API endpoints
* Fully responsive design
* Internationalization support

== Upgrade Notice ==

= 1.3.0 =
* This update adds optional interactive maps for events, with a map location picker. No Google Maps API key required. No action required.

= 1.2.0 =
* Added Custom CSS field in Settings to allow custom styles without modifying plugin files.

= 1.1.4 =
* Modified algorithm that calculates event positions in the calendar

= 1.1.3 =
* Modified algorithm that calculates event positions in the calendar and minor CSS improvements

= 1.1.2 =
* Fixed timezone-related date offset in Add to calendar URL generation, minor CSS improvements and minor adjustment to calendar bars positioning.

= 1.1.1 =
* Regenerated POT file.

= 1.1.0 =
This update adds new calendar View type and Layout settings, new "Add to my calendar" buttons for Google Calendar and Apple Calendar, and includes UI improvements. No action required.

= 1.0.2 =
Corrected plugin version in main file and readme.

= 1.0.1 =
Added Start and End Date columns in the Events admin list and adjusted some UI strings.

= 1.0.0 =
Initial release of VentoCalendar. Install and start managing your events today!

== Privacy & Compliance ==

VentoCalendar is designed with privacy and WordPress.org guidelines in mind:

= License =

* **GPLv2 or later** - This plugin is free software licensed under the GNU General Public License v2 or later
* Designed to comply with WordPress.org guidelines
* All code is open source and available for review

= No Obfuscated Code =

* All JavaScript, PHP, and CSS code is readable and unminified
* No build process or compilation required
* All source code is human-readable and editable
* No hidden functionality or encrypted code

= No Mandatory External Services =

* **Self-hosted solution** - The core calendar functionality runs on your WordPress server
* No mandatory third-party services or API keys required
* Vue.js library is bundled with the plugin and loaded locally (no CDN)
* MapLibre GL JS library is bundled with the plugin and loaded locally (no CDN)
* No external accounts, subscriptions, or paid services needed for the calendar

= Optional Maps =

Maps are optional. When a map is displayed, VentoCalendar uses OpenFreeMap to provide the map resources.

* No Google Maps API key or account is required
* No address is sent to a geocoding service
* Map coordinates are stored with the event in your WordPress database
* OpenFreeMap is only used when an event map is displayed

= No Advertising =

* The plugin does not display advertisements in the WordPress admin area or on the frontend
* No affiliate links or sponsored content are included
* All core calendar features are available without promotional notices or upsells

= Privacy Friendly =

* The plugin does not collect, track, or transmit user data for analytics or advertising
* No analytics, tracking scripts, or cookies are added by the plugin
* Event data is stored locally in the WordPress database and remains under the site owner’s control
* Optional map resources are loaded from OpenFreeMap when a map is displayed

= What Data is Stored =

* Event information (titles, descriptions, dates, colors) - stored in WordPress database
* Event location and address information, when provided
* Map coordinates and map display settings, when a map is enabled
* Event data remains on your server under your control

== Developer Notes ==

= Architecture =

The plugin follows WordPress Plugin Boilerplate architecture with a centralized loader system. All hooks are registered through the main plugin class.

= REST API =

**Endpoint:** `GET /wp-json/ventocalendar/v1/events`

**Parameters:**

* `start` (optional) - Start date filter (Y-m-d format)
* `end` (optional) - End date filter (Y-m-d format)

**Response:** Array of event objects with id, title, dates, times, color, and permalink.

= File Structure =

* `admin/` - Admin-specific functionality (PHP, JS, CSS)
* `public/` - Public-facing functionality (PHP, JS, CSS)
* `includes/` - Core plugin classes
* `includes/blocks/` - Gutenberg block classes
* `includes/cpt/` - Custom post type definitions
* `languages/` - Translation files

= Development =

No build process required. All files are unminified and ready for direct editing. The plugin uses standard WordPress enqueue functions for all assets.

== Donations ==

This plugin is free. Donations are voluntary support for its development.

If you find VentoCalendar useful, you can support its development here: [Donate via Ko-fi](https://ko-fi.com/albertogato)

== Support ==

For support, feature requests, or bug reports, please visit the plugin's support forum on WordPress.org.

== Credits ==

* Built using [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate)
* Calendar powered by [Vue.js](https://vuejs.org/)
* Interactive maps powered by [OpenFreeMap](https://openfreemap.org/) and [MapLibre](https://maplibre.org/)
