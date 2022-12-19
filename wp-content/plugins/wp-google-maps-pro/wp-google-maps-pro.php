<?php
/*
Plugin Name: WP Google Maps - Pro Add-on
Plugin URI: http://www.wpgmaps.com
Description: This is the Pro add-on for WP Google Maps. The Pro add-on enables you to add descriptions, pictures, links and custom icons to your markers as well as allows you to download your markers to a CSV file for quick editing and re-upload them when complete.
Version:  8.1.20
Author: WP Google Maps
Author URI: http://www.wpgmaps.com
*/

/* 
 * 8.1.20 - 2022-03-29
 * Fixed issue where get directions button would not work with some info-window styles
 * Fixed issue with apostrophes in the category editor
 *
 * 8.1.19 - 2022-03-03
 * Fixed issue where clicking in an info window would close it when 'click map to close info-window' option is enabled (OpenLayers)
 * Removed comments from marker listing templates
 *
 * 8.1.18 - 2022-02-03
 * Fixed issue where carousel description formatting would be stripped due to nested 'p' tag usage
 * Fixed issue where two distances would be shown depending on map show distance setup is configured
 * Fixed issue where custom field order was not being respected in info-windows
 * Fixed issue where custom field value could not be '0' as it would evaluate as 'empty'
 * Fixed issue where 'My Location' text was not being used for user location markers
 * Fixed issue where 'Use Map ID' option would not be retained when editing a scheduled import
 * Fixed issue where modern plus info-window would catch pointer events instead of the map tileset (OpenLayers)
 * Added support for VGM redirect within shortcode handler (Primary)
 * Tested up to WordPress 5.9 
 * 
 * 8.1.17 - 2021-12-09
 * Fixed issue where modern info-window color fields would not be shown for the respective styles
 * Fixed issue where modern info-window color fields did not have the color type set accordingly
 * Fixed issue where modern info-window colors would be output with a double # symbol 
 * Fixed issue where user location marker would not be shown in some cases for Firefox users
 * Improved user location management when using pan and show user location settings at the same time
 *
 * 8.1.16 - 2021-11-02
 * Fixed issue where VGM approve button would not show in the map editor
 * Fixed issue where some stylesheets would have a double slash on the css path (//css)
 *
 * 8.1.15 - 2021-10-18
 * Improved marker editor geocode usage to only geocode when an address has changed, or is being added for the first time. (Reduced API calls due to usage)
 * Fixed issue where editing a marker which has already been position adusted would trigger a geocode on the original address, moving the marker back to the original placement
 * Fixed issue where ACF marker importer would not respect the permalink structure for the relevant post
 * Fixed issue where post meta driven map/marker would not show the 'address' correctly, and instead showed 'undefined'
 * Fixed issue where editing a marker with the text view pre-opened would cause no data to be loaded
 * Fixed issue where users with the 'visual editor' disabled in their profile would prevent the marker description editor from loading (tinyMCE) 
 * Fixed issue where legacy store locator layouts 'radius' label would not have a 'for' label set accordingly
 * Fixed issue where 'Extract address from picture' control would not provide feedback when no image has been added to gallery
 *
 * 8.1.14 - 2021-09-01
 * Fixed issue where marker titles would store '&' symbols as '&amp;'
 * Fixed issue where marker links would store '&' symbols as '&amp;'
 * Fixed issue where modern plus infowindow would not restore pan state once closed (OpenLayers)
 *
 * 8.1.13 - 2021-07-28
 * Fixed issue where Datatable sort with compressed paths would fail in some environments
 * Fixed issue where modern directions result panel would be visible on smaller screens by default
 * Fixed issue where marker listing pagination controls would not respect the push in map option
 * Fixed issue where some environments would not be able to sort by distance due to floatval formatting issue
 * Fixed issue where modern info-window would display incorrectly in OpenLayers engine
 * Fixed issue where applying a custom field filter would not reset DataTable pagination to page one, which leads to 'no results' for pages above the currentPage * length (when applicable)
 * Removed rogue development console.log 
 * Added support for marker listing zoom override in the Marker Listing tab
 * Tested up to WordPress 5.8 
 *
 * 8.1.12 - 2021-06-15
 * Fixed issue where Authenticated Persistent XSS could be executed via the Custom Field Editor (Thanks to Visse) 
 * Fixed issue where Authenticated Persistent XSS could be executed via the Category editor (Thanks to Visse) 
 *
 * 8.1.11 - 2021-06-03
 * Fixed issue where the 'no markers found' alert would be shown when resetting the store locator with hide markers until a search is done option enabled
 * Fixed issue with default interface style and modern store locator override category selection
 * Fixed issue with backup list generation on some installations
 * Fixed issue where polygon 'Get Directions' link would fail
 * Fixed issue where category column would be empty for marker data CSV exports
 * Fixed issue where hide image option was not being respected for carousel listings
 * Fixed issue where modern info-windows would not close when close on map click was enabled
 * Fixed issue where polygon click would cause polygon info-window to close in OpenLayers, when close on map click setting was enabled
 * Fixed issue where the 'Show center point as an icon' image uploader would not activate for the Store Locator
 * Fixed issue where directions box would not apply a default width on frontend if none is set
 * Fixed issue where 'Directions Box Open by Default' setting would not be respected with compact/minimal user interfaces. Bear bones is the only option which ignores this entirely
 * Fixed issue where modern 'show options' directions styles were applied to other layouts
 * Fixed issue where the step selection in OpenLayers directions was not focusing, or firing as you would expect
 * Fixed issue where OpenLayer Polygon info-windows would be misplaced based on a zoom level
 * Fixed issue where modern directions panel would be too small on mobile devices
 * Fixed issue where modern directions panel would add 15px padding to the left of the internal container
 * Fixed issue where modern marker info-window (panel) focus button would not function as expected
 * Fixed issue where opacity field in polygon CSV importer would not support legacy naming
 * Fixed issue where the user location marker retina checkbox would not store correctly
 * Fixed issue where the store locator center point marker retina checkbox would not store correctly
 * Fixed issue where the directions origin and destination markers retina checkbox could not be disabled once enabled
 * Fixed issue where store locator center point would not respect retina option on frontend
 * Fixed issue where user location marker would not respect retina option on frontend
 * Fixed issue where directions origin and destination markers would not respect retina option on frontend
 * Fixed issue where update output would sometimes fail to unserialize data
 * Fixed issue where directions step renderer would not respect store locator distance. This will likely become a new setting later.
 * Added check to prune import logs when they exceed a specific file size (5mb)
 * Removed PHP8 conditional lockouts, as we now support PHP
 * Removed custom CSS localization, we don't need to do this in Pro. Now fully managed by basic
 * Added additional output logging to the importer to provide better insight into failures and processing
 * Added support for sticky column import when using CSV importer
 * Added support for PHP8, this is a prelim pass but from tests works well. May be revisited in the future
 * Added support for 'userCreated' VGM markers, specifically in the hide markers until search is done logic
 *
 * 8.1.10 - 2021-03-08
 * Fixed issue where default user icon could not be uploaded, button unresponsive
 * Fixed issue where automatic backups system would try and backup ratings table, even when it is not available
 *
 * 8.1.9 - 2021-02-18
 * Fixed issue where modern info-windows could not be reopened once closed in OpenLayers engine
 * Fixed issue where marker listing that were pushed into the map would cause a page scroll to the map container
 * Fixed issue where post meta custom fields functionality would not drop a pin at the specified lat/lng
 * Fixed issue with 'miles away' spacing on store locator searches
 * Fixed issue where fit bounds to markers setting would cause a lat/lng error when no markers have been placed on the map
 * Fixed issue where mashup shapes would not load, only marker data would be respected
 * Added option to show/hide store locator distances 
 * Added automatic backup fuctionality (beta)
 *
 * 8.1.8 - 2021-02-04
 * Fixed an issue with warnings being shown for categories on certain hosts if the category is a parent of itself
 * Fixed issue where parent filtering would not bubble from child categories (Result of patch above)
 * Fixed issue where clicking a marker listing to open a marker with clustering enabled may not zoom in far enough to open the cluster (Gold)
 * Added description support to polygon KML imports
 * 
 * 8.1.7 - 2021-02-01
 * Fixed a bug where marker galleries would not initialize on some installations
 * Fixed a bug where having modern popout marker listing (6) assigned pre-update would disable info-windows when changing marker listings
 * Fixed a bug where integrations could be enabled, although there 3rd party plugins were not found
 * Fixed a bug where OpenLayers would not draw the route polyline for directions
 * Added support for polygon info-windows OpenLayers
 * Added option to disable polygon info-windows
 * Added option to fit map to route bounds for directions system
 * Added GeoJSON import support (Point, Line, Polygon). This the first version of this system, some changes may be required as customers begin using this feature more.
 *
 * 8.1.6 - 2021-01-26
 * Fixed issue where default Google marker icon would use http protocol in basic table and grid marker listing styles
 * Fixed issue where disabling datables for marker listings would also prevent it from loading in admin area (Map/Marker lists)
 * Fixed issue where meta attempts to set itself when not available in advanced table listing
 * Fixed issue where no descriptio would be saved if you use only the TinyMCE 'text' editor
 * Fixed issue where legacy marker listing option would take preference after resaving the map with new settings. Could not disiable/change a marker listing style
 * Fixed issue where fit to bounds settings would be enabled by default for users migrating from V8.0 to V8.1
 * Fixed issue where hide point of interest settings would be enabled by default for users migrating from V8.0 to V8.1
 * Fixed issue where user location icon selector would not be shown in map editor 
 * Fixed issue where store locator center icon selector would not be shown in map editor
 * Fixed issue where 'disable lightbox' setting would not deactivate lightboxes
 * Fixed issue where 'disable lightbox' map setting would not affect the single map, only global would be used
 * Fixed issue where 'override user location zoom level' toggle option was missing
 * Fixed issue where 'user location zoom level' slider was missing
 * Fixed issue where category retina option would not be applied to a marker
 * Added option to disable Owl Carousel library from loading
 * Added option to disable Owl Carousel theme from loading
 * Added import logs to advanced area of the plugin
 *
 * 8.1.5 - 2021-01-21
 * Fixed issue where marker icon would be set to the category icon when editing a marker and saving it, this causes the icon to become static, no longer feeding from categories
 * Fixed issue where it was possible to set a category parent to itself, causing an recursive loop
 * Fixed issue where recursion loop notice would be shown causing all category functionality to fail
 * Fixed issue with moving circular category definitions into the global stack
 * Fixed issue where 'upload' button would not work in the category creator/editor
 * Fixed issue where kml option was map incorrectly
 * Added DELETE method simulation for map deletion end point
 * Added notice for legacy circular categories to the category editor, suggesting the user resave the parent field to a preferred value
 * Improved the Request Denied error decription when importing marker data
 * Removed category image field, it is not used anymore as we now have an icon controller instead which manages this correclty
 * Removed console log from pro polygon module
 * Fixed Modern InfoWindow stlying issues
 *
 * 8.1.4 - 2021-01-18
 * Fixed issue where directions logic attempt to run on polygon info-windows
 * Fixed issue where polygon do not auto-click link if map setting is enabled
 * Fixed issue where polygon info-window would not open when using non-default info-window style
 * Fixed issue where store locator no results found message would be shown twice
 * Fixed issue where store locator not found text setting was not respected
 * Fixed issue where store locator could not have category selection enabled
 * Fixed issue where fit to map bounds would not run on map initialization if enabled
 * Fixed issue where user location would be requested in the map editor on page load
 * Fixed issue where polygon hover opacity would not be respected
 * Fixed issue with storing custom field values for a specific marker
 * Fixed issue with custom field filtering stylings
 * Fixed issue where ACF markers would not respect the map default marker
 * Fixed issue where ACF integration cannot be disabled when enabled
 * Fixed issue with marker listing sort by distance on some installations
 * Fixed default infowindow height and width not respected in frontend
 * Fixed issue with category selection not being respected for markers
 * Fixed issue where link text override was not being respected in the advanced table listing style
 * Fixed issue with push in map control not woring for in some instances (zIndex undefined)
 * Fixed issue where hide address from info-window would not work with modern info-window styles
 * Fixed issue where marker library would not be available in settings area. This affected some add-ons
 *
 * 8.1.3 - 2021-01-14 - Low priority
 * Fixed a bug with near vicinity markers not working (gold add-on)
 * 
 * 8.1.2 - 2021-01-13 - High priority
 * Fixed a bug where the "Open infowindow links in new tab" was not working
 * Fixed an issue where the MarkerLibraryDialog would cause headers already sent error on some sites, causing a white screen on admin-post.php
 * Fixed an issue with ManageWP remote update check causing a PHP warning due to an undefined global variable
 * Fixed an issue where an empty string might sometimes be output during the update check
 * Fixed an issue where frontend datatables would not load the datatables language files for non 'en' locales
 * Fixed an issue with overflow scroll being forced on some marker listings
 *
 * 
 * 8.1.1 - 2021-01-11 - High priority
 * Fixed issue with custom markers not loading when paginating to the next page of the data tables results in the map editor
 * Fixed issue with new marker listing style setting
 * Fixed bug with the hide markers until a search is done not being enabled even though the setting is enabled in the database
 * Fixed issue where all categories would be shown to all maps, even when the category is not assigned to the map in question
 * Added setting to control category filter logic (AND / OR)
 * Added setting to control category filter style (Dropdown / Checkbox) 
 * Fixed Retina-ready marker icon bug
 * Added a new "Edit" button in the marker infowindow within the map editor
 * Fixed a bug that caused "headers already sent" on the map editor page in some cases
 * 
 *
 * 8.1.0
 * DataTables strings now fully customisatble, with options to hide specific table components
 * New, easy-to-use and highly efficient shape drawing tools
 * New, searchable, paginated, sortable tables for polygons, polylines, heatmaps, circles and rectangles
 * New Vector render mode setting for OpenLayers - Significantly improves performance with large amount of markers
 * New "batched marker loading" feature allows marker loading to be broken up into parts for a smoother loading experience with large amount of markers
 * OpenLayers now fully supports shapes
 * Map editor now "all-in-one" with all controls on a single page
 * Map editor and settings page are now fully W3C and WCAG compliant
 * Map editor and settings page are now using DOM for easy and flexible customisation
 * Map editor and settings page now handle setting serialization dynamically
 * Marker, polygon, polyline, heatmap, rectangle and circle panels now handle setting serialization dynamically
 * All backend content, logic and presentation is now separate
 * All miscellaneous JavaScript now fully modular and fully extensible
 * AJAX loading fully supported
 * Fixed issue with ACF permalink imports
 *
 * 8.0.37 :- 2020/12/22 :- Medium priority
 * Added compatibility notice check for PHP8 users, to prevent fatal errors when switching to PHP 8 (Not currently supported)
 *
 * 8.0.36 :- 2020/12/17 :- Medium priority
 * Fixes issues with marker library not initializing for some users
 *
 * 8.0.35 :- 2020/12/10 :- High priority
 * Tested up to WordPress 5.6
 * Added support for jQuery 3
 * Fixed bug with map duplicate button
 * Fixed bug with map trash button
 *
 * 8.0.34 :- 2020/07/22 :- Medium priority
 * Fixed category ID's missing from CSV import
 * Fixed styling isues - Affects VGM submit button
 * Fixed missing map priority and linked map column in the categories tab
 * Fixed WPGMZA is undefined on advanced page
 * Fixed distance and distance sorting incorrect for ACF markers
 * Fixed incorrect radius for Earth in distance calculations (now 6,371km instead of 6,381km)
 * Fixed shapes not appearing on map editor if global settings have never been saved
 *
 * 8.0.33 :- 2020/07/10 :- Medium priority
 * Fixed WPGMZA.XMLCacheConverter is not a constructor
 *
 * 8.0.32 :- 2020/07/09 :- Medium priority
 * Fixed too many GDPR notices when using multiple maps on one page on the front end
 * Fixed unapproved markers causing issues with marker listing
 *
 * 8.0.31 :- 2020/06/12 :- Medium priority
 * Fixed Google Autocomplete not working due to failed check in minified code
 *
 * 8.0.30 :- 2020/06/10 :- Medium priority
 * Added new Cloud API functionality
 * Fixed infinite loop when category has parent set to itself
 * Fixed Advanced Table marker listing sort by distance not working on PHP installations which evaulate string "false" as true
 *
 * 8.0.29 :- 2020/05/07 :- Medium priority
 * Retina support added for OpenLayers markers
 * Mashup maps now aggregate marker category counts
 * Prevented quotes being escaped in category name
 * Fixed OpenLayers markers not visible in some instances
 * Fixed category table always showing "All Maps" for every category on admin category table
 * Fixed "All" not showing in category filters
 * Fixed poor UX with buttons moving on admin map table
 * Fixed category count showing count for all maps, rather than the current map
 * Fixed enter not triggering search on Modern style store locator
 * Fixed custom field and category data not duplicating with maps properly
 * Fixed IIS failing under certain conditions (+ now URL encoded for IIS servers in compressed path variables)
 * Fixed incorrect marker icon on newly created / newly edited markers before refreshing
 *
 * 8.0.28 :- 2020/04/21 :- Medium priority
 * Added safeguard for fatal error upon activating Pro on some installations due to marker icon class not loading
 * Re-added ProMarker.prototype.setIcon for compatibility reasons
 * Fixed custom marker icons not applied in OpenLayers
 * Fixed admin map table stuck on Processing... on some installations
 * Fixed marker saving default icon explicitly, causing category icons to be overwritten
 *
 * 8.0.27 :-2020/04/16 :- Medium priority
 * Optimized Google marker performance by removing redundant labels
 * Solved "Value must be an instance of WPGMZA.MarkerIcon or icon literal" following last patch
 *
 * 8.0.26 :- 2020/04/14 :- Medium priority
 * Added retina support to map default marker icon
 * Improved Custom Fields visibility user experience
 * Marker icon field now supports JSON
 * Added filter wpgmza_custom_field_filter_controller_query
 * Added filter wpgmza_custom_field_filter_controller_filtered_marker_ids
 * Fixed fullscreen lightbox workaround not working for single item galleries
 * Fixed retina setting not respected for category icons
 * Fixed cannot trash map containing markers with custom fields or categories
 *
 * 8.0.25 :- 2020/03/16 :- Medium priority
 * Minimum required basic version is now 8.0.20 (for DOMQueryResults and StoreLocator)
 * Improved OpenLayers gesture handling UX to prevent browser zooming when Ctrl is pressed over map viewport when the gesture notice is visible
 * Re-factored the category list
 * Added "replace map data" option to integration importers
 * "Fit bounds to markers after search" will no longer reset bounds, overriding store locator zoom, when no markers are found
 * acf_post_id shortcode attribute now opens marker infowindow
 * Sticky markers now have a high z-index
 * Developer Mode is now inaccessible
 * Tested up to WordPress 5.4
 * Deprecated wpgmza_get_icon
 * Fixed notices in class.directions-box-settings-panel.php
 * Fixed OpenLayers longitude not wrapping properly causing markers to disappear when moving outside -180 to +180
 * Fixed UI style setting not changing
 * Fixed inconsistent links with ACF markers between viewport and marker listing, when using specific permalink settings
 * Fixed zero opacity store locator not working with modern style
 * Fixed Cannot use 'in' operator to search for 'x' in undefined when pressing enter on OpenLayers modern style store locator
 * Fixed ol.Sphere is not a constructor preventing OpenLayers store locator w/legacy radius from working correctly
 *
 * 8.0.24 :- 2020/02/12 :- Medium priority
 * Changed UI style dropdown to radio buttons and added descriptions
 * Hide default directions options when using modern style, also change according to UI style
 * Added functionality to support multiple markers in focus attribute
 * Improved and added new checks to the fitMapBoundsToMarkers function 
 * Changed load to wpgmza_pro_load to fix collision
 * Made it possible to override category filter widgets
 * Made ProStoreLocator's categories property configurable
 * Added new setting useRawJpegCoordinates
 * Fixed address from picture not working since 8.0.23 optimisation
 * Fixed trashed maps appearing in Bulk JPEG import map select
 *
 * 8.0.23 :- 2020/02/06 :- Medium priority
 * Optimized marker panel, now only geocodes when the address has been changed
 * Markers can be edited after manual positioning without re-geocoding address
 * HTML now allowed in all default style infowindow fields
 * Database installer now uses WordPress' character set and collation, rather than the databases default
 * UX improvements in the Map Editor page and Map List page
 * Improvements to the way we handle Google Maps API Key warnings
 * Deprecated wpgmza_pro_update_control
 * Experimental WordPress' category integration for 3rd party integrated markers
 * Fixed ACF integration not working with new ACF data format
 * Fixed FeatherLight opening behind map in fullscreen
 * Fixed JavaScript error preventing OpenLayers InfoWindow from opening correctly
 * Fixed notice on fresh install in wpgmaps_admin_menu_custom_fields
 * Fixed issue with adding markers by right click
 * Fixed cannot change open infowindow by setting
 * Fixed modern style checkbox filters not clickable
 *
 * 8.0.22 :- 2020/01/30 :- Medium priority
 * Fixed Jump to nearest marker on initialization setting not saving
 * Optimized category marker count by only fetching counts when the show count setting is on
 * Set user location marker to always appear above other markers
 * Fixed no results when combining date / time custom field filtering with other widget types
 * Fixed greedy gesture handling not respected by OpenLayers
 * Fixed Jump to nearest marker on initialization not working
 * Temporarily removed two finger panning for OpenLayers due to inconsistent behaviour across touch devices
 *
 * 8.0.21 :- 2020/01/28 :- Medium priority
 * Added comprehensive warning messages when CSV import line ending conversion fails, allowed fallback when this happens
 * Added check for recursion in category tree to prevent tree build hitting memory limit
 * Temporarily removed two finger panning for OpenLayers due to inconsistent behaviour across touch devices
 * Fixed no results in advanced table after search when using MySQL 8.0 and sort by distance by adding ST_ prefixes for MySQL >= 8
 * Fixed JavaScript error when using OpenLayers and modern style infowindows
 * Fixed raw HTML entities being displayed in new category filter widgets
 *
 * 8.0.20 :- 2020/01/23 :- Medium priority
 * Suppressed warnings from DOMDocument when loading pro-marker-panel.html.php due to wp_editor returning invalid HTML
 * Hide store locator icon controls when using Auto search area
 * Fixed fatal error on categories page
 * Fixed CategoryTree counting markers more than once in ancestral nodes where the marker appears more than once in the child nodes
 * Fixed greedy gesture handling not respected by OpenLayers
 * Fixed styling conflict on Map Edit page
 *
 * 8.0.19 :- 2020/01/21 :- Medium priority
 * Added new branding
 * Removed padding from directions box on all placements
 * Removed duplicate infowindow link setting in global general settings
 * Removed Fusion Table field
 * "Replace map data" is now available when using "Use map ID's specified in the file"
 * Re-factored category filtering widgets
 * Fixed external behaviour directions box not populating destination address in native maps app
 * Fixed CSV importer not importing categories
 * Fixed modern style popout infowindow not respecting marker disableInfoWindow property
 * Fixed can't toggle infowindow link setting
 * Fixed "click" and "hover" not translatable
 * Fixed bad ID on custom CSS and JS textareas, category logic radio buttons, category filter type, open marker by and marker pull
 *
 * 8.0.18 :- 2020/01/16 :- Medium priority
 * Marker listing will cancel current REST request if reload is called during the request
 * Modern Directions styling improvements
 * Fixed JavaScript error in DirectionsBox.getExternalURLParameters
 *
 * 8.0.17 :- 2020/01/15 :- Medium priority
 * Added Gesture Handling (Ctrl + Zoom and Two-finger pan) for OpenLayers
 * Added "Enable All Interactions" button to map edit page when one or more interactions are disabled
 * "No Google Maps API key entered" message will no longer obscure map for new users
 * ACF integration will now pull post title to display as marker title
 * Fixed error in WPGMZA.DirectionsBox.isUsingAppleMaps
 * Fixed custom field marker icon not appearing in InfoWindows
 * Fixed intelligent search not working after resetting the store locator
 * Fixed issue with store locator not found message
 * Fixed count issue with dropdown
 * Fixed Modern style infowindows will only open once
 *
 * 8.0.16 :- 2020/01/07 :- Medium priority
 * Corrected typo in previous changelog
 * ProDatabase no longer specifies InnoDB as database engine, enabling support for MyISAM
 * ProDatabase no longer specifies UTF-8 as character set for tables, the default for the database will be used
 * Scheduled import now assumes JSON in absense of file / URL extension
 * Issue warning for duplicate custom field names
 * Added checks for Gold version in marker listings
 * Fixed table headings in marker description breaking Advanced Table marker listing
 * Fixed unexpected output during activation
 * Fixed error when trying to pick marker from Mappity Marker Library
 * Fixed stack trace displayed when submitting VGM marker
 * Fixed incorrect marker count on category filter checkboxes when using category count
 * Fixed custom field filters not resetting correctly
 * Fixed directions route not working if language is not supported
 *
 * 8.0.15 :- 2020/01/02 :- Medium priority
 * Enhanced failed importer geocode logging and error reporting
 * ToolSet / WooCommerce integration now labelled "ToolSet"
 * WPGMZA.EventDispatcher can now unbind all event listeners with off / removeEventListener
 * Marker Library Dialog strips base64 header from icon data which was causing some servers to block the upload request
 * Added warning to Developer Mode setting
 * New function WPGMZA.DirectionsBox.prototype.getExternalURLParameters
 * New function WPGMZA.DirectionsBox.prototype.getWaypointAddresses
 * New property WPGMZA.DirectionsBox.prototype.openExternal
 * Directions box logic now DRYer
 * "No Global Setting" changed to "Inherit Global Setting" and now uses translatable text
 * Importer now supports CR newlines 
 * Custom field filter checkbox widget no longer displays empty values
 * Sort by rating no longer displayed for users running Gold < 5.0.0 (or without Gold)
 * InfoWindow galleries now floated right
 * Improved Modern style Store Locator category filtering user experience
 * Fixed waypoints being ignored by external directions
 * Fixed redirection loop after entering Google Maps API key into prompt on map edit page
 * Fixed "Add Marker" button stays disabled when no results are found for the entered address
 * Fixed "Cannot read property 'markerFilter' of undefined" in modern-store-locator.js
 * Fixed bottleneck significantly decreasing map editor load time when using DB pull
 * Fixed OpenLayers HTML marker in wrong position after refreshing and switching tabs during load
 * Fixed ProMarker writing "categories" to other_data
 * Fixed classname for sticky marker listing items set as .wpgmza-sticky
 * Fixed category picker not populating from marker categories array
 * Fixed marker listing sort by category sorting on numeric category ID's rather than category names
 * Fixed infowindowclose.wpgmza being dispatched from the map, rather than the infowindow
 * Fixed category data missing after import on markers created with Pro >= 8
 *
 * 8.0.14 :- 2019/12/13 :- Medium priority
 * New event on window - ready.wpgmza. This fires when all modules are ready, but maps haven't started loading
 * WPGMZA.LatLng.fromString is now static
 * Removed console logging on theme panel when theme data is invalid
 * Fixed OpenLayers geocoder trying to geocode coordinates resulting in markers added by coordinates moving
 *
 * 8.0.13 :- 2019/12/11 :- Medium priority
 * Added new method applyIntegrationQueryClauses to ProMarkerFilter
 * Added ProMarker::getIntegratedPermalink()
 * Added WPGMZA.LatLngBounds.prototype.toLiteral
 * Enhanced OpenLayers directions, now multilingual
 * Enhanced ModernDirectionsBox, now inherits DirectionsBox, loads and opens via standard mechanism
 * Event markerlistingupdated now bubbles up to map
 * CSV importer now converts UTF-8 special characters to HTML entities
 * Prevented scrolling when dataTables responsive expand / collapse button is clicked
 * Moved modern style store locator title search after address
 * Removed console logging from OLDirectionsService
 * Fixed modern infowindow custom colours not correctly applied when two or more maps with different colour settings are present
 * Fixed experimental vector source layer markers firing multiple click events when their icons overlap
 * Fixed ProStoreLocator.prototype.onFilteringComplete being called twice
 * Fixed modern marker listing directions button not working
 *
 * 8.0.12 :- 2019-11-26 :- Medium priority
 * Added check for $wpgmza in updater code to prevent fatal error when updater tries to run a check without WP Google Maps installed
 * Added clear warning and suggestions when server side geocoding fails due to HTTP referrer check failing
 * Added animated panning effect for OpenLayers infowindows, including a dynamic offset to make infowindows fully visible on click
 * Added "keep map ID" setting to CSV importer
 * acf_post_id shortcode attribute now supports "this" as a value
 * Relaxed handling of bad map ID's from shortcodes to issue a warning rather than throwing an exception
 * Directions box no longer attempts to initialise on back end
 * Fixed notice in class.category-tree-node.php
 * Fixed shortcodes not being rendered in Advanced Table marker listing description field
 * Fixed modern style directions box opening when directions behaviour is set to external (or intelligent on mobiles)
 * Fixed fallback marker listing not loading when sort by distance is used on non https:// sites
 * Fixed pagination widget display not resetting to first page after resetting a store locator search
 * Fixed modern InfoWindow not reading description from legacy XML format
 * Fixed infowindow directions link not working with multiple maps on a page
 * Fixed modern style directions box not displaying step by step directions
 * Fixed modern style directions box showing two native maps app icons
 * Fixed default map icon not respected in admin marker table
 *
 * 8.0.11 :- 2019-11-12 :- Medium priority
 * Added "fit map bounds to markers after flitering"
 * ACF integration module now understands doubles used in new versions of ACF
 * Fixed polygon fields not initially populated
 * Fixed duplicating map does not duplicate circles and rectangles
 * Fixed notices when duplicating
 * Fixed directions open by default not respecting all interface styles
 * Fixed directions turned on after updating
 * Fixed category remapping not working with existing category IDs
 * Fixed issue with OpenLayers circle colour
 *
 * 8.0.10 :- 2019-11-06 :- Medium priority
 * New directions box setting "Intelligent"
 * New directions box setting "Force Google Maps mobile app"
 * Moved heatmap table installation to ProDatabase
 * Deprecated wpgmaps_handle_db_pro
 * Integrated markers now use formatted permalinks based on WordPress' settings
 * Pan to user location no longer applied on map edit page
 * User location sharing prompt now only appears once per session
 * Removed duplicate directions code from ProInfoWindow, solves directions always opening externally on mobile devices
 * Adjusted radio button styling to be circular
 * Fixed integrated markers not appearing
 * Fixed category table not being created upon activation
 * Fixed JSON importer not re-creating categories that have been trashed
 * Fixed undefined color on OpenLayers circle preventing marker listing from being updated on a store locator search
 * Fixed "No API key" error showing until the page is refreshed after entering API key in notice
 *
 * 8.0.9 :- 2019-11-04 :- Medium priority
 * Changed category name character limit from 50 to 200
 * Fixed ?skip_cache=1 breaking plain permalink REST URLs by detecting when query variables are in use
 *
 * 8.0.8 :- 2019-10-28 :- Medium priority
 * Restored marker edit panel marker icon picker now reverts to default marker icon after saving marker
 * Fixed notice when clicking "Get Directions" with external behaviour selected
 * Fixed notice in category tree on map edit page
 * Fixed "click opens marker link" not working
 * Fixed missing link button in modern style marker listing
 * Fixed unexpected behaviour with infowindows when using retina ready markers on mobile devices
 * Fixed directions box starts open not respected for new UI styles
 *
 * 8.0.7 :- 2019-10-22 :- Medium priority
 * ProMarker icon property is now recalculated after setting category / categories property
 * Re-added behaviour to center on marker following marker creation in map edit page
 * Setting global retina dimensions to blank will now cause GoogleProMarker to auto-detect the dimensions
 * Added a warning when in developer mode stating that marker listing is initialised even though no marker listing style is selected (to still enable support for marker listing category filter with no marker listing)
 * InfoWindow now tracks open / closed state in this.state
 * InfoWindow no longer dispatches infowindowclose.wpgmza event if already closed
 * Fixed icon initially incorrect on map edit page when using category icons on marker
 * Fixed global retina dimensions not respected
 * Fixed notice when trying to update gallery items
 * Fixed notice issued when ProMap tries to reference marker listing element when no marker listing style is present
 * Fixed directions link not working in default style InfoWindow
 * Fixed marker icon picker preview not updated after using marker library
 *
 * 8.0.6 :- 2019-10-18 :- Medium priority
 * Fixed default marker icon overriding category icon after editing a marker
 * Fixed notice due to marker listing attempting to initialise on back end
 * Fixed "not found" error when attempting to import
 *
 * 8.0.5 :- 2019-10-17 :- Medium priority
 * ACF integration module now ignores posts with empty location data
 * OwlCarousels 100% image width overriden to fix large icons in carousel
 * Added vertical margin to carousel marker icons
 * Fixed description not visible in Modern style marker listing
 * Fixed link not visible in Modern style marker listing
 * Fixed invalid element for marker icon picker on category list page
 * Fixed notice for wpgmza_override_users_location_zoom_levels in legacy-core.php
 * Fixed directions link not working for mashup markers in marker listings
 * Fixed can't open modern style infowindows for mashup markers
 * Fixed marker description not visible in modern style marker listing marker view
 * Fixed directions link not scrolling down to box in modern style marker listing marker view
 * Fixed Google / Apple icon not displayed properly in modern style directions box
 * Fixed [object Object] in directions destination when using modern style infowindows
 * Fixed description not visible in Modern marker listing marker view
 * Fixed shortcodes not being rendered in marker listings
 * Fixed pagination not working for Advanced Table listing since 8.0.3
 * Fixed "No results" in advanced table when only one result is present
 *
 * 8.0.4 :- 2019-10-15 :- Medium priority
 * Added workaround for Enfold theme issue with Safari preventing map from loading
 * Theme parser will now attempt to strip slashes before abandoning parsing
 * Fixed cannot change marker listing type (wrong selection displayed)
 * Fixed "cannot read propertly 'thumbnail' of undefined" in modern infowindow when marker has no pictures (fixes fields not displayed in modern infowindow)
 *
 * 8.0.3 :- 2019-10-11 :- Medium priority
 * Fixed can't save empty string in exclude/include API on page settings
 * Fixed order by distance not respected for Advanced Table marker listing
 * Fixed can't delete all gallery items
 * Added "Remove duplicate markers" button to Utilities tab
 * Added new Utilities tab on Advanced page
 *
 * 8.0.2 :- 2019-10-10 :- Medium priority
 * Fixed category page marker icon picker not working
 * Fixed dragging a marker also pans map when using OpenLayers
 * Fixed marker panel marker icon picker not updating when opening a marker for editing
 * Fixed retina checkbox missing from marker panel
 * Fixed manually entered retina dimensions not respected for SVG icons
 *
 * 8.0.1 :- 2019-10-08 :- Medium priority
 * Added streetview, bearing and pitch attributes for shortcode
 * Fixed issue with MarkerRatings class missing (fixes cannot save global settings, markers not displayed on some installations)
 * Fixed Warning: unserialize() expects parameter 1 to be string, array given
 * Fixed restructured code preventing marker delete and approve buttons from working
 * Fixed marker description shortcodes not rendered over REST API
 * Merged in 7.11.58
 *
 * 8.0.0 :- 2019-10-07 :- Medium priority
 * Added Advanced Custom Fields integeration
 * Added WooCommerce / ToolSet integration
 * Added Sort by Distance for marker listings
 * Added zoned searching (auto) for store locator
 * Added settings for directions box behaviour
 * Added route color, weight and opacity settings for directions
 * Added start and destination icon pickers for directions
 * Added text labels for polygons
 * Added descriptions for polygons
 * Added support for Directions with OpenLayers through OpenRouteService
 * Added bulk JPEG upload
 * Added marker gallery to support multiple images per marker
 * Added lightbox to expand marker gallery items upon click
 * Added new Grid style marker listing
 * Added several new REST API endpoints
 * Added date and time custom field filtering widgets
 * Added new theme editor
 * Added global user interface style setting
 * Added category sort order setting
 * Added sticky markers (always on top of marker listing)
 * Added comprehensive logging, log view and log download for imports and scheduled imports
 * Significant performance optimizations
 *
 * 7.11.57 :- 2019-09-26 :- Medium priority
 * Added new property isFilterable to markers
 * Fixed store locator search making separator grouped markers visible when group state was closed, obscuring group placeholder (group would not open)
 * Fixed hide all markers until a search is performed not working with marker separator
 * Fixed "zero results" message showing after resetting store locator when hide all markers until a search is performed is enabled
 *
 * 7.11.56 :- 2019-09-24 :- Medium priority
 * Added Astra theme compatibilty module, fixes InfoWindows not opening with Astra theme
 * Fixed invalid heatmap gradient JSON breaking map edit page
 * Fixed sort order wrong after initial advanced tables pagination on servers that interpret string "false" as true
 *
 * 7.11.55 :- 2019-09-19 :- Medium priority
 * Fixed create shapes buttons not working in map edit page before Global Settings (engine) have been saved
 * Fixed Modern Store Locator Radius causing Safari (mobile) to reload page
 * Fixed Advanced Table sort order not respected after pagination first changed
 *
 * 7.11.54 :- 2019-09-13 :- Medium priority
 * Added markerlistingupdated.wpgmza event to AdvancedTableMarkerListing
 * Fixed markers not visible in Internet Explorer when using OpenLayers (append is not a method)
 * Removed try/catch around legacy core marker add code to ease debugging
 *
 * 7.11.53 :- 2019-09-10 :- Medium priority
 * Added more comprehensive logging for CSV imports, including when address empty and no coordinates
 * Added jQuery dependency for remodal, fixes JS errors in edge cases
 * Fixed poor desktop user experience with checkbox custom field filter widget
 * Fixed issue where sort would be ignored with advanced table when overrideListingOrderSettings had been cast to an integer
 * Removed experimental polygon label function
 * 
 *
 * 7.11.52 :- 2019-08-28 :- Medium priority
 * Added new setting importer_google_maps_api_key
 * Added additional protocol stripping to WPGMZA.ProMarker.prototype.updateIcon (fixes some icons not loading when migrating to https://)
 * Adding icons through Marker Library (Mappity.org) now creates media attachments
 * Changed deprecated references to wpgmza_localized_strings to WPGMZA.localized_strings
 * WPGMZA.ProInfoWindow now dispatches infowindowopen.wpgmza with modern styles
 * Fixed all URL imports (except Google Sheets) assumed to be JSON format, now uses extension (will use Content-type in the future)
 * Fixed mashupIDs not used by ProMarkerFilter
 * Fixed 0 key custom fields being added to custom field data
 *
 * 7.11.51 :- 2019-08-12 :- Medium priority
 * Fixed $ is undefined in legacy-map-edit-page.js since 7.11.49 on some installations
 *
 * 7.11.50 :- 2019-08-09 :- Medium priority
 * Fixed users below administrator level can't use marker library
 * Fixed JSON importer not correctly remapping categories
 * Fixed "hide all markers before a search is done" hiding markers on back end
 *
 * 7.11.49 :- 2019-07-31 :- Medium priority
 * Added nonce check to duplicate map function
 * Fixed map type setting not "live" in map editor
 * Fixed map type setting not initially reflected in map editor
 *
 * 7.11.48 :- 2019-07-29 :- Medium priority
 * Fixed store locator not working in OpenLayers since 7.11.47 due to missing nonce
 *
 * 7.11.47 :- 2019-07-29 :- Medium priority
 * REST API security enhanced with additional per-route nonces
 * getScrollAnimationOffset now accounts for WP admin bar height
 * removeMarker now closes marker InfoWindow before removing it
 * Fixed advanced table search having no effect when used in conjunction with a store locator search
 * Fixed markers not removed from map panel following bulk delete
 * Fixed notice when entry already exists in category import
 *
 * 7.11.46 :- 2019-07-23 :- Medium priority
 * Fixed marker library not saving icon on category page
 * Fixed duplicate primary key notice when saving categories
 *
 * 7.11.45 :- 2019-07-22 :- Low priority
 * Added infowindowopen event for Modern style infowindows
 * Added new filter wpgmza_legacy_localize_polygon_data
 * Altered wpgmza_get_category_data to respect category priority
 * Fixed category priority not respected by marker category icon in map panel
 *
 * 7.11.44 :- 2019-07-11 :- High priority
 * Closed potential security vulnerabilities
 * Added console warning when older version of dataTables is loaded
 * Fixed category filtering not working with legacy style store locator
 * Fixed click event for map firing when a marker is clicked
 * Re-added AJAX fallback for setups with REST API blocking
 *
 * 7.11.43 :- 2019-07-05 :- Medium priority
 * Fixed category filtering not working with modern style store locator
 * Fixed marker links being stripped of percent encoded characters
 *
 * 7.11.42 :- 2019-07-02 :- Low priority
 * Added admin-ajax fallback for when REST API is blocked
 *
 * 7.11.41 :- 2019-06-25 :- Low priority
 * Mappity (Marker Library) loading deferred until dialog opens
 *
 * 7.11.40 :- 2019-06-24 :- Medium priority
 * Added new event userlocationmarkerplaced.wpgmza
 * Fixed advanced table marker listing stuck on "Processing..." when no results are found
 * Fixed "Get directions" in InfoWindow not working
 * Fixed checkbox category filtering not triggering filter update
 * Fixed REST API warning persisting after dismissal
 *
 * 7.11.39 :- 2019-06-21 :- Medium priority
 * Fixed marker listing category filter not triggering filtering update since last update
 * Fixed marker listing directions link not working
 *
 * 7.11.38 :- 2019-06-19 :- Medium priority
 * Fixed store locator category checkboxes triggering marker filter to update before any buttons were pressed
 * Fixed undefined variable notice in class.pro-admin-marker-table.php
 * Fixed clicking advanced table marker listing list item not updating map panel
 *
 * 7.11.37 :- 2019-06-18 :- Low priority
 * Added checkbox to disable Autoptimize workaround for setups where the workaround prevents CSS aggregation
 * Performance for filtering and marker listings significantly improved when using REST caching / CDN and experimental compressed request setting
 * Fixed issues with Safari and modern style store locator radius, check now added to ProStoreLocator module
 * 
 * 7.11.36 :- 2019-06-12 :- Low priority
 * Improved handling of invalid JSON data in custom fields attribute column
 * Fixed ambiguous column `active` in page.categories.php
 * Fixed store locator center point icon not respected
 *
 * 7.11.35 :- 2019-06-06 :- Low priority
 * Added experimental setting "Use compressed path variables" to enable CDN's and JSON caching plugins to cache REST responses
 * Added notice when PHP version is 5.4.45 in which a PHP bug causes localization to fail
 * Fixed custom field ID's being writable
 *
 * 7.11.34 :- 2019-06-03 :- Medium priority
 * Added nonce to settings form on admin post action
 * Attempting to set a custom field which does not exist will now create the field
 * Changed background to background-image in data_table_front.css to allow more flexible control over table headings in Advanced Table marker listing
 * Fixed error in class.pro-marker-filter.php when Basic has non-standard folder name
 *
 * 7.11.33 :- 2019-05-29 :- Medium priority
 * Added properties category and categories to ProMarker
 * ProMarker categories can now be set as an array or as a comma separated string through new properties
 * JSON importer now uses CRUD classes
 * Fixed lat and lng shortcode attributes not working
 * Fixed "can't use method return value in write context" on PHP <= 5.4
 * Fixed "no results found" not showing when new MarkerFilter returns zero results
 *
 * 7.11.32 :- 2019-05-23 :- Medium priority
 * Tested with WordPress 5.2.1
 * Fixed Modern Store locator title search not working
 * Removed !important from CSS for border on .wpgmza-basic-listing-image-holder img
 *
 * 7.11.31 :- 2019-05-21 :- Medium priority
 * Fixed $ is undefined in legacy-map-edit-page.js
 * Fixed store locator category filter triggering marker listing category filter update mechanism
 * Fixed google maps check in core.js
 * Fixed ProMarkerFilter update not working with hide all
 * Add support for Google MyMaps KML polygon style (normal/highlight) imports
 * Add support for Custom Field importing via marker CSV imports
 *
 * 7.11.30 :- 2019-05-20 :- Medium priority
 * Added checks for non-standard basic directory name causing errors in class.pro-map.php
 * Added new settings WPGMZA.settings.forceNativeDirections
 * Fixed polygons and polylines not visible when engine is undefined (checks now assume Google by default)
 * Fixed "Hide all markers until a search is done" revealing all markers when a not found location is entered
 * Relaxed MIME type checking for JSON in scheduled imports (pending fix on a WordPress issue)
 *
 * 7.11.29 :- 2019-05-13 :- Low priority
 * Tested with WordPress 5.2
 * Added more robust error handling for missing files and failed initialisations (when NOT in developer mode)
 * Added more robust checks for Custom Fields tables in CustomFields::installed()
 * Fixed notice in legacy-core.php
 *
 * 7.11.28 :- 2019-05-08 :- Low priority
 * Fixed REST API endpoint / map editor not retreving a ProMarker, causing custom fields to be blank in editor
 * Added the ability to toggle auto night mode as well as a theme
 * Added a min height to bakend map so that it does not break when height is set to 100%
 * Added alt attribute to all marker listing styles
 * Added shift-click range selection to admin marker table
 * XML URL's are now protocol-less, solving issues when migrating to a secure site
 *
 * 7.11.27 :- 2019-04-16 :- Medium priority
 * Added categories JS module
 * Fixed incorrect value being interpreted as "yes" for the setting "store_locator_hide_before_search"
 * Fixed search not working for admin marker DataTable
 * Re-allowed iframe, input, select, option and img in description for users below administrator
 *
 * 7.11.26 :- 2019-04-12 :- Medium priority
 * Fixed "wpgmaps_localize" is undefined on map edit page when default marker is applied
 *
 * 7.11.25 :- 2019-04-10 :- Medium priority
 * Fixed markers disappearing after adding or editing marker following XML pull fix
 * Fixed memory leak on map edit page when adding, editing or removing markers
 * Corrected store locator circle covers polygons making them unclickable
 *
 * 7.11.24 :- 2019-04-09 :- Medium priority
 * Prevented reading $_GET['id'] in map setting overrides, this is now ignored, as it was causing conflicts on some setups
 * Re-implemented search for string in category and custom field data for admin marker table and Advanced style marker listing
 * Fixed undefined variable notice in class.category.php
 * Fixed infowindow displaying incorrect marker data on map edit page when using XML pull method
 * Fixed country restriction not respected by OpenLayers
 * Fixed Exporter where the deleted maps shows in the JSON file
 * Added Automatically Pan to user location setting
 *
 * 7.11.23 :- 2019-04-03 :- Medium priority
 * Re-implemented behaviour to hide all markers on store locator reset when "Hide all markers before a search is done" is selected
 * Fixed custom field filters not showing following Gutenberg Custom CSS fix (7.11.22)
 * Fixed wpgmaps-admin.css being enqueued on the front end
 *
 * 7.11.22 :- 2019-04-02 :- Low priority
 * Relaxed importer security restrictions to allow importing PHP over URLs (currently assumes JSON)
 * Added shortcode attribute classname
 * Added missing custom fields in modern infowindows feature
 * Fixed Custom CSS classes in Gutenberg editor not being applied
 *
 * 7.11.21 :- 2019-03-28 :- Low priority
 * Added "Hide points of interest"
 * Fixed infowindow opening on store locator center marker
 * Fixed animation and infowindow dropdowns blank when editing marker
 * Fixed SEO tools reporting 404 on Print Directions link
 *
 * 7.11.20 :- 2019-03-20 :- Medium priority
 * Improved performance on saving markers and maps, rebuildTableFromLegacyField only performs operation on relevant object
 * Marker description no longer passed through KSES function for administrators
 * Moved code to initially add markers to clusterer into onMarkersPlaced in ProMap
 * Fixed "cat" shortcode attribute not being applied when no category filtering UI controls are present
 * Fixed develoer mode checkbox not reflecting setting when developer mode is enabled
 * Fixed "hide all markers until a search is done" causing only markers in the first search radius to appear on the map
 * Fixed clusters appearing before a search is done when "hide all markers until a search is done" is selected
 *
 * 7.11.19 :- 2019-03-18 :- Medium priority
 * Fixed GDPR notice not displayed when it should be, when Google Translate JS API is loaded
 * Fixed emojis in description field breaking marker add/save mechanism
 * Fixed incorrect logic causing unapproved markers to appear in marker listing
 * Fixed store locator marker not cleared after resetting store locator search
 * Fixed store locator circle not cleared after resetting store locator search
 * Disabled infowindow on store locator center point marker
 *
 * 7.11.18 :- 2019-03-15 :- Medium priority
 * Prevented iterateOverMarkerData being called more than once per map, fixing filtering and clustering inconsistencies
 * Performance improved by only allowing iterateOverMarkerData to be called on initialisation
 *
 * 7.11.17 :- 2019-03-14 :- Medium priority
 * Changed isset check to empty check on developer_mode
 * Fixed developer mode being checked when developer_mode is false in memory
 *
 * 7.11.16 :- 2019-03-13 :- Low priority
 * Fixed markerid URL logic sometimes failing
 * Fixed mzoom ignored on URL
 * Fixed Google / Apple / Maps App directions link opening on current location due to lack of GPS coordinates on Modern infowindows
 *
 * 7.11.15 :- 2019-03-11 :- Low priority
 * Added try/catch around legacy core.js marker add code to catch invalid marker data and issue console warning
 * Fixed "Hide address column" hiding title instead of address for Basic Table style marker listing
 * Fixed calling setZoom immediately after panTo on OpenLayers map cancelling pan animation
 *
 * 7.11.14 :- 2019-03-08 :- Low priority
 * Fixed modern marker listing marker view not working correctly with mashups
 * Fixed full width button container on modern marker listing obscuring close buttons
 *
 * 7.11.13 :- 2019-03-07 :- Low priority
 * Fixed store locator search creating duplicate markers
 * Fixed "Use My Location" button not appearing in Modern style store locator
 * Re-added "Use my location" button to store locator
 *
 * 7.11.12 :- 2019-03-05 :- Medium priority
 * Changed "Gold is not compatible with OpenLayers" notice to advise useres to update to Gold 4.11 or above to use Gold with OpenLayers
 * Changed Google Maps API error handler to render in a panel rather than in a modal dialog
 * Fixed jQuery shorthand in legacy-map-edit-page.js preventing marker edit on some installations
 * Fixed Circle.createInstance returning OLCircle by default where engine is undefined (the default engine is Google)
 * Fixed inconsistent behaviour with "Hide fields" settings in Maps -> Settings -> Marker Listings, code is now implemented in MarkerListing::removeHiddenFields
 * Fixed some columns missing in various marker listing styles
 * Re-added Link column to Advanced Table
 *
 * 7.11.11 :- 2019-03-01 :- Medium priority
 * Changed pointer cursor to default cursor when hovering a marker with "disable infowindows" set
 * Improved store locator UX - address and title search no longer cleared by resetting
 * Stopped markers being removed by InitMap following recent filtering optimizations
 * Fixed InfoWindow title not displayed in default style infowindow
 * Fixed global "disable infowindows" not respected
 * Fixed ProInfoWindow not detecting empty fields correctly and omitting corresponding elements
 * Fixed buttons missing from modern style infowindows
 * 
 * 7.11.10 :- 2019-02-28 :- Medium priority
 * Moved "User location marker" code to ProMarker, ProMap and ProInfoWindow
 * Moved wpgmaps_lang_my_location from global scope to WPGMZA.localized_strings
 * Moved check for modern marker listing style above check for native style infowindow, fixing both styles opening
 * Moved infowindow starts open, click opens link, shortcode to open marker and GET to open marker infowindow to ProMarker module
 * Re-factored and moved code to build default style infowindow to ProInfoWindow module
 * Removed "User location marker" code from core.js
 * Dropped !important from default InfoWindow image max width
 * Improved JavaScript performance by deferring InfoWindow HTML creation until needed
 * Non-standard jQuery versions now issue console warning rather than cancelling map initialisation
 * Fixed "Get directions" from marker listing not populating "To" field in modern style marker listing
 * Fixed mashups preventing marker listing from being displayed
 * Fixed Modern style marker listing trying to open on user location marker
 * Fixed Modern style marker listing trying to open on store locator center marker
 * Fixed marker filter affecting user location marker
 * Fixed "undefined" infowindow over user location marker
 *
 * 7.11.09 :- 2019-02-25 :- Low priority
 * Custom field filter reset button now sets dropdowns to their placeholder, as opposed to none / the first item
 * Fixed both style infowindows opening when map has never been saved
 * Fixed max infowindow width not being respected for default style infowindows
 * Fixed infowindow close event firing after infowindow element removed
 *
 * 7.11.08 :- 2019-02-22 :- Medium priority
 * Added try/catch in legacy-map-edit-page.js to catch bad latitude and longitude, issue warning and continue execution
 * Fixed bad marker ID in ProInfoWindow.open preventing modern style infowindows from opening in some circumstances
 *
 * 7.11.07 :- 2019-02-21 :- Medium priority
 * All marker data is now passed to WPGMZA.Marker.createInstance
 * Fixed undefined notice wpgmza_marker_list_ouput when using mashups
 * Fixed filtering not working correctly for mashup maps
 *
 * 7.11.06 :- 2019-02-20 :- Medium priority
 * Improved modern store locator UX by switching reset button back to search button when text is inputted into address field
 * Moved infowindow open and close code from core.js to Marker and InfoWindow modules
 * Moved infowindow open code from core.js to Marker Listing module
 * Added property "style" to WPGMZA.MarkerListing
 * Added global setting "disable_scroll_on_marker_listing_click" (not yet present on UI)
 * Added global setting "zoom_level_on_marker_listing_click" (not yet present on UI)
 * Added global setting "marker_listing_item_click_scroll_offset" (not yet present on UI)
 * Added event infowindowopen.wpgmza
 * Added event infowindowclose.wpgmza
 * Added timeout to prevent multiple control change events firing causing multiple, identical filtering requests to be issued
 * Added code to abort previous filtering request when controls change
 * Fixed previously opened infowindow not closing when opening a new default style infowindow
 * Fixed inconsistencies where different InfoWindow styles were picked in global vs local map settings
 * Fixed filtering not correctly applied when controls are rapidly changed
 * Fixed "disable double click zoom" logic flipped
 * Fixed bulk delete not working due to removed legacy function
 * Fixed "show user location" only working on last map where multiple maps were present
 *
 * 7.11.05 :- 2019-02-15 :- Low priority
 * Added code to support sort by distance in Pro v8
 * Re-added data-wpgmza-datatable-options attribute to datatables
 * Fixed "infowindow starts open" only working for one map on pages with multiple maps
 * Fixed clicking a marker on one map closes infowindows on other maps
 * Fixed undefined variable notice in class.export.php
 *
 * 7.11.04 :- 2019-02-13 :- Medium priority
 * Shortcode attributes are now passed to map element through data-shortcode-attributes
 * DataTables is no longer enqueued twice on map edit page
 * DataTables translation re-applied following new AJAX implementation
 * Fixed undefined variable proper_filename in page.import-export.php
 * Fixed filtering broken when no custom field filter controller present
 * Fixed search features broken on some installations where custom field filter tries to initialise before map
 * Fixed store locator keyword search not working due to malformed query
 * Fixed category shortcode attribute being ignored
 * Fixed marker listing not reflecting modern store locator results due to radius not being transmitted
 *
 * 7.11.03 :- 2019-02-11 :- Medium priority
 * Added extra functions to LatLngBounds to support upcoming Gold patch  (extendByPixelMargin, contains)
 * Added Caltopo to tile servers
 * Replaced $ with jQuery in several points in legacy-map-edit-page.js
 * Fixed issues editing markers, rectangles or circles on installations where $ is undefined on the map edit page
 * Fixed custom field dropdown filter widget using * to represent "all", where an empty string should be used instead
 *
 * 7.11.02 :- 2019-02-06 :- Medium priority
 * Moved basic version check out of legacy-core.php
 * Marker listings now show blank instead of all results when no store locator matches are found
 * Fixed advanced table stuck on "Processing" when no results are present
 * Fixed show all marker listing list items breaking advanced table marker listing
 * Fixed Advanced Table marker listing not respecting initial order settings
 *
 * 7.11.01 :- 2019-02-05 :- Medium priority
 * Added code to attempt to parse improperly escaped map settings JSON
 * Added code to catch exceptions when parsing map setting data fails, issuing a console warning
 * Restored old behaviour of Marker Category filter still used when "No Marker Listing" selected
 * Dropped all redundant listing filtering AJAX calls
 * Stripped all redundant listing JS and listing filtering JS from core.js
 * Fixed filter not clearing when no categories are selected
 * Fixed "Map Type" not being applied
 * Fixed marker not focusing upon Advanced Table item click
 * Fixed infowindow not opening upon Advanced Table item click
 * Fixed native and custom CSS and JS not binding to Advanced Table items due to missing classnames
 * Fixed filtering not applied to Advanced Table marker listing
 * Fixed store locator reset not triggering marker listing update
 *
 * 7.11.00 :- 2019-01-30 :- Medium priority
 * Added pagination to all marker listing styles
 * Added javascript ; to directions links for styling purposes
 * Added classnames to custom field admin table rows for back end and UGM
 * Added address geocoder and option to JSON importer
 * Added WPGMZA.LatLng.prototype.toLatLngLiteral
 * Removed map edit page 5,000 marker limit
 * Changed KML JS to use map_id rather than entry
 * Relaxed importer file type security checks preventing JSON import
 * Nominatim Cache now records country restriction
 * Improved robusticity of data-settings attribute handling
 * Marker listings now use JSON endpoint
 * Nominatim Cache now records country restriction
 * Animated scroll disabled on marker list item click when push in map is used
 * "Push in map" no longer affects Modern style marker listing
 * XML caches will only be regenerated when setting was DB previously
 * XML caches are no longer formatted, giving a performance boost
 * Checks for namespace, DOMDocument and WP REST API added in main file, a warning is issued if these are missing rather than a fatal error
 * Relaxed OLProMarker.setIcon to issue warning on an invalid icon rather than throwing an error
 * Fixed all filtering issues with Modern style marker listing
 * Fixed admin marker table resetting after adding / editing marker
 * Fixed and re-added link column to advanced table marker listing
 * Fixed slashes accumulating in map title where apostraphies are used
 * Fixed apostraphy in map title breaking JSON
 * Fixed duplicate primary key notice when creating first category in debug mode
 * Fixed country restriction not respected by OpenLayers
 * Fixed interaction controls (disable pan, double click zoom and mousewheel) ignored in OpenLayers due to wrong scope
 * Fixed zoom limits not respected in OpenLayers
 * Fixed zoom limits incorrectly interpreted by Google
 * Fixed "minZoom cannot exceed maxZoom" breaking Google maps when settings are reversed
 * Fixed modern style marker listing not working with mashup maps
 * Fixed WPGMZA.getCurrentPosition not firing callback until user moves position
 *
 * 7.10.60
 * Print directions now opens in a new tab
 *
 * 7.10.59 :- 2019-01-10 :- Medium priority
 * Temporarily removed new "link" column from advanced table pending a fix to DataTables error
 *
 * 7.10.58 :- 2019-01-09 :- Medium priority
 * Added universal workaround for non-visible maps not rendering properly when they become visible (tabs, accordions, etc.)
 * Moved event markersplaced.wpgmza into iterateOverMarkerData
 * Changed behaviour when map becomes visible to fire resize event, not reset the entire map
 * Fixed "InfoWindow starts open" not working with XML cache
 * Fixed "More Details" displayed on marker listing rows with no link
 *
 * 7.10.57 :- 2019-01-02 :- Medium priority
 * Fixed infowindows not opening on second map
 * Fixed can't close modern style infowindow
 * Removed CSS causing infowindow content overflowing without scrollbar
 *
 * 7.10.56 :- 2018-12-27 :- High priority
 * Altered real mime type check to prevent image uploads failing
 *
 * 7.10.55 :- 2018-12-27 :- Medium priority
 * Added event markersplaced.wpgmza
 * Added link field to marker listing options
 * Added placeholder to custom field inputs
 * Changed GoogleMap fitBounds to accept a WPGMZA.LatLngBounds
 * Changed OLMap fitBounds to accept a WPGMZA.LatLngBounds
 * Removed the requirement to enable "show user location" to use "use my address" on store locator
 * Fixed WPGMZA.LatLngBounds setting individual coordinates to LatLngs
 * Fixed WPGMZA.GoogleMap.fitBounds not working with native LatLngBounds
 * Fixed cannot import marker categories when categories checkbox disabled
 * Fixed Pro Gutenberg module issuing notice when map ID select in initial state
 * Fixed modern marker listing marker view retaining fields from last selected marker when new fields are blank
 * Fixed styling issues with moderm marker listing (buttons overlapping, negative margin on picture, buttons not visible)
 * Fixed modern directions box not opening
 * Fixed "Use my location" not working on store locator
 *
 * 7.10.54 :- 2018-12-20 :- Medium priority
 * Show user location now follows user location live
 * Bumped required basic version from 7.10.00 to 7.10.34 for ScriptLoader::getPluginScripts requirement
 * Removed closing PHP tag from class.google-pro-maps-loader.php
 * Fixed Modern Directions Box not opening from Modern style Info Windows
 * 
 * 7.10.53 :- 2018-12-18 :- Medium priority
 * Added missing spatial prefixes for marker column, circle and rectangle data functions
 * Added workaround to import_mimes for WordPress issue https://core.trac.wordpress.org/ticket/45615 causing CSV imports to fail
 *
 * 7.10.52 :- 2018-12-14 :- Low priority
 * Added missing spatial prefix to add marker AJAX callback
 * Fixed undefined errors when running Elementor
 * Fixed "Cannot read property 'wpgmza_iw_type' of undefined" preventing infowindow opening
 * Fixed categories page not respecting category priority
 * Fixed categories page dispalying categories without parent as child
 *
 * 7.10.51 :- 2018-12-12 :- Medium priority
 * Added XMLCacheConverter to convert XML data into DB method format
 * XML and DB method now both use the same JavaScript
 * Fixed Modern Marker Listing not working with XML marker pull method
 * Removed large section of repeated code for processing XML
 *
 * 7.10.50 :- 2018-12-10 :- Medium priority
 * Removed debug output following infowindow fix
 *
 * 7.10.49 :- 2018-12-10 :- Medium priority
 * Added blank alt attribute to OpenLayers marker img element
 * Updated WP version supported to 5.0
 * Fixed scroll to map code not firing following click on basic list marker listing
 * Fixed both modern and native infowindows opening when global and local settings differ
 * Fixed styling not correctly applied to modern infowindows when global and local settings differ
 *
 * 7.10.48 :- 2018-12-05 :- Low priority
 * Improved Gutenberg integration (new buttons added)
 *
 * 7.10.47 :- 2018-12-03 :- Medium priority
 * Custom field values are now passed through translation functions
 * Added a check for wp.editor in Gutenberg JS module
 * Fixed category dropdown filter selection not reflecting cat shortcode attribute
 * Fixed OpenLayers JavaScript broken when checking for Google Maps API, when other google modules are present
 * Fixed access level always administrator for custom fields page
 * Fixed access level always administrator for map general settings tab
 * Fixed WPGMZA.OLMap returning zoom one level too far in (fixes map zooms in one level on save)
 *
 * 7.10.46 :- 2018-11-22 :- Low priority
 * Fixed custom fields outputting blank attributes
 * Added data-custom-field-id attribute to front end output
 * InfoWindow description containing element changed from <p> to <div>, browsers will no longer push paragraphs out of the description element
 * Removed all redundant calls to getPlace
 * Places AutoCompletes now only request the "name" and "formatted_address" fields
 *
 * 7.10.45 :- 2018-11-20 :- Medium priority
 * Fixed traffic layer etc. always on in map edit page
 * Fixed general map settings (disable controls) not respected on map edit page
 * Fixed infoWindow sometimes has zero maxWidth when using markerid GET variable
 * Fixed store locator circle and radius not displayed when no markers are present
 * Fixed "dataTable responsive undefinded" (NB: dataTables will be enqueued twice, pending a different fix)
 * Fixed slashes accumulating in marker custom field value field
 * Unified store locator circle and radius logic for both XML and DB marker pull
 *
 * 7.10.44 :- 2018-11-12 :- Medium priority
 * Fixed places autocomplete not initializing with modern store locator
 * Fixed theme data not applied during map edit page load
 * Fixed conflict with Autoptimize with large amounts of data by bypassing CSS optimization where shortcode is present
 * Fixed category filtering not correctly applied using dropdown marker listing category filter
 * Fixed marker custom field data not duplicated on map duplicate
 * Fixed open link in new tab not respected when using XML marker pull method
 * Fixed modern directions box opening for all maps on page when get directions is clicked
 * Removed enqueue dataTables, already enqueued by ScriptLoader module
 * Enter key now triggers search on modern store locator
 *
 * 7.10.43 :- 2018-11-07 :- Medium Priority
 * Fixed WPGMZA.isSafari is not a function on Custom Fields page
 * Fixed heatmap notices and display issues on map edit page
 * Fixed heatmap radius and gradient not respected on map edit page
 * Fixed polygons not visible in map edit page
 * Fixed polylines not visible in map edit page
 *
 * 7.10.42 :- 2018-10-31 :- High priority
 * Fixed Gutenberg Pro module not loading correctly following safety fix
 * Fixed shorthand array syntax breaking PHP < 5.4
 * Fixed cannot add custom fields in Safari
 * Closed potential XSS vulnerability in PHP_SELF on map edit page
 *
 * 7.10.41 :- 2018-10-22 :- Low priority
 * Fixed custom field data fields not populated in legacy-map-edit-page.js when $ is not defined
 * Fixed custom field data not saving in the same circumstances as above
 * Fixed empty max infowindow width causing malformed CSS
 * Fixed empty max infowindow width causing zero max width when using GET variable
 * Fixed can't save GPS coordinate address when $ is not defined
 * Added CSS to limit infowindow image size by default (when no max infowindow width is defined)
 * Moved modules from core.js to /v8
 * Removed $ jQuery shorthand from various places for sites on which this is not defined
 *
 * 7.10.40 :- 2018-10-18 :- Low priority
 * Fixed issue with resetting modern store locator with OpenLayers engine
 *
 * 7.10.39 :- 2018-10-17 :- Medium priority
 * Fixed Infowindow not opening on touch device when using "hover" action
 *
 * 7.10.38 :- 2018-10-16 :- High priority
 * Fixed "Class 'WPGMZA\Gutenberg' not found" with Basic < 7.10.39
 *
 * 7.10.37 :- 2018-10-15 :- High priority
 * Fixed JS error when Gutenberg framework not loaded
 *
 * 7.10.36 :- 2018-10-15 :- Medium priority
 * Added markerselected.wpgmza event for marker listing
 * Added code to catch exception when trying to initialize fontawesome icon picker in custom fields page
 * Added mashup fix (use boolean rather than string for flag)
 * Paving the way for Gutenberg integration
 * Fixed jQuery dataTable version conflict when using external dataTables library
 * Fixed custom fields not cleared when pressing reset button
 * Fixed no markers visible following clearing custom fields
 * Fixed add marker button still disabled following failed geocode
 * Fixed $ is not defined in legacy-map-edit-page.js when using custom fields on sites where $ is not defined
 * Removed function body deprecated in 6.09
 * Removed .gitignore from /js/v8
 * Removed console.log in class.import-csv.php
 *
 * 7.10.35 :- 2018-09-27 :- Low priority
 * Fixed undefined variable on iOS breaking store locator
 * Fixed edit marker using REST API not working when API route has two slashes
 *
 * 7.10.34 :- 2018-09-25 :- Low priority
 * Fixed XML caches not regenerated following import
 * Fixed category not added when priority or parent column missing from legacy installation
 * Fixed marker listing initially blank when AND category filtering is selected
 * Fixed change in basic 7.10.35 causing problems with OLMarker click event, preventing infowindow opening
 * Dropped .gitignore which was causing deployment issues, now using .gitattributes to ignore minified files
 * Removed var_dump in legacy export code
 *
 * 7.10.33 :- 2018-09-20 :- Medium priority
 * Fixed marker dispatching click event after drag when using OpenLayers
 * Fixed map dispatching click event after drag when using OpenLayers
 * Fixed map editor right click marker appearing multiple
 * Fixed map editor right click marker disappearing after map drag
 * Fixed modern store locator circle crashing some iOS devices by disabling this feature on iOS devices
 * Fixed gesture handling setting not respected when theme data is set in
 *
 * 7.10.32 :- 2018-09-19 :- Medium priority
 * Added code to bail and issue meaningful error message when import gets near PHP execution time limit
 * Fixed $ is not defined in legacy-map-edit-page.js
 * Fixed missing map import functionality in v7 importer
 *
 * 7.10.31 :- 2018-09-17 :- High priority
 * Fixed some Google settings being enabled causing JS errors in legacy-map-edit-page.js
 *
 * 7.10.30
 * Added plugin load order notice when Basic loads before Pro as a workaround
 * Fixed can't disable category count after enabling
 * Moved map edit page JavaScript out of /wp-google-maps-pro.js and into /js/legacy-map-edit-page.js
 * All data now passed to map edit page through wp_localize_script
 * jQuery 3.x document ready compatibility changes
 * Removed redundant locationSelect dropdown
 *
 * 7.10.29 :- 2018-09-06 :- Low priority
 * Fixed carousel marker listing settings being reset on update
 * Fixed wp-google-maps-pro.php attempting to create google.maps.MarkerImage when using OpenLayers engine
 *
 * 7.10.28 :- 2018-09-05 :- Low priority
 * Improved JSON import error reporting
 * Fixed "Parameter must be an array or an object that implements Countable" in marker listing module
 * Fixed core.js attempting to create google.maps.MarkerImage when using OpenLayers engine
 *
 * 7.10.27 :- 2018-08-30 :- Medium Priority
 * Fixed NaN zoom level causing Google Maps to hang
 *
 * 7.10.26 :- 2018-08-29 :- Medium Priority
 * Improved CSV import parser to split on both newlines and carriage return
 * Improved GoogleAPIErrorHandler, modal dialog with documentation links is now shown back end and front end for administrators
 * Implemented setOptions for generic marker module and WPGMZA.GoogleMarker module
 * Added event storelocatorgeocodecomplete (native) and storelocatorgeocodecomplete.wpgmza
 * Added event storelocatorresult (native) and storelocatorresult.wpgmza
 * Cleaned up legacy searchLocations function
 * Fixed unexpected behaviour when importing with empty latlng column
 * Fixed map controls not applied without toggling developer mode
 * Fixed white border around new Google logo
 * Fixed some global settings not respected (zoom controls, etc.)
 * Fixed zero longitude when editing markers with OpenLayers engine selected
 * Fixed legacy code acknowledging longitudes -180 - -90, +90 - +180 as part of a lat/lng pair
 *
 * 7.10.25 :- 2018-08-22 :- Medium priority
 * Fixed OpenLayers Pro infowindow no longer opening following 7.10.23 fix
 * Fixed slashes added to title and description when adding marker
 * Fixed gesture handling not passed to map (NB: There are still issues with the Google API ignoring this)
 * Fixed must implement countable notice
 *
 * 7.10.24 :- 2018-08-17 :- Low priority
 * Fixed engine being switched to OpenLayers following saving settings on a fresh install
 *
 * 7.10.23 :- 2018-08-15 :- Medium priority
 * Fixed some combinations of infowindow settings causing infowindows not to open
 *
 * 7.10.22 :- 2018-08-15 :- Low priority
 * Fixed slashes being added to marker text fields when saving
 * Fixed Google infowindow opening when modern infowindow style selected
 * Fixed CSS styling issues following Google UI updates
 * Removed GoogleAPIErrorHandler (moved to Basic)
 *
 * 7.10.21 :- 2018-07-31 :- Medium priority
 * Fixed AND category filtering logic broken in marker listing module
 *
 * 7.10.20 - Medium Priority
 * Fixed can't reset store locator following searching a blank address
 * Fixed Undefined variable: wpgmza_force_jquery
 * Fixed addresses geocoding to 0, 0 following editing marker address
 * Removed custom fields filter div when no custom fields filters are present
 *
 * 7.10.19 - 2018-07-25 :- Medium Priority
 * Added Custom Fields reset button
 * Fixed can't save pre-existing marker with GPS coordinates as address
 * Dropped override jQuery setting
 *
 * 7.10.18 - 2018-07-23 :- Low Priority
 * Fixed call to undefined function in page.export-import.php for users running Basic <= 7.10.17
 * Fixed typo causing call this.markers undefined
 *
 * 7.10.17 - 2018-07-19 :- Medium Priority
 * Fixed [object Object] displayed instead of error message on import
 * Fixed misaligned settings in global settings panel
 * Fixed custom fields not cleared after editing marker
 * Fixed custom marker field data not exporting correctly
 * Fixed custom marker field data not importing correctly
 * Fixed mashup IDs not respected in modern marker listing
 * Fixed can't read property recalc of undefined (DataTables().reponsive) by adding a check
 * Fixed clustering broken (Gold add-on) when multiple maps are on one page
 * Fixed clicking list item doesn't center map properly in OpenLayers
 * Fixed client side marker data not updated after editing existing marker
 * Fixed edit marker button not re-enabled following unsuccessful geocode
 * Fixed "Parameter must be an array or an object that implements Countable" in MarkerListing class
 * JSON Custom Field import / export now uses custom field CRUD classes
 * Moved Import / Export page JavaScript into standalone file
 * Importer error detection improved
 * Importer error reporting more comprehensive
 * Importer will attempt to convert strings to UTF-8 where they aren't already
 * Improved "Edit Marker" to pull data from database through REST API (solves discrepencies between client and server side data when editing)
 *
 * 7.10.16 - 2018-07-06 :- Medium Priority
 * Removed custom fields message
 * Fixed "Disable InfoWindows" not working
 * Fixed importer not geocoding addresses correctly
 *
 * 7.10.15 :- Low Priority
 * Re-built minified JavaScript file following UGM changes in basic plugin
 *
 * 7.10.14 - 2018-07-05 :- Medium priority
 * Fixed can't disable require GDPR consent before load
 * Fixed Access to undeclared static property: WPGMZA\Plugin::$settings
 * Fixed hard coded map ID in circle
 * Fixed update code sometimes not running or geting wrong version number
 * Fixed add circle and add rect prototype issue
 * Fixed invalid markup in marker table
 * Fixed click marker listing item not selecting marker in map
 *
 * 7.10.13 - 2018-06-27 :- Low Priority
 * Fixed CSV importer only parsing single category
 *
 * 7.10.12 - 2018-06-18 :- Low priority
 * Added dataTables CSS to local folder (as opposed to CDN)
 * Fixed "please update basic" notice always displayed
 *
 * 7.10.11 - 2018-06-14 :- Low priority
 * Fixed category marker icon not respected
 *
 * 7.10.10 - 2018-06-13 :- Medium priority
 * Fixed store locator reset not working
 * Fixed disabling map controls not working
 * Fixed store locator radio button
 *
 * 7.10.09 - 2018-06-12 :- Medium Priority
 * Handed FontAwesome loading over to ScriptLoader module
 * Deprecated global function wpgmza_enqueue_fontawesome
 * Fixed circles and rectangles only working on map ID 1
 * Fixed mashup not respected in advanced table listing after changing category filter (missing code)
 * Cleaned up marker creation code ahead of modularization
 * Fixed "use my location" button not working
 *
 * 7.10.08 - 2018-06-08 :- Low priority
 * Fixed uncaught JS error in some instances, where Places API was not enqueued
 * Fixed directions dipslay not working
 * Temporary workaround for "Unexpected token % in JSON"
 *
 * 7.10.07 - 2018-06-05 :- Medium priority
 * Fixed map center stuck on focused marker lat/lng after store locator search when using shortcode attribute "marker"
 * Fixed src not set on HTML marker img when using a custom category marker icon and OpenLayers engine
 * Fixed mashup not respected in advanced table listing after changing category filter
 * Fixed stuck on "Saving..." after editing marker
 * Fixed "InfoWindow" starts open breaking map edit page
 * 
 * 7.10.06 - 2018-06-01 :- Medium priority
 * Fixed $ is not a function in some circumstances
 * Fixed call to enqueueGoogleMaps on null when saving post
 *
 * 7.10.05 - 2018-06-01 :- Low priority
 * Re-build minified JS after fixing "Can't drag marker in edit marker location page"
 * Fixed setting with value "open-street-map", changed to "open-layers"
 *
 * 7.10.04 - 2018-05-31
 * Solved issue with category AND logic
 * Fixed Marker Clustering settings not visible
 * Fixed Marker Clustering not working
 * Google Errors no longer caught and displayed when in developer mode
 *
 * 7.10.03 - 2018-05-30
 * Fixed category icon not respected in some circumstances
 * Fixed both default infowindow and modern infowindow opening
 * Fixed geocode response coordinates not interpreted properly
 *
 * 7.10.02 - 2018-05-30
 * Added Google API status ahead of user consent option
 *
 * 7.10.01 - 2018-05-29
 * Map engine dialog selection bug fixed
 *
 * 7.10.00 - 2018-05-29
 * OpenLayers / OpenStreetMap integration
 * New JavaScript engine
 * Added custom store locator radii
 * Added new Javascript modules
 * Class AutoLoading implemented
 * Fixed some strings not being translated in German
 * 
 * 7.09
 * Added MapSelect module (map dropdown)
 * Added ModalDialog module
 * Added "Add Map" content editor button and dialog
 * Added support for shortcodes in marker description
 * Custom fields are no longer displayed on front end if they are empty
 *
 * 7.08
 * Improved spatial data migration function to be more robust
 * Fixed undefined index use_fontawesome
 *
 * 7.07
 * Added option to select FontAwesome version
 *
 * 7.06
 * Fixed compatibility with Bootstrap themes loading jQuery 3.x.x
 * Fixed admin Select All and Bulk Delete functionality
 * 
 * 7.05
 * Added functionality to fit map to bounds when editing shapes
 * Fixed a bug where in some circumstances marker filtering was not applied to modern listing
 * Fixed custom fields installer erroneously running on every page load
 *
 * 7.04
 * Fixed FontAwesome CSS being enqueued as script
 * Fixed JS error in for ... in loop when adding methods to Array prototype
 * Fixed \WPGMZA\CustomFieldFilterWidget\Checkboxes calling the wrong parent constructor
 *
 * 7.03 - 2018-04-06
 * Switched to WebFont / CSS FontAwesome 5 for compatibility reasons
 *
 * 7.02 - 2018-04-05
 * Fixed Warning: require_once(): https:// wrapper is disabled in the server configuration by allow_url_include=0
 *
 * 7.01 - 2018-04-05
 * Fixed fatal error in settings override code
 *
 * 7.00 - 2018-02-19
 * Added marker icon library
 * Added custom fields
 * Added custom field filters
 * Added circles
 * Added rectangles
 * Added alerts for Google Maps API errors
 * Added Import CSV, GPX, JSON, KML Export JSON
 * Added concise error / failure reporting to CSV importer
 * Added progress bar to CSV importer
 * When creating new map store locater defaults to modern store locater and radius
 * jQuery.parseJson replaced with JSON.parse
 * Category data now parsed with .split rather than JSON.parse
 * PHP files restructured
 * 
 * 6.19 - 2018-01-14 - Medium priority
 * Fixed a bug that caused the plugin to want to update all the time
 * Fixed a bug that caused the Google Maps API files to be loaded on all pages
 * Fixed PHP warnings
 * 
 * 6.18 - 2017-11-16 - Low priority
 * Fixed a missing image in the map settings panel
 *
 * 6.17 - 2017-11-15 - Low priority
 * Fixed a bug causing map infowindow style settings not being respected
 * Fixed a bug where infowindow style was being disregarded 
 * Fixed a bug where custom CSS would have HTML entities encoded (eg >)
 *
 * 6.16 - 2017-09-27 - Low priority
 * Fixed a bug that caused the plugin to think that certain ZIP codes are LAT/LNG
 * 
 * 6.15 - 2017-09-27 - Low priority
 * Fixed a bug that caused iOS devices to not open the Maps App when clicking on Get Directions
 *
 * 6.14 - 2017-09-25 - Medium Priority
 * Bug Fix: Parent ID column is now created as expected when updating the plugin.
 * 
 * 6.13 - 2017-01-26 - Medium priority
 * Fixed the bug that caused the directions box to show up automatically if waypoints are being used in the shortcode
 * Fixed a bug that caused the Google Maps API call to be added to all pages
 * 
 * 6.12 - 2017-01-24 - Medium priority
 * Direction waypoint functionality added via shortcode. Example: [wpgmza id='1' directions_from='New York' directions_to='Los Angeles' directions_waypoints='Wisconsin|Texas' directions_auto='true']
 * Fixed bug that caused our JS to be enqueued on all pages
 *
 * 6.11 - 2017-01-19 - Medium priority
 * Fixed PHP warning bugs
 * Added sub-category functionality
 *  - You can now add sub-categories (infinite levels)
 *  - You can now choose to use "AND" or "OR" logic when selecting and markers based on your category selection
 *  - You can now choose to show/hide marker counts per category on the front end
 * Fixed a bug that caused marker lists to not be updated correctly when using mashups
 * 
 * 
 * 6.10 - 2017-01-13 - Medium priority
 * Fixed a bug that caused multiple maps to stop working
 * Fixed a bug that caused markers to not display when using the XML data method
 * Fixed a bug that caused slashes to appear in category names with apostrophes
 * UI improvements to the marker listing buttons in the admin section
 * 
 * 6.09 - 2017-01-11 - Medium priority
 * Moved all echoed out JS variables to localized variables - this will also fix issues with single quotes and double quotes with some translated strings that are pushed to JS variables
 * Enqueued the Google Maps API with wp_enqueue_script instead of using document.write() which caused console warnings
 * Moved the map stylesheet to the footer and fixed the invalid W3C in-line styling
 * Refactored some JS code in the core.js file to suit the new localized JS variables 
 * Added Gesture Override (Two Finger Override)
 * Added Compat for Custom Cluster Options
 * Added a new attribute to the shortcode handler that allows you to focus on a specific marker. Example: [wpgmza id='1' marker='5' zoom='13']
 * Added a new attribute that allows you to disable directions via the shortcode. Example: [wpgzma id='1' enable_directions='0'] (disables directions)
 * Added a new attribute that allows you to disable category filtering via the shortcode. Example: [wpgzma id='1' enable_category='0'] (disables category filtering)
 * Added a new attribute that allows you to open marker infowindow links in a new window (override global settings) via the shortcode. Example: [wpgzma id='1' new_window_link='yes']
 * You can now disable the display of the VGM form by using disable_vgm_form='1' within the shortcode
 * Fixed a bug that caused the map to display at the top of the page where the shortcode was used
 * Fixed a bug that added slashes to category names with quotation marks
 * Fixed a bug that caused non-utf8 characters within an address to cause the insertion of the marker to fail
 * 
 * 6.08 - 2016-10-27 - Medium priority
 * Fixed a bug that caused a JS error in the admin section when adding or editing a marker (dataTables)
 * You can now add a default address to your store locator
 * Full screen map functionality added
 * Fixed a bug that caused PHP warnings when a polygon or polyline had no polydata
 * JS and short code refactoring
 * Updated DataTables.js and DataTables.min.js
 * Removed unnecessary anchor tags for each marker
 * 
 * 
 * 6.07 - 2016-09-15 - Medium priority
 * Fixed markup bugs in the admin section
 * UI improvements to the add/edit marker section
 * New feature: Set your markers to be hidden on the front end and only show in the backend
 * Fixed a bug that caused the map error to flash before the map loaded on the front end
 * Fixed a bug that caused a PHP warning when trying to delete a category
 * Fixed a bug whereby the map category-map link data was not actually deleted in the table when deleting a category
 * Fixed a bug that caused deleted categories to show up in the category filtering functionality
 * Added labels to the category checkboxes on the front end
 * Datatables updated
 * When a marker is deleted, the view does not reset
 * You can now set the zoom level via the shortcode. Example: [wpgmza id='1' zoom=8]
 * 
 * 6.06 - 2016-08-04 - Low priority
 * Store locator bug fixed
 * 
 * 6.05 - 2016-08-01 - Medium priority
 * Fixed a bug that stripped out all HTML from the marker description when editing
 * Adding security patches to the admin side
 * Removed inline styling from store locator elements and added class names
 * Added functionality to allow for the dropdown category selector in the store locator
 * Removed the fixed CSS width for the checkbox filter container - caused conflicts when the map was used in a widget
 * Changed the default width for the directions box from 250px to 100%
 * Fixed the bug that caused the directions output box to keep the old directions data when the directions were reset and calculated again
 * Added Transit directions functionality
 * Added functionality that would report on "ZERO_RESULTS" if the directions API came back with nothing
 * Set the directions to show alternative routes by default
 * Added "Avoid Ferries" to directions options
 * Added additional tab support (tri-tabs-nav span)
 * Fixed a bug that caused the map editor to display when trying to delete a map
 * Fixed a bug that showed deleted maps in the map list when creating a new category
 * The category drop down now sorts categories alphabetically by default
 * Fixed a styling issue for the marker title in the modern infowindow
 * 
 * 
 * 6.04 - 2016-07-19 - Low priority
 * Bug fix for adding markers
 * 
 * 6.03 - 2016-07-18 - High priority
 * Security patches
 * Additional tab support
 * The full image (as opposed to the thumbnail) is now used by default when uploading an image to a marker
 * CSS validation fixes
 * Fixed a bug where the directions were not reset but rather added onto
 * Fixed a bug that sometimes cause "null" to be displayed for the marker description
 * Fixed a bug that caused wpgmaps_localize_marker_data is not defined
 * 
 * 6.02 - 2016-07-07 - Medium priority
 * Fixed a bug which prevented markers from being hidden until search is done
 * Added option to disable Maps API from being loaded on front end
 * Added the Places lbrary to API calls as these were removed in previous update
 * Fixed styling conflict which prevented modern info-window from closing when close button is clicked
 *
 * 6.01 - 2016-07-06 - Medium priority
 * Fixed Resize Bug
 *
 * 6.00 - 2016-06-27 - Medium priority
 * Modernized UI introduced
 * Heatmap functionality added
 * New marker description editor added
 * More comprehensive import/export functionality added
 * Two new modern infowindows added
 * A new map wizard has been added
 * Videos can now be added to your markers
 * You can now set the default store locator icon
 * You can now set the default user location icon
 * You can now set the minimum zoom level
 * Added event listeners for both jQuery and accordions so that the map can init correctly when placed in a tab or accordion
 * Fixed issues that caused errors to show up when saving your map
 * Fixed the positioning issue of the modern infowindow with the new Google Maps API. It now appears in the top right by default.
 * Modern Infowindow scroll bars now only appear when they need to 
 * Marker clustering icons fixed
 * 
 * 5.70 - 2016-04-13 - High priority for Gold users
 * Fixed the marker clustering icons
 * Fixed issues that caused errors to show up when saving your map
 * 
 * 5.69 - 2016-04-15 - High priority
 * Deprecated google maps api 3.14 and 3.15, added 3.23 and 3.24
 * 
 * 5.68 - 2016-04-13 - Low Priority
 * You can now enter in your own Google Maps API key
 * 
 * 5.67 - 2016-04-04 - Low Priority
 * Google Maps API sensor removed from API call 
 * Fixed a bug that caused an error for the autocomplete functionality when the Gold add-on was active
 * Autocomplete functionality added to the 'to' and 'from' directions fields
 * Fixed a bug that affected the HTML structure on some sites
 * Weather & Cloud layer options removed (Deprecated in the Google Maps API)
 * 
 * 5.66 - 2016-03-04 - Low priority
 * Fixed a bug that showed an error message below the map if directions was enabled and open above the map.
 * Improvements to the import functionality
 * 
 * 
 * 5.65 - 2016-01-08 - Low priority
 * Style bug fix with the modern infowindow
 * Fixed an IIS bug
 * 
 * 5.64 - 2016-01-07 - Low priority
 * Additional tab compatibility
 * UI improvements - add/edit marker section
 * Fixed a bug that caused the wrong default marker icon to be displayed when using multiple maps on one page
 * Fixed a bug that caused the modern infowindow to show incorrectly when using multiple maps on one page
 * Fixed a bug that caused the enter key to not work when searching using the store locator
 * Fixed a bug that if you previously clicked on a marker in a list (which zooms to the location), and then click on another marker on the map, it will now no longer zoom to the marker location but rather just open the marker as expected, without zooming.
 * Fixed a map mashup bug that stopped the marker list from reflecting the correct markers
 * Fixed a bug that caused some content to not be loaded through SSL when you are using SSL
 * Fixed a bug that stopped the advanced marker listing from updating when using IE.
 * 
 * 5.63 - 2015-12-01- - Low priority
 * Added custom hooks and filters
 * 
 * 5.62 - 2015-11-23 - Low Priority
 * Fixed a bug that caused the map to break when a theme was not selectd
 * 
 * 5.61 - 2015-11-19 - Low Priority
 * Theme directory and functionality added to the Pro version
 * Fixed a bug that served http content on an SSL site in the marker clusterer functionality
 * Fixed a bug that took the Print Directions page to a 404 error
 * You can now close a modern info window by clicking on an x
 * A class has been added to the containing div when filtering by category
 * 
 * 5.60 - 2015-09-04 - Low priority
 * Added 5 themes to the map editor
 * Added a native map widget so you can drag and drop your maps to your widget area
 * Fixed a bug that incorrrectly geocoded certain GPS co-ordinates when using the Store Locator
 * Fixed an undefined notice
 * Removed old version warnings
 * Added another tab to the tabs compatibility list
 * Turkish translation added - thank you Suha Karalar
 *  
 * 5.59 - 2015-08-20 - High priority
 * Fixed a bug that caused a conflict with WordPress 4.3's jQuery and DataTables.js
 * Styling bug fixes in WordPress 4.3
 * 
 * 
 * 5.58 - Liberty Update
 * Fixed a bug that caused the default marker listing quantity to reset after selecting a category
 * Minor bug fixes
 * Fixed a bug that caused a jQuery error message to be displayed briefly before the map loads\
 * Fixed a bug that caused the center of the map to be incorrectly changed when clicking on a marker in a marker list on certain iOS devices
 * Added the autocomplete functionality to the add marker section in the map editor
 * 
 * 5.57
 * New infowindow style available: Modern Infowindow
 * Bug fix with JS error being produced as a result of the new google maps autocomplete
 * Minor bug fixes in the google map HTML output
 * SSL bug fix
 * CSV import bug fix ('retina' and 'approved' where not being imported)
 * Support added for 3 new tabs
 * Added accessibility support for the directions box (http://achecker.ca/)
 * Fixed the bug that caused an alert to pop up regarding jQuery dataTables when filtering the markers
 * Fixed the bug that caused the marker list advanced table to span passed the width of the main parent DIV
 * Added Google Autocomplete to the store locator
 * Removed the slashes in the category name within the marker listing
 * 
 * 5.56
 * Directions now open up in native map app if on a mobile device (thank you pelicanpaul!)
 * Fixed the "auto approve" bug with the VGM add-on
 * Polygon and polyline bug fixes with mashup functionality
 * Rocketscript fix (Cloudfare)
 * Categories are now displayed in alphabetical order (thank you Duncan McMillan!)
 * Fixed the bug that caused the directions width type to show as PX instead of %
 * Map centers to original center location when resized
 * json_encode (extra parameter) issue fixed for hosts using PHP version < 5.3
 * PHP Notice fixes
 * 
 * 5.55
 * Directions box width can now be set to either PX or %
 * Marker image width and/or height can now be left blank to automatically set the width/height
 * Clicking/hovering on a marker no longer pans the map to that marker
 * Mass marker bug fix
 * Now using sensor=true in the geocoding API calls
 * Fixed max zoom bug
 * Fixed the bug that caused the marker title to not show up in the marker listing (basic table) in certain instances
 * "Get directions" now appears in the basic table marker list
 * 
 * 5.54 2015-03-16
 * Timthumb removed
 * New marker listing functionality - you can now list your markers in the map itself
 * Category filter (dropdown) bug fix
 * You can now set the width and height for Retina markers in the settings page
 * Advanced marker listing table is now responsive
 * Major improvements to how the plugin handles marker sorting
 * You can now force a marker infowindow to open by using a GET variable (?markerid=x). You can also assign a zoom level (&mzoom=x)
 * Fixed the MaxZoom bug not allowing you to go to zoom level 0
 * The map now automatically shows in the language you have set in your WordPress settings
 * Code improvements in the main JS file
 * Fixed the bug that didnt allow for category filtering when multiple maps are on the same page
 * Mashup (via database method) bug fix
 * Store locator datatables bug fix
 * Fixed bug that didnt allow filtering when multiple maps are on the same page
 * Fixed a bug that caused the wrong map to setCentre when clicking on a polygon with multiple maps on a page
 * SSL Compatibility for the datatables theme css file
 * Fixed a bug that caused the image url to not be inserted when trying to use an image that doesnt have a standard wordpress thumbnail size
 * Refactored the way we handle category filtering
 * 
 * 
 * 5.53 2015-02-18 Low priority
 * Timthumb will be phased out and replaced with standard WordPress image handling in the next version - notices and new options added to this version
 * Small bug fix with the store locator
 * You can now use the Enter key to submit a store locator search
 * Fixed a bug that caused the map to not show in certain situations
 * 
 * 5.52 2015-02-16 High priority
 * Fixed the bug that didnt allow you to add a new marker to a blank map if you had the "database" option selected
 * 
 * 5.51 2015-02-03
 * Safari bug fix
 * New support page added
 * Bug fix - filter by checkbox is now working
 * Bug fix - Hide columns in advanced marker listing is now working
 * Added a space between the number and "miles" or "kilometers" for the store locator.
 * Added a Max Zoom option for your google map
 * PHP notices fixed
 * Fixed a bug that caused the map to not display if the polygon was corrupted
 * 
 * 5.50 2015-02-01
 * Bug fix for french translations
 * 
 * 5.49 2015-01-27
 * Core.js bug fixes
 * Fixed a bug that tried to check file permissions for the XML file even if the user selected the Database method for the marker pull option
 * Removed the marker limit warning
 * Duplicate map functionality added
 * Added support for the VGM add-on (auto approve markers)
 * 
 * 5.48
 * Fixed approval bug
 * Fixed a bug that caused polygons and polylines to now show on certain installations
 * Fixed a bug that caused more than one map to not display on certain installations
 * Fixed a bug that caused issues when using the database marker pull method and multiple maps
 * Added classes to the TO and FROM elements in the direction box
 * Code improvements in the core.js file
 * CSV import bug fixes - retina and approved columns now gets imported
 * 
 * 
 * 5.47
 * Fixed the marker ordering bug for the basic table
 * 
 * 5.46
 * Introduced a new method of pulling and displaying the marker data
 * 
 * 5.45
 * Code improvements
 * 
 * 5.44 2014-11-27
 * Code refactoring within the main class
 * Infowindow styling improvements (attempt at minimizing scrollbars and including more classes and structure to the infowindow)
 * Fixed the bug whereby the marker listing table was not ending correctly
 * Added compatibility for maps displaying within Elegant Builder tabs
 * Added title/description search options and functionality to the store locator (beta)
 * Fixed the map from not showing when using Hebrew locale
 * Added placeholders for the store locator inputs
 * PreserveViewport now set to true when using KML files (avoid zoom override)
 * Retina display support for markers
 * Added new strings to the PO file
 * "Lowest level of access to the map editor" option added to the pro version
 * A simple map can now be generated by using custom fields in a post/page. See our blog for more details.
 * Fixed the bug that didnt display the correct markers when the Store Locator was used and a map mashup was being used
 * 
 * 5.43 2014-11-05
 * Fixed IE bug (console log)
 * Fixed bug that switched the datatables back to English upon filtering when using another language
 * Fixed a marker sorting bug (sort by Marker ID)
 * 
 * 5.42 2014-11-04
 * New marker listing option - "Carousel"
 * Code improvements to both PHP and the JS core file
 * Shortcode additions: Map type and Streetview
 * New option: You can now show or hide the Store Locator bouncing icon
 * New option: Select default items to display in the advanced marker listing
 * Bug fixes
 *  IE8 issue with mashups
 *  IE8 issue with multiple KML files
 * 
 * 5.41
 * Better marker file handling
 * Permission error bug fix
 * Multiple KML/KMZ/GeoRSS files can now be used (comma separated)
 * Small bug fixes (Thank you Thomas)
 * 
 * 5.40
 * Enfold / Avia theme conflict (Google Maps API loading twice) resolved
 *  
 * 5.39 2014-09-29
 * Security updates (thank you www.htbridge.com)
 * Fixed the bug that didnt correctly check the category checkboxes when editing your marker
 * Code improvements (PHP warnings)
 * Code improvements (file permissions) (Thank you Thomas)
 * Fixed bug that showed "Show _MENU_ entries" when it should have displayed "No records found" (Thank you Thomas)
 * Broken image bug fix (Thank you Thomas)
 * 
 * 5.38
 * Removed "the map could not load" error that showed briefly before the map loads.
 * 
 * 5.37
 * Fixed the bug that was not causing the marker lists to be updated on a store locator search or category filtering
 * 
 * 5.36
 * Code improvements (PHP warnings)
 * 
 * 5.35
 * Code improvements (PHP warnings)
 * 
 * 5.34
 * New features:
 *  - Marker filtering now changes the marker list below
 *  - Store locator filtering now changes the marker list below
 *  - Markers can now have mulitple categories
 *  - You can now right click to add a marker to the map
 *  - New markers can be dragged
 *  - Polygons and polylines now have labels
 * Backend UI improvements
 * Polyline bug fix
 * Fixed incorrect warning about permissions when permissions where "2755" etc.
 * 
 * 5.33
 * Print directions bug fix
 * 
 * 5.32
 * New feature: Print directions
 * You can now set the query string for the store locator
 * 
 * 5.31
 * Bug fixes
 *  - Incorrect polyline data caused the map to not load
 *  - Changed incorrect HTML in the directions box on the front end
 * 
 * 5.30
 * Bug fix - multiple maps with polygons now work
 * 
 * 5.29
 * Small bug fix (warning)
 * 
 * 5.28
 * New feature: Geocode on import now available (BETA) - Thank you Tony Palleschi - http://apartcreations.com/
 * New polygon functionality: add "on hover" properties, a title and a link to your polygons.
 * Fixed a bug that when threw off gps co-ordinates when adding a lat,lng as an address
 * 
 * 5.27
 * Minor code improvements (warnings)
 * Multisite bug fix (marker location)
 * 
 * 5.26
 * Minor code improvements
 * 
 * 5.25
 * You can now choose which folder your markers are saved in
 * Better error reporting for file permission issues
 * 
 * 5.24
 * Fixed a language bug with the use of datatables (thank you Jean-Philippe Boily)
 * 
 * 5.23
 * Fixed more PHP warnings
 * Code improvements
 *  
 * 5.22
 * Fixed PHP notice warnings (shown in debug mode)
 * Fixed marker location bug when the default uploads directory has been changed
 * 
 * 5.21
 * Fixed a bug that caused KML, Fusion tables and polygons to appear on the first map instead of individual maps when multiple maps where used on one page
 * Fixed a map width bug (%)
 * Added the option to select which API version you would like to use
 * 
 * 5.20
 * Introduced ini_set("auto_detect_line_endings", true); for better mac/pc importing of CSV files
 * Maps now work automatically when put in tabs
 * Added more options for the store locator
 * Added opacity options for polygon lines
 * 
 * 5.19
 * Small bug fix
 * 
 * 5.18
 * Mutlisite marker location bug fixed
 * 
 * 5.17
 * Markers are now stored in the uploads/wp-google-maps/ directory
 * 
 * 5.16
 * Small bug fix
 * 
 * 5.15
 * Performance improvements
 * 
 * 5.14
 * Added the option to display categories as a dropdown or as checkboxes
 * Added store locator functionality. More functionality for this to follow soon (Still in BETA)
 * Fixed the bug that swapped the variables around for disabling "double click zoom"
 * Fixed a bug that forced a new geocode on every marker edit, even if the address wasnt changed
 * New functionality:
 *  - You can now choos to open a marker from click or hover
 *  - Better error handling
 * 
 * 5.13
 * Fixed a conflict between KML layers and Polygons whereby clicks on markers within a KML layer were not triggering if the polygon overlapped the KML layer markers. Polygons 'clickable' now set to false
 * 
 * 5.12
 * Fixed the category selection bug that did not revert back to 'all' markers.
 * 
 * 5.11
 * Small bug fix
 * 
 * 5.10
 * Small bug fix
 * 
 * 5.09
 * Added category filtering via shortcode
 * 
 * 5.08
 * Fixed a conflict with the NextGen plugin
 * 
 * 5.07
 * Fixed a bug that stopped directions from working with multiple maps on the same page
 * 
 * 5.06
 * Small bug fixes in the core.js file
 * 
 * 5.05
 * Fixed a bug causign JS conflicts in IE8
 * 
 * 5.04
 * Fixed a bug that messed up the iamge sizes in some browsers
 * 
 * 5.03
 * Fixed a bug that caused all control elements on the map to disspear
 * 
 * 5.02
 * Fixed an marker icon bug for some hosts
 * Fixed small bug with resetting select boxes within the add marker section
 * 
 * 5.01
 * Small bug fixes
 * 
 * 5.0
 * Complete re-code
 * Upgrade: The JavaScript is now in it's own file
 * Better error handling
 * You now have the ability to add a default "To" address for the directions.
 * Fixed map align center bug
 * Fixed infowindow styling issues when images are used
 * Fixed the bug that caused the map to not load if a blank polyline/polygon was created
 * Fixed cross-browser infowindow styling bugs
 * You can now hide/show columns of your choice with the advanced listing option
 * Fixed many smaller bugs
 * 
 * 
 * 4.18
 * You can now add HTML into the description field
 * Functionality added for category icons
 * You can now assign categories to specific maps or all maps
 * Bug fixes:
 *  Fixed the sorting markers bug
 *  Fixed the bug that stopped you from deleting polylines
 *  Fixed the bug that caused no markers to display in the marker list when "Select" was selected in the category filter drop down.
 * 
 * 4.17
 * There is now the option to hide the Category column
 * 
 * 4.16
 * Fixed an infowindow styling bug
 * 
 * 4.15
 * Added a check to see if the Google Maps API was already loaded to avoid duplicate loading
 * Fixed some SSL bugs
 * Added extra style support for the standard marker listing
 * Advanced marker list now updates with category drop down selection
 *
 * 4.14
 * Added a min-width to the DIV within the InfoWindow class to stop the scroll bars from appearing in IE10
 *
 * 4.13
 * Map mashups are now available by modifying the shortcode.
 * Added Category functionality.
 * Fixed a bug with the normal marker list layout
 * Added backwards compatibility for older versions of WordPress
 * Fixed a few small bugs
 * Replaced deprecated WordPress function calls
 * Added Spanish translation - Thank you Fernando!
 * Coming soon in 4.14: Map mashup via custom fields in post.
 *
 * 4.12
 * Fixed a small bug
 *
 * 4.11
 * Better localization support
 * Fixed a SSL bug
 * 
 * 4.10
 * Added Polygon functionality
 * Added Polyline functionality
 * You can now show your visitors location on the map
 * Markers can now be sorted by id,title,description or address
 * Added better support for jQuery versions
 * Plugin now works out the box with jQuery tabs
 * Added standards for the advanced marker list style
 * Added user access support for the visitor generated markers add-on
 * Adjusted the KML functionality to avoid caching
 * Fixed small bugs causing PHP warnings
 * Fixed a bug that stopped the advanced marker listing from working
 * 
 * 4.09
 * Fixed a bug that didnt allow for multiple clicks on the marker list to bring the view back to the map
 * 
 * 4.08
 * This version allows the plugin to update itself moving forward
 * 
 * 4.07
 * Fixed a bug that was causing a JavaScript error with DataTables
 * 
 * 4.06
 * Added troubleshooting support
 * Fixed a bug that was stopping the plugin from working on IIS servers
 * 
 * 4.05
 * Added support for one-page-style themes.
 * Fixed a firefox styling bug when using percentage width/height and set map alignment to 'none'
 * Added support for disabling mouse zooming and dragging
 * Added support for jQuery1.9+
 * 
 * 4.04
 * Fixed a centering bug - thank you Yannick!
 * Italian translation added
 * Fixed an IE9 display bug 
 * Fixed a compatibility bug between the VGm add-on and the Pro add-on
 * Fixed a bug with the VGM display option
 * Fixed a bug with importing markers whereby it always showed as an error even when importing correctly
 *
 * 4.03
 * Fixed a firefox styling bug that caused the Directions box to load on the right of the map instead of below.
 * Added support code for the new WP Google Maps Visitor Generated Markers plugin
 * Added the option for a more advanced way to list your markers below your maps
 * Added responsive size functionality
 * Added support for Fusion Tables
 *
 * 4.02
 * Fixed the bug that caused the directions box to show above the map by default
 * Fixed the bug whereby an address was already hard-coded into the "To" field of the directions box
 * Fixed the bug that caused the traffic layer to show by default
 *
 * 4.01
 * Added the functionality to list your markers below the map
 * Added more advanced directions functionality
 * Fixed small bugs
 * Fixed a bug that caused a fatal error when trying to activate the plugin on some hosts.
 *
 * 4.0
 * Plugin now supports multiple maps on one page
 * Bicycle directions now added
 * Walking directions now added
 * "Avoid tolls" now added to the directions functionality
 * "Avoid highways" now added to directions functionality
 * New setting: open links in a new window
 * Added functionality to reset the default marker image if required.
 *
 * 3.12
 * Fixed the bug that told users they had an outdated plugin when in fact they never
 *
 * 3.11
 * Fixed the bug that was causing both the bicycle layer and traffic layer to show all the time
 * 
 * 3.10
 * Added the bicycle layer
 * Added the traffic layer
 * Fixed the bug that was not allowing users to overwrite existing data when uploading a CSV file
 *
 * 3.9
 * Added support for KML/GeoRSS layers.
 * Fixed the directions box styling error in Firefox.
 * Fixed the bug whereby users couldnt change the default location without adding a marker first.
 * When the "Directions" link is clicked on, the "From" field is automatically highlighted for the user.
 * Added additional settings
 *
 * 3.8
 * Markers now automatically close when you click on another marker.
 * Russian localization added
 * The "To" field in the directions box now shows the address and not the GPS co-ords.
 *
 * 3.7
 * Added support for localization
 *
 * 3.6
 * Fixed the bug that caused slow loading times with sites that contain a high number of maps and markers
 *
 * 3.5
 * Fixed the bug where sometimes the short code wasnt working for home pages
 *
 * 3.4
 * Added functionality for 'Titles' for each marker
 *
 * 3.3
 * Added functionality for WordPress MU
 *
 * 3.2
 * Fixed a bug where in IE the zoom checkbox was showing
 * Fixed the bug where the map wasnt saving correctly in some instances

 * 3.1
 * Fixed redirect problem
 * Fixed bug that never created the default map on some systems

 * 3.0
 * Added Map Alignment functionality
 * Added Map Type functionality
 * Started using the Geocoding API Version 3  instead of Version 2 - quicker results!
 * Fixed bug that didnt import animation data for CSV files
 * Fixed zoom bug

 * 2.1
 * Fixed a few bugs with the jQuery script
 * Fixed the shortcode bug where the map wasnt displaying when two or more short codes were one the post/page
 * Fixed a bug that wouldnt save the icon on editing a marker in some instances
 *
 * 
 *
*/

