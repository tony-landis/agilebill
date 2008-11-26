<a href="?_page=voip:overview">{translate module=voip}user_overview{/translate}</a> | 
<a href="?_page=voip:activity">{translate module=voip}user_activity{/translate}</a> | 
<a href="?_page=voip_vm:user">{translate module=voip}user_voicemail{/translate}</a>{if $list->is_installed(voip_fax)} | 
<a href="?_page=core:user_search&module=voip_fax&_next_page=user_search_show">{translate module=voip}user_fax{/translate}</a> | 
<a href="?_page=voip:features">{translate module=voip}user_features{/translate}</a> |
{/if}
