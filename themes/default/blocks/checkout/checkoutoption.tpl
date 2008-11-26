{$method->exe("checkout","checkoutoption")}
{if $plugin_template != false}
	{ $block->display($plugin_template) } 
{else}
	<p>Sorry, that checkout option is not valid.</p>
{/if}

