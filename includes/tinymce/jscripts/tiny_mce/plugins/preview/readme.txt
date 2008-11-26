 Preview plugin for TinyMCE
-----------------------------------

Installation instructions:
  * Copy the preview directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "preview".
  * Add the preview button name to button list, example: theme_advanced_buttons3_add : "preview".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "preview",
    theme_advanced_buttons3_add : "preview",
    plugin_preview_width : "500",
    plugin_preview_height : "600"
  });

Configuration:
  plugin_preview_width - Preview window width. Defaults to 550.
  plugin_preview_height - Preview window height. Defaults to 600.
  plugin_preview_pageurl - Custom preview page URL relative from theme
                           use "../../plugins/preview/example.html" for a example.
