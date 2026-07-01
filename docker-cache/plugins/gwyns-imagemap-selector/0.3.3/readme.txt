=== Gwyn's Imagemap Selector ===
Contributors: GwynethLlewelyn
Donate link: http://gwynethllewelyn.net/
Tags: imagemap
Requires at least: 3.0
Tested up to: 4.5
Stable tag: trunk

Uses shortcodes to display imagemaps with categories of posts.

== Description ==

Gwyn's Imagemap Selector uses shortcodes to define imagemaps, assign an image to it, and automatically make queries on the WordPress database to extract the appropriate links.

Includes some fixes by Tom Rusko. Thanks, Tom!

== Installation ==

1. Download plugin from http://wordpress.org/extend/plugins/gwyns-imagemap-selector/
2. Go to Plugins > Add New
3. Select the Upload tab
4. Upload gwyns-imagemap-selector.zip

Or install it simply from within your WordPress admin panel.

Then go to any page, post, or text widget and use the following syntax:

`[imagemap category="Uncategorized" img="/your/link/to/your/image"]
[area]1,2,3,4[/area]
[area]5,6,7,8[/area]
[/imagemap]`

See complete syntax on the **Other Notes > Usage** page.

This plugin is also translated into Portuguese. More translators are welcome :)

== Frequently Asked Questions ==

= I only get the first 10 posts retrieved, but I have more than 10 areas in my imagemap =

Just add **nopaging=true** to your **[imagemap]** tag. By default, WordPress will respect the post limit set on **Reading Settings > Blog pages show at most** (which is 10 by default).

= Queries fail when using multiple parameters with & =

