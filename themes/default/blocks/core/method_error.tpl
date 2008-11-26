{if ($method->error == 'auth')} 
	{if $SESS_LOGGED==1} 
	{else} 
		{translate}login_required{/translate}
		{ $block->display("account:login") } 
	{/if} 
{else} {$method->error} {/if} 