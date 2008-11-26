<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Antialiasing usage
 * 
 * Other: 
 * Setup canvas, Many plotareas with one legend, Setup fillarray (filling one
 * plot with different colors depeding on dataset)
 * 
 * $Id: antialias.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
// include libraries
require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

// create a PNG canvas and enable antialiasing (canvas implementation)
$Canvas =& Image_Canvas::factory('png', array('width' => 600, 'height' => 300, 'antialias' => 'native'));      

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 8 pixels
$Font->setSize(8);

// set the font
$Graph->setFont($Font);

// create the layout
$Graph->add(
    Image_Graph::vertical(
    Image_Graph::factory('title', array('Antialiased Sample Chart', 12)),
        Image_Graph::vertical(
            Image_Graph::horizontal(
                $Plotarea1 = Image_Graph::factory('plotarea'),
                $Plotarea2 = Image_Graph::factory('plotarea')
            ),
            $Legend = Image_Graph::factory('legend'),
            80
        ),
    5
    )
);

// add grids
$Grid =& $Plotarea1->addNew('line_grid', IMAGE_GRAPH_AXIS_Y);
$Grid->setLineColor('silver');
$Grid =& $Plotarea2->addNew('line_grid', IMAGE_GRAPH_AXIS_Y);
$Grid->setLineColor('silver');

// setup legend
$Legend->setPlotarea($Plotarea1);
$Legend->setPlotarea($Plotarea2);

// create the dataset
$Datasets =
    array(
        Image_Graph::factory('random', array(10, 2, 15, true)),
        Image_Graph::factory('random', array(10, 2, 15, true)),
        Image_Graph::factory('random', array(10, 2, 15, true))
    );

// create the plot as stacked area chart using the datasets
$Plot =& $Plotarea1->addNew('Image_Graph_Plot_Area', array($Datasets, 'stacked'));

// set names for datasets (for legend)
$Datasets[0]->setName('Jylland');
$Datasets[1]->setName('Fyn');
$Datasets[2]->setName('Sjælland');

// set line color for plot
$Plot->setLineColor('gray');

// create and populate the fillarray
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');

// set a fill style
$Plot->setFillStyle($FillArray);

// add other plots
$Plot =& $Plotarea2->addNew('line', $Datasets[0]);
$Plot->setLineColor('blue@0.2');
$Plot =& $Plotarea2->addNew('line', $Datasets[1]);
$Plot->setLineColor('yellow@0.2');
$Plot =& $Plotarea2->addNew('line', $Datasets[2]);
$Plot->setLineColor('green@0.2');

// set color
$Plotarea1->setFillColor('silver@0.3');
$Plotarea2->setFillColor('silver@0.3');

// output the Graph
$Graph->done();

?>