<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing axis inversion.
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
 * @package    Image_Graph
 * @subpackage Tests
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: inversion.php,v 1.4 2005/08/03 21:21:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 600));
// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Testing Axis Inversion', 10)),               
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(4, 3)),           
        5            
    )
);

$DS[0] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 2, '2' => 0)));
$DS[1] =& Image_Graph::factory('dataset', array(array('0' => -1, '1' => 2, '2' => 2)));
$DS[2] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 3, '2' => 2)));

for ($row = 0; $row < 4; $row++) {
    for ($col = 0; $col < 3; $col++) {
        if (isset($DS[$col])) {
            $Plotarea =& $Matrix->getEntry($row, $col);
            $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
            $AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);

            if ($row >= 1) {
                $AxisY->setInverted(true);
                $AxisY->setTitle('Inverted Y', 'vertical');
            } else {
                $AxisY->setTitle('Normal Y', 'vertical');
            }
            
            if ($row >= 2) {
                $AxisX->setInverted(true);
                $AxisX->setTitle('Inverted X');
            } else {
                $AxisX->setTitle('Normal X');
            }
            
            if ($row >= 3) {
                $AxisX->setAxisIntersection('max');
                $AxisX->setTitle('Inverted X at Bottom (set X intersection)');
            }
            
            $Plot =& $Plotarea->addNew('line', $DS[$col]);
            $Plot->setLineColor('red');
            $Plotarea->setBackgroundColor('blue@0.2');
        }
    }
}

$Graph->done();
?>