{ $method->exe($VAR.module,"search_export") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{/if}