{ $method->exe("newsletter","popup") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

<!-- Loop through each record -->
{foreach from=$newsletter item=newsletter}
 
{$newsletter.description}<br>
{/foreach}
{/if}
