=== Subscribe to Category ===
Contributors: dansod, VanDeStouwe, FREEZhao, tariqlu786
Tags: subscribe to post, subscribe category, subscribe to taxonomy, subscribe to news, subscribe
Requires at least: WP 5.0
Tested up to: 6.1
Requires PHP: 7.0
Stable tag: 2.7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Subscribe to posts within a certain category or categories.

== Description ==
This plugin lets a user subscribe and unsubscribe to posts within a certain category or categories.
Subscribers will receive an e-mail notification with a link to the actual post.

For a complete overview of STC read: <a href="https://vandestouwe.com/stcmanual">STC's User Manual version 2.6.12</a>.
This document also includes a Step by Step Get Started chapter.

The following features are available

*   E-mail notification template with placeholders
*   Custom Posts
*   Custom Taxonomies
*   Enhanced keyword search in Title, Content, Tags and Taxonomies
*   E-mail notification on timer or daily basis
*   SMS notification on timer or daily basis
*   For newly created and / or updated Posts
*   Works with classic editor and the block editor
*   Export and Import subscribers
*   Implementation by widget and / or by shortcode

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `subscribe-to-category` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress Admin
3. Inspect and Save your settings with the dashboard menu item 'STC Subscribe > Settings'.
4. Create a page and add shortcode [stc-subscribe] or use the STC Widget to display STC form subscription.
5. Inspect /create / update subscribers with the dashboard menu item STC Subscriber > Subscribers

= Shortcode Attributes =
'category_in' - Use this attribute if you only want one or several categories to be available for subscription. Value to be entered is the name of the category. Note that only level 0 (no parent) values can be entered.
'category_id_in' - The difference to above is to use the category ID instead of category name.
'category_not_in' - Use this attribute if you want to exclude categories to be available for subscription. Value to be entered is the name of any category.
'category_id_not_in' - The difference to above is to use the category ID instead of category name.
For the above attributes you can use a comma sign to separate multiple categories, like [stc-subscribe category_in="news, article"].

'hide_unsubscribe' - hide_unsubscribe="true" disables the unsubscribe feature but only if the shortcode attribute is also enabled at the admin setting
'treeview_folded' - treeview_folded="false" unfolds the category / taxonomy tree view by default
'treeview_enabled' - treeview_enabled="false" displays the categories / taxonomies as one list ignoring hierarchy. Please note that only the selected categories are in or out
'keyword_search' - keyword_search="on" enables the search for keywords in new or updated selected post types. Default is keyword_search="off"
'subscriber_notification' - subscriber_notification="true" enables email notification on an individual per subscriber basis. Default is subscriber_notification="false"
'mobile_phone' - mobile_phone="on" enables the SMS Notification function. This will send SMS Notifications just like the the E-mail notifications for new or updated posts.

= Filter and hooks =
Following filters and hooks can be used for customizing the email message.

