 Print plugin for TinyMCE
-----------------------------

About:
  This plugin adds a print button to TinyMCE.

Installation instructions:
  * Copy the print directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "print".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "print",
    theme_advanced_buttons1_add : "print",
  });
