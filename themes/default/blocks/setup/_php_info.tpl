{ $method->exe("setup","_php_info") } 
{ if ($method->result == FALSE) } 
{ $block->display("core:method_error") } 
{/if}