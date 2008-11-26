<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="task_add" name="task_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_rate}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="voip_rate_name" value="{$VAR.voip_rate_name}" {if $voip_rate_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_pattern
                    {/translate}
                  </td>
                  <td width="65%">
                    <textarea id="voip_rate_pattern" name="voip_rate_pattern" {if $voip_rate_pattern == true}class="form_field_error"{/if} cols="55" rows="5">{$VAR.voip_rate_pattern}</textarea>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_connect_fee
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_connect_fee" value="{$VAR.voip_rate_connect_fee}" {if $voip_rate_connect_fee == true}class="form_field_error"{/if}> (eg: 0.025 for a 2.5 cents connect charge.)
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_increment_seconds
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_increment_seconds" value="{$VAR.voip_rate_increment_seconds}" {if $voip_rate_increment_seconds == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_seconds_included
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_seconds_included" value="{$VAR.voip_rate_seconds_included}" {if $voip_rate_seconds_included == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_amount
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_amount" value="{$VAR.voip_rate_amount}" {if $voip_rate_amount == true}class="form_field_error"{/if}> (eg: 0.025 for 2.5 cents per min.)
                  </td>
                </tr>



                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_min
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_min" value="{if $VAR.voip_rate_min}{$VAR.voip_rate_min}{else}0{/if}" {if $voip_rate_min == true}class="form_field_error"{/if}> (0 for no minimum) {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>
                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_max
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="voip_rate_max" value="{if $VAR.voip_rate_max}{$VAR.voip_rate_max}{else}-1{/if}" {if $voip_rate_max == true}class="form_field_error"{/if}> (-1 for no maximum) {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>

                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_type
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_type" >
                      <option value="0" {if $VAR.voip_rate_type == "0"}selected{/if}>
                      {translate module=voip_rate}
                      type_innetwork
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.voip_rate_type == "1"}selected{/if}>
                      {translate module=voip_rate}
                      type_local
                      {/translate}
                      </option>
                      <option value="2" {if $VAR.voip_rate_type == "2"}selected{/if}>
                      {translate module=voip_rate}
                      type_regular
                      {/translate}
                      </option>
                      <option value="3" {if $VAR.voip_rate_type == "3"}selected{/if}>
                      {translate module=voip_rate}
                      type_default
                      {/translate}
                      </option>

                    </select>
                  </td>
                </tr>

                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_direction
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_direction" >
                      <option value="0" {if $VAR.voip_rate_direction == "0"}selected{/if}>
                      {translate module=voip_rate}
                      direction_inbound
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.voip_rate_direction == "1"}selected{/if}>
                      {translate module=voip_rate}
                      direction_outbound
                      {/translate}
                      </option>
                      <option value="2" {if $VAR.voip_rate_direction == "2"}selected{/if}>
                      {translate module=voip_rate}
                      direction_both
                      {/translate}
                      </option>

                    </select>
                  </td>
                </tr>

				<tr valign="top"> 
                  <td width="35%"> 
                    {translate module=voip_rate}
                    field_combine
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="voip_rate_combine" >
                      <option value="0" {if $VAR.voip_rate_combine == "0"}selected{/if}> 
                      {translate module=voip_rate}
                      type_no
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.voip_rate_combine == "1"}selected{/if}> 
                      {translate module=voip_rate}
                      type_yes
                      {/translate}
                      </option>
                    </select> {translate module=voip}field_postpaid_only{/translate}
                  </td>
                </tr>

                <tr valign="top">
                  <td width="35%">
                    {translate module=voip_rate}
                    field_percall
                    {/translate}
                  </td>
                  <td width="65%">
                    <select name="voip_rate_perCall" >
                      <option value="0" {if $VAR.voip_rate_perCall == "0"}selected{/if}>
                      {translate module=voip_rate}
                      type_no
                      {/translate}
                      </option>
                      <option value="1" {if $VAR.voip_rate_perCall == "1"}selected{/if}>
                      {translate module=voip_rate}
                      type_yes
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>

                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
					<input type="hidden" name="voip_rate_date_added" value="NOW()">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="voip_rate:view">
                    <input type="hidden" name="_page_current" value="voip_rate:add">
                    <input type="hidden" name="do[]" value="voip_rate:add">
                  </td>
                </tr>
              </table>
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  </form>
