<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate SWF canvas
 * 
 * Other: 
 * None specific
 * 
 * $Id: driver_swf02.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('swf', array('width' => 600, 'height' => 400));


// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas); 

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 15 pixels
$Font->setSize(15);
// add a title using the created font    

$Dataset_SmoothedLine =& Image_Graph::factory('dataset');    
$Dataset_SmoothedLine->addPoint('DK', 6);
$Dataset_SmoothedLine->addPoint('UK', 8);
$Dataset_SmoothedLine->addPoint('PO', 2);
$Dataset_SmoothedLine->addPoint('NL', 4);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Layout and Legend Sample', &$Font)),
        Image_Graph::horizontal(
            Image_Graph::vertical(
                Image_Graph::horizontal(
                    Image_Graph::factory('title', array('This is the Y Axis', &$_Image_Graph_verticalFont)),
                    Image_Graph::vertical(
                        $Plotarea_BarAndLine = Image_Graph::factory('plotarea'),
                        Image_Graph::factory('title', array('This is the X Axis', &$_Image_Graph_font)),
                        95
                    ),
                    5
                ),
                $Legend_BarAndLine = Image_Graph::factory('legend'),
                95
            ),                 
            Image_Graph::vertical(
                Image_Graph::horizontal(
                    $Plotarea_SmoothedLine = Image_Graph::factory('plotarea'),
                    $Legend_SmoothedLine = Image_Graph::factory('legend'),
                    80
                ),                    	
            	$Plotarea_Radar = Image_Graph::factory('Image_Graph_Plotarea_Radar')
            ), 
            65
        ),
        9
    )
);

$Legend_BarAndLine->setPlotarea($Plotarea_BarAndLine);
$Legend_SmoothedLine->setPlotarea($Plotarea_SmoothedLine);
              	
// create a Y grid
$Grid_Radar =& $Plotarea_Radar->addNew('line_grid', IMAGE_GRAPH_AXIS_Y); 
$Grid_Radar->setLineColor('lightgrey');

// create a Y grid
$Grid_BarAndLine =& $Plotarea_BarAndLine->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y); 
// that is light gray in color
$Grid_BarAndLine->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')));

// create a Y grid
$Grid_SmoothedLine =& $Plotarea_SmoothedLine->addNew('line_grid', IMAGE_GRAPH_AXIS_Y);
$Grid_SmoothedLine->setLineColor('lightgrey');	
	
// create the 1st dataset
//$Dataset_SmoothedLine =& new Image_RandomDataset(4, 2, 15, true);
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot_SmoothedLine =& $Plotarea_SmoothedLine->addNew('Image_Graph_Plot_Smoothed_Line', $Dataset_SmoothedLine);
$Plot_SmoothedLine->setLineColor('orange');

// create a 3rd dataset
$Dataset_BarChart =& Image_Graph::factory('random', array(8, 10, 120, false));
// create the 3rd plot as line chart using the 2nd dataset
$Plot_BarChart =& $Plotarea_BarAndLine->addNew('bar', $Dataset_BarChart);
// set the fill style of the barchart to the almost transparent BlueAlpha
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$Plot_BarChart->setFillStyle($FillArray);
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL, 'white', 'red'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'blue'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'yellow'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL, 'white', 'green'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'purple'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'orange'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'black'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'brown'));        

// create the 2nd dataset
$Dataset_LineMarker =& Image_Graph::factory('random', array(8, 20, 100, false));
// create the 2nd plot as line chart using the 2nd dataset
$Plot_LineMarker =& $Plotarea_BarAndLine->addNew('line', $Dataset_LineMarker);

$Marker =& Image_Graph::factory('Image_Graph_Marker_Array');
$CrossMarker =& Image_Graph::factory('Image_Graph_Marker_Cross');
$CircleMarker =& Image_Graph::factory('Image_Graph_Marker_Circle');
$Marker->add($CrossMarker);
$Marker->add($CircleMarker);
$Plot_LineMarker->setMarker($Marker);
// Create a red line
$CrossMarker->setLineColor('red');
// Create a blue line
$CircleMarker->setLineColor('blue');
			
// Show arrow heads on the axis
$AxisX =& $Plotarea_BarAndLine->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisY =& $Plotarea_BarAndLine->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisX->showArrow();
$AxisY->showArrow();

// create an data label array for formatting label data based on an array
$ArrayData =& Image_Graph::factory('Image_Graph_DataPreprocessor_Array',
    array( 
        array(
            1=>'A Point', 
            2=>'Another', 
            6=>'Yet another'
        )
	)
);

// use the data label array on the X axis
$AxisX->setDataPreprocessor($ArrayData);
$AxisFontX =& $Graph->addNew('Image_Graph_Font_Vertical');
$AxisX->setFont($AxisFontX);    

$Legend_BarAndLine->setFillColor('white');
$Legend_SmoothedLine->setFillColor('white');    

$Font2 =& $Graph->addNew('font', 'Verdana');
$Font2->setSize(8);
$Legend_BarAndLine->setFont($Font2);
$Legend_SmoothedLine->setFont($Font2);
   
$Plot_SmoothedLine->setTitle('Oil');
$Plot_LineMarker->setTitle('Data Set 1');
$Plot_BarChart->setTitle('Data Set 2');
		 
// create the dataset
$Dataset_Radar1 =& Image_Graph::factory('random', array(8, 1, 5));
$Dataset_Radar2 =& Image_Graph::factory('random', array(8, 1, 5));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot_Radar1 =& $Plotarea_Radar->addNew('Image_Graph_Plot_Radar', $Dataset_Radar1);
$Plot_Radar2 =& $Plotarea_Radar->addNew('Image_Graph_Plot_Radar', $Dataset_Radar2);
	
//$Dataset_Radar1->setMaximumY(7);

$DataPreprocessor =& Image_Graph::factory('Image_Graph_DataPreprocessor_Array',
    array( 
		array(
            'Irrelevance', 
            'Partly',
            'Regular',
            'Relevance', 
            'Something', 
            'Everything', 
            'Nothing', 
            'Irregular'
        )
    )
);

$AxisX =& $Plotarea_Radar->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setDataPreprocessor($DataPreprocessor);	

//$Plot_Radar1->setMarker(new Image_CrossMarker(), 'Marker');
//$Plot_Radar1->setLineStyle($YELLOW);

$AverageMarker =& Image_Graph::factory('Image_Graph_Marker_Average');
$Plot_SmoothedLine->setMarker($AverageMarker);
$AverageMarker->setLineColor('purple');

// set a standard fill style
$Plot_Radar1->setFillColor('yellow@0.2');            
$Plot_Radar2->setFillColor('green@0.2');            
// output the Graph
    
$Graph->done();
?>