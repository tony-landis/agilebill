{if $SESS_LOGGED == "1" }

{ $method->exe("affiliate","user_view") } 
{ if ($affiliate_user_view == false) }
	
<!-- signup for an affiliate account -->
{ $block->display("affiliate:user_add") }

{else}

<!-- display the affiliate account -->
{ $block->display("affiliate:user_view") }

{/if}
{else}

<!-- login/register for an account -->
{ $block->display("account:login") }
{/if}
