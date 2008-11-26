/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     postfilter.lang.php
 * Type:     postfilter
 * Name:     lang
 * Version:  1.0
 * Date:     August 12, 2002
 * Purpose:  Parses the intermediate tags left by compiler.lang
 *           and replaces them with the translated strings,
 *           according to the $compile_id value (language code).
 *          
 * Install:  Drop into the plugin directory, call
 *           $smarty->load_filter('post','lang');
 *            or
 *           $smarty->autoload_filters = array('post' => array('lang'));
 *           from application.
 * Author:   Alejandro Sarco <ale@sarco.com.ar>
 * -------------------------------------------------------------
 */ 
function smarty_postfilter_lang1($tpl, &$smarty) {
 
 //Include your own respective translation strings here
 //include('path/to/your/languages/directory/'.$smarty->compile_id.'/.your_language_file.php');
 
 $offset = 0; 
 while ( $start = strpos($tpl, '<?php ($lang.', $offset )) {
  $end = strpos($tpl, ') ?>', $start );
  $rplstr =  substr($tpl, $start + 13, $end - ($start + 13));
  $tpl = substr_replace($tpl, $lang[$rplstr], $start, $end - ($start - 4));
  $offset = $end + 4;
 }
 
 return $tpl;
}
 
?>
