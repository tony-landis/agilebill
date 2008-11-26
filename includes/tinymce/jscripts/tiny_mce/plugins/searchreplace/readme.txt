 searchreplace plugin for TinyMCE
-----------------------------

About:
  This plugin adds search/replace dialogs to TinyMCE.

Installation instructions:
  * Copy the searchreplace directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "searchreplace".
  * Add buttons "search,replace" to the button list.

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "searchreplace",
    theme_advanced_buttons1_add : "search,replace",
  });
