# Text Date plugin for Craft CMS

This plugin adds a Text Date field to Craft with an optional input mask and support for three date orders (YYYYMMDD, DDMMYYYY, and MMDDYYYY).

## Features
* Choose your own separator character in the settings, or leave it blank to turn off the mask
* Support for incomplete date values.
* A textdate Twig filter is included as a replacement for the standard date filter.

## Installation
1.  Upload the textdate/ folder to your craft/plugins/ folder.
2.  Go to Settings > Plugins from your Craft control panel and enable the Text Date plugin.

## Usage
When creating a new Text Date field, be sure to select your preferred date order and an optional input mask separator character.

While entering dates, you can leave out unknown segments by replacing them with 9s (ex., 2015-02-99 for an unknown day in February 2015 or 2015-99-99 when you only know the year).

Text Date values are saved to the database using the VARCHAR(8) data type instead of DATETIME. Because of this, Twig's standard date formatting filter won't work. A textdate filter has been included to handle ISO 8601 date strings.

To output a field which may or may not have incomplete values in it, you can specify up to 3 fallback formatting options:

```
{{ entry.myField|textdate('F j, Y', 'F Y', 'F j', 'Y') }}
```

The filter cycles through your formatting options in order until it finds one it can use. If it doesn't have enough date information to use any of them, a blank string is returned.

Here are some expected results from the tag above:

* 2015-02-23 = February 23, 2015
* 2015-02-99 = February 2015
* 9999-02-23 = February 23
* 2015-99-23 = 2015
* 9999-99-99 = 
