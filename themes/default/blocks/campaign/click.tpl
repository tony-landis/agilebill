{ $method->exe("campaign","click") } 
{ if ($method->result == FALSE) } 
{ $block->display("core:method_error") } 
{ /if }

