 Emotions plugin for TinyMCE
------------------------------

Installation instructions:
  * Copy the emotions directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "emotions".
  * Add the emotions button name to button list, example: theme_advanced_buttons3_add : "emotions".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "emotions",
    theme_advanced_buttons3_add : "emotions"
  });

Copyright notice:
  These emotions where taken from Mozilla Thunderbird.
  I hope they don't get angry if I use them here after all this is a open source project
  aswell and I realy love their product.