This is because WordPress will pretty much encode all characters safely before saving an article. An attempt has been made to deal with the encoded ampersands, but it will only work with PHP 5.1.0 or greater (if you're using an earlier version of PHP, you should be upgrading it anyway!).

= I want to have a series of blog articles displayed in a very specific, strict order inside the imagemap. How do I manage that? =

1. Make sure that all their dates are correctly set in sequence, since the default order is by date (you can then specify ascending or descending, whatever is appropriate).
2. If you wish to keep the date but change the order, you have another option. You can add a Custom Field, say, Position (to add custom fields to any post under WP 3.1+, you need to click on the **Screen Options** dropdown and check that option). Then fill up this field with the order you wish (1 for the first post, 2 for the second, and so forth. It's fine to skip numbers and/or have the same number twice, WP will still return the correct order).

Then use the following syntax on the **[imagemap]** tag:

**[imagemap query="cat=4&meta_key=Position&orderby=meta_value_num&nopaging=true&order=ASC"]**

The number for the category is of course the one used to classify your articles (you can add multiple categories there, too, separated by a comma).

= I don't want the image to show its id name while hovering outside of any area =

This yellow popup is browser-dependent (but pretty much every browser today has implemented it). To fix it, since 0.3.2, you can leave the **alt** and/or **title** tags empty on the **[imagemap]** shortcode. Technically, leaving **alt** off is an HTML violation.

= How do I edit a generated imagemap again? =

Sorry, that's beyond my ability to do. The original source code for the imagemap creator has been done by [Adam Maschek](http://code.google.com/p/imgmap/), not by me, and I have no idea how to change it to do just that. What I do is to simply copy & paste the existing coordinates on the top part (instead of re-selecting everything). This is a pain if you have many entries, but far better than doing everything from scratch again!

= How to change the popup (restyling it to another colour, font, etc.) when a user hover over an area defined by the imagemap (**not** the Ajax hover function, the small yellow box)?  =

It's not possible. The small hovering popup is not created by the plugin at all: it's browser-dependent. Remember, imagemaps are ancient HTML features, they predate Javascript and even CSS by many years. What all modern browsers do is to read the alt/title tags and generate this "fake" popup (which is built-in into the browser), but there is no control about how it's styled.

If you really want to change that popup, you will need to use a Javascript/Flash/Java-based plugin instead. There are a few available. This plugin avoids Javascript/Flash/Java on the frontend — deliberately so, for those few browsers which have Javascript/Flash/Java turned off. HTML imagemaps will always work, since they are pure HTML. But, yes, they have their limitations!

Of course, the Ajax hover box can be fully styled from the Settings tab.


== Screenshots ==

1. Current options panel (under WP 3.2 Beta 2) showing popup CSS code
2. Web-based imagemap creator (with generated WP shortcodes for imagemap)
3. Uploading media files to the imagemap creator

== Changelog ==

= 0.0.1 =
* First release

= 0.0.2 =
* Added checkbox for debugging

= 0.0.3 =
* Allows multiple categories/tags
* Fixes paging issues

= 0.0.4 =
* Fixed bug with nopaging
* Sanitise excessive &amp; when using the query parameter (only works with PHP > 5.1.0)

= 0.0.5 =
* Added full translation support and added Portuguese translation

= 0.0.6 =
* Patched slight bug that did make translations unworkable

= 0.1.0 =
* Created new admin panel, preparing for adding extra functionality

= 0.2.0 =
* Added the ability to have a floating pop-up with Ajax for each imagemap

= 0.2.1 =
* Fixed non-fully-HTML-compliant AJAX links

= 0.2.2 =
* Reverted fix, because it breaks everything just to remain fully compliant!

= 0.2.3 =
* Slight changes on the internal popup code
* Added CSS textarea for restyling internal popup

= 0.3.0 =
* Adds Javascript imagemap creator (http://code.google.com/p/imgmap/)
* Fix tiny bug when adding an invisible div for popups (start with display: none)

= 0.3.1 =
* Integrates imgmap creator with the WordPress Media Library popup
* Fixed bug with multiple imagemaps on the same post

= 0.3.2 =
* If the **alt** or **title** tags are un-set on the shortcode, then the resulting image will not have any title/alt, which will make most browsers omit the small yellow popup (which is browser-dependent and not under JS control)
* Added warning that this plugin won't work with tools that dynamically resize images (like Jetpack's Photon) for the purpose of mobile viewing
* Added support for the **target** attribute, both from the imagemap creator interface as well as inside shortcodes
* When area URLs are left empty for some reason, the link is set to href="#", which might be used for some Javascript to do fancy things
* Allows anyone with read access to use the plugin (it was limited to administrators), but only administrators can change settings
* Switched tab order. The "Main" tab was renamed to "Settings" and is now the last option; the plugin will open with the page for creating new imagemaps. This is more logical, specially for non-admin users

= 0.3.3 =
* Added code fixes by Tom Rusko to support WP 3.5's new media library, LTR support, and stricter non-admin permissions. Thanks Tom!

== Upgrade notice ==

Adds Javascript imagemap creator (http://code.google.com/p/imgmap/) and integrates imgmap creator with the WordPress Media Library popup.

Fix tiny bug when adding an invisible div for popups (start with display: none)

Fixed bug with multiple imagemaps on the same post

== Usage ==

Gwyn's Imagemap Selector uses shortcodes to define imagemaps, assign an image to it, and automatically make queries on the WordPress database to extract the appropriate links.

There are two basic usages of this plugin. The first is if you know exactly which URLs are linked to each area of the imagemap. This is appropriate for menus (and very likely used on a widget) that will not change much over time. This uses the "direct" approach and requires URLs to be explicitly named on each area; thus, it's quite similar to directly placing the imagemap HTML inside the post/page/widget (the only advantage of using the shortcode is to get automatic ids).

The second variant makes a query on the WordPress database and returns the appropriate permalinks for each post. You can query by category, tag, or even add a free query (it will be passed to WP_Query so the same syntax applies; see http://codex.wordpress.org/Function_Reference/WP_Query). The order of permalinks thus retrieved will dynamically be assigned to each area (in the order those are written). If there are more areas than permalinks, the remaining areas will be ignored (the reverse situation is undefined). Category and tag queries can be made by name or id and can be retrieved either in the descending (default) or ascending order.

A few extra parameters are available to add names, titles, ids, and classes. If those are omitted, this plugin will provide "best effort" alternatives to comply with HTML guidelines. This also allows for extra styling.

The overall syntax is:

`[imagemap (category=["category id"|"category name"] 
			| category_name="category name"
			| tag=["tag id"|"tag name"]
			| tag_name="tag name"
			| query="a WP_QUERY string") 
				  | direct=[0|1] 
		img="/your/url/to/image"
		title="Image title" 
		order=["DESC"|"ASC"]
		nopaging=["true"|"false"]
		map="imagemap name" 
		id="HTML id" 
		class="CSS style"
		alt="Alt text for the image"
		popup="true|false"
		thumbnail="true|false"
		excerpt="true|false"]
[area shape=["rect"|"poly"|"circle"]
	url="/direct/link/for/this/area 
	alt="name for this area"
	class="CSS style"
	target=["_self"|"_blank"|"_top"|...]
	title="name for this area"]coord1,coord2,coord3,...,coordN[/area]
[/imagemap]`

Thus, imagemaps can be either in direct mode (direct=1) or in query mode (omit the direct clause). When in query mode, you can use query by categories (id or name/slug), tags (id or name), or a free query that complies with WP_Query types of queries. Queries by category or tag can be made in descending (default; can be omitted) or ascending order (this parameter is ignored when using a WP_Query-compliant query instead). The **img** parameter is mandatory. **map**, **id**, **class**, and **alt** are all optional and a best effort to fill them with plausible values will be provided.

Queries are usually paged, i.e. they will respect the post limit set on **Reading Settings > Blog pages show at most**. To override, use **nopaging="true"**.

Each area has just a mandatory section, the one between [area][/area] tags. Default shape is a rectangle (shape="rect") and this would require 4 coordinates (coordinate numbers are neither checked nor validated). For direct mode, an additional url for each area has to be supplied (inside the [area] tag). **alt**, **class**, and **title** are optional and they will be filled automatically with plausible values if omitted.

Multiple categories/tags should work (as well as excluding categories/tags); note that **category=Uncategorized** will work (even though it should be **category_name=Uncategorized**) but **category=first,second,last** will not (use **category_name=first,second,last** instead).

If you wish, you can get a hovering popup for the imagemap that shows the article linked to it. Use **popup=true** and you can optionally specify a thumbnail and/or just show the excerpt. CSS styling is done via the **class** parameter.

You can also specify your own AJAX handler and call it remotely instead of using the built-in handler. All parameters are passed via **GET**, and you should at least pass a post ID (parameter **id**). Optionally you can receive the CSS class for styling on the parameter **class**. **excerpt** and **thumbnail** will be set to true if the user specifies those parameters on the shortcode tag for the imagemap.

Also, several imagemaps with the same name in the same post/page/widget haven't been tested either. In theory you could define an imagemap **without** an associated image, and insert images and change manually the imagemap for each, so that the same imagemap is used across several images in the same page. None of this was tested.

Imagemaps can be conveniently created from the Imagemap Selector page, which uses [Adam Maschek's imgmap Javascript library](http://code.google.com/p/imgmap/) to provide an interactive, Web-based imagemap creator.

The plugin will now work for anyone with read capabilities (and not only administrators), but only administrators have access to the "Settings" tab.

Warning: this plugin will not work with any automatic image-resizing mechanisms (e.g. Jetpack's Photon or similar tools, specially those for dynamic/fluid themes which resize images for mobile viewing). This is a limitation of the **imagemap** HTML command, which is very simple and basic, and relies upon absolute values relative to an image's dimensions, which are supposed to be known in advance. There are a few techniques to rewrite imagemaps (basically recalculating the selection areas based on the redimensioned image), but these are a bit pointless for the purpose of this plugin, which totally avoids Javascript (for browsers which have it turned off), and there are already better, Javascript/Flash/Java-based solutions which allow clickable areas on resized images.

== To-do ==

* Add a "limit" clause
* Save imagemaps (is that truly useful? TBD)