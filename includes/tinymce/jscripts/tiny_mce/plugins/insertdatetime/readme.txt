 InsertDateTime plugin for TinyMCE
-----------------------------------

Installation instructions:
  * Copy the insertdatetime directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "insertdatetime".
  * Add the insertdate or inserttime button name to button list, example: theme_advanced_buttons3_add : "insertdate,inserttime".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "insertdatetime",
    theme_advanced_buttons3_add : "insertdate,inserttime",
    plugin_insertdate_dateFormat : "%Y-%m-%d",
    plugin_insertdate_timeFormat : "%H:%M:%S"
  });

Configuration:
  plugin_insertdate_dateFormat - Format that the date is output as. Defaults to: "%Y-%m-%d".
	Replacement variables:
	%y - year as a decimal number without a century (range 00 to 99)
	%Y - year as a decimal number including the century
	%d - day of the month as a decimal number (range 01 to 31)
	%m - month as a decimal number (range 01 to 12)
	%D - same as %m/%d/%y
	%r - time in a.m. and p.m. notation
	%H - hour as a decimal number using a 24-hour clock (range 00 to 23)
	%I - hour as a decimal number using a 12-hour clock (range 01 to 12)
	%M - minute as a decimal number (range 00-59)
	%S - second as a decimal number (range 00-59)
	%p - either `am' or `pm' according to the given time value
	%% - a literal `%' character

  plugin_insertdate_timeFormat - Format that the time is output as. Defaults to: "%H:%M:%S".
