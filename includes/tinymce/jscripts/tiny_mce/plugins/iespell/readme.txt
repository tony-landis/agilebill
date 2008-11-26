 ieSpell plugin for TinyMCE
----------------------------

Installation instructions:
  * Copy the iespell directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "iespell".
  * Add the iespell button name to button list, example: theme_advanced_buttons3_add : "iespell".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "iespell",
    theme_advanced_buttons3_add : "iespell"
  });

Requirements:
  The end user will need MSIE on Windows with the ieSpell installed. This can be downloaded
  from http://www.iespell.com/download.php. Notice on other browsers than MSIE the spellchecking
  button will not be visible.
