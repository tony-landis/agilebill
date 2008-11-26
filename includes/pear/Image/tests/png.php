<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing canvas fundamental canvas functionality.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Canvas
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: png.php,v 1.2 2005/08/03 21:17:48 nosey Exp $
 * @link       http://pear.php.net/pepr/pepr-proposal-show.php?id=212
 */
  
// SPECIFY HERE WHERE A TRUETYPE FONT CAN BE FOUND
$testFont = 'c:/windows/fonts/Arial.ttf';

require_once 'Image/Canvas.php';

$canvas =& Image_Canvas::factory('png', array('width' => 600, 'height' => 600));

require_once './canvas_body.php';

?>