 Table plugin for TinyMCE
------------------------------

Installation instructions:
  * Copy the table directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "table".
  * Add the table button name to button list, example: theme_advanced_buttons3_add_before : "tablecontrols".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "table",
    theme_advanced_buttons3_add_before : "tablecontrols"
  });
		html += tinyMCE.getControlHTML("row_props");
		html += tinyMCE.getControlHTML("cell_props");

Table controls:
  tablecontrols               All table control below and some separators between them.
  table                       Insert table control.
  row_props                   Edit row properties (tr).
  cell_props                  Edit cell properties (td).
  delete_col                  Delete column control.
  delete_row                  Delete row control.
  col_after                   Column after control.
  col_before                  Column before control.
  row_after                   Row after control.
  row_before                  Row before control.
  row_after                   Row after control.
  row_before                  Row before control.

Table plugin commands:
  mceInsertTable            Inserts a new table at cursor location the default size is 2x2.
                            If the value parameter is specified it should contain a name/value array,
                            this array has the following options cols, rows, border, cellspacing, cellpadding.
                            The default border is set to: 0.
  mceTableInsertRowBefore   Inserts a row before/above the current cursor location.
  mceTableInsertRowAfter    Inserts a row after/under the current cursor location.
  mceTableDeleteRow         Deletes the row at the current cursor location.
  mceTableInsertColBefore   Inserts a column before the current cursor location.
  mceTableInsertColAfter    Inserts a column after the current cursor location.
  mceTableDeleteCol         Deletes the column at the current cursor location.
