<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     linkalize
 * Version:  1.0 
 * Purpose:  parse a string and turn text links into clickable links...
 * Input:    string to catenate
 * Example:  {$var|linkalize}
 * Notes: 	 make sure there is an http:// on all URLs
 * -------------------------------------------------------------
 */

function smarty_modifier_linkalize($string)
{
    return linkalize($string);
}

function linkalize($text) 
{
      $text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $text);          
      $text = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<A TARGET=\"_blank\" HREF=\"$1\">$1</A>", $text); //make all URLs links
      $text = preg_replace("/[\w-\.]+@(\w+[\w-]+\.){0,3}\w+[\w-]+\.[a-zA-Z]{2,4}\b/i","<ahref=\"mailto:$0\">$0</a>",$text);
      return $text;
} 
?>