<?php 
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate pie & donut plots
 * 
 * Other: 
 * Radial gradient fillings
 * 
 * $Id: gradient_pie.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';
    
// create the graph
$Graph = & Image_Graph::factory('graph', array(400, 300));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);


// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Gradient Filled Donut/Pie Chart', 12)),
        Image_Graph::horizontal(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            60
        ),
        5
    )
);

$Legend->setPlotarea($Plotarea);
$Legend->setAlignment(IMAGE_GRAPH_ALIGN_VERTICAL);

// create the dataset
$Dataset = & Image_Graph::factory('dataset');
$Dataset->addPoint('Beef', rand(1, 10), 'beef');
$Dataset->addPoint('Pork', rand(1, 10), 'pork');
$Dataset->addPoint('Poultry', rand(1, 10), 'poultry');
$Dataset->addPoint('Camels', rand(1, 10), 'camels');
$Dataset->addPoint('Other', rand(1, 10), 'other');

// create the dataset
$Dataset2 = & Image_Graph::factory('dataset');
$Dataset2->addPoint('Beer', rand(1, 10), 'beer');
$Dataset2->addPoint('Wine', rand(1, 10), 'wine');
$Dataset2->addPoint('Alcohol', rand(1, 10), 'alcohol');
$Dataset2->addPoint('Coffee', rand(1, 10), 'coffee');
$Dataset2->addPoint('Milk', rand(1, 10), 'milk');
$Dataset2->addPoint('Water', rand(1, 10), 'water');

// create the plot as pie chart using the dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Pie', array(array(&$Dataset, &$Dataset2)));
$Plotarea->hideAxis();   

// create a Y data value marker
$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_TOTAL);
// fill it with white
$Marker->setFillColor('white');
// and use black border
$Marker->setBorderColor('black');
// and format it using a data preprocessor
$Marker->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));
$Marker->setFontSize(7);

// create a pin-point marker type
$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
// and use the marker on the plot
$Plot->setMarker($PointingMarker);
// format value marker labels as percentage values
$Plot->Radius = 2;

// create a fillstyle for the plot
$FillArray = & Image_Graph::factory('Image_Graph_Fill_Array');
$Plot->setFillStyle($FillArray);
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'green'), 'beef');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'blue'), 'pork');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'yellow'), 'poultry');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'red'), 'camels');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'orange'), 'other');        


$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'dimgray', 'white'), 'beer');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'sandybrown', 'white'), 'wine');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'sienna', 'white'), 'alcohol');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'powderblue', 'white'), 'coffee');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'purple', 'white'), 'milk');
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'thistle', 'white'), 'water');

$Plot->explode(20, 'Beer');

$Plot->setLineColor('lightgrey');

// output the Graph
$Graph->done();
?>
