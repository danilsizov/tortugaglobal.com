=== My Custom CSS ===
Contributors: DarkWolf
Donate link: https://www.paypal.me/SalvatoreN
Tags: css, style, custom, theme, plugin, stylesheet, darkwolf, laltroweb, ace, cloud9 editor
Requires at least: 4.0
Tested up to: 4.4.1
Stable tag: 3.0

Enable to add Custom CSS Code via admin panel with Ace (Ajax.org Cloud9 Editor) 
+ backup (see screen) and static file cache (best performance) :)

== Description ==

Maked by Salvatore Noschese: https://laltroweb.it/

With this plugin you can put custom css code without edit your theme and/or your plugins (really useful in case of any theme/plugin update).

It contain also <a href="https://ace.c9.io/">Ace (Ajax.org Cloud9 Editor)</a> Code Editor for write a good css code.

You can see in action (source code) here: <a href="http://www.vegamami.it/">VegAmami</a> :)

PS: support file backup and - very important - static css file (fantastic for performance) ;)

= Links =

* Author Homepage: [Salvatore Noschese](https://laltroweb.it/)
* Plugin maked for (demo link): [VegAmami](http://www.vegamami.it/)
* Ace (Ajax.org Cloud9 Editor): [Ace (Ajax.org Cloud9 Editor)](https://ace.c9.io/)

= Language =

* English
* Italian
* Full translatable!

== Screenshots ==

1. New advanced menu, in 3.0!!!
2. My Custom CSS Panel with All feature and DarkTheme
3. My Custom CSS Panel with LightTheme
4. Preview in browser sourcecode
5. Backup List (New Feature)!
6. Confirm when delete backup!

== Installation ==

1. Upload `my-custom-css` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==

= 3.0 =

* More advanced setting for enable/disable many feature!

== Changelog ==

= 3.0 =

* More advanced setting for enable/disable many feature:
* Enable/Disable invisible elements;
* Enable/Disable print margin;
* Enable/Disable line numbers;
* Enable/Disable backups (end, if enabled, save only if exist data to save = no more empty backup)!
* Enable/Disable autocomplete;
* All setting saved with cookies only when an admin access in backend!

= 2.9 =

* Added version querystring in css and javascript (prevent cache issue from old release).

= 2.8 =

* More code change/fix/clean (expecially in textarea and search bar) + removed old codemirror fix.

= 2.7 =

* Search bar no longer fixed (caused graphics problems in some circumstances)

= 2.6 =

* Fix a bug with 2.5 in svn repository!

= 2.5 =

* More code clean, fix, and optimization!
* All string translated (also in backup pages)!
* Now with live auto-complete; text-zoom; theme-changer; and search bar!!!

= 2.4 =

* More code clean, bugfix, optimization and performance!
* Now support https and fix incompatibility issue with blue admin plugin!

= 2.3 =

* Some code change/fix and now use Ace (Ajax.org Cloud9 Editor) instead of CodeMirror !!! :D

= 2.2 =

* More code clean/rewrite/optimization!
* my_style.css no more deleted if blank (but still no show in output)...
* Now script generate blank index.html page for prevent directory listing
* File now in "wp-content/uploads/" directory (i hope this can fix some issue with some server - sorry, i prefer to don't touch/old user file via script - so, move yourself if u glad)!
* Top button improved with code of another my plugin: smooth-page-scroll-to-top (some little code only for this page - a good jquery effect)!
* Now only one (big) Saved button on top-right (fixed)!
* Now backup button is on bottom/left.

= 2.1 =

* Now my_style.css and backup will be saved in content directory! (no more delete on upgrade)!
* More other change in code...

= 2.0 =

* Now use "$wp_filesystem->put_contents" and "wp_mkdir_p" instead of "file_put_contents" and "mkdir" (fix issue permission)...
* Move backup in "hidden directory" - with old backup migration (prevent long css list in wordpress plugin editor)...

= 1.9 =

* Add css stylesheets in backup management (just some little improvements)...!
* Some fix in multisite network (disable view/erase backup except for blog_id 1)!

= 1.8 =

* New important feature! Backup in setting panel (make backup when u click on save)!!! :D
* Update Syntax CodeMirror to Version 5.8: <a href="http://codemirror.net/">codemirror.net</a>
* Update Support/Author link to: <a href="https://laltroweb.it/">https://laltroweb.it/</a>
* Update Donate link to: <a href="https://www.paypal.me/SalvatoreN">https://www.paypal.me/SalvatoreN</a>

= 1.7 =

* Now use "link" instead of @import (link improve performance!)...

= 1.6 =

* Tested with wordpress 4.3.1
* More code rewrite (fix for translator)!
* Fix icon css issue.

= 1.5 =

* New feture: Now is full compatible in network mode (multisite support)!

= 1.4 =

* Fix a small issue in "@import url()" (add ";") with safari browser!

= 1.3 =

* Now code is saved both on database and also in file "my_style.css[+ '?filemtime' to fix browser cache]" when you click on "Save" (made and updated via db+php only if is present css code). Thanks to this I can see custom css code in admin panel via database and put in source via file with '@import url("my_style.css[+ '?filemtime' to fix browser cache]")'. I think (and hope) that this can optimize source code view and time load!
* New "Save" button in plugin page (fixed via css in top right position)!
* New "Top" button in bottom right position (classic "anchor" top button)!
* Many other code clean and optimization!

= 1.2 =

* Removed background in plugin list: <a href="http://wordpress.org/support/topic/plugins-page-colour">support/topic/plugins-page-colour</a>

= 1.1 =

* Updated CodeMirror to release 3.1!

= 1.0 =

* Add CSS Style background and icon in plugins page :)

= 0.9 =

* Very minor change: Plugin priority to 999 (now latest in header)
* Some little fix and clean/indent in php code

= 0.8 =

* Updated CodeMirror to release 3.02!
* Some little change to readme.txt (removed faq and fixed other info).

= 0.7 =

* Changed plugin URI from darkwolf.it to wordpress.org
* Some CSS fix if no JavaScript enabled in browser
* Updated CodeMirror (Syntax) to latest release (atm 3.01)
* New Support and Setting link in plugins list
* Translated Description and Support/Settings links in Ita

= 0.6 =

* Some little fix in CSS!
* Fix incompatibility with WP Editor Plugin: <a href="http://wordpress.org/extend/plugins/wp-editor/">/extend/plugins/wp-editor/</a>

= 0.5 =

* Update Donate link to: <a href="http://www.darkwolf.it/donate-wp">darkwolf.it/donate-wp</a>
* Update CodeMirror (Syntax) to release 3.0: <a href="http://codemirror.net/">codemirror.net</a>
* Add strip tag to prevent bad code: <a href="http://php.net/manual/en/function.strip-tags.php">function.strip-tags.php</a>

= 0.4 =

* Some little fix in css auto height (codemirror.css)

= 0.3 =

* Update Syntax CodeMirror to Version 2.15: <a href="http://codemirror.net/">codemirror.net</a>

= 0.2 =

* Now you can see in source code only if is present custom css
* Blog's homepage redirect for direct access in my-custom-css.php
* Empty "index.html" in all directory to Prevent Directory Listing
* New menu in admin panel (after "Appearance" and before "Plugins") with custom icon

= 0.1 =

* First release