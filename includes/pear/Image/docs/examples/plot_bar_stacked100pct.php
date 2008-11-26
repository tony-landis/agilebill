<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show stacked bar chart 100%
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_bar_stacked100pct.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */


include_once('Image/Graph.php');
include_once('Image/Graph/Marker/Value.php');
	
class myValueMarker extends Image_Graph_Marker_Value {

  function getDisplayValue($Values) {
  	return "\{$Values[Y]}";
  }

}
	

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300)); 
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Stacked Bar 100% Chart Sample', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);        

for ($j = 0; $j<4; $j++) {
	$DX =& Image_Graph::factory('dataset');
	$Datasets[$j] =& $DX;
	for ($i = 0; $i<10; $i++)
		$DX->addPoint($i, rand(2, 15), $j);
}
	
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('bar', array($Datasets, 'stacked100pct'));

// set a line color
$Plot->setLineColor('gray');

// create a fill array
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');
$FillArray->addColor('red@0.2');
$FillArray->addColor('gray@0.2');

// set a standard fill style
$Plot->setFillStyle($FillArray);

// create a Y data value marker
$Marker =& $Plot->add(new myValueMarker(IMAGE_GRAPH_VALUE_Y));
// and use the marker on the 1st plot
$Plot->setMarker($Marker);	

// output the Graph
$Graph->done();
?>
