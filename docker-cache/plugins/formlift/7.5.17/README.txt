=== FormLift for Infusionsoft Web Forms ===
Contributors: trainingbusinesspros
Tags: Infusionsoft, Optin, Form, Editor, Official, FormLift, Web Form, Forms, Form Editor
Requires at least: 4.9
Donate link: https://formlift.net
Tested up to: 5.7
Stable tag: 7.5.17
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import Infusionsoft Web Forms into WordPress and easily customize their style. Display with short-codes.

== Description ==

Need help? [Watch Our Tutorials](https://formlift.net/video-tutorials/)

What does it do? [See The Features List](https://formlift.net/features/)

Need more functionality? [Check out our extensions!](https://formlift.net/extensions/)

Not technically inclined? [Launch Guided Setup](https://formlift.net/use-formlift/)

Simply put, this is the easiest, fastest, and most user friendly solution to putting Infusionsoft Web Forms on your website. FormLift's 5 minute setup time allows you to install this plugin and run, not walk. It takes 30 seconds to create a new form and place it on your site!

The Free version hosted on WordPress allows for amazing functionality that will make any Infusionites nightmares disappear in seconds...

= Unlicensed Features =
* Over 45 global styling options
* Forms auto populate with contact info From emails
* Form Validation and spam protection
* Conversion Rate Tracking
* Personalized Page Short-codes for thak you pages
* Import form directly via the Infusionsoft API, no copy/pasting
* Customizable Date Picker that actually works
* Personal Identifiable Information protection to keep your contacts' data safe.

But if you want to unlock the full automation power of FormLift, you can [start your 14 day free trial](https://formlift.net/free-trial-3/) right now to unlock even more tools!

= Premium Features =
* Auto population link builder
* Conditional Thank You Page Redirect Creator Tool
* In form Google reCAPTCHA integration, works better than the native Infusionsoft one.
* Individual Form Styling
* Conditional Form Display Tool
* Require login for submit
* File Uploads from your web form

FormLift empowers regular IFS forms by keeping all the native affiliate tracking & contact tracking intact while allowing you to finally have good looking webforms on your website.

See how easy it is to get setup today!

[vimeo https://vimeo.com/245092055]                 

[Want extra functionality? Level up with extensions!](https://formlift.net/store/)

== Installation ==

This section describes how to install the plugin and get it working.

Method 1:
    1. Upload via Plugins -> Add New page
    2. Install and Activate
    3. Start using!

Method 2:
    1. Upload to wp-content/plugins/
    2. extract .zip file contents
    3. Go to All Plugins page
    4. Activate and start using.

== Frequently Asked Questions ==

= What PHP level is required =
PHP 7.0 or better is recommended, however 5.6 and up works fine. Below 5.6 is at your own risk...

= Will this work with my existing Infusionsoft Web Forms in my campaigns? =
YES! All you need to do is import them and they will work as if they were regular infusionsoft forms.

= Is there support? =
Yes, I so far response time is lees the 24 hours. You can [contact me directly](https://formlift.net/contact-us/), or get in touch via our [new facebook group](https://www.facebook.com/groups/143721343008129/)

= Is it compatible with other Infusionsoft based plugins?
Yes, The Gravity Forms Infusionsoft Add-On, Memberium, and the new Infusionsoft Official Web Form Plugin, and Thrive Leads are all tested as compatible.
If an error arises it is likely on part of another plugin and not FormLift, however our support will investigate in every case.

== Screenshots ==

1. Simple drag and drop editor for those who like a little extra control.
2. Over 45 different styling options to customize! (button options shown)
3. Rock solid form validation to protect your forms from spam! (Premium)
4. Auto population feature to help your automation kick in!
5. Nice customizable date picker so your clients can pick the correct date for once.
6. Condition Redirect Tool to send leads where they need to go! (Premium)
7. Conditional display tool so no one submits forms past their expiry date. (Premium)
8. Personalized short-codes to make your leads feel welcome on your site!

== Compliance ==

FormLift, like many other form plugins, collects personally identifiable information to improve a user's experience,
provide them with marketing, transactional emails, and so on and so forth.

With the MANY compliance initiatives out there such as GDPR, it is important to understand the following BEFORE using formlift
for your business.

FormLift is used expressly for Infusionsoft, an SMB marketing CRM based in Pheonix Arizona. Infusionsoft maintains the compliance of your business
with regards to Personally identifiable information in MOST cases, but not all.

FormLift has several functions you will either want to disable, or enable depending on your industry or region of the world.

= Data Collection =
When a user submits a form, that data is sent to YOUR server for validation and is not necessarily stored.
MOST data collected with FormLift after a submission is sent to Infusionsoft where it is stored and is then forgotten by your website EXCEPT in the following cases.

= Session Tracking =
This feature comes with FormLift and is enable automatically, it allows the persistence of user data from page to page without having to pass UTM variables, almost as if the user is logged in.
These sessions are used on secure connections only (SSL) hence if you do not have an SSL certificate this feature will be disabled.
This session is stored in your database following the last interaction with the user for 30 days until it expires and is removed automatically.
Depending on your level of compliance, you may wish to disable this feature which you can do in the settings, OR you may specify the number of days the user's information is stored to a smaller number, like 1 day which would be enough for most browsing sessions.

= Cookies =
FormLift uses cookies to track user interactions, such as the above session tracking, form submissions, or other form interactions.

= Saved Submissions =
This is a premium module, but if you have it enabled there can be serious compliance issues if you are medical practitioner in Canada and in the United States in some cases.

= File Uploads =
This premium feature may store files on your server indefinitely. You may turn off this feature in the settings if you have this module installed.

= GDPR =
There is a special field you can add to your forms called GDPR which will automatically make your forms GDPR compliant.

= HIPAA =
FormLift is NOT HIPAA compliant by default. You can make FormLift HIPAA compliant by installing an SSL certificate and disabling "Session Tracking" and "Saved Submissions".

= API Connection =
* OAuth : If you are connected to Infusionsoft via the Oauth method, your authentication Tokens are passed through an intermediary server "ouath.formlift.net." No personal information is ever passed through this medium however. Hence all API calls made with FormLift are communicated to Infusionsoft Directly with the exception of refreshing tokens and the initial authentication request.
* API Usage: Anonymous API usage statistics are collected if you use the OAuth Method.
* Legacy: If you are connected to Infusionsoft via the Legacy Method, all API calls are made to Infusionsoft directly and "oauth.formlift.net" is not involved. No usage statistics are collected.
* Methods: The only API methods FormLift uses are for the uploading of Files to a contact's FileBox and the retrieval of WebForms. FormLift will never "retrieve" information from Infusionsoft.

= Infusionsoft or other CRMs =
Infusionsoft or whichever CRM you use with FormLift is the primary holder of Information collected with FormLift. To ensure you are compliant in regard to the storage of information, please consult them if it's beyond the scope of the above.

== Upgrade Notice ==
Due to a bug in a previous version FormLift may not successfully update in which case you will have to delete FormLift via FTP and then re-install. Your data and forms will not be affected.

== Changelog ==

= 7.5.17 =
* TWEAKED No longer enforce SSL to do sessions tracking as it's required for localhost development

= 7.5.16 =
* ADDED Function to delete all stats in the even you get loading errors when loading all forms.
* TWEAKED the form title (`post_name`) is now automatically set to the form name from Infusionsoft so you don't have to specify it each time.

= 7.5.15 =
* ADDED Ability to set custom placeholder text when the label is enabled.
* TWEAKED Notices now use the built in dismissible button and will only appear once.
* FIXED Fatal error when importing a broken/non-existent form from Infusionsoft.
* FIXED Update the oauth server to the new address.

= 7.5.14.3 =
* FIXED issue with FormLift personalization codes.
* FIXED issue causing failed update of the core plugin.

= 7.5.14 =
* TWEAKED Updated Groundhogg ad in settings page
* FIXED installation errors showed because of missing DB error
* FIXED oauth connection broken

= 7.5.13 =
* Module loader now includes file name to avoid potential harmful files to be included.

= 7.5.12 =
* Added replacement code support for URL param mapping
* Removed stats collection, was causing warnings and is no longer needed.

= 7.5.11 =
* Fixed issue related to radio buttons with values of 0 being ignored

= 7.5.10 =
* Added prompt to try Groundhogg
* Did some IE compatibillity stuff.

= 7.5.9 =
* changed XMLHttpRequest.DONE to the number 4 to workaround some sites changing the DONE property to a function, or not recognizing it at all.
* Fixed some spelling errors.

= 7.5.8 =
* Updated the icon which appears in Notices created by FormLift
* Fixed rewards bug causing rewards to be given to those who did not earn them.

= 7.5.7 =
* Fixed messy HTML in settings page

= 7.5.6 =
* Changed condition to check for valid license. More strict to avoid false positives coming from FormLift.net
* Added required functions and HTML to start contest when ready.

= 7.5.5 =
* Fixed visual editor firefox bug.

= 7.5.4 =
* Added explanations to the field editor settings.

= 7.5.3 =
* Extended the license check time to 3 days instead of day.
* Removed the add custom field button from the editor bar, and instead there is now an inline addition button.

= 7.5.2 =
* Send the source url to the API refresh to allow blacklisting.
* Send an email to the admin whenever an extension license expires.
* Added an API log.

= 7.5.1 =
* Added explanations to all plugin & style settings.
* Simplified the settings UI.
* hardened the credit again.

= 7.5.0 =
* Nice UI update made to the editor. No functional changes, just the small version number were getting pretty big.
* Added border styling options to the form container under "FORM CSS"

= 7.4.29 =
* moved the "Powered by FormLift" Credit to below the main button text. hardened the css to prevent users CSSing it away. We have to make money too ya know.

= 7.4.28 =
* removed nonce validation on frontend forms on account of page caching conflict with nonces.

= 7.4.27 =
* Added session encryption for total user session encryption.
* Fixed bug with button self closing improperly.

= 7.4.25 =
* API connection fixes, throwing exception caused undo error. Changed to WP_Error

= 7.4.24 =
* Provided FormLift sessions with their own DB table.

= 7.4.23 =
* Added session encryption for single variable user attributes with OPEN SSL.

= 7.4.22 =
* fixed incorrect settings page redirect.

= 7.4.21 =
* added try catch to load custom fields.

= 7.4.20 =
* Fixed default serttings not settign radio and checkbox field options
* Set options to allow session recovery

= 7.4.19 =
* Added FormLift stats collection notice.

= 7.4.18 =
* Added awards to make users feel good!
* fixed gdpr bug.

= 7.4.16 =
* allowed the use of the GDPR field with custom fields in Infusionsoft.

= 7.4.15 =
* Changed the order of credit to appear below the message box rather than on top.
* Added the "EU ONLY" option to the GDPR consent box.

= 7.4.14 =
* Send an email to the admin whenver the FormLift connection fails.
* When the refresh fails do not wipe existing tokens in case of retry.

= 7.4.13 =
* Fixed the post type orderby function not working for new forms.

= 7.4.12 =
* Somewhere along the line the Infusionsoft tracking code got removed, we re-included it.

= 7.4.10 =
* Removed html entities direct inclusion in the PHP code and replaced with ::before in stylesheet.
* fixed some stylesheet stuff.

= 7.4.10 =
* Changed brand assets to match new logo!

= 7.4.9 =
* fixed potential file inclusion error concerning the EDD plugin updater library for Premium extensions.
* added some new CSS rules.
* re-ordered som settings.

= 7.4.8 =
* minor usability fixes
* changed API method for appending form code to also include the form ID

= 7.4.7 =
* allow accents in name fields
* allowed the importing and exporting of Form Level Settings

= 7.4.6.2 =
* fixed import feature importing incorrect form if not on the "Add New Form" screen

= 7.4.6.1 =
* fixed bad typo

= 7.4.6 =
* better error checking for additional error checks

= 7.4.5 =
* At the request of a user we have added the date format option to date pickers.
* we have added backwards compatibility for the conditional thank you pages new URL structure.
* we have allowed the $FormLiftUser Variable to populate even if disable UTM removal is enabled.

= 7.4.4 =
* added backwards support in case that you don't upgrade the DB.

= 7.4.3 =
* Added support for shortcodes in select, listbox, radio buttons and other fields' attributes.

= 7.4.2 =
* added support for shortcodes in the Label & Value fields attributes.

= 7.4.1 =
* added better extensions compatibility.
* removed domain from admin_ajax path for better security.
* better handling of errors from extensions.
* added better update handling.

= 7.4 =
* Compliance Update. To see the full list of extensive changes please review [this changelog](https://formlift.net/formlift-7-4-compliance-update).

= 7.3.13 =
1. Usage stats will be collected from premium activated instalations as per our updated Privacy Policy.
2. Removed the "infusion-radio" class form the checkbox because it was causing way too much spacing. 

= 7.3.12 =
1. Added some new messages when submitting forms. Success!, Error(s)!
2. Changed the visual appearance of the loader when waiting for a form to submit to make it look nicer.
3. Added a small, non intrusive credit link to the bottom left of the form for free users only.
4. Fixed a few bugs.

= 7.3.11.1 =
1. Fixed the composition of Urls comming from the redirect cretor with extraneous "?" at end of output.

= 7.3.11 =
1. Fixed Urls with query strings in the redirect creator escaping the html params causing the link to not work.
2. Fixed special characters in DB names not handling well when opening the field editor.
3. Added the option to parse html form code into formlift rather than using the API. Useful when there are special characters in DB names as those do not work well with the API.

= 7.3.10.1 =
1. Fixed form preview not displaying the style.

= 7.3.10 =
1. Increased the specificity of the styleing options so that themes don't override them.
2. Removed the "Make readonly" option ftom the button field type.

= 7.3.9 =
1. Added option to stop FromLift from strip PII utm variables from url query string.
2. Added option to exclude specific variables from removal
3. Changed Placeholder color settings
4. added option to submit for to a new page.

= 7.3.8 =
1. Changed Error messages to just messsages to make sense with context of adding the option to change the "please wait text"
2. Added options make fields readonly
3. Added options to add custom CSS classes to field containers
4. Aded more notices to ensure users their Infusionsoft connection is active  

= 7.3.7 =
1. Added tool to get the auto-population link of a form for a particular page.
2. added the option to specify the border type of the button.
3. Added option to specify the font size of the radio options vs. labels.

= 7.3.6 =
1. Added IP Blacklist that will check the user's IP to allow submission
2. Added KeyWord blacklist that will check EVERY field in the user's submitted data
3. Removed the infusionsoft required special fields form the builder and made them unedittable
4. hid the xid of the form until a successful submission is recorded so spammers cannot compose the URL from the xid and access form directly
5. Fixed the session population so it also removes user data from the query string for Google PII policies
6. Added the option to remove the flag from the phone type field.
7. timezone not getting added correctly if multiple forms on 1 page.
8. hidden fields will automatically be set to autopopulate because of user feedback

= 7.3.5.2 =
1. Redirect tool not sending query string to default thank you page.
2. Set checking for params in redirect tool to isset() rather than !empty() to loosen restrictions on checking for data

= 7.3.5.1 =
1. Fixed bug where Redirect Creator wasn't pulling options for select or radio fields upon intial form Import without first saving the form.
2. New sdk wasn't uploading Files Correctly
3. Adding new radio and select options wasn't giving the pre-selected option.

= 7.3.5 =
1. Removed the Infusionsoft NOVAK SDK and replaced it with My own as FormLift only uses 3 API methods anyway.
2. Changed the call behaviour so that if the Oauth Request fails, it will fall back to the Legacy credentials if they exist eliminating downtime.
3. Made the refresh behaviour & disconnect behaviour more stable.

= 7.3.4.1 =
1. fixed refresh form list button not working only when adding new webforms.
2. Added disconnect oauth button as users are experiencing strange API behaviour.

= 7.3.4 =
1. Overhauled Oauth after reports of many erros authenticating.
2. Fixed Oauth not working on multisite
3. Better handling of the authorization when transfering of tokens
4. Added static webform list with update option so It doesn't reload the webform list every time.
5. added refresh button to "refresh" the webform list

= 7.3.3.7 =
1. More Reporting on tokens, details are important
2. fixed refresh token button not working as intended

= 7.3.3.6 =
1. More error reporting when re-authenticating tokens in WP
2. Delete tokens and require Re-authentication if re-authentication initially fails

= 7.3.3.4 =
1. I took it for granted most themes include the jQuery sortable library on everty page, so I have added it into formlift just in case.

= 7.3.3.2 =
1. Resolved Typeform Conflict, again hopefully.
2. Resolved issue of loading APP Domain 

= 7.3.3.1 =
1. Added refresh connection button

= 7.3.3 =
1. fixed tokens not refreshing
2. fixed typeform conflict with form editor
3. set notices on cron job rather than on login to avoid conflict with memberium

= 7.3.2 =
1. Added filter for user data when autofilling forms.

= 7.3.1 =
1. Quick CSS fix

= 7.3 =
1. Added the Oauth Integration Method which will now be required given the sunsetting of the infusionsoft API key
2. Added the Require Login Option for forms
3. Added the phone number internalization library. "Fingers Crossed it works"

= 7.2.2 =
1. Explenations and info added to some Form fields in the editor  and redireect box
2. Quick bug fix when sending info to a page with a veriable being undeclared
3. finally got the query string replacement to work with th redirect editor...

= 7.2.1 =
1. Quick bug fix where trashed forms coulldn't be restored

= 7.2 =
1. File Uploads! File Uploads! File Uploads! Yes, file uploads to the contact FileBox
2. Enhanced UI improvements in form editor
3. Hidden Fields now work with the redirect editor
4. Forms save contact info to sessions now rather than cookies, this will limit the risk of bleeding contact information
5. Fixed some style settings not populating on installation.

= 7.1.5 =
1. Fixed Checkboxes not sending values to Infusionsoft
2. Added ClearFix to columned forms for improved style and looks
3. Fixed some CSS issues with the drag and drop builder
4. Added the option to FORGO the validation and show the POST url.

= 7.1.4 =
1. fixed function typo oops.

= 7.1.3 =
1. Fixed html not saving properly and not being able to save quotes with CSS options

= 7.1.2 =
1. Custom CSS for radio buttons and Checkboxes for added flare
2. Fixed bug where select options and radio options were not deleting.
3. Added ability to add custom option to select & radio types
4. Fixed infuriating thickbox loading issues when themes or plugins load the media uploader on every page.
5. Switching in between Radio buttons and Select options will convert the options from one to the other!
6. Fixed importing settings bug when users are non premium.
7. Added option to settings panel to "Opt Out" of notices from formlift.net. That will make me sad though so don't.

= 7.1.1 =
1. Added special case for session SAVED emails when users use the email.name+extension@gmail.com syntax
2. Prevented the g-recaptcha-reponse message from being saved into the submissions table.

= 7.1 =
1. Added submissions table to track form submissions in WP! (Premium Only)
2. Added devloper API
3. added more css options for radio buttons
4. FormLift no longer imports a bunch of JS from the infusionsoft form because most of it is just not needed.
5. Edit popup actually loads in the correct size now.

= 7.0.16 =
1. New notice API with FormLift.net to retrieve notices live on login. #MarketingFTW
2. Better ERROR handling when importing Infusionsoft forms goes wrong.
3. Added filters and actions for external developers who might want to modify the available CSS classes or play with form submission data.

= 7.0.15 =
1. required fields not being checked
2. added special case for YES/NO radio button custom fields

= 7.0.14 =
1. Backend code cleanup and optimization for faster loading of the admin panel
2. decreased formlifts packet size

= 7.0.12 =
1. Fixed conflict issue with formlift security lockdown affecting checkout in woocommerce stores.

= 7.0.11 =
1. Fixed non-required fields being validated when submitting emtpy values

= 7.0.10 =
1. Added new field type "Password"
2. Added password matching validation for referral partner creation forms

= 7.0.9 =
1. Fixed Required Field message not showing for radio buttons
2. Fixed Website field type validation not firing

= 7.0.8 =
1. Fixed function calling before plugins_loaded complete causing 500 internal server error

= 7.0.7 =
1. Fixed crashing in php 7

= 7.0.6 =
1. fixed some backwards compatibilty errors.

= 7.0.5 =
1. Fixed PHP warnings caught by WP_DEBUG mode.
2. Added Actions For External Devopers to formlift_Submit.php
3. Fixed Zip code & Postal code validation not firing.

= 7.0.4 =
1. Added shortcode support to custom HTML block in form builder
2. Fixed bug where single quotes in the redirect creator caused it to not load
3. Fixed bug where special HTML charaters caused strange functionality in form elements.
4. Fixed bug where Labels for select elements would not show.

= 7.0.3 =
1. Deprecated the old HTML Editor and replaced with a drag and drop builder.
2. Added server side ReCaptcha validation as well as regular field server side validation.
3. Many performance enhancements
4. Security enhancements convering the handling of user data
5. Form auto populates with user data if logged in

= 6.4.16 =
1. Fixed WP-color-picker-alpha conclift with wp 4.9

= 6.4.15 =
1. Fixes minor errors occuring with PHP 5.6

= 6.4.14 =
1. Fixed notices not dismissing properly.

= 6.4.13 =
1. Fixed some very minor errors with compatibility between PHP versions. No "important" functionality was affected.

= 6.4.12 =
1. TimeZone was not being set on a successful form submission, now it does. Just saying, infusionsoft did not make the search to do this easy at all, so your welcome for figuring it out.

= 6.4.11 =
1. Oops, forgot to change the formatting of the preview form in the settings page causing an error loading the color-picker.

= 6.4.10 =
1. Javascript loading issue of Recaptcha box

= 6.4.9 =
1. Small Bug fixes including Apostrophe's in error messages causing form load error
2. Fail safe optimization of form code
3. Added transparency option to all color options!

= 6.4.8 =
1. Fixed tracking date not setting properly

= 6.4.7 =
1. Some files disappeared randomly causing a downtime in API integration. They have been replaced.
2. Chanced some logic syntax to follow standards
3. Added better handling of exceptions thrown by the Infusionsoft SDK

= 6.4.6 =
1. More stable conversion tracking, some conversion rates may be skewed towards lower end results
2. Small auto-fill bug fix.

= 6.4.5 =
1. Make Redirects sortable for ease of use.

= 6.4.4 =
1. Require PHP 5.6 or higher to work

= 6.4.3 =
1. To new logic conditions added to the premium redirect builder. "Starts With" & "Ends With".

= 6.4.2 =
1. Minor bug fixes
2. Removed self hosted update feature
3. Ability to copy settings from another form.

= 6.4 =
1. Major update there's too much to cover. Please see the plugin homepage for more information on recent updates!

= 5.8.0 =
1. Added functionality of cookie-ing user data on form submission
2. Auto-fills based on cookied user data
3. Cookies user data that is passed through URL params
4. Conditions on auto filling form data now appears in the Settings tab of Formlift Defaults
5. Added a Redirect making metabox
    - Create Redirects based on Dropdowns and Radio Buttons
    - Use the thank you page URL as the thank you page URL in Infusionsoft
6. Added the ability to change the Placeholder colour pf text fields

= 5.5 =
1. Added a User Manual with specific instruction on how to setup lead source tracking in infusionsoft and auto populate fields
2. Added campaigns, a custom taxonomy that allows users to associated multiple web-forms with a specific campaign so directly compare conversions in case they are split testing multiple landing pages.
3. Changed the Remove Labels option to a yes/no drop down selection.
4. Re-added checkboxes to the formLift columns in admin panel.

= 5.0 =
1. Restructured code to move away from functional to object oriented.
2. Decreased code size dramatically
3. Removed live updates to preview when options are changed
4. Removed Modals pending further work.
5. Added Ajax Based Conversion tracking.
6. Required fields have been moved back to the main editing area

= 4.8 =
1. Fixed Fatal error where script wasn't firing on Safari

= 4.7 =
1. Massive UI changes
2. jQuery Color Picker is now included for all color areas to make selecting colors easier
3. live Updates to form preview based on input
4. the required fields area has been moved to the preview metabox to ensure people see it and set them.
5. required fields are now displayed as their associated label.

= 4.5 =
1. Includes new CodeMirror Library to improve the readability and editability of HTML code!

= 4.0 =
1. Modals have been introduced in limited functionality. BETA testing only, so use at your own risk.
    -Updates include:
        - A button shortcode that activates a modal
        - A modal shortcode, automatically includes the form so there is no need to place both the modal and the form shortcode on a page.
        - Copy buttons in the EDIT form area.
2. The User interface has been remodeled to improve the learning curve and increase the intuitiveness of the software.
    -Changes include:
        -dropdown tabs for different styling options both in the defaults area
        -dropdown tabs for different styling options both in the create form area
        -better labelling of fields and sections
3. The validation algorithm has again been lightened to improve speed.
4. The errors no longer appear under the fields to improve space usage and mobile friendliness, and now appear under the form in a list of errors format.
5. The radio button error has be removed and will now use the default missing field error
6. Date support! Date fields carried over from Infusionsoft will be reformatted and have a DATE picker installed so you can choose dates with a UI
7. If you decide to do so, the following fields will all have REQUIRED support. Password, Date, Number, Text, Textarea, Select, checkbox, radio

= 3.8 =

1. Backend scalability has been improved
2. Default Settings have been tweaked a bit.
3. Preparations for introducing further implementation. Hint hint... Modals are coming soon.

= 3.7.6 =

1. The validation was a bit loose and causing unexpected checking when parsing the form. Validation is now much more specific giving more variability

= 3.7.5 =

1. Overhauled form validation, again...
2. Over hauled the way required fields are required, you can now select which are required and which are not using checkboxes.
3. The Email field will be required by default, to protect the user and to avoid spam.
4. Button alignment is now a dropdown and no longer a radio button. Added some stuff to the instructions
5. Added a quick function to make `<textarea>` tags behave well in6the `form_code` area
6. form processing time is now a bit faster on the front end. But as slowed down in the editing area due to new options.

= 3.7 =

1. Added automatic updates!

= 3.6.2 =

1. Fixed bug that wouldn't allow you to submit pform post without filling out preview form fields...
2. Added new functions to handle validation.

= 3.6.1 =

1. Changed all function name calls to associate with the prefix formlift_ (form lift pro)
2. Deactivates LITE version on activation to not cause conflicts between the two.
3. Added an instructions page to make the user experience slightly easier.

= 3.6 =

1. Rewrote recognition algorithm to include global functions to increase page loading speed.
2. Added preview forms to Edit form pages
3. Cleaned up code and fixed minor bugs.

= 3.5.1 =

Added a style option to align the submit button.

= 3.5 =

First public release version