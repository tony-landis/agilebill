<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate "semi-real" graph
 * 
 * Other: 
 * One legend for many plotareas, Plotarea customizations, Fillarray usage,
 * Datapreprocessor, Axis customizations 
 * 
 * $Id: misc01.php,v 1.6 2005/10/05 20:51:18 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('Image_Graph', array(600, 400));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Dataset_SmoothedLine =& Image_Graph::factory('dataset');    
$Dataset_SmoothedLine->addPoint('DK', 6);
$Dataset_SmoothedLine->addPoint('UK', 8);
$Dataset_SmoothedLine->addPoint('PO', 2);
$Dataset_SmoothedLine->addPoint('NL', 4);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('A Sample Demonstrating Many-a-thing', 15)),
        Image_Graph::vertical(
            Image_Graph::horizontal(
                $Plotarea_BarAndLine = Image_Graph::factory('plotarea'),
                Image_Graph::vertical(
                    $Plotarea_SmoothedLine = Image_Graph::factory('plotarea'),
                	$Plotarea_Radar = Image_Graph::factory('Image_Graph_Plotarea_Radar')
                ), 
                65
            ),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        9
    )
);

$Legend->setPlotarea($Plotarea_BarAndLine);
$Legend->setPlotarea($Plotarea_SmoothedLine);
$Legend->setPlotarea($Plotarea_Radar);

//    $Plotarea_SmoothedLine->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'lightred', 'brown')));
$Plotarea_SmoothedLine->setBorderColor('black');
$Plotarea_SmoothedLine->showShadow();
$Plotarea_SmoothedLine->setPadding(15);    
$Plotarea_SmoothedLine->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'brown')));

// create a Y grid
$Grid_Radar =& $Plotarea_Radar->addNew('line_grid', IMAGE_GRAPH_AXIS_Y); 
$Grid_Radar->setLineColor('lightgrey');

// create a Y grid
$Grid_BarAndLine =& $Plotarea_BarAndLine->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y); 
// that is light gray in color
$Grid_BarAndLine->setFillColor('blue@0.3');

// create a Y grid
$Grid_SmoothedLine =& $Plotarea_SmoothedLine->addNew('line_grid', false, IMAGE_GRAPH_AXIS_Y);
$Grid_SmoothedLine->setLineColor('gray');  
$Grid_SmoothedLine =& $Plotarea_SmoothedLine->addNew('line_grid', false, IMAGE_GRAPH_AXIS_X);
$Grid_SmoothedLine->setLineColor('gray');  
	
// create the 1st dataset
//$Dataset_SmoothedLine =& new Image_RandomDataset(4, 2, 15, true);
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot_SmoothedLine =& $Plotarea_SmoothedLine->addNew('Image_Graph_Plot_Smoothed_Line', $Dataset_SmoothedLine);
$Plot_SmoothedLine->setLineColor('orange');

// create a 3rd dataset
$Dataset_BarChart =& Image_Graph::factory('random', array(7, 10, 120, false));
// create the 3rd plot as line chart using the 2nd dataset
$Plot_BarChart =& $Plotarea_BarAndLine->addNew('bar', array(&$Dataset_BarChart));
// set the fill style of the barchart to the almost transparent BlueAlpha
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$Plot_BarChart->setFillStyle($FillArray);
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'red'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL, 'white', 'blue'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_HORIZONTAL_MIRRORED, 'orange', 'white'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, 'green', 'white'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'purple'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_DIAGONALLY_TL_BR, 'white', 'brown'));    
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_DIAGONALLY_BL_TR, 'white', 'black'));    

// create the 2nd dataset
$Dataset_LineMarker =& Image_Graph::factory('random', array(7, 20, 100, false));
// create the 2nd plot as line chart using the 2nd dataset
$Plot_LineMarker =& $Plotarea_BarAndLine->addNew('line', array(&$Dataset_LineMarker));

$LineStyle =& Image_Graph::factory('Image_Graph_Line_Dotted', array('black', 'transparent')); 
$Plot_LineMarker->setLineStyle($LineStyle);

$Marker =& Image_Graph::factory('Image_Graph_Marker_Array');
$CrossMarker =& Image_Graph::factory('Image_Graph_Marker_Cross');
$PlusMarker =& Image_Graph::factory('Image_Graph_Marker_Plus');
$StarMarker =& Image_Graph::factory('Image_Graph_Marker_Star');
$Marker->add($CrossMarker);
$Marker->add($PlusMarker);
$Marker->add($StarMarker);
$Plot_LineMarker->setMarker($Marker);

$CrossMarker->setLineColor('black');
$CrossMarker->setFillColor('green');
$PlusMarker->setLineColor('black');
$PlusMarker->setFillColor('red');
$StarMarker->setLineColor('black@0.4');
$StarMarker->setFillColor('yellow');

// Show arrow heads on the axis
$AxisX =& $Plotarea_BarAndLine->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisY =& $Plotarea_BarAndLine->getAxis(IMAGE_GRAPH_AXIS_Y);

$AxisY->setLabelInterval(5, 2);
$AxisY->setTickOptions(-1, 1, 2);

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
$AxisX->setFontAngle('vertical');
$AxisX->setTitle('Very obvious', array('angle' => 0, 'size' => 10));

$Plotarea_BarAndLine->setAxisPadding(array('left' => 20, 'right' => 20));
           
$Plot_SmoothedLine->setTitle('Oil');
$Plot_LineMarker->setTitle('Clearly not a good day');
$Plot_BarChart->setTitle('Buy or Sell');
		 
// create the dataset
$Dataset_Radar1 =& Image_Graph::factory('random', array(8, 1, 5));
$Dataset_Radar2 =& Image_Graph::factory('random', array(8, 1, 5));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot_Radar1 =& $Plotarea_Radar->addNew('Image_Graph_Plot_Radar', array(&$Dataset_Radar1));
$Plot_Radar1->setTitle('Obscurity');
$Plot_Radar2 =& $Plotarea_Radar->addNew('Image_Graph_Plot_Radar', array(&$Dataset_Radar2));
$Plot_Radar2->setTitle('Clarity');
	
//$Dataset_Radar1->setMaximumY(7);

$DataPreprocessor =& Image_Graph::factory('Image_Graph_DataPreprocessor_Array',
    array( 
		array(
            1 => 'Irrelevance', 
            2 => 'Regular',
            3 => 'Partly',
            4 => 'Relevance', 
            5 => 'Something', 
            6 => 'Everything', 
            7 => 'Nothing', 
            8 => 'Irregular'
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