# WP Go Maps V9
Welcome to **Atlas Novus!**

Map creation should be a creative and intuitive process. This is what Atlas Novus set's out to solve, with a new user interface, tools and features, creators can set up their maps in minutes.

Drawing inspiration from some of the latest UI/UX trends, this build aims to provide creators with everything they could need to create stunning maps, in a way that's intuitive and relatable to other visual creation tools. 

## Work In Progress
Version 9 (Atlas Novus) is in active development and has not been fully completed. In most instances the nuanced development tasks are tracked locally using tools like Todoist, however, major items will be logged and added to the tasks list if needed.

Version 8.1 is actively maintained, however, feature development will become less involved moving forward. The idea here is that we isolate new features to this version to avoid developing things twice, for two different architectures. 

When bug fixes or small feature additions are made to V8.1, this will need to be diffed into V9. We recommend you finish a full release build, merge everything up, and then use a tool like **DiffMerge** to facilitate merging the entire build.

Take care here, you will need to do a line by line reviews, which can be time-consuming but must be done properly. Atlas Novus includes new templating core modules which manage to load specific files based on the selected build version. Missing this step, or rushing through it could lead to bugs resurfacing on V9 release days, or worst case could remove the Atlas Novus core code which manages the internal build systems.

## Internal Build Engines
There are two internal build engines present with this version of WP Go Maps, namely, Atlas Novus and Legacy. Users can select which build they prefer to use from the global settings area, by adjusting the **Build** setting. 

Changing between builds will not alter any database structures, existing data, or any other dynamic content stored within the plugin. Changes are purely visual, and new features will still be shared by both engines as much as possible. 

New installations will be randomly assigned to a build with a 30% probability factor, leaning towards Atlas Novus. Without getting too technical, you can think of this as **1 in 3** new installations will automatically have Atlas Novus enabled.

Existing installations will automatically be set to Legacy, which means nothing changes for these users visually. Those users will instead receive a notice about Atlas Novus, asking them if they'd like to try it out. They can opt out of this at any stage.

## File Structure
To make this dual build system possible, some fundamental file changes had to be made to the plugin. An important note here is that fundamentally PHP classes and JS modules are not changing. To keep the core functionality the same, we need to make sure PHP core and JS core behaves the same way in both builds. 

Some custom PHP classes and JavaScript modules will be loaded specifically for Atlas Novus as it will include new UI elements which require new events at its core to function. 

Additionally, some base classes are being changed to execute build conditional loading more fully. This allows specific CSS and HTML template files to be loaded based on the build engine. 

**HTML** 
We use HTML templates for most elements within the core, by default these are shared by both builds, however, it is now possible to define a duplicate/override of any file within `html/atlas-novus`. This allows developers to create new UI with a fallback to an older panel style as a fallback

**CSS**
This follows the same rules as the HTML system, however, it should be noted that Atlas Novus should fundamentally move away from ever loading the original files. This is so that we can ensure a new clean, developer-friendly style in general

**Theme Overrides**
In the longer term, we'd like to allow theme developers to create custom panel and styling overrides if they prefer, as such, at some stage, the template loader will be updated to support a custom `wp-google-maps` folder within a theme directory. If HTML or CSS files are present here, they will be loaded instead of our internal files. 

## Developer Focus
Although this build puts a heavy emphasis on creators, we also wanted to include a stronger focus on developers. This version aims to add more developer hooks to the core system than ever before, with filters and actions seeing a major comeback in this version. 

If you are adding a new area to the plugin, make sure to include sufficient hooks throughout to allow 3rd party developers to extend the plugin fully. For the future, isolate these to the Atlas Novus build as it will become the default once we are sure the UI is sensible. 

We will also be writing coherent, versions, and maintained **Developer Documentation** which means developers will be expected to document code when releasing new features. 

## User Interface Styling
Atlas Novus fully embraces the new orange branding and moves away from the yellow seen in the past. We found the yellow to be hard to read visually, and it often leads to users assuming errors were present where they were not. Developers should use the primary colours we set out as part of Atlast Novus and steer clear of using any legacy colours in the new build. 

If new features are added to the Legacy controls, the yellow should be maintained accordingly, however, this should be a rare edge-case. 

To make branding easier as a whole, we are changing the way we style things and the way we structure HTML. This should make reusing components much simpler. We will also be making use of CSS variables where possible to standardize themes across the board

## JS Prototype Extension
Where possible, proper JavaScript extension and inheritance should be applied to all new modules. As we move to a fully OOP paradigm, it is important to avoid inline events, unnamed functions (within reason) and other redundancies. 

Where possible, leverage the `WPGMZA` JS API to access datasets within a map or settings area, instead of checking DOM elements directly or leaning on jQuery events. When firing off JavaScript events, do not use jQuery.events excessively, and instead focus on using our internal event system. This standardizes events and adds suffixes that help avoid conflicts.

## PHP Classes
As mentioned, we are making a big push away from procedural programming in favour of OOP instead. The idea here is that individual modules are easier to maintain than a long block of top-to-bottom executable code. 

Extend and inherit wherever possible, and avoid leaning on more traditional procedural code. We know this can be tricky at first, but once you endorse this methodology fully, you will see the benefits. 

If you do find you are repeating code/logic frequently, we highly recommend segmenting this into a helper method, attached to the specific class the logic applies to instead of simply copy-pasting the code as you go. 

This idea of **Do Not Repeat Yourself** (DRY) can seem pointless the first time you deal with it, but it does make maintenance much easier. In the same breath, this does not mean you need to modularize everything, finding a balance is key. If you are unsure, ask a lead developer, we can help! 

## Deprecation
At its core, the plugin has always been mostly backwards compatible and this version will be no different. It will attempt to maintain full support for older structures, however, with the scale of this build, some deprecation will be unavoidable. 

Consider V9 a standalone build and treat coding for it accordingly. It's better to disable a feature for the legacy build engine, in favour of better quality code in Atlas Novus.

## Code Quality
When we review your pull requests, we will be looking at good practices, staying within the paradigms we are using, and document those changes. Keep things clean and no revisions will be needed. When you do receive a review with some notes, make sure to truly absorb the feedback as we truly believe this will lead to better development as a whole.

## PHP 8
Thanks to the most recent 8.1.12 build being ported to 9.0.0, we now fully support PHP8. This should always be maintained when adding new functionality, switch between major builds frequently to ensure that this is maintained. 
