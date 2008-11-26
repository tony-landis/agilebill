 ########### MS TASK BATCH FILE ####################
 #   Be sure to replace the path to php.exe below  #
 #    with the actual path to your php.exe file.   #
 #          Do not change anything else.           #
 ###################################################

@cls
@c:\php\php.exe -q index.php _task=1 _escape=1 _page=core:blank > task_log.htm


