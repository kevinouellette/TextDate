# Text Date plugin for Craft CMS

This plugin adds a Text Date field to Craft with an optional input mask and support for three date orders (YYYYMMDD, DDMMYYYY, and MMDDYYYY).

## Features
* Optional use of [jQuery input mask] (https://github.com/RobinHerbots/jquery.inputmask).
* Partial support for incomplete date values.
* A textdate Twig filter is included as a replacement for the standard date filter.

## Installation
1.  Upload the textdate/ folder to your craft/plugins/ folder.
2.  Go to Settings > Plugins from your Craft control panel and enable the Text Date plugin.

## Usage
When creating a new Text Date field, be sure to select your preferred date order and an optional input mask separator character. Leave the separator field blank to turn off masking.

While entering dates, you can leave out unknown segments by replacing them with 9s (ex., 2015-02-99 for an unknown day in February 2015 or 2015-99-99 when you only know the year).

Twig's standard date formatting filter won't work with incomplete values, so a textdate filter has been included to handle ISO 8601-formatted date strings.

To output a field which may or may not have incomplete values in it, you can specify up to 3 fallback formatting options:

```
{{ entry.myField|textdate('F j, Y', 'F Y', 'F j', 'Y') }}
```

The filter cycles through your formatting options in order until it finds one it can use. If it doesn't have enough date information to use any of them, a blank string is returned.

Here are some expected results from the tag above:

* 20150223 = February 23, 2015
* 20150299 = February 2015
* 99990223 = February 23
* 20159923 = 2015
* 99999923 = 

*Note: Due to the way partial dates are processed, the unlikely scenario of outputting multiple variations of the same date segment won't work in a fallback.*
