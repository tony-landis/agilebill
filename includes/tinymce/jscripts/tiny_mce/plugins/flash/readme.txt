 FLASH plugin for TinyMCE
-----------------------------

About:
  This is the INSERT FLASH Dialog contributed by Michael Keck.
  This one supports popup windows and targets.

Note:
  The placeholder for Flash is called 'mce_plugin_flash' and needs a class 'mce_plugin_flash' in the 'css_-style'.
  Do not name another image 'name="mce_plugin_flash"!

Installation instructions:
  * Copy the flash directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "flash".
  * Add this "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]" to extended_valid_elements option.

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "flash",
    extended_valid_elements : "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name]"
    flash_external_list_url : "example_flash_list.js" // Optional URL to a list of Flash movies
  });


----------------------------------------------------------------
ADDITIONAL NOTE:

The flash plugin has been heavily modified (the original is editor_plugin_original.js) since the original did not play nicely with html content that 
already contained existing flash tags and in fact stripped out the object
tags for existing flash html. The rewrite corrects this as well attempts
to preserve the existing flash tags where possible. The tinyMCE.init call
should be be something like: 

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "flash",
    extended_valid_elements : "img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|obj|param|embed]"
  });

Note the extra obj,param,embed attributes for the img tag. These attributes
are used to serialize data from existing flash tags so that they can be
properly restored. Editing a flash tag with the plugin will cause this
information to be lost (sorry !) but still produces a working flash nevertheless.  

