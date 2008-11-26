<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * "Semi-real" plot
 * 
 * Other: 
 * Gradient filling, Icon markers (and marker array), Datapreprocessor
 * 
 * $Id: misc06.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300));

// add a TrueType font
$Arial =& $Graph->addNew('font', 'Verdana');
// set the font size to 8 pixels
$Arial->setSize(8);
// set default font color to white
$Arial->setColor('white');

// make the entire graph use this font
$Graph->setFont($Arial);
    
// create the graph layout
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('German Car Popularity', 11)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        7
    )
);

// associate the legend with the plotarea
$Legend->setPlotarea($Plotarea);

// make the graph have a gradient filled background    
$Graph->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'green', 'lightblue')));
// and a black border
$Graph->setBorderColor('black');

// create and populate the dataset for 'popularity'
$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Audi', 100);
$Dataset->addPoint('Mercedes', 41);
$Dataset->addPoint('Porsche', 78);
$Dataset->addPoint('BMW', 12);

// create and populate the dataset for 'defects / 1000 units'
$Dataset2 =& Image_Graph::factory('dataset');
$Dataset2->addPoint('Audi', 10);
$Dataset2->addPoint('Mercedes', 17);
$Dataset2->addPoint('Porsche', 12);
$Dataset2->addPoint('BMW', 21);

// add a line grid  
$GridY =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
// make it have a slightly transparent white color
$GridY->setLineColor('white@0.8');

// create the plot as bar chart using the 'popularity' dataset
$Plot =& $Plotarea->addNew('bar', array(&$Dataset));
// set the plot title (for legends)    
$Plot->setTitle('Popularity');

// create a fill array to make the bars have individual fill's
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'orange'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'blue'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'yellow'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'red'));

// make the 'popularity' plot use this fillarray   
$Plot->setFillStyle($FillArray);

// create a marker array and populate it with icon markers
$Marker =& $Graph->addNew('Image_Graph_Marker_Array');
$Marker->addNew('icon_marker', './images/audi.png');
$Marker->addNew('icon_marker', './images/mercedes.png');
$Marker->addNew('icon_marker', './images/porsche.png');
$Marker->addNew('icon_marker', './images/bmw.png');

// make the plot use the marker array    
$Plot->setMarker($Marker);

// create the 2nd plot ('defects / 1000 units') as a line plot and associate
// it with the secondary y-axis (implicitly this creates a y-axis of the class
// Image_Graph_Axis)
$Plot2 =& $Plotarea->addNew('line', array(&$Dataset2), IMAGE_GRAPH_AXIS_Y_SECONDARY);
// set the plot title
$Plot2->setTitle('Defects');    
// and line style
$Plot2->setLineColor('gray@0.8');

// create a value marker to display the actual y-values
$Marker =& $Graph->addNew('value_marker', IMAGE_GRAPH_VALUE_Y);
// and make the line plot use this
$Plot2->setMarker($Marker);
// make the marker print using font-size 7
$Marker->setFontSize(7);
// ... in blue
$Marker->setFontColor('blue');

// get the y-axis
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
// and create a datapreprocessor to make the labels print out as percentage valuexs    
$AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.0f%%'));    
// force a maximum on the y-axis to 105
$AxisY->forceMaximum(105);
// set the axis title and make it display vertically ('vertical' = down->up)
$AxisY->setTitle('Popularity', 'vertical');

// get the secondary y-axis
$AxisYsec =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
// set the axis title and make it display vertically ('vertical2' = up->down)
$AxisYsec->setTitle('Defects / 1000 units', 'vertical2');

// output the Graph
$Graph->done();

?>