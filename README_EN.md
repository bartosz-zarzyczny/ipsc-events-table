# IPSC Events Table

This repository contains a small WordPress plugin file `ipsc-events-table.php` that renders an event table with filters and pagination.

## What it does

The plugin displays a table of events from a custom `tec_events` table, joined to WordPress posts and taxonomy data. It provides:

- event list display with columns:
  - Event name
  - Start date
  - End date
  - Location
  - Tags
  - URL
- filters for:
  - text search across title, post content, dates, timezone and event tags/categories
  - date range filtering
  - location selection (województwo / zagranica)
  - tag selection
- pagination with 30 records per page
- expandable event content modal
- shortcode support for embedding in a page or post

## Installation

1. Copy `ipsc-events-table.php` into your WordPress site directory.
2. Include it from a plugin or theme file if not already loaded.
3. Use the shortcode `[ipsc_events_table]` in a page or post to display the table.

## Configuration

### Custom table prefix

The plugin supports a configurable custom table prefix for the custom events table.

Edit the prefix in one place near the top of `ipsc-events-table.php`:

```php
$ipsc_table_prefix = 'prec'; // custom table prefix: set this value once
```

If the installation uses a different prefix, update this value and the plugin will use it when resolving the custom events table name.

### Default date behavior

On first page load without explicit date filters, the table will default to:

- `Date from` = current date
- `Date to` = maximum `end_date` found in the events table

When the user submits custom date values, those are used instead.

## Code overview

### Main functions

- `ipsc_styles()`
  - returns inline CSS for the table, filters, and modal.

- `ipsc_events_table()`
  - builds the filter form, query, and table output.
  - reads GET parameters for `ipsc_search`, `ipsc_date_from`, `ipsc_date_to`, `ipsc_location[]`, `ipsc_tags[]`, and pagination `ipsc_page`.
  - generates SQL with filtering logic and pagination.
  - renders HTML output and hidden modal content.

- `ipsc_events_table_shortcode()`
  - returns output from `ipsc_events_table()` for use with shortcode `[ipsc_events_table]`.

## Shortcode

Place this shortcode into any page or post:

```text
[ipsc_events_table]
```

## Notes

- The code uses `wpdb` for database access and standard WordPress sanitization.
- The custom event table is assumed to be named `${prefix}tec_events`, where `${prefix}` is set by `$ipsc_table_prefix`.
- The plugin expects WordPress taxonomy tables for `tribe_events_cat` and `post_tag` data.

## Additional changes

- Added a top support banner with social media links to Instagram, YouTube, Facebook, and X.
- Added a footer linking to the GitHub repository.
- Added license files `LICENSE_PL.md` and `LICENSE_EN.md` containing an "as is" disclaimer, liability exclusion, and a requirement not to remove social media and GitHub links without the author's permission.
