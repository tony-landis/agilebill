
<form name="form" method="post" action="">
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">Module Configuration</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="row1">
                <tr> 
                  <td width="28%">Module Name : <br>
                  </td>
                  <td width="72%"> 
                    <input type="text" size="32"  name="module">
                  </td>
                </tr>
                <tr> 
                  <td width="28%">Table Name : </td>
                  <td width="72%"> 
                    <input type="text" size="32"  name="table">
                    (should be the same as the Module name in most cases)</td>
                </tr>
                <tr> 
                  <td width="28%">Dependancy(s):</td>
                  <td width="72%"> 
                    <input type="text" size="32"  name="dependancy">
                    (comma separated list of required modules, core assumed) </td>
                </tr>
                <tr> 
                  <td width="28%">Cache: (in seconds)</td>
                  <td width="72%"> 
                    <input type="text" size="5"  name="cache" value="0">
                    (not implemented)</td>
                </tr>
                <tr> 
                  <td width="28%"> Order By Field: </td>
                  <td width="72%"> 
                    <select name="order_by" >
                      {foreach from=$VAR.f item=field}
                      {if $field != ''}
                      <option value="{$field}"> 
                      {$field}
                      </option>
                      {/if}
                      {/foreach}
                    </select>
                    (for searches)</td>
                </tr>
                <tr> 
                  <td width="28%">Default Select Limit: </td>
                  <td width="72%"> 
                    <input type="text" size="5"  name="limit" value="35">
                    (for searches)</td>
                </tr>
                <tr> 
                  <td width="28%">Module Parent</td>
                  <td width="72%"> 
                    <input type="text" size="32"  name="module_parent">
                    (blank for self)</td>
                </tr>
                <tr> 
                  <td width="28%">Module Notes</td>
                  <td width="72%"> 
                    <textarea cols="32"  name="module_notes"></textarea>
                  </td>
                </tr>
                <tr> 
                  <td width="28%">Display In Menu?</td>
                  <td width="72%"> 
                    <input type="checkbox" name="module_menu_display" value="1" checked>
                  </td>
                </tr>
                <tr> 
                  <td width="28%">Display Export/Print Bar?</td>
                  <td width="72%"> 
                    <input type="checkbox" name="module_export_bar" value="1">
                    (for search results page)</td>
                </tr>
                <tr> 
                  <td width="28%">Sub-Module Names</td>
                  <td width="72%"> 
                    <input type="text" size="32"  name="module_sub_module">
                    (comma separated list of sub-modules) </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <p><br>
    <br>
  </p>
  <p><br>
    <br>
    <font color="#000099" size="4"><br>
    <b>Field Configuration: </b></font><br>
    <br>
    {foreach from=$VAR.f item=field}
    {if $field != ''}
   
    <input type="hidden" name="f[]" value="{$field}">
  </p>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> [ 
                {$field}
                ] column settings</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1" align="center">
                <tr> 
                  <td width="78%"> Translated Name: 
                    <input type="text" name="field[{$field}][name]" size="32" >
                    <br>
                    <table width="100%" border="0" cellspacing="1" cellpadding="1" class="row1">
                      <tr> 
                        <td width="26%">Field Type:</td>
                        <td width="29%">Default Value:</td>
                        <td width="23%">PDF Width</td>
                      </tr>
                      <tr> 
                        <td width="26%"> 
                          <select name="field[{$field}][type]" >
                            <option value="C(16)">VARCHAR [16]</option>
                            <option value="C(32)">VARCHAR [32]</option>
                            <option value="C(128)">VARCHAR [128]</option>
                            <option value="C(255)">VARCHAR [255]</option>
                            <option value="X2">Text</option>
                            <option value="L">TRUE / FALSE (0/1)</option>
                            <option value="I4">Integer (8)</option>
                            <option value="I8">Integer (20)</option>
                            <option value="F">Floating Point Number</option>
                            <option value="N">Numeric or Decimal Number</option>
                          </select>
                        </td>
                        <td width="29%"> 
                          <input type="text" name="field[{$field}][default]" size="12" >
                        </td>
                        <td width="23%"> 
                          <input type="text" name="field[{$field}][pdf_len]" size="3" >
                        </td>
                      </tr>
                      <tr> 
                        <td width="26%">Min Length</td>
                        <td width="29%">Max Length</td>
                        <td width="23%">Index</td>
                      </tr>
                      <tr> 
                        <td width="26%"> 
                          <input type="text" name="field[{$field}][min_len]" size="3" >
                        </td>
                        <td width="29%"> 
                          <input type="text" name="field[{$field}][max_len]" size="3" >
                        </td>
                        <td width="23%"> 
                          <input type="checkbox" name="field[{$field}][index]" value="1">
                        </td>
                      </tr>
                      <tr> 
                        <td width="26%">Validate</td>
                        <td width="29%">Convert Type</td>
                        <td width="23%">Unique</td>
                      </tr>
                      <tr> 
                        <td width="26%"> 
                          <select name="field[{$field}][validate]" >
                            <option value="">-- none --</option>
                            <option value="any">Any</option>
                            <option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="time">Time</option>
                            <option value="date-time">Date-Time</option>
                            <option value="address">Address</option>
                            <option value="zip">Zip</option>
                            <option value="phone">Phone</option>
                            <option value="cc">Credit Card</option>
                            <option value="check">Check</option>
                            <option value="numeric">Numeric</option>
                            <option value="alphanumeric">Alphanumeric</option>
							<option value="float">Floating Point/Decimal Number</option>
                            <option>Non-numeric</option>
                          </select>
                        </td>
                        <td width="29%"> 
                          <select name="field[{$field}][convert]" >
                            <option value="">-- none --</option>
                            <option value="date">Date</option>
                            <option value="time">Time</option>
                            <option value="date-now">Current time</option>
                            <option value="date-time">Date-time</option>
							<option value="array">Array</option>
                            <option value="md5">MD5</option>
                            <option value="rc5">RC5</option>
                            <option value="crypt">Crypt</option>
                            <option value="gpg">GPG</option>
                            <option value="pgp">PGP</option>
                          </select>
                        </td>
                        <td width="23%"> 
                          <input type="checkbox" name="field[{$field}][unique]" value="1">
                        </td>
                      </tr>
                      <tr> 
                        <td width="26%">Associated Table</td>
                        <td width="29%">Associated Field</td>
                        <td width="23%">Default Length</td>
                      </tr>
                      <tr> 
                        <td width="26%"> 
                          <input type="text" name="field[{$field}][asso_table]" size="12" >
                        </td>
                        <td width="29%"> 
                          <input type="text" name="field[{$field}][asso_field]" size="12" >
                        </td>
                        <td width="23%"> 
                          <input type="text" name="field[{$field}][def_len]" size="3" >
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td width="22%" bgcolor="#CCCCCC"> 
                    {foreach from=$VAR.m item=method}
                    <input type="checkbox" name="method[{$method}][{$field}]" value="1" checked>
                    {$method}
                    <br>
                    {/foreach}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">[ 
                {$field}
                ] page settings</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="25%"> 
                    <div align="center">ADD</div>
                  </td>
                  <td width="22%"> 
                    <div align="center">VIEW</div>
                  </td>
                  <td width="27%"> 
                    <div align="center">SEARCH FORM</div>
                  </td>
                  <td width="26%"> 
                    <div align="center">SEARCH SHOW</div>
                  </td>
                </tr>
                <tr> 
                  <td width="25%"> 
                    <div align="center"> 
                      <input type="checkbox" name="field[{$field}][page_view][include]" value="1" {if $field != "id" && $field != "site_id"}checked{/if}>
                    </div>
                  </td>
                  <td width="22%"> 
                    <div align="center"> 
                      <input type="checkbox" name="field[{$field}][page_add][include]" value="1" {if $field != "id" && $field != "site_id"}checked{/if}>
                    </div>
                  </td>
                  <td width="27%"> 
                    <div align="center"> 
                      <input type="checkbox" name="field[{$field}][page_search_form][include]" value="1" {if $field != "id" && $field != "site_id"}checked{/if}>
                    </div>
                  </td>
                  <td width="26%"> 
                    <div align="center"> 
                      <input type="checkbox" name="field[{$field}][page_search_show][include]" value="1" {if $field != "id" && $field != "site_id"}checked{/if}>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td width="25%"> 
                    <div align="center"> 
                      <select name="field[{$field}][field_type]" >
                        <option value="text_medium">Medium Text</option>
                        <option value="text_small">Small Text</option>
                        <option value="text_large">Large Text</option>
                        <option value="date_time">Date-time</option>
                        <option value="date">Date</option>
                        <option value="date_now">Curent date/time</option>
                        <option value="bool">True/False</option>
                        <option value="menu">Menu List</option>
                        <option value="account_menu">Account List</option>
                      </select>
                    </div>
                  </td>
                  <td width="22%"> 
                    <div align="center">Can Update? 
                      <input type="checkbox" name="field[{$field}][page_view][type]" value="1" checked>
                    </div>
                  </td>
                  <td width="27%"> 
                    <div align="center"></div>
                  </td>
                  <td width="26%"> 
                    <div align="center"></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <p><b> </b></p>
  <p><br>
    <br>
    <br>
    <br>
    {/if}
    {/foreach}
    <br>
    <br>
    {foreach from=$VAR.m item=method}
    <b> 
    <input type="hidden" name="m[]" value="{$method}">
    </b><br>
    <u><B><font size="3" color="#990000"> </font></B></u></p>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> [ 
                {$method}
                ] Method Settings</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellpadding="1" >
                <tr> 
                  <td width="17%">Block Title</td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][block_name]" size="32" >
                  </td>
                </tr>
                <tr> 
                  <td width="17%">Menu Title</td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][menu_name]" size="32" >
                  </td>
                </tr>
                <tr> 
                  <td width="17%">Display In Module Menu?</td>
                  <td width="83%"> 
                    <input type="checkbox" name="method[{$method}][method_display]" value="1">
                  </td>
                </tr>
                <tr> 
                  <td width="17%">Menu Link </td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][method_page]" size="32"  value="{if $method == 'view'}core:search&amp;module=%%&_escape=1{elseif $method == 'search'}%%:search_form{elseif $method == 'add'}%%:add{/if}">
                    (page to link to) </td>
                </tr>
                <tr> 
                  <td width="17%">Trigger(s): [success] </td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][trigger_success]" size="32" >
                  </td>
                </tr>
                <tr> 
                  <td width="17%">Trigger(s): [failure] </td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][trigger_failure]" size="32" >
                  </td>
                </tr>
                <tr> 
                  <td width="17%">Note(s): </td>
                  <td width="83%"> 
                    <input type="text" name="method[{$method}][method_notes]" size="32" >
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <p>&nbsp; </p>
  <p> 
    {/foreach}
    <br>
    <b> </b></p>
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">Language Pack Settings</div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellpadding="4" class="row1">
                <tr>
                  <td>Module Name: 
                    <input type="text" size="32"  name="lang[name]">
                    <br>
                    Menu Title: 
                    <input type="text" size="32"  name="lang[menu]">
                    <br>
                    Theme: 
                    <input type="text" name="theme_name" size="32"  value="default">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <p><b> </b><br>
    <input type="submit" name="Submit" value="Generate">
    <input type="hidden" name="_page" value="module:dev_add">
    <input type="hidden" name="do[]" value="module:dev_add">
    <br>
    <br>
  </p>
  </form>
<p>&nbsp; </p>
