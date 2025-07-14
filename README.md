# Curatescape
This plugin provides the core functionality for Curatescape mobile app projects (and others), adding a variety of features to facilitate location-based narrative storytelling and walking tours. Learn more at [curatescape.org](https://curatescape.org/). Curatescape is a project of the [Center for Public History + Digital Humanities](https://csudigitalhumanities.org/) at Cleveland State University.

## Overview
### Item Type & Metadata Elements
The first time you activate this plugin, it will install a new [Item Type](https://omeka.org/classic/docs/Content/Item_Types/) called *Curatescape Story*. This Item Type employs multiple new metadata [Elements](https://omeka.org/classic/docs/Admin/Settings/Item_Type_Elements/), including *Story*, *Subtitle*, *Lede*, *Street Address*, *Access Information*, *Official Website*, and several more. These Elements form a schema for location-based narrative storytelling.

### Item Display
Through various user-configurable settings, items using the *Curatescape Story* Item Type are displayed in a consistent, thematically-appropriate layout that uses semantic typographic design while fitting in with the look and feel of your chosen theme. Geolocation maps for each item include accessibility enhancements, e.g. the addition of text-equivalent content using the *Street Address* and/or *Access Information* elements.

### File Display
In compatible themes, media files can be presented in multiple customizable, accessible media layouts and displayed in an interactive [PhotoSwipe](https://photoswipe.com/) lightbox, with various improvements to the display of audio, video, and PDF documents (PDFs can be displayed inline in Gecko- and Chromium-based browsers including Chrome, Edge, Opera, and Firefox).

### Tours
Curatescape adds a new content type called Tours. Each tour is a curated selection of items, placed on a map, and presented with additional metadata and contextual information. Any location-enabled *Curatescape Story* Item can be added to any number of Tours. Tours can be thematic or geographically-based and are presented alongside an interactive map to assist users with navigation.

### Sitewide Enhancements
Curatescape also enables a variety of sitewide enhancements, including the addition of a simple, cacheable JSON API for mobile app and/or headless data exchange of select content; a custom RSS feed designed specifically for narrative content; custom meta tags for rich social media previews; custom shortcodes (see below); Google Analytics integration; admin bar enhancements; and much more. View the plugin configuration page for all options (there are _a lot_ of them).

## Requirements
The [Geolocation plugin](https://omeka.org/classic/plugins/Geolocation/) is required. 

Any server that meets the [Omeka system requirements](https://omeka.org/classic/docs/Installation/System_Requirements/) should be fine. If you have server compatibility issues, please let us know on the [Curatescape Forum](https://forum.curatescape.org/).

## Compatibility
This plugin should work with any Omeka Classic theme, though features and level of integration may vary. For example, certain media-specific features may not be available for themes that already use filters to enhance the presentation of media files (e.g. The Daily, Center Row, and Big Picture). Efforts have been made to accommodate all [publicly-available Omeka Classic themes](https://omeka.org/classic/themes/), particularly those created by the Omeka Team, which generally share a common approach in terms of markup and layout. If you have a question or issue relating to theme compatibility, please let us know on the [Curatescape Forum](https://forum.curatescape.org/).

### Recommended Theme
For additional features, [vetted accessibility](https://curatescape.org/accessibility), and improved performance, use the [Curatescape Echo theme](https://github.com/CPHDH/theme-curatescape-echo).

## Shortcodes
Curatescape adds the following [shortcodes](https://omeka.org/classic/docs/Content/Shortcodes/).
### App Store Buttons
Uses configured plugin settings for mobile apps to create button-styled links for iOS and Android app stores. With the default CSS classes, the links will be presented in a flexible CSS container that adjusts the layout based on available space.
#### Usage
```
[curatescape_app_buttons icons="true"]
```
#### Parameters
| Option | Type | Description |
| -------- | ---- | ----------- |
| buttonclass | string | Replace the default CSS class for the button links. Omit to use the default button link styles. Default class: `curatescape-shortcode-button` |
| containerclass | string | Replace the default CSS class for the container. Omit to use the default container styles. Default class: `curatescape-shortcode-app-buttons` |
| icons | boolean |  Use "true" to include vector icons for App Store and Google Play. |
| platform | string | Use "ios" for the iOS link only or "android" for the Android link only. Omit to include both. When using this option, it is generally recommended to override the default button link and container classes. |

## Support & Troubleshooting
### User Forums
Use the [Curatescape Forum](https://forum.curatescape.org/) to get support with Curatescape-specific issues. For general Omeka support, please use the [Omeka Forum](https://forum.omeka.org/).

## Frequently Asked Questions
### Do I need to pay for Curatescape?
No. The Curatescape plugin and themes are free and open source. We offer *optional* mobile app and hosting services at affordable rates, which helps us to recover some of the costs associated with the ongoing development of Curatescape. Learn more about our services at [curatescape.org](https://curatescape.org/).

### Does this single plugin replace others like Tour Builder, Curatescape JSON (Mobile JSON), Curatescape Admin Helper (Send To Admin Header), and Super RSS?
Yes, those plugins are all deprecated and their functionality has been moved into the standalone Curatescape plugin, which includes performance improvements, additional features, and greater theme compatibility. If you are migrating an existing Curatescape project, you should deactivate and remove the deprecated plugins _after_ ensuring you have installed any outstanding plugin updates. If you have existing Tours, take care to uninstall Tour Builder only _after_ installing the Curatescape plugin to ensure your tours are preserved. The only other plugin you need is [Geolocation](https://omeka.org/classic/plugins/Geolocation/).

### Does Curatescape have an accessibility statement?
Yes, please refer to the [Curatescape accessibility statement](https://curatescape.org/accessibility) to review our report and download the Voluntary Product Accessibility Template (VPAT). Note that our accessibility report applies only to the Curatescape plugin and themes and not to other themes or plugins, nor to Omeka Classic itself. See [Omeka Classic accessibility statement](https://omeka.org/classic/docs/GettingStarted/Accessibility_Statement/) for additional information.

### How can I upload a file for use as a custom meta image?
The Curatescape plugin includes an option to define a default meta image for search engine and social media previews. This option accepts a URL. As a convenient alternative, theme developers may add the following option to their theme's `config.ini` file so that the image file can be easily changed in theme settings.

```
curatescape_meta_image.type = "file"
curatescape_meta_image.options.label = "Meta Image"
curatescape_meta_image.options.description = "Upload a custom meta image file for use when there is not a content-related image available (for example, on the homepage and browse pages). Recommended dimensions: 1200px × 630px (1.91:1)."
curatescape_meta_image.options.validators.count.validator = "Count"
curatescape_meta_image.options.validators.count.options.max = "1"
```

### Why don't the File Display Settings change anything in my theme?
Each theme is created differently. Some themes – such as Big Picture, Center Row, and The Daily – already have their own custom file viewers with their own display options. You can configure the plugin setting for Media Gallery to "None (use theme)" and turn off the PhotoSwipe-related options if you are using a theme that has a built-in media viewer. Other options should still work as intended.

### Can I use Item Types other than Curatescape Story?
Technically yes, you can use any other item type, though some features are specific to the Curatescape Story item type. Projects with have mobile apps should generally only use the Curatescape Story item type. This will ensure a consistent presentation and user experience. Any project that is focused strongly on location-based storytelling as a whole should probably use the Curatescape Story item type for all items.

### Do all of my Items need to have Geolocation data?
Technically no, but using Geolocation to add your item to the map is strongly recommended for Curatescape Story items. Curatescape is designed specifically for location-based storytelling. For projects with mobile apps, the only content that will appear in the app are items that use the Curatescape Story item type and have Geolocation data.

### My project is primarily centered around publishing archival metadata records, should I still use Curatescape?
You can certainly use Curatescape to add some narrative content to your items database. However, depending on how your content is structured, you might consider using the Exhibit Builder instead of – _or in addition to_ – Curatescape to better curate traditional metadata-driven archival content. See related question below.

### How is Curatescape different from – or similar to – Exhibit Builder?
Exhibit Builder is intended for curating discrete item records into multi-page exhibits, whereas Curatescape treats _each item_ as a sort of single-page exhibit. Curatescape Story items are centered around a location-based narrative, where multiple files are added at the item level to support the text. Any item that has Geolocation data and uses the Curatescape Story item type may be added to a Tour, which is similar to an Exhibit in that it is a curation of related items. Tours are comparatively simpler and centered primarily on geospatial relationships; the single-page, map-based presentation is designed specifically for curating location-based narratives into easily digestible walking, driving, and cycling tours, which are compatible with Curatescape mobile apps.

### How can I add a new language translation for this plugin?
If you have a translation prepared already and are comfortable making pull requests, please do so and we will review your submission as soon as possible. Use the provided English language `.pot` template in the `/languages` directory. [Poedit](https://poedit.net/) is recommended for general purposes and for compiling `.mo` files. To generate a new or updated `.po` file, we recommend installing [gettext](https://formulae.brew.sh/formula/gettext) via [Homebrew](https://brew.sh/) (`brew install gettext`) and then running a command like the following from the root of the plugin directory: 
```
find . -iname "*.php" | xargs xgettext -k__ --from-code=UTF-8 -o languages/NEW.pot *.php
```