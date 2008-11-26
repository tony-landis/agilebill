<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show candlestick chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_candlestick.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */


require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);


// create the plotareas
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Candlestick Diagram', 12)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);

// create the dataset
$Dataset =& Image_Graph::factory('dataset');

$base = mktime(0, 0, 0, 11, 1, 2004);           
$open = rand(20, 100);
//for ($i = 0; $i < 61; $i++) {
for ($i = 0; $i < 60; $i++) {
    $span = rand(-25, 25);
    $close = ($open + $span < 0 ? $open - $span : $open + $span);
    $min = max(1, min($close, $open) - rand(1, 20));        
    $max = max($close, $open) + rand(1, 20);
    $date = $base + $i * 86400;                
    $Dataset->addPoint(date('d-M-y', $date), array('min' => $min, 'open' => $open, 'close' => $close, 'max' => $max));
    $open = $close;
}

$Grid =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_X);
$Grid->setLineColor('lightgray@0.1'); 
$Grid =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y); 
$Grid->setLineColor('lightgray@0.1'); 

$Plot =& $Plotarea->addNew('Image_Graph_Plot_CandleStick', array(&$Dataset));    
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red@0.4', 'red');
$Fill->addColor('green@0.4', 'green');
$Plot->setFillStyle($Fill);    
$Plot->setTitle('Image_Graph Daily');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setFontAngle('vertical');
$AxisX->setLabelInterval(5);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setLabelInterval(20);
$AxisY->setTitle('Stock Price', array('size' => 10, 'angle' => 90));

$Legend->setFontSize(10);

// output the Graph
$Graph->done();
?>