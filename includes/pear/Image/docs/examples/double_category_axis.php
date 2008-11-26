<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * PDF canvas
 * 
 * Other: 
 * Datapreprocessor, Axis markers
 * 
 * $Id: double_category_axis.php,v 1.2 2005/10/05 20:51:18 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300));
// add a TrueType font
$Font =& $Graph->addNew('ttf_font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Testing Category Axis', 10)),               
        $Plotarea = Image_Graph::factory('plotarea', array('Image_Graph_Axis_Category', 'Image_Graph_Axis_Category')),           
        5            
    )
);

//$DS =& Image_Graph::factory('dataset');
//$DS->addPoint('Apache', 'Open Source');
//$DS->addPoint('BSD', 'Open Source');
//$DS->addPoint('Linux', 'Open Source');
//$DS->addPoint('Microsoft', 'Proprietary');
//$DS->addPoint('Micro', 'Proprietary');
//$DS->addPoint('Minisoft', 'Proprie');
//$DS->addPoint('Millisoft', 'Prop');
//
//$DS2 =& Image_Graph::factory('dataset');
//$DS->addPoint('Apache', 'Open Source');
//$DS->addPoint('BSD', 'Open Source');
//$DS->addPoint('Linux', 'Open Source');
//$DS->addPoint('Microsoft', 'Proprietary');
//$DS->addPoint('Micro', 'Proprietary');
//$DS->addPoint('Minisoft', 'Proprie');
//$DS->addPoint('Miniority', 'Proprias');
//
//$Plot =& $Plotarea->addNew('scatter', $DS);
//$Marker =& Image_Graph::factory('Image_Graph_Marker_Plus');
//$Marker->setFillColor('red');
//$Marker->setLineColor('black');
//$Plot->setMarker($Marker);
//
//$Plot2 =& $Plotarea->addNew('scatter', $DS2);
//$Marker =& Image_Graph::factory('Image_Graph_Marker_Cross');
//$Marker->setFillColor('blue');
//$Marker->setLineColor('black');
//$Plot2->setMarker($Marker);
//
//$Graph->done();

$DS =& Image_Graph::factory('dataset');
$DS->addPoint('Germany', 'England');
$DS->addPoint('Denmark', 'France');
$DS->addPoint('Sweden', 'Denmark');
$DS->addPoint('England', 'France');
$DS->addPoint('Norway', 'Finland');
$DS->addPoint('Denmark', 'Finland');
$DS->addPoint('Iceland', 'Germany');
$DS->addPoint('Norway', 'France');

$DS2 =& Image_Graph::factory('dataset');
$DS2->addPoint('Sweden', 'France');
$DS2->addPoint('Austria', 'Germany');
$DS2->addPoint('Norway', 'Holland');
$DS2->addPoint('Denmark', 'Germany');
$DS2->addPoint('Sweden', 'Holland');
$DS2->addPoint('Iceland', 'Denmark');

$Plot =& $Plotarea->addNew('scatter', $DS);
$Marker =& Image_Graph::factory('Image_Graph_Marker_Cross');
$Marker->setFillColor('blue');
$Marker->setLineColor('black');
$Plot->setMarker($Marker);

$Plot2 =& $Plotarea->addNew('scatter', $DS2);
$Marker2 =& Image_Graph::factory('Image_Graph_Marker_Plus');
$Marker2->setFillColor('yellow');
$Marker2->setLineColor('black');
$Plot2->setMarker($Marker2);

$Graph->done();
?>