`<?php
// FILTERS
// Parameters: $value
add_filter( 'stc_message_length_sum_of_words', 'stc_message_length_sum_of_words', 10, 1 ); //set return value to a negative number to show the full content


// Parameters: $value, $post_id, $subscriber_post_id
add_filter( 'stc_message_title_html', 'my_stc_message_title_html', 10, 3 );
add_filter( 'stc_message_link_to_post_html', 'my_stc_message_link_to_post_html', 10, 3 );
add_filter( 'stc_message_unsubscribe_html', 'my_stc_message_unsubscribe_html', 10, 3 );
add_filter( 'stc_message_subscribe_html', 'my_stc_message_subscribe_html', 10, 3 );

// Parameters: $email_adress, $email_subject, $email_contents, $email headers
// Return: $email_address if email address still needs to be send by STC else an empty ""
add_filter( 'stc_filter_wp_mail', 'my_stc_filter_wp_mail', 10, 4 );

// HOOKS
// Parameters: $post_id, $subscriber_post_id  
add_action( 'stc_before_message', 'my_stc_before_message', 10, 2 );
add_action( 'stc_before_message_title', 'my_stc_before_message_title', 10, 2 );
add_action( 'stc_after_message_title', 'my_stc_after_message_title', 10, 2 );
add_action( 'stc_before_message_content', 'my_stc_before_message_content', 10, 2 );
add_action( 'stc_after_message_content', 'my_stc_after_message_content', 10, 2 );
add_action( 'stc_after_message', 'my_stc_after_message', 10, 2 );

// Parameters: $subscriber_post_id, $categories, bool $all_categories
add_action( 'stc_after_update_subscriber', 'my_stc_after_update_subscriber', 10, 3 );
add_action( 'stc_after_insert_subscriber', 'my_stc_after_insert_subscriber', 10, 3 );

// Parameters: $subscriber_post_id, 
add_action( 'stc_before_unsubscribe', 'my_stc_before_unsubscribe', 10, 1 ); // runs before deleting a subscriber from database
add_action( 'stc_before_subscribe', 'my_stc_before_subscribe', 10, 1 ); // runs before adding a subscriber to the database


/**
 * Example for adding featured image to STC email
 */
function my_stc_after_message_title( $post_id ){
	echo get_the_post_thumbnail( $post_id, 'thumbnail' );
}
add_action( 'stc_after_message_title', 'my_stc_after_message_title', 10, 2 );

?>`

= Optionally but recommended =
As WordPress Cron is depending on that you have visits on your website you should set up a Cron job on your server to hit http://yourdomain.com/wp-cron.php at a regular interval to make sure that WP Cron is running as expected. In current version of Subscribe to Category the WP Cron is running once every hour, that might be an option that is changeable in future versions. 
Therefore, a suggested interval for your server Cron could be once every 5 minutes. 

== Frequently Asked Questions ==
= Can STC insert extra HTML content in the notification e-mail =
Yes, by using the available hooks to insert (on 6 spots) HTML contents in the notification e-mail But it requires programming skills to hook on to STC. Since version 2.4.9 this is possible in a much more sophisticated way. See for the places where you can insert HTML screenshot-10.jpg

For a fresh start the easy way to is delete all the HTML E-Mail templates (published and trashed). Then STC will automatically setup an editable STC Demo E-mail Template like in screenshot-11. Do not forget to check this template in the STC Subscribe -> Settings. 

Or follow the 3 steps below.
step 1: STC Subscribe->Notifications->Add New notification to create a new e-mail notification template. 
Step 2: Edit the post best in block-editor using a single html block. All styling must be within each HTML element. Remember not all email providers handle all HTML styles. Use the placeholders like {{post_title}},{{post_content}},{{justify}} and {{unsubscribe}}. For more details look in Chapter 5 of STC's User Manual
step 3: Enable the required post template in the STC admin settings.

= Can the STC Widget handle attributes =
Yes, from version 2.4.16 the settings area of the widget has a field to add attributes. The format is the same as used in the short code.

= Can STC handle Custom Post Types =
Yes, from version 2.4.1 it is possible to link STC with Custom created public Post Types. You can select the CPT's on the STC admin settings page

= Can STC handle custom Taxonomies =
Yes, from version 2.3.1 it is possible to link STC with Custom created Taxonomies. You can activate these within STC on the STC admin settings page.

= Why can I only have level 0 categories / taxonomies for the "category in filters"
This is because if you select a child category to filter in, the treeview cannot start in the middle of nowhere. However, when the attribute 'treeview_enabled="false"' is active it is possible to select a child without a parent due the fact that they are listed as a single category / taxonomy.

= How can I have the categories and taxonomies listed instead of a tree view
You can have a category / taxonomy as a list by adding the shortcode attribute treeview_enabled="false". Please note that when the attribute is active the category / taxonomy "in filters" ignore the hierarchy, resulting in exactly the list as given in the "in filter" attribute.

= What should the format for importing subscribers be? =
STC can only handle tab delimited files because the categories are separated with commas from each other.
Excel gives (amongst others) you the possibility to export the worksheet as an tab delimited txt file.

