{$method->exe("asset_invoice","main")}
{if !$pools}
<h3>No Asset Pools Configured</h3>
{else}
<h3>Select an Asset Pool</h3>
<form>
<select name="pool_id">
{foreach from=$pools item=p}
 <option value="{$p.id}">{$p.name}</option>
{/foreach}
</select>
<input type="hidden" name="_page" value="asset_invoice:invoice" />
<input type="hidden" name="do[]" value="asset_invoice:invoice" />
<input type="submit" value="Submit" />
</form>
{/if}