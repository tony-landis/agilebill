Save plugin for TinyMCE
(Dec 2004) by SlyD - d.herwald@dsh-elektronik.de
--------------------------

About:
Adds a "save" button that submits the form.

Installation instructions:
  * Copy the save directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "save".
  * Add the save button name to button list, example: theme_advanced_buttons3_add : "save".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "save",
    theme_advanced_buttons3_add : "save"
  });