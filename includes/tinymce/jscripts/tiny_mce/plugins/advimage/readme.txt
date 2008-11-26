 Advimage plugin for TinyMCE
-----------------------------

About:
  This is a more advanced image dialog mostly based on code contributed by Michael Keck.
  This one supports mouseover/out image swapping.

Installation instructions:
  * Copy the advimage directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "advimage".
  * Add this "a[name|href|target|title|onclick]" to extended_valid_elements option.

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "preview",
    extended_valid_elements : "a[name|href|target|title|onclick]"
  });
