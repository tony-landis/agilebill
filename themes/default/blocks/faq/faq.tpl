<!-- prototype & aculo -->
<script src="includes/javascript/prototype.js" type="text/javascript"></script>
<script src="includes/javascript/effects.js" type="text/javascript"></script> 
<script src="includes/javascript/controls.js" type="text/javascript"></script>

{literal}
<style>
	div.auto_complete { width: 450px; background: #fff; }
	div.auto_complete ul { border:1px solid #888; margin:0; padding:0; width:100%; list-style-type:none; }
	div.auto_complete ul li { margin:0; padding:3px; }
	div.auto_complete ul li.selected {  background-color: #ffb;  }
	div.auto_complete ul strong.highlight {  color: #800;  margin:0; padding:0; }
	div.faq_search_results { background-color:#F0FAEF; border: 1px dashed #999; padding:5px; padding-top:0px;}
	div.faq_search_results ul li {  padding-bottom:2px; padding-top:opx; }
	input.faq_search_txt { font-size: 14px; border:2px solid #999; padding:2px; }
	div.main { text-align:left }
</style>
 
<script language="javascript"> 
function showFaqSearch(string) {
	var url = 'ajax.php?do[]=faq:faq_search&search='+string; 	
	var http = getHTTPObject();
  	http.open("GET", url, true);
  	http.onreadystatechange = function() { 
		if (http.readyState == 4) {  
			new Effect.Appear('faq_search_results', {duration:0});  
			$('faq_search_results').innerHTML = http.responseText; 	
		} 
	}
	http.send(null);
}

function showFaqCat(id) {
	var url = 'ajax.php?do[]=faq:faq_search&category_id='+id; 	
	var http = getHTTPObject();
  	http.open("GET", url, true);
  	http.onreadystatechange = function() { 
		if (http.readyState == 4) {  
			new Effect.Appear('faq_search_results', {duration:0});  
			$('faq_search_results').innerHTML = http.responseText; 	
		} 
	}
	http.send(null);
} 

function showFaq(id) {
	var url = 'ajax.php?do[]=faq:faq_show&id='+id; 	
	var http = getHTTPObject();
  	http.open("GET", url, true);
  	http.onreadystatechange = function() { 
		if (http.readyState == 4) {  
			new Effect.Appear('faq_search_results', {duration:0});  
			$('faq_search_results').innerHTML = http.responseText; 	
		} 
	}
	http.send(null);
}
</script>
{/literal}
 
<div class="main">

<div id="faq_search"> 
	<p> 	  		 
	<input type="text" autocomplete="off" id="faq_autofill" name="faq_autofill" size="40" onBlur="showFaqSearch(this.value)" onClick="this.value=''; $('faq_autofill').focus();  " class="faq_search_txt" />  
	<b>{html_link name=search action="showFaqSearch( $('faq_autofill').value );"}</b>
	</p>	
</div>


<div id="faq_search_results" class="faq_search_results" {style_hide}></div>


<div id="faq_categorys">
{ $method->exe("faq","faq_categories")} 
{if faq_category_list}
<h2>{translate module=faq}faqs{/translate}</h2>
<ul>
	{foreach from=$faq_category_list item=record key=key}
		<li>		  
		<a href="#" onclick="showFaqCat('{$record.id}')">{$record.name}</a>
		</li>		  
	{/foreach} 
</ul>
{/if}
</div>


{if $VAR.id}
	{* show specified faq *}
	<script language="javascript">showFaq('{$VAR.id}');</script>
{elseif $VAR.category_id}
	{* show specific faq category *}
	<script language="javascript">showFaqCat('{$VAR.category_id}');</script>
{elseif $VAR.search}
	{* run a search *}
	<script language="javascript">showFaqSearch('{$VAR.search}');</script>	
{/if}

</div>