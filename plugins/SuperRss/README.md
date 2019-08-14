# SuperRss
A plugin that adds customized RSS output to Omeka sites. Made especially for sites using the Curatescape framework (but should work fine with any Omeka site)

## Functionality
This plugin takes Omeka item records and outputs them in an RSS format that is typical of blogs and news articles, including a featured image; title, subtitle, lede, and story fields; and a custom "read more" link. This is arguably a bit more approachable than the metadata-centered kitchen sink output that is included in Omeka's default RSS feed.
### Item Fields
- **Title** (Dublin Core:Title)
- **Subtitle** (either a second instance of Dublin Core:Title or a custom Item Type Metadata field called Subtitle, such as is used in Curatescape sites)
- **Lede** (a custom Item Type Metadata field called Lede, such as is used in Curatescape sites)
- **Story** (either a Dublin Core:Description or a custom Item Type Metadata field called Story)
### Item Image
The **first image** in the item record is included at the top of the feed item. 
### Item Footer
The footer for each RSS item may optionally be appended with **social media** and **app store** links. If the item includes **additional media files**, the optional "read more" link can be configured to include additional details.
