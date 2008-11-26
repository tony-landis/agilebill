{ $method->exe("campaign","affiliate") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

<!-- Loop through each record -->
{foreach from=$campaign item=campaign} <a name="{$campaign.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="campaign_view" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=campaign}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file1 != ""}
                <tr> 
                  <td> 
                    <div align="center"> 
                      <p><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&_log=no" target="_blank" border="0"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=1&_log=no&aid={$VAR.curr_aid}" border="0"></a></p>
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td> 
                    <div align="center"> 
                      <textarea name="code"  cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=1&aid={$VAR.curr_aid}"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=1"></a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file2 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=2&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=2&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"><a href="{$URL}?_page=campaign:view&file=2&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      </a> 
                      <textarea name="textarea" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=2"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=2&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file3 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=3&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=3&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"><a href="{$URL}?_page=campaign:view&file=3&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      </a> 
                      <textarea name="textarea2" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=3"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=3&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file4 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=4&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=4&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"><a href="{$URL}?_page=campaign:view&file=4&id={$VAR.id}&campaign_id={$campaign.id}&do%5B%5D=campaign:delete_add"> 
                      </a> 
                      <textarea name="textarea3" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=4"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=4&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="6" class="row2">
                <tr> 
                  <td> 
                    <div align="center"> 
                      <table width="100%" border="0" cellpadding="5">
                        {if $campaign.file5 != ""}
                        <tr> 
                          <td> 
                            <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=5&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=5&_log=no" border="0"></a> 
                            </div>
                          </td>
                        </tr>
                        <tr> 
                          <td class="row1"> 
                            <div align="center"> 
                              <textarea name="textarea5" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=5"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=5&aid={$VAR.curr_aid}"> </a></textarea>
                              <br>
                              <br>
                              <br>
                            </div>
                          </td>
                        </tr>
                        {/if}
                      </table>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file6 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=6&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=6&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea6" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=6"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=6&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file7 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=7&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=7&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea7" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=7"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=7&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file8 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=8&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=8&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea8" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=8"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=8&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file9 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=9&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=9&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea9" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=9"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=9&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file10 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=10&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=10&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea10" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=10"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=10&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file11 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=11&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=11&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea11" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=11"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=11&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellpadding="5">
                {if $campaign.file12 != ""}
                <tr> 
                  <td> 
                    <div align="center"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=12&_log=no&aid={$VAR.curr_aid}" target="_blank"><img src="{$URL}modules/campaign/?id={$campaign.id}&file=12&_log=no" border="0"></a> 
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td class="row1"> 
                    <div align="center"> 
                      <textarea name="textarea12" cols="75" rows="3"><a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file=12"> <img src="{$URL}modules/campaign/?id={$campaign.id}&file=12&aid={$VAR.curr_aid}"> </a></textarea>
                      <br>
                      <br>
                      <br>
                    </div>
                  </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">&nbsp;</td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">&nbsp;</td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">&nbsp;</td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <p>&nbsp;</p>
              <p align="center"><b><br>
                </b> 
                {translate module=campaign}
                random_code 
                {/translate}
              </p>
              <p align="center"><br>
              </p>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1" align="center"> 
              <textarea name="textarea4"  cols="75" rows="5"><script language="Javascript">{literal}<!--
var currentdate = 0;
var core = 0;
function StringArray (n) {
  this.length = n;
  for (var i  = 1; i <= n; i++) {
    this[i]   = " ";
  }
}{/literal}
{counter start=0 skip=1 assign="counter"}{if $campaign.file2 != ""}{counter}{/if}{if $campaign.file3 != ""}{counter}{/if}{if $campaign.file4 != ""}{counter}{/if}{counter}
image = new StringArray({$counter});
{counter start=0 skip=1 assign="counter"} 
image[0] = '1';  
{if $campaign.file2 != ""}{counter}image[{$counter}] = '2';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '3'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '4'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '5';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '6'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '7'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '8';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '9'; 
{/if}
{if $campaign.file4 != ""}{counter}image[{$counter}] = '10'; 
{/if}
{if $campaign.file2 != ""}{counter}image[{$counter}] = '11';  
{/if}
{if $campaign.file3 != ""}{counter}image[{$counter}] = '12'; 
{/if} 

var ran = 60/image.length
{literal}function ranimage() {
  currentdate = new Date()
  core = currentdate.getSeconds()
  core = Math.floor(core/ran)
  return(image[core])
}{/literal}
var fileId = ranimage(); 
var write1 = '<a href="{$URL}?_page=campaign:click&caid={$campaign.id}&file='+fileId+'&aid={$VAR.curr_aid}&_escape">';
var write2 = '<img src="{$URL}modules/campaign/?id={$campaign.id}&file='+fileId+'" border="0"></a>';
document.write(write1 + "" + write2);
//--></script></textarea>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <p>
    <input type="hidden" name="_page" value="campaign:view">
    <input type="hidden" name="campaign_id" value="{$campaign.id}">
    <input type="hidden" name="do[]" value="campaign:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </p>
  <p>&nbsp; </p>
</form>
  {/foreach}
{/if}
