<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing the setting of the intersection of
 * a x-axis on a secondary y-axis, and the intersection of a secondary-y-
 * axis on the x-axis.
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
 * @version    CVS: $Id: intersection_secondary_axis.php,v 1.5 2005/08/03 21:21:58 nosey Exp $
 * @link       http://pear.php.net/package/Image_Graph
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(800, 600));
// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Testing Secondary Axis Intersection', 10)),               
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(3, 3)),           
        5            
    )
);

$DS[0] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 2, '2' => 0)));
$DS[1] =& Image_Graph::factory('dataset', array(array('0' => -1, '1' => 2, '2' => 1)));
$DS[2] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 3, '2' => 2)));
$DS2[0] =& Image_Graph::factory('dataset', array(array('0' => -1, '1' => 2, '2' => 1)));
$DS2[1] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 3, '2' => 2)));
$DS2[2] =& Image_Graph::factory('dataset', array(array('0' => 1, '1' => 2, '2' => 1)));

for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        if (isset($DS[$col])) {
            $Plotarea =& $Matrix->getEntry($row, $col);
            $AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
            $AxisY->setLineColor('silver');
            
            $AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
            $AxisX->setAxisIntersection(($row < 1 ? 0 : 1), IMAGE_GRAPH_AXIS_Y_SECONDARY);
            $AxisX->setTitle("Intersect at\ny2=" . ($row < 1 ? '0' : '1'));
            
            $Plot =& $Plotarea->addNew('line', $DS2[$col]);
            $Plot->setLineColor('red@0.1');
            $Plot2 =& $Plotarea->addNew('line', $DS[$col], IMAGE_GRAPH_AXIS_Y_SECONDARY);
            $Plot2->setLineColor('green');
            $Plotarea->setBackgroundColor('blue@0.2');

            $AxisYsec =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);                
            if ($row > 1) {
                $AxisYsec->setAxisIntersection(1);
                $AxisYsec->setTitle("Intersect\nat x=1");
            } else {
                $AxisYsec->setTitle("Intersect\nat x=max");
            }
        }
    }
}

$Graph->done();
?>