/*
 * NOTICE:
 *
 * Core code moved to legacy-core.php. This file checks two things:
 *
 * 1) PHP version >= 5.3 - needed for namespace and anonymous functions
 * 2) DOMDocument, increasingly used throughout the plugin
 *
 * The following checks will cause the script to return rather than loading legacy-core.php,
 * which would cause syntax errors in case of 1) and fatal errors in case of 2)
 *
 */

define('WPGMZA_PRO_FILE', __FILE__);

$fromVersion = get_option('wpgmza_db_version');
if ($fromVersion == NULL) {
	$fromVersion = get_option('wpgmaps_current_version');
}
if(version_compare($fromVersion, '8.1.0', '<')){
	add_action('admin_notices', 'wpgmaps_pro_81_notice');
	return;
}
function wpgmaps_pro_81_notice() {
	$fromVersion = get_option('wpgmza_db_version');
	if(version_compare($fromVersion, '8.1.0', '<')){
		?>

		<div class="notice notice-error">
			<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
			<h3><?php _e('WP Google Maps', 'wp-google-maps'); ?></h3>
			<p><?php
				echo sprintf(__('In order to use WP Google Maps Pro 8.1, you need to <a href="%s">update your basic version</a> to the latest version (8.1*).', 'wp-google-maps'),'update-core.php');
			?></p><br />
			<p>&nbsp;</p>
		</div><br />

		<?php
	}
}
if(version_compare($fromVersion, '8.1.0', '<')){ } else {



	global $wpgmza_pro_version;
	$wpgmza_pro_version = null;
	$subject = file_get_contents(plugin_dir_path(__FILE__) . 'wp-google-maps-pro.php');
	if(preg_match('/Version:\s*(.+)/', $subject, $m))
		$wpgmza_pro_version = trim($m[1]);

	define('WPGMZA_PRO_VERSION', $wpgmza_pro_version);

	require_once(plugin_dir_path(__FILE__) . 'constants.php');

    /*
     * We are now PHP8 compatible, so we don't need t stop anything 
    */
    /*
	if(version_compare(phpversion(), '8.0', '>=')){
		return;
	}
    */
	 
	// Pro MUST load before Basic or the plugin will break. This will change in future versions as the initialization code is altered to use the appropriate hooks
	function wpgmza_load_order_notice()
	{
		?>
		<div class="notice notice-error">
			<p>
				<?php
				_e('<strong>WP Google Maps:</strong> The plugin and Pro add-on did not load in the correct order. Please ensure you use the correct folder names for the plugin and Pro add-on, which are /wp-google-maps and /wp-google-maps-pro respectively.', 'wp-google-maps');
				?>
			</p>
		</div>
		<?php
	}

	function wpgmza_check_load_order()
	{
		global $wpgmza_version;
		
		require_once(ABSPATH . 'wp-admin/includes/plugin.php');
		
		$apl = get_option('active_plugins');
		$plugins = get_plugins();
		$activated_plugins = array();
		
		foreach ($apl as $p)
		{
			if(isset($plugins[$p]))
				array_push($activated_plugins, $plugins[$p]['Name']);
		}
		
		$basic_index	= array_search('WP Google Maps', $activated_plugins);
		$pro_index		= array_search('WP Google Maps - Pro Add-on', $activated_plugins);
		
		if($basic_index === false || $pro_index === false)
			return;
		
		if($basic_index < $pro_index)
			add_action('admin_notices', 'wpgmza_load_order_notice');
	}

	if(is_admin())
		add_action('init', 'wpgmza_check_load_order');

	if(!function_exists('wpgmza_show_php_version_error'))
	{
		function wpgmza_show_php_version_error()
		{
			?>
			<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Google Maps:</strong> This plugin does not support PHP version 5.2 or below. Please use your cPanel or contact your host to switch version.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
		}
	}

	if(!function_exists('wpgmza_show_dom_document_error'))
	{
		function wpgmza_show_dom_document_error()
		{
			?>
			<div class="notice notice-error">
				<p>
					<?php
					_e('<strong>WP Google Maps:</strong> This plugin uses the DOMDocument class, which is unavailable on this server. Please contact your host to request they enable this library.', 'wp-google-maps');
					?>
				</p>
			</div>
			<?php
		}
	}

	function wpgmza_show_php_5_4_45_error()
	{
		?>
		<div class="notice notice-error">
			<p>
				<?php
				_e('<strong>WP Google Maps:</strong> Due to a known issue with PHP 5.4.45 and JSON serialization, the Pro add-on cannot function correctly. We strongly recommend you switch to more up to date version of PHP.', 'wp-google-maps');
				?>
			</p>
		</div>
		<?php
	}

	global $wpgmza_cached_basic_dir;

	function wpgmza_get_basic_dir()
	{
		global $wpgmza_cached_basic_dir;
		
		if($wpgmza_cached_basic_dir)
			return $wpgmza_cached_basic_dir;
		
		if(defined('WPGMZA_PLUGIN_DIR_PATH'))
			return WPGMZA_PLUGIN_DIR_PATH;
		
		$plugin_dir = plugin_dir_path(__DIR__);
		
		// Try default folder name first
		$file = $plugin_dir . 'wp-google-maps/wpGoogleMaps.php';
		
		if(file_exists($file))
		{
			$wpgmza_cached_basic_dir = plugin_dir_path($file);
			return $wpgmza_cached_basic_dir;
		}
		
		// Scan plugins
		$plugins = get_option('active_plugins');
		foreach($plugins as $slug)
		{
			if(preg_match('/wpGoogleMaps\.php$/', $slug))
			{
				$file = $plugin_dir . $slug;
				
				if(!file_exists($file))
					return null;
				
				$wpgmza_cached_basic_dir = plugin_dir_path($file);
				return $wpgmza_cached_basic_dir;
			}
		}
		
		return null;
	}

	function wpgmza_get_basic_version()
	{
		global $wpgmza_version;
		
		// Try already loaded
		if($wpgmza_version)
			return trim($wpgmza_version);
		
		if(defined('WPGMZA_VERSION'))
			return trim(WPGMZA_VERSION);
		
		$dir = wpgmza_get_basic_dir();
		
		if(!$dir)
			return null;
		
		$file = $dir . 'wpGoogleMaps.php';
		
		if(!file_exists($file))
			return null;
		
		// Read version strintg
		$contents = file_get_contents($file);
			
		if(preg_match('/Version:\s*(.+)/', $contents, $m))
			return trim($m[1]);
		
		return null;
	}

	function wpgmza_get_required_basic_version()
	{
		return '8.0.0';
	}

	function wpgmza_is_basic_compatible()
	{
		$basic_version = wpgmza_get_basic_version();
		$required_version = wpgmza_get_required_basic_version();
		
		return version_compare($basic_version, $required_version, '>=');
	}

	function wpgmza_show_basic_incompatible_notice()
	{
		$basic_version = wpgmza_get_basic_version();
		$required_version = wpgmza_get_required_basic_version();
		$pro_version = WPGMZA_PRO_VERSION;
		
		$notice = '
		<div class="notice notice-error">
			<p>
				' .
				__(
					sprintf(
						'<strong>WP Google Maps Pro:</strong> Pro add-on %s requires WP Google Maps to be activated, the minimum required version of WP Google Maps is version %s. Please update the basic plugin to version %s to use WP Google Maps Pro %s', 
						$pro_version,
						$required_version,
						$required_version,
						$pro_version
						),
					'wp-google-maps'
				) . '
			</p>
		</div>
		';
		
		echo $notice;
	}
	 
	function wpgmza_pro_preload_is_in_developer_mode()
	{
		$globalSettings = get_option('wpgmza_global_settings');
			
		if(empty($globalSettings))
			return !empty($_COOKIE['wpgmza-developer-mode']);
		
		if(!($globalSettings = json_decode($globalSettings)))
			return false;
		
		return isset($globalSettings->developer_mode) && $globalSettings->developer_mode == true;
	}
	 
	if(version_compare(phpversion(), '5.3', '<'))
	{
		add_action('admin_notices', 'wpgmza_show_php_version_error');
		return;
	}

	if(version_compare(phpversion(), '5.4.45', '=='))
	{
		add_action('admin_notices', 'wpgmza_show_php_5_4_45_error');
		return;
	}

	if(!class_exists('DOMDocument'))
	{
		add_action('admin_notices', 'wpgmza_show_dom_document_error');
		return;
	}

	if(!wpgmza_is_basic_compatible())
	{
		add_action('admin_notices', 'wpgmza_show_basic_incompatible_notice');
		return;
	}

	if(wpgmza_pro_preload_is_in_developer_mode())
		require_once(plugin_dir_path(__FILE__) . 'legacy-core.php');
	else
	{
		try{
			require_once(plugin_dir_path(__FILE__) . 'legacy-core.php');
		}catch(Exception $e) {
			add_action('admin_notices', function() use ($e) {
				
				?>
				<div class="notice notice-error is-dismissible">
					<p>
						<strong>
						<?php
						_e('WP Google Maps', 'wp-google-maps');
						?>:</strong>
						
						<?php
						_e('The Pro add-on cannot be loaded due to a fatal error. This is usually due to missing files. Please re-install the Pro add-on. Technical details are as follows: ', 'wp-google-maps');
						echo $e->getMessage();
						?>
					</p>
				</div>
				<?php
				
			});
		}
	}

	// Adds filter to stop loading datatables from class.script-loader.php line 106
	add_filter('wpgmza-get-library-dependencies', 'wpgmza_do_not_load_datatables', 10, 1);
			
	function wpgmza_do_not_load_datatables($dep){
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");
		 if (!empty($wpgmza_settings['wpgmza_do_not_enqueue_datatables']) && !is_admin()) {
			if (isset($dep['datatables'])) {
				unset($dep['datatables']);
			}
		}
		return $dep;
	}


	/**
	 * Localized strings to pass to page
	 *
	 * TODO: Rebuild into proper architecture spec
	 * 		 - This solution is temporary as info-windows are non-functional presently
	 */
	add_filter('wpgmza_localized_strings', function($arr) {
		$wpgmza_settings = get_option("WPGMZA_OTHER_SETTINGS");

		if (isset($wpgmza_settings['wpgmza_settings_infowindow_link_text'])) { $wpgmza_settings_infowindow_link_text = $wpgmza_settings['wpgmza_settings_infowindow_link_text']; } else { $wpgmza_settings_infowindow_link_text = false; }
		if (!$wpgmza_settings_infowindow_link_text) { $wpgmza_settings_infowindow_link_text = __("More details","wp-google-maps"); }

		return array_merge($arr, array(
			'directions' => __('Directions', 'wp-google-maps'),
			'get_directions' => __('Get Directions', 'wp-google-maps'),
			'more_info' => $wpgmza_settings_infowindow_link_text
		));
		
	});

	add_action('admin_notices', 'wpgmza_81_pro_extension_notices');

	function wpgmza_81_pro_extension_notices(){
		global $wpgmza_ugm_version;

		if(defined("WPGMZA_GOLD_VERSION")){
			if(version_compare(WPGMZA_GOLD_VERSION, '5.1.0', '<')){
			?>

				<div class="notice notice-error">
					<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
					<h3><?php _e('WP Google Maps', 'wp-google-maps'); ?></h3>
					<p><?php
						echo sprintf(__('In order to use WP Google Maps Gold with your current Pro version, you need to <a href="%s">update your gold version</a> to the latest version (5.1*).', 'wp-google-maps'),'update-core.php');
					?></p><br />
					<p>&nbsp;</p>
				</div><br />

			<?php
			}
		}

		if(!empty($wpgmza_ugm_version)){
			if(version_compare($wpgmza_ugm_version, '3.30', '<')){
			?>

				<div class="notice notice-error">
					<h1><?php _e('Urgent notice', 'wp-google-maps'); ?></h1>
					<h3><?php _e('WP Google Maps', 'wp-google-maps'); ?></h3>
					<p><?php
						echo sprintf(__('In order to use WP Google Maps Visitor Generated Markers with your current Pro version, you need to <a href="%s">update your VGM version</a> to the latest version (3.30*).', 'wp-google-maps'),'update-core.php');
					?></p><br />
					<p>&nbsp;</p>
				</div><br />

			<?php
			}
		}
	} 
}