== Usage ==
For a complete overview of STC read: <a href="https://vandestouwe.com/stcmanual">STC User Manual</a>

== Screenshots ==

1. Settings page showing the STC menu structure and some pointers
2. With Bootstrap framework.
3. Without Bootstrap framework, override and add your own CSS.
4. When resend post is enabled in settings there is a new option available when editing a post.
5. When resend post and Gutenberg editor settings are both enabled, resend options are available in the Gutenberg document tab
6. Taxonomy edit possibility
7. STC Subscribers list with one custom taxonomy called "Taxonomies"
8. Subscribe as created by the shortcode (in treeview mode)
9. Subscribe as created by the shortcode (in normal list mode)
10. Notification e-mail with all 6 HTML insert section active
11. E-mail Notification template to get started
12. Setting Widget Attributes

== Changelog ==
= 2.7.4.
* fixed some php warnings that popped up using the latest PHP versions

= 2.7.3.
* added add_filter feature for users that want to send bulk emails
* small bug fixes

= 2.7.1.
* fixed double "WP" call for $_GET receiving subscribe notification url.

= 2.6.13.
* fixed multiple subscribers in Approval state when refressing the subscription page/widget

= 2.6.12.
* added {{post_featured_image_url}} and {{post_author}} placeholders

= 2.6.11.
* Fixed Approval Waiting message to become translatable with WP

= 2.6.10.
* added Create STC metafields for posts added by others not following WP save post protocoll

= 2.6.9.
* added action-xhr="#" to form satisfy amp error: missing tag

= 2.6.8.
* Moved setting post status from the end of the send cycle to the beginning but leaving the sent time back at the end. 

= 2.6.7.
* Fixed small problem with rescheduling hourly timer
* Moved setting post status from the end of the send cycle to the beginning
* Added set_time_limit(0) to avoid triggerring the maximum execution time
 
= 2.6.6.
* Fixed problem with scheduled post not processed by STC

= 2.6.5.
* Initialization error when changing email address field with all categories checkbox selected
* two small bug fixes

= 2.6.4.1
* textmagic files missing on version 2.6.4 (only needed on sms notofications)

= 2.6.4 =
* corrrected typo in text of confirmation e-mail to subscribers
* fixed added "stc-" to function name to avoid conflict in name of function "general_admin_notice()" with plugin "quiz-maker"

= 2.6.3 =
* added possibility to use jet smart filter checkbox settings as subscription categories.
* buf fixes

= 2.6.2 =
* On import subscribers changed seperator from ", " to "," and allowing for subscribers with zero assigned Taxonomies
* Simplified Import and Export so it can now cope with custom taxonomy groups
* Text changes to allow for *.txt and *.csv files on import subscribers
* STC Subscription CSS changed theme ul style causing problems for some themes 
* minor bug fixes

= 2.6.1 =
* Added admin setting to enable the SMS Notifications (default is off))
* Added Mobile Number and SMS Status Fields to the STC Subscribers List
* fixed several debug notices, warnings and code bugs.

= 2.5.9 =
* Added attribute to show taxonomy hierarchy in the reason why a notification was sent.
* minor bug fixes
 
= 2.5.8 =
* Fixed warning on directory or file does not exsist
* Fixed problems with containers with two or more columns

= 2.5.7 =
* added confirmpage slug: stcs-confirmation-page for stage one of email confirmation
* added SMS Notification feature (workonly with TextMagic as send sms provider)
* Fixed problem with email notification subject
* Fixed mixed some text messages wrongly using Blog where it should be Post
* Fixed problem with do not send this Post on new/update 
* minor bug fixes.

= 2.5.5 =
* added landingspage with slug stcs-landing-page
* solved bug in succesfull reply on import susbscribers
* solved bug in subscribers not having a proper default value for individual subscribtions 

= 2.5.4 =
* new placeholders
* choice for weekdays, daily and hourly notifications on individual basis
* problem with word / character cutoff in middle of html tag's
* small bugfixes

= 2.5.3 =
* added new place holder {{post_excerpt}}
* added new place holder {{post_featured_image}}
* new STC Basic e-Mail Template V2.1 (delete older versions)
* solved problem {{search_reason}} containing "" in some cases.
 
= 2.5.2 =
* added possibility in the post ducoment sidebar to inhibbit send e-mail notifications
* Ignore default treeview enabled when category attributes are used in the same shortcode this makes it possible to upgrade STC from older (before treeview) versions

= 2.5.1 =
* revised readme.txt and introduction of STC's User Manual 
* Added enhanced keyword search facilities.
* Added stc_update_count_callback function which filters out the terms count for custom post type 'stc'.
* Check for STC meta keys present on custom posts created by others. Add STC meta keys/values if not present.
* Added more placeholders to control the e-mail notification template
* For newly created e-mail notification post are created with content explanatory content and possible placeholders.
* small bugfixes

= 2.4.18 =
* Removal of all the non textual tags preventing cutoff's causing e-mail layout failure
* Added multibyte characters count for the E-Mail Notification
* Added hidden <hr> tags to separate multiple updates and / or saves posts. HR styling is available in body <style> class "stc-notify-hr" </style>
* Added workaround to control justification in the contents row of the e-mail notification template. text-align: left">{{post_content}} is used by STC to replace the word left for center allowing dynamic justification depending on content cutoff is 0 or > 0
* small bugfixes

= 2.4.17 =
* Created "STC Demo E-mail Template V1.1" to enable background-color style change with Chinese SMTP provider (tested with outlook, gmail and exmail).
* Improved handling of E-mail Template
* small bugfixes

= 2.4.16 =
* Unsubscribe via e-mail link firing more then once causing incorrect fault message
* New single HTML E-mail Notification layout with placeholders for title, content and unsubscribe
* Widget settings for STC Widget: new possibility to use shortcode attributes 
* small bugfixes
 
= 2.4.14 =
* adding a feature to send all email notifications once per day
* revised the STC Settings Notification E-mail status pane
* small bugfixes

= 2.4.12 =
* Added/Revised code and HTML section post to create an initial html template setup like in screenshot-11. Delete all HTML sections (published and trashed) to replace existing HTML sections
* corrected quite a view spelling mistakes in messages and readme.txt
* small bugfixes

= 2.4.11 =
* Redesigned the STC menu structure. Dashboard: STC Subscribe => Submenu: (Settings, Notifications, Subscribers)
* Added code to create the basic notification structure automatically (if HTML section names not checked && post does not exist) {wp_insert_post(....)}
* small bugfixes

= 2.4.10 =
* created a way that categories are default on until the user itself disables it through STC admin settings
* redesigned option structure for compatibility reasons

= 2.4.9 =
* major change in adding HTML text to the notification e-mail (read FAQ concerning HTML inserts)
* categories can now be disabled to accommodate users how only work with custom taxonomies
* small bugfixes
 
= 2.4.8 =
* small bug fixes.

= 2.4.7 =
* STC used Sendmail instead of PHPMailer because in version 5.5 from WordPres wp_mail can’t cope with a encoded_basic64. Analasys of the PHPMailer's showed that it takes care of 8-bit characters itself so no need to do this in STC. wp_mail() again used enabling Mail plugins to pick up STC mails.

= 2.4.6 =
* added attribute 'treeview_folded' -> treeview_folded="false" unfolds the category / taxonomy tree view by default
* added attribute 'treeview_enabled' -> treeview_enabled="false" displays the categories / taxonomies as one list ignoring hierarchy. Please note that only the selected categories are in or out
* added/changed some French translations
* small bug fixes.

= 2.4.5 =
* filter categories caused the tree view to be messed up completely now any category can be taken out but only categories of level 0 can be filtered in.

= 2.4.4 =
* allow for no categories or taxonomies selected at all
* fixed issue that subscribers in trash taxonomies where picked up
* fixed issue displaying wrong indent for category and taxonomy treeview
  
= 2.4.3 =
* fixed issue on shortcodes attributes: category_in, category_id_in, category_not_in, category_id_not_in

