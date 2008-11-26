<?php
/* 
 **************************************************************************
 *
 * OpenSRS-PHP
 *
 * Copyright (C) 2000, 2001, 2002, 2003 Colin Viebrock
 *   and easyDNS Technologies Inc.
 *
 **************************************************************************
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.   
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 **************************************************************************
 *
 * vim: set expandtab tabstop=4 shiftwidth=4:
 * $Id: test.php,v 1.1 2004/09/30 09:25:23 Tony Exp $
 *
 **************************************************************************
 */


require_once 'openSRS.php';

$O = new openSRS('test','XCP');

$cmd = array(
        'action'        => 'lookup',
        'object'        => 'domain',
        'attributes'    => array(
                'domain'        => 'domain.com',
                'affiliate_id'  => '12345'
        )
);

echo "<h1>Command</h1>\n";
print_r($cmd);

$result = $O->send_cmd($cmd);

echo "<HR />";
echo "<h1>Result</h1>\n";
print_r($result);

echo "<HR />";
echo "<h1>Log</h1>\n";
$O->showlog();

echo "<HR />";
echo "<h1>OPS XML Log</h1>\n";
$O->_OPS->showlog('xml','raw');

echo "<HR />";
echo "<h1>OPS Raw Log</h1>\n";
$O->_OPS->showlog('raw');
