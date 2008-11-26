 { $method->exe("newsletter","subscribe_confirm") } 
 { if ($method->result == FALSE) } 
 { $block->display("core:method_error") } 
 {/if}