= 2.4.2 =
* fixed issue on STC settings page when no custom post or no custom taxonomies where available
* fixed issue on subscription warning when no taxonomies where selected
* fixed issue on first use of the block editor STC resend checkbox
  
= 2.4.1 =
* Added Custom Posts Functionality
* Minor bugfixes

= 2.3.9 =
* Fixed error in CSS file stc-tax-style.css causing CSS optimization to fail

= 2.3.8 =
* Revised category and taxonomy selection for the shortcode and widget
* Several changes to make understand STC better
* Fix bug in wp-mail sending corrupted base64 encode subject in notification
* Other bugfixes

= 2.3.6 =
*bugfixes

= 2.3.5 =
* Small changes to adapt to WP 5.5
* bugfixes

= 2.3.1 =
* New admin setting to select custom taxonomies names (shortcode for taxonomy selection comes later for now use admin edit of subscription)
* Multiple post to one subscriber are added together in one notification.
* bugfixes

= 2.2.1 =
* Introduced subscriber verification for subscriptions and updates while not logged in to WordPress
* Introduced disabling of the unsubscribe checkbox
* New admin setting to control the length of the content in the email notification
* bugfixes

= 2.1.7 =
* Added email conformation to intended STC subscriber. When confirmed the new STC subscriber becomes active. This is not required when the user is in admin mode. 

= 2.1.6 =
* minor bugfixes

= 2.1.5 =
* changed subscription method for existing subscribers to enable updating the subscription
* bug fixed with only one category causing nothing to show

= 2.1.4 =
* Changed text for using the Gutenberg/Block editor to be more clear
* Redirect to first page after subscription stays now on the current page
* Bug fixed causing not to update the subscribers when using the classic WP editor

= 2.1.3 =
* Added an admin feature to import a csv subscriber list in exactly the same format as created by the export function

= 2.1.2 =
* Added an admin setting to control the WP-Cron STC rescheduling "send email time". Default is 3600 second but the user can now set it to any value between 180 and 3600 => 2 minutes and 1 hour
 
= 2.1.1 =
* during sanitizing process, the email address from the unsubscriber lost the "@" sign causing failed unsubscribing issues

= 2.1.0 =
* implemented sanitizing and escaping according WPCS
* adapted internationalization text domain to plugin standards
* added 'numberposts' => -1 to the arguments for exporting all subscribers to excel it was defaulting to a maximum of 5
* minor bugfixes

= 2.0.0 =
* adapted STC to wp 5.3 features by adding a STC resend option to the document tab

= 1.9.0 =
* Email address preset for logged in users.
* Japanese, Dutch, German and Norwegian language added.

= 1.8.1 =
* Added pot file to be used for translation.
* Bugfix - changed text domain to string instead of constant.

= 1.7.0 =
* Added some new hooks: stc_after_update_subscriber, stc_after_insert_subscriber, stc_before_unsubscribe.

= 1.6.0 =
* Added a Widget for subscription form.
* Don't show category list if only one is available (thanks to davefx).
* Extended short code attributes with an option to use category id instead of category name (thanks to Stingray_454).

= 1.3 =
* Added hooks and filters to make the plugin extensible.
* Added Lithuanian language.

= 1.2.1 =
* Fixed some undefined variables that might have caused some errors for some environments.
* Renamed language files for Russian language to correct syntax.
* Added Italian language.

= 1.2.0 =
* Possibility to re-send a post on update that has already been sent. This option needs to be activated in the settings for the plugin.
* Attribute 'category_in' added to shortcode to show only entered categories in the subscribe form. Multiple categories are separated by a comma sign.
* Attribute 'category_not_in' added to shortcode to exclude categories in the subscribe form. Multiple categories are separated by a comma sign. 


= 1.1.0 =
* Added php sleep() function to prevent sending all e-mails in the same scope. 
* Using Ajax when send is manually triggered in back-end.

= 1.0.0 =
* First release

== Upgrade Notice ==
* Aditional add_filter/hook function to allow bulk sending of email notifications. This will avoid STC tripping out while sending bulk emails.