{ $method->exe("invoice","pdf") }{ if ($method->result == FALSE) }{ $block->display("core:method_error") }{else}{/if}
