 Zoom plugin for TinyMCE
--------------------------

About:
 Adds a zoom drop list in MSIE5.5+, this plugin was mostly created to
 show how to add custom droplists as plugins.

Installation instructions:
  * Copy the zoom directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "zoom".
  * Add the preview button name to button list, example: theme_advanced_buttons3_add : "zoom".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "preview",
    theme_advanced_buttons3_add : "zoom"
  });

Requirement:
  This plugin requires MSIE on Mozilla the button will not be visible.
