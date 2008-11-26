<?php
	
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */
	
/*
* Vars available from server config:
* host
* ssl 
* 
* http://demo.interworx.info:2080/nodeworx/nodeworx.php
* demo@interworx.info
* demo
* 
*/  
class plgn_prov_INTERWORX_CP_1_8
{
    function plgn_prov_INTERWORX_CP_1_8()
    {
        $this->name             = 'INTERWORX_CP_1_8';
        $this->task_based       = false;
        $this->remote_based     = true;
    }

    # add new service
    function p_new()
    { 
    }


    # edit service  (not used)
    function p_edit()
    { 
    }


    # activate service
    function p_inactive()
    { 
    }


    # deactivate service
    function p_active()
    { 
    }


    # delete service
    function p_delete()
    { 
    }


    # construct echo all updates
    function p_one($id)
    {
        global $C_debug;

        # Get the service details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
                   id               =  ' . $db->qstr( $id ) . ' AND
                   site_id          =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if($rs->RecordCount() == 0) {
            return false;
        }
        $this->service      = $rs->fields;
        @$this->plugin_data = unserialize($this->service['host_provision_plugin_data']);

        # Get the account details
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account WHERE
                    id           =  ' . $db->qstr( $this->service['account_id'] ) . ' AND
                    site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $acct = $db->Execute($sql);
        $this->account = $acct->fields;

        # Get the server details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
                   id           =  ' . $db->qstr( $this->service['host_server_id'] ) . ' AND
                   site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if (@$rs->RecordCount() == 0) {
            return false;
        } else {
            $this->server = $rs->fields;
            @$this->server_cfg = unserialize($rs->fields['provision_plugin_data']);
        }

       

        # determine the correct action
        switch ($this->service['queue'])
        {
                # new
                case 'new':
                    $result = $this->p_new();
                    # send the user the details
                    include_once(PATH_MODULES.'email_template/email_template.inc.php');
                    $email = new email_template;
                    $email->send('host_new_user', $this->account['id'], $rs->fields['id'], '', '');
                break;

                # active
                case 'active':
                    $result = $this->p_active();
                    # send the user the details
                    #include_once(PATH_MODULES.'email_template/email_template.inc.php');
                    #$email = new email_template;
                    #$email->send('host_edit_user', $this->account['id'], $rs->fields['id'], '', '');
                break;

            # inactive
            case 'inactive':
                $result = $this->p_inactive();
            break;

            # edit
            case 'edit':
                if ($this->service['active'] == 1  )
                    $result = $this->p_active();
                else
                    $result = $this->p_inactive();
            break;

            # delete
            case 'delete':
                $result = $this->p_delete();
            break;
        }


        # update service record
        if(@$result != false)
        {
            # update
            $sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
                        queue        =  ' . $db->qstr( 'none' ) . ',
                        date_last    =  ' . $db->qstr( time() ) . '
                        WHERE
                        id           =  ' . $db->qstr( $rs->fields['id'] ) . ' AND
                        site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $upd = $db->Execute($sql);

        } else {
            # error log
            $C_debug->error($this->name.'php', $this->service['queue'], @$result);
        }
    }
}
?>