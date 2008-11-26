<?php
	ob_start();  

    # Require the needed files...
    require_once('config.inc.php');
    require_once(PATH_ADODB  . 'adodb.inc.php');
    require_once(PATH_CORE   . 'auth.inc.php');
    require_once(PATH_CORE   . 'database.inc.php'); 
    require_once(PATH_CORE   . 'session.inc.php'); 	
    require_once(PATH_CORE   . 'setup.inc.php');
    require_once(PATH_CORE   . 'vars.inc.php');
    require_once(PATH_CORE   . 'xml.inc.php');

    # start the debugger
    $C_debug 	= new CORE_debugger; 

    # remove conflicting s variable
    if (isset($_GET['s']))
    {
        $_GET_s = $_GET['s'];
        unset($_GET['s']);
    }
    else if( isset($_POST['s']))
    {
        $_POST_s = $_POST['s'];
        unset($_POST['s']);
    }
    
    # get the vars...
    $C_vars 	= new CORE_vars;
    $VAR = $C_vars->f;

    # initialize the site setup
    $C_setup 	= new CORE_setup;

    # initialize the session handler
    $C_sess 	= new CORE_session;

    # define the other session variables as constants
    $C_sess->session_constant();
  
    # update the session constants
    $C_sess->session_constant_log();
    
    # initialze the authentication handler
    $force          = false;
    $C_auth  	    = new CORE_auth ($force);
 	
    ############################################################################
    # Verify the User's Access
    $authorized = false;
  	if(defined("SESS_LOGGED") && SESS_LOGGED == "1" && agile_check_auth ( _HTACCESS_ID ) )
        $authorized = true;

	############################################################################
	## forward to login page:
    if ( !$authorized )
    {
        header("Location: ".URL."?_page=account:login_cookie&_htaccess_id=" . _HTACCESS_ID. "&_next_page="._RETURN_URL);
        exit();
    }
    
    
    ### Reset the 's' var
    if(isset($_POST_s))
    {
        $_POST['s'] = $_POST_s;
    }
    else if (isset($_GET_s))
    {
        $_GET['s'] = $_GET_s;
    }


    ##############################
    ##  Check Authentication    ##
    ##############################
    function agile_check_auth($id)
    {
        ### Check if user is a member of one of the authorized groups:
        $db     = &DB();
        $sql    = 'SELECT status,group_avail FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
                site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
                status      = ' . $db->qstr('1') . ' AND
                id          = ' . $db->qstr($id);
        $result = $db->Execute($sql);                                      
        if($result->RecordCount() > 0)
        {
            global $C_auth;
            @$arr = unserialize($result->fields['group_avail']);
            for($i=0; $i<count($arr); $i++)
            {
                if($C_auth->auth_group_by_id($arr[$i]))
                {
                    return true;
                }
            }
        }
        return false;
    }
	
	ob_end_flush();
?>