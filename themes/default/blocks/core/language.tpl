<center>  
  <form name="form1" method="post" action="">
    { $list->menu_files("", "lid", $smarty.const.SESS_LANGUAGE, "language", "", "_core.xml", "form_menu") }
    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
  </form>
</center>
