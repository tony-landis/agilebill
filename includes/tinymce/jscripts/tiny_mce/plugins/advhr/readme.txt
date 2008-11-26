 Advhr plugin for TinyMCE
-----------------------------

About:
  This is a more advanced hr dialog contributed by Michael Keck.
  This one supports noshade, width and size.

Installation instructions:
  * Copy the advhr directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "advhr".
  * Add this "hr[class|width|size|noshade]" to extended_valid_elements option.

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "advhr",
    theme_advanced_buttons1_add : "advhr",
    extended_valid_elements : "hr[class|width|size|noshade]"
  });
