<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show all plot types (horizontal)
 * 
 * Other: 
 * Matrix layout, Axis customization
 * 
 * $Id: plot_all_horizontal.php,v 1.2 2005/09/08 19:02:17 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';    

$Graph =& Image_Graph::factory('graph', array(800, 800));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Arial');
// set the font size to 11 pixels
$Font->setSize(6);

$Graph->setFont($Font);

$Matrix =& $Graph->addNew('Image_Graph_Layout_Matrix', array(5, 4, false));

for ($i = 0; $i < 5; $i++) {
    for ($j = 0; $j < 4; $j++) {
        $Matrix->setEntry(
            $i, $j,             
            Image_Graph::factory(
                'plotarea', 
                array(
                    'Image_Graph_Axis_Category',
                    'Image_Graph_Axis',
                    'horizontal'
                )
            )
        );
    }
}
    
$Dataset =& Image_Graph::factory('random', array(10, 2, 15, false));
$Dataset2 =& Image_Graph::factory('random', array(10, 2, 15, false));
$Dataset3 =& Image_Graph::factory('random', array(10, 2, 15, false));

$Plotarea =& $Matrix->getEntry(0, 0);
$Plot =& $Plotarea->addNew('line', array(&$Dataset));
$Plot->setLineColor('red');
$Plotarea->addNew('title', array('Image_Graph_Plot_Line', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(0, 1);
$Plot =& $Plotarea->addNew('area', array(&$Dataset));
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');
$Plotarea->addNew('title', array('Image_Graph_Plot_Area', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(0, 2);
$Plot =& $Plotarea->addNew('bar', array(&$Dataset));
$Plot->setLineColor('gray');
$Plot->setFillColor('green@0.2');
$Plot->setSpacing(2);
$Plotarea->setAxisPadding(1, 'left');
$Plotarea->addNew('title', array('Image_Graph_Plot_Bar', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');
  
$Plotarea =& $Matrix->getEntry(0, 3);
$Plot =& $Plotarea->addNew('smooth_line', array(&$Dataset));
$Plot->setLineColor('orange');
$Plotarea->addNew('title', array('Image_Graph_Plot_Smoothed_Line', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(1, 0);
$Plot =& $Plotarea->addNew('smooth_area', array(&$Dataset));
$Plot->setLineColor('purple@0.4');
$Plot->setFillColor('purple@0.2');
$Plotarea->addNew('title', array('Image_Graph_Plot_Smoothed_Area', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(1, 1);
$Plot =& $Plotarea->addNew('pie', array(&$Dataset));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red@0.2');
$Fill->addColor('blue@0.2');
$Fill->addColor('green@0.2');
$Fill->addColor('yellow@0.2');
$Fill->addColor('orange@0.2');
$Fill->addColor('purple@0.2');
$Plot->setFillStyle($Fill);
$Plot->setLineColor('gray');
$Plotarea->hideAxis();
$Plot->explode(10, 1);
$Plotarea->addNew('title', array('Image_Graph_Plot_Pie', array('size' => 7)));

$Plotarea =& $Matrix->getEntry(1, 2);
$Plot =& $Plotarea->addNew('step', array(&$Dataset));
$Plot->setLineColor('yellow@0.5');
$Plot->setFillColor('yellow@0.3');
$Plotarea->addNew('title', array('Image_Graph_Plot_Step', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(1, 3);
$Plot =& $Plotarea->addNew('impulse', array(&$Dataset));
$Plot->setLineColor('blue');  
$Plotarea->addNew('title', array('Image_Graph_Plot_Impulse', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');
  
    $Plotarea =& $Matrix->getEntry(2, 0);
    $Plot =& $Plotarea->addNew('scatter', array(&$Dataset));
$Marker =& $Plot->addNew('Image_Graph_Marker_Circle');
$Marker->setSize(4);
$Marker->setLineColor('green@0.4');
$Marker->setFillColor('green@0.2');
$Plot->setMarker($Marker);
$Plotarea->addNew('title', array('Image_Graph_Plot_Dot', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');
  
$Plotarea =& $Graph->addNew('Image_Graph_Plotarea_Radar');
$Matrix->setEntry(2, 1, $Plotarea);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Radar', array(&$Dataset));
$Plot->setLineColor('orange@0.4');
$Plot->setFillColor('orange@0.2');
$Plotarea->addNew('title', array('Image_Graph_Plot_Radar', array('size' => 7)));

$Dataset1 =& Image_Graph::factory('dataset');
$base = mktime(0, 0, 0, 11, 1, 2004);           
$open = rand(20, 100);
//for ($i = 0; $i < 61; $i++) {
for ($i = 0; $i < 10; $i++) {
    $span = rand(-25, 25);
    $close = ($open + $span < 0 ? $open - $span : $open + $span);
    $min = max(1, min($close, $open) - rand(1, 20));        
    $max = max($close, $open) + rand(1, 20);
    $date = $base + $i * 86400;                
    $Dataset1->addPoint(date('d-M', $date), array('min' => $min, 'open' => $open, 'close' => $close, 'max' => $max));
    $open = $close;
}
$Plotarea =& $Matrix->getEntry(2, 2);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_CandleStick', array(&$Dataset1));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red@0.4', 'red');
$Fill->addColor('green@0.4', 'green');
$Plot->setFillStyle($Fill);
$Axis =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);   
$Axis->setLabelInterval(3);
$Plotarea->addNew('title', array('Image_Graph_Plot_CandleStick', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(2, 3);
$Dataset1 =& Image_Graph::factory('dataset');
for ($i = 0; $i < 40; $i++) {
    $v1 = rand(5, 15);
    $v2 = $v1 + rand(10, 20);
    $Dataset1->addPoint($i, array('low' => $v1, 'high' => $v2));
}
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Band', array($Dataset1));
// set a line color
$Plot->setLineColor('gray');
$Plot->setFillColor('teal@0.2');     
$Axis =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);   
$Axis->setLabelInterval(5);
$Plotarea->addNew('title', array('Image_Graph_Plot_Band', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(3, 0);
$Dataset1 =& Image_Graph::factory('dataset');
for ($i = 0; $i < 4; $i++) {
    $data = array();
    $min = rand(1, 10);
    $max = rand(15, 30);
    for ($j = 0; $j < 20; $j++) {
        $data[] = rand($min, $max);
    }
    $Dataset1->addPoint($i, $data);
}
$Plot =& $Plotarea->addNew('Image_Graph_Plot_BoxWhisker', array($Dataset1));
$Plot->setWhiskerSize(3);    
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red', 'min');
$Fill->addColor('green', 'max');
$Fill->addColor('orange@0.2', 'quartile1');
$Fill->addColor('blue@0.2', 'median');
$Fill->addColor('orange@0.2', 'quartile3');
$Fill->addColor('yellow@0.1', 'box');
$Plot->setFillStyle($Fill);
$Line =& Image_Graph::factory('Image_Graph_Line_Solid', 'black@0.6');
$Line->setThickness(1);
$Plot->setLineStyle($Line);
$Plotarea->addNew('title', array('Image_Graph_Plot_BoxWhisker', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Graph->addNew('Image_Graph_Plotarea_Radar');
$Matrix->setEntry(3, 1, $Plotarea);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Radar', array(&$Dataset));
$Plot->setLineColor('red@0.4');
$Plot->setFillColor('red@0.2');
$Plotarea->addNew('title', array('Image_Graph_Plot_Smoothed_Radar', array('size' => 7)));
          
$Plotarea =& $Matrix->getEntry(4, 0);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Bar', array(array(&$Dataset, &$Dataset2)));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red@0.2');
$Fill->addColor('blue@0.2');
$Plot->setFillStyle($Fill);
$Plot->setSpacing(2);
$Plotarea->setAxisPadding(2, 'left');
$Plotarea->addNew('title', array('Image_Graph_Plot_Bar (normal)', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(4, 1);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Area', array(array(&$Dataset, &$Dataset2, &$Dataset3), 'stacked'));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('khaki@0.2');
$Fill->addColor('indianred@0.2');
$Fill->addColor('gold@0.2');
$Plot->setFillStyle($Fill);
$Plotarea->addNew('title', array('Image_Graph_Plot_Area (stacked)', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');
  
    $Plotarea =& $Matrix->getEntry(4, 2);
    $Plot =& $Plotarea->addNew('Image_Graph_Plot_Bar', array(array(&$Dataset, &$Dataset2), 'stacked'));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('maroon@0.5');
$Fill->addColor('peru@0.5');
$Plot->setFillStyle($Fill);
$Plot->setSpacing(2);
$Plotarea->setAxisPadding(1, 'left');
$Plotarea->addNew('title', array('Image_Graph_Plot_Bar (stacked)', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');

$Plotarea =& $Matrix->getEntry(4, 3);
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Step', array(array(&$Dataset, &$Dataset2, &$Dataset3), 'stacked100pct'));
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('orange@0.2');
$Fill->addColor('yellow@0.2');
$Fill->addColor('lightgrey@0.2');
$Plot->setFillStyle($Fill);
$Plotarea->setAxisPadding(-1, 'left');    
$Plotarea->addNew('title', array('Image_Graph_Plot_Step (stacked 100%)', array('size' => 7)));
$Plotarea->setAxisPadding(10, 'top');
                
$Graph->done();
?>