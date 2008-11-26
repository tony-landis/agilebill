<?php
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Aidan Lister <aidan@php.net>                                |
// |          Stephan Schmidt <schst@php.net>                             |
// +----------------------------------------------------------------------+
//
// $Id: strripos.php,v 1.1 2005/07/23 05:56:03 Tony Exp $


/**
 * Replace strripos()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.strripos
 * @author      Aidan Lister <aidan@php.net>
 * @author      Stephan Schmidt <schst@php.net>
 * @version     $Revision: 1.1 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('strripos')) {
    function strripos($haystack, $needle, $offset = null)
    {
        if (!is_scalar($haystack)) {
            user_error('strripos() expects parameter 1 to be scalar, ' .
                gettype($haystack) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_scalar($needle)) {
            user_error('strripos() expects parameter 2 to be scalar, ' .
                gettype($needle) . ' given', E_USER_WARNING);
            return false;
        }

        if (!is_int($offset) && !is_bool($offset) && !is_null($offset)) {
            user_error('strripos() expects parameter 3 to be long, ' .
                gettype($offset) . ' given', E_USER_WARNING);
            return false;
        }

        // Manipulate the string if there is an offset
        $fix = 0;
        if (!is_null($offset)) {
            // If the offset is larger than the haystack, return
            if (abs($offset) >= strlen($haystack)) {
                return false;
            }

            // Check whether offset is negative or positive
            if ($offset > 0) {
                $haystack = substr($haystack, $offset, strlen($haystack) - $offset);
                // We need to add this to the position of the needle
                $fix = $offset;
            } else {
                $haystack = substr($haystack, 0, strlen($haystack) + $offset);
            }
        }

        $segments = explode(strtolower($needle), strtolower($haystack));

        $last_seg = count($segments) - 1;
        $position = strlen($haystack) + $fix - strlen($segments[$last_seg]) - strlen($needle);

        return $position;
    }
}

?>