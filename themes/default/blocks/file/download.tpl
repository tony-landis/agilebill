{ $method->exe("file","download") } 
{ if ($method->result == FALSE) } 
{ $block->display("core:method_error") } 
{ /if }

