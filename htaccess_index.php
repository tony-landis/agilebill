<?php 
	ob_start();             
	
	# Define the file types
	$_IncludeFiles = Array ( 'htm', 'html', 'php', 'php3', 'php4', 'phtml', 'inc', 'phps' );
	$_VirtualFiles = Array ( 'cgi', 'shtml', 'pl' );
	$_PassFiles    = Array
                (
                    Array ('name' => 'jpg',     'type' => 'image/jpeg',            			'disposition' => 'inline'),
                    Array ('name' => 'jpeg',    'type' => 'image/jpeg',            			'disposition' => 'inline'),
                    Array ('name' => 'jpe',    	'type' => 'image/jpeg',            			'disposition' => 'inline'),
                    Array ('name' => 'gif',     'type' => 'image/gif',             			'disposition' => 'inline'),
                    Array ('name' => 'bmp',     'type' => 'image/bmp',             			'disposition' => 'inline'),
                    Array ('name' => 'tif',     'type' => 'image/tif',             			'disposition' => 'inline'),
                    Array ('name' => 'png',     'type' => 'image/png',             			'disposition' => 'inline'),
                    Array ('name' => 'wbmp',    'type' => 'image/vnd.wap.wbmp',             'disposition' => 'inline'),
                    
                    Array ('name' => 'pdf',     'type' => 'application/pdf',       			'disposition' => 'inline'),
                    Array ('name' => 'exe',     'type' => 'application/octet-stream',		'disposition'=>  'attatchment'),
                    Array ('name' => 'zip',     'type' => 'application/x-zip',     			'disposition' => 'attatchment'),
                    Array ('name' => 'gzip',    'type' => 'application/gzip',      			'disposition' => 'attatchment'),
					Array ('name' => 'tgz',     'type' => 'application/tgz',       			'disposition' => 'attatchment'),
                    Array ('name' => 'gz',      'type' => 'application/gz',        			'disposition' => 'attatchment'),
                    Array ('name' => 'doc',     'type' => 'application/ms-word',   			'disposition' => 'inline'),
                    Array ('name' => 'xls',     'type' => 'application/ms-excel',  			'disposition' => 'inline'),
                    Array ('name' => 'csv',     'type' => 'application/ms-excel',  			'disposition' => 'inline'),
                    Array ('name' => 'swf',   	'type' => 'application/x-shockwave-flash', 	'disposition' => 'inline'), 
                     
                    Array ('name' => 'txt',     'type' => 'text/plain',            			'disposition' => 'inline'),
                    Array ('name' => 'text',    'type' => 'text/plain',            			'disposition' => 'inline'),
                    Array ('name' => 'rtf',     'type' => 'text/richtext',         			'disposition' => 'inline'),
                    Array ('name' => 'xml',     'type' => 'text/xml',       				'disposition' => 'inline'),
                    Array ('name' => 'css',     'type' => 'text/css',            			'disposition' => 'inline'),
                    Array ('name' => 'js',      'type' => 'text/plain',            			'disposition' => 'inline'),
                    Array ('name' => 'wml',     'type' => 'text/vnd.wap.wml',            	'disposition' => 'inline'),
                     
                    Array ('name' => 'avi',     'type' => 'video/avi',            			'disposition' => 'attatchment'),
                    Array ('name' => 'mpg',     'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
                    Array ('name' => 'mpeg',    'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
                    Array ('name' => 'mpe',     'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
                    Array ('name' => 'wmv',   	'type' => 'video/x-ms-wmv', 				'disposition' => 'attatchment'),
                    Array ('name' => 'asf',   	'type' => 'video/x-ms-asf', 				'disposition' => 'attatchment')                    
                );
 
    # Load the config file:
    require_once('config.inc.php');

    # Require the needed files...
    require_once(PATH_ADODB  . 'adodb.inc.php');
    require_once(PATH_CORE   . 'auth.inc.php');
    require_once(PATH_CORE   . 'database.inc.php');
    require_once(PATH_CORE   . 'method.inc.php');
    require_once(PATH_CORE   . 'session.inc.php');
    require_once(PATH_CORE   . 'translate.inc.php');
    require_once(PATH_CORE   . 'setup.inc.php');
    require_once(PATH_CORE   . 'vars.inc.php');
    require_once(PATH_CORE   . 'xml.inc.php');

	## Path to the error file
	define ( 'ERROR_GIF',   PATH_THEMES.DEF_THEME_N.'/images/htaccess_error.gif' );
	
    # start the debugger
    $C_debug 	= new CORE_debugger; 

    # initialize the GET/POST vars
    $C_vars 	= new CORE_vars;
    $VAR = $C_vars->f;

    # initialize the site setup
    $C_setup 	= new CORE_setup;

    # initialize the session handler
    $C_sess 	= new CORE_session;

    # define the other session variables as constants
    $C_sess->session_constant();

    # initialize the translation handler
    $C_translate = new CORE_translate;

    # update the session constants
    $C_sess->session_constant_log();

    # initialze the authentication handler
    $force          = false;
    $C_auth  	    = new CORE_auth ($force);

    ########################################################################
    # Verify the User's Access
    $authorized = false;
  	if(defined("SESS_LOGGED"))
		if(SESS_LOGGED == "1" && check_auth($VAR['_HTACCESS_ID']))
            $authorized = true;

    ############################################################################
    ## If this was a GET:
    if ( isset($REQUEST_URI ) )
    {
        $ARRAY = explode ( '?',  $REQUEST_URI);
        $REQUEST_URI = $ARRAY[0] ;
    }

    ## Define global system vars...
    if(!isset($DOCUMENT_ROOT)) $DOCUMENT_ROOT 		= $_SERVER["DOCUMENT_ROOT"];
    if(!isset($REQUEST_URI)) $REQUEST_URI 			= $_SERVER["REQUEST_URI"];
    if(!isset($SCRIPT_FILENAME)) $SCRIPT_FILENAME 	= $_SERVER["SCRIPT_FILENAME"];


    ############################################################################
    ### Check if File Exists:
    if  (file_exists($DOCUMENT_ROOT.$REQUEST_URI) &&
        ($SCRIPT_FILENAME            != $DOCUMENT_ROOT.$REQUEST_URI) &&
        ($REQUEST_URI                != "/") &&
        (!preg_match( '@[////]{2,}$@', $REQUEST_URI ) ) )
        {

        $url = $REQUEST_URI;

        ########################################################################
        # Check Passthu File Types:

        for ($i=0; $i<count($_PassFiles); $i++)
        {
            $ext =  substr (strrchr ($DOCUMENT_ROOT.$url, "."), 1);
            if ( strtolower ( $ext ) == $_PassFiles[$i]["name"] )
            {
                if ($authorized)
                {
                	# determine the filename:
                	$ext1 = $_PassFiles[$i]['name'];
                	@$arr2 = explode('/', $REQUEST_URI); 
                	$file_name = 'download.'.$ext1;
                	for($ii=0; $ii<count($arr2); $ii++) 
                		$file_name = $arr2[$ii]; 
                	
                    # Set the correct header info:
                    header("Content-type: " . $_PassFiles[$i]['type']);
                    header("Content-Disposition: " . $_PassFiles[$i]['disposition'] . ";filename=$file_name");
                    header("Cache-Control: no-store, no-cache, must-revalidate");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    @readfile ($DOCUMENT_ROOT.$url, "r");
                    exit();
                }
                else
                {
                    # Display the error gif:
                    header("Content-type: image/gif");
                    header("Content-Disposition: inline;filename=error.gif");
                    header("Cache-Control: no-store, no-cache, must-revalidate");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    @readfile (ERROR_GIF, "r");
                    exit();
                }
            }
        }


        ########################################################################
        # Check Include File Types:

        for ($i=0; $i<count($_IncludeFiles); $i++)
        {
            $ext =  substr (strrchr ($DOCUMENT_ROOT.$url, "."), 1);
            if ( strtolower ( $ext ) == $_IncludeFiles[$i] )
            {
                if ($authorized)
                {
                    ## run:
                    include_once ( $DOCUMENT_ROOT.$url );
                    exit();
                }
                else
                {
                    ## forward to login page:
                    header("Location: ".URL."?_page=account:login_htaccess&_htaccess_id=" . $VAR['_HTACCESS_ID'] . '&_htaccess_dir_id=' . $VAR['_HTACCESS_DIR_ID']);
                }
            }
        }



        ########################################################################
        # Check Virtual File Types:
        for ($i=0; $i<count($_VirtualFiles); $i++)
        {
            $ext =  substr (strrchr ($DOCUMENT_ROOT.$url, "."), 1);
            if ( strtolower ( $ext ) == $_VirtualFiles[$i] )
            {
                virtual ( $DOCUMENT_ROOT.$url . "?" . $variables); // < needs some work!
                exit();
            }
        }
    }

        ########################################################################
        ### Load the index file:
         
        $url=strip_tags($REQUEST_URI); 
        $url_array=explode("/",$url);
        array_shift($url_array); 
        if ( $authorized ) {
            if(!empty($url_array) && file_exists($DOCUMENT_ROOT.$url.INDEX_FILE) ) {
                include(INDEX_FILE);
                exit();
            } else {
            	## Locate the index file, if any
            	for($i=0; $i<count($_IncludeFiles); $i++) { 
            		if(file_exists($DOCUMENT_ROOT.$url.'index.'.$_IncludeFiles[$i])) {
            			include($DOCUMENT_ROOT.$url.'index.'.$_IncludeFiles[$i]);
            			exit();
            		}
            	}
            	
            	## No index located!
                echo "<BR><BR><B><CENTER>PAGE NOT FOUND</CENTER></B>";
                exit();
            }
        } else {
            ## forward to login page:
            header("Location: ".URL."?_page=account:login_htaccess&_htaccess_id=" . $VAR['_HTACCESS_ID'] . '&_htaccess_dir_id=' . $VAR['_HTACCESS_DIR_ID']);
            exit();
        }


       	########################################################################
        # Filetype not defined, force download:

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=".@basename($DOCUMENT_ROOT.$url).";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".@filesize($DOCUMENT_ROOT.$url));
        @readfile("$DOCUMENT_ROOT.$url");
        exit();


        ##############################
        ##  Check Authentication    ##
        ##############################
        function check_auth($id)
        {
        	### Check if user is a member of one of the authorized groups:
        	$db     = &DB();
        	$sql    = 'SELECT status,group_avail FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                        status      = ' . $db->qstr('1') . ' AND
                        id          = ' . $db->qstr($id);
        	$result = $db->Execute($sql);
        	if($result->RecordCount() > 0) {
        		global $C_auth;
        		@$arr = unserialize($result->fields['group_avail']);
        		for($i=0; $i<count($arr); $i++)
        		if($C_auth->auth_group_by_id($arr[$i]))
        		return true;
        	}
        	return false;
        }
		
	ob_end_flush();
?>
