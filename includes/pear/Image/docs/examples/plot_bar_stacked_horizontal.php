<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show stacked bar chart
 * 
 * Other: 
 * Data selector
 * Fill style array (no ID) working in sync with legend
 * 
 * $Id: plot_bar_stacked_horizontal.php,v 1.1 2005/09/08 19:02:17 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300)); 
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Stacked Bar Chart Sample', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea', 
                array(
                    'category',
                    'axis',
                    'horizontal'
                )
            ),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);   

$Legend->setPlotarea($Plotarea);        
    
// create the dataset
$Datasets[] =& Image_Graph::factory('random', array(10, 0, 4, false));
$Datasets[] =& Image_Graph::factory('random', array(10, 0, 4, false));
$Datasets[] =& Image_Graph::factory('random', array(10, 0, 4, false));


// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('bar', array($Datasets, 'stacked'));

// set a line color
$Plot->setLineColor('gray');

// create a fill array   
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');

// set a standard fill style
$Plot->setFillStyle($FillArray);

// create a Y data value marker
$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
// and use the marker on the 1st plot
$Plot->setMarker($Marker);	

$Plot->setDataSelector(Image_Graph::factory('Image_Graph_DataSelector_NoZeros'));

// output the Graph
$Graph->done();
?>
