# VentoCalendar

A lightweight and intuitive events calendar plugin for WordPress.

Official development repository for the WordPress plugin:  
https://wordpress.org/plugins/ventocalendar/

Current stable version: **1.1.4**

## About

VentoCalendar is a powerful yet simple WordPress plugin for creating and managing events. It provides an intuitive interface for adding events with start and end dates, customizable colors, and an interactive calendar view.

Key characteristics:

- Custom Event Post Type
- Gutenberg blocks and shortcodes
- REST API endpoints for integrations
- No external services, no tracking, no ads
- Fully self-hosted and GPL licensed

The plugin follows WordPress coding standards and best practices and is designed to work with any properly coded theme.

## Features

- Custom Event Post Type with start and end dates  
- Color-coded events  
- Monthly calendar view and event list view  
- Gutenberg blocks for calendar and event info  
- Flexible shortcodes  
- Built-in REST API  
- Fully responsive  
- Internationalization ready  

## REST API

Base endpoint:

GET /wp-json/ventocalendar/v1/events

Optional parameters:

- `start` — Start date filter (Y-m-d)
- `end` — End date filter (Y-m-d)

Returns an array of event objects with dates, times, color and permalink.

## Development

This repository contains the source code of the VentoCalendar WordPress plugin.

The codebase follows WordPress coding standards and best practices.  

All files are unminified and ready for direct inspection and modification.

Releases published here correspond to the versions available on WordPress.org.

## File Structure

- `admin/` - Admin area functionality
- `public/` - Frontend functionality
- `includes/` - Core plugin classes
- `includes/blocks/` - Gutenberg block classes
- `includes/cpt/` - Custom post type definitions
- `languages/` - Translation files

## Contributing

Contributions are welcome.

- Open an issue for bug reports or feature requests  
- Submit merge requests for improvements or fixes  

Please follow WordPress coding standards.

## License

GPL v2 or later.  
This plugin is free software released under the GNU General Public License.

## Author

Developed and maintained by **Alberto Gato Otero**.  
WordPress.org profile: https://profiles.wordpress.org/albertogato/

Donations: https://ko-fi.com/albertogato
