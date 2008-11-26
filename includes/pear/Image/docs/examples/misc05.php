<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate a "semi-real" graph
 * 
 * Other: 
 * None specific
 * 
 * $Id: misc05.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));

$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(9);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::vertical(
            Image_Graph::factory('title', array('Weather Conditions by Month', 12)),
            Image_Graph::factory('title', array('Location: Århus, Denmark', 8)),
            80
        ),
        Image_Graph::vertical(
            $Plotarea_Weather = Image_Graph::factory('plotarea'),
            $Legend_Weather = Image_Graph::factory('legend'),
            85
        ),
    9)
);
$Legend_Weather->setPlotarea($Plotarea_Weather);

$GridY_Weather =& $Plotarea_Weather->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
$GridY_Weather->setLineColor('gray@0.1');

$Dataset_TempAvg =& Image_Graph::factory('dataset');
$Dataset_TempAvg->addPoint('Jan', 0.2);
$Dataset_TempAvg->addPoint('Feb', 0.1);
$Dataset_TempAvg->addPoint('Mar', 2.3);
$Dataset_TempAvg->addPoint('Apr', 5.8);
$Dataset_TempAvg->addPoint('May', 10.8);
$Dataset_TempAvg->addPoint('Jun', 14.1);
$Dataset_TempAvg->addPoint('Jul', 16.2);
$Dataset_TempAvg->addPoint('Aug', 15.9);
$Dataset_TempAvg->addPoint('Sep', 12.1);
$Dataset_TempAvg->addPoint('Oct', 8.7);
$Dataset_TempAvg->addPoint('Nov', 4.4);
$Dataset_TempAvg->addPoint('Dec', 1.8);
$Plot_TempAvg =& $Plotarea_Weather->addNew('smooth_line', array(&$Dataset_TempAvg));
$Plot_TempAvg->setLineColor('blue');
$Plot_TempAvg->setTitle('Average temperature');

$Dataset_TempMin =& Image_Graph::factory('dataset');
$Dataset_TempMin->addPoint('Jan', -2.7);
$Dataset_TempMin->addPoint('Feb', -2.8);
$Dataset_TempMin->addPoint('Mar', -0.9);
$Dataset_TempMin->addPoint('Apr', 1.2);
$Dataset_TempMin->addPoint('May', 5.5);
$Dataset_TempMin->addPoint('Jun', 9.2);
$Dataset_TempMin->addPoint('Jul', 11.3);
$Dataset_TempMin->addPoint('Aug', 11.1);
$Dataset_TempMin->addPoint('Sep', 7.8);
$Dataset_TempMin->addPoint('Oct', 5.0);
$Dataset_TempMin->addPoint('Nov', 1.5);
$Dataset_TempMin->addPoint('Dec', -0.9);
$Plot_TempMin =& $Plotarea_Weather->addNew('smooth_line', array(&$Dataset_TempMin));
$Plot_TempMin->setLineColor('teal');
$Plot_TempMin->setTitle('Minimum temperature');

$Dataset_TempMax =& Image_Graph::factory('dataset');
$Dataset_TempMax->addPoint('Jan', 2.4);
$Dataset_TempMax->addPoint('Feb', 2.5);
$Dataset_TempMax->addPoint('Mar', 5.4);
$Dataset_TempMax->addPoint('Apr', 10.5);
$Dataset_TempMax->addPoint('May', 15.8);
$Dataset_TempMax->addPoint('Jun', 18.9);
$Dataset_TempMax->addPoint('Jul', 21.2);
$Dataset_TempMax->addPoint('Aug', 20.8);
$Dataset_TempMax->addPoint('Sep', 16.3);
$Dataset_TempMax->addPoint('Oct', 11.8);
$Dataset_TempMax->addPoint('Nov', 6.9);
$Dataset_TempMax->addPoint('Dec', 4.1);
$Plot_TempMax =& $Plotarea_Weather->addNew('smooth_line', array(&$Dataset_TempMax));
$Plot_TempMax->setLineColor('red');
$Plot_TempMax->setTitle('Maximum temperature');

$Marker_AverageSpan =& $Plotarea_Weather->addNew('Image_Graph_Axis_Marker_Area', IMAGE_GRAPH_AXIS_Y);
$Marker_AverageSpan->setFillColor('green@0.2');
$Marker_AverageSpan->setLowerBound(3.8);
$Marker_AverageSpan->setUpperBound(11.4);

$Marker_Average =& $Plotarea_Weather->addNew('Image_Graph_Axis_Marker_Line', IMAGE_GRAPH_AXIS_Y);
$Marker_Average->setLineColor('blue@0.4');
$Marker_Average->setValue(7.7);

$Dataset_Rainfall =& Image_Graph::factory('dataset');
$Dataset_Rainfall->addPoint('Jan', 60);
$Dataset_Rainfall->addPoint('Feb', 41);
$Dataset_Rainfall->addPoint('Mar', 48);
$Dataset_Rainfall->addPoint('Apr', 42);
$Dataset_Rainfall->addPoint('May', 50);
$Dataset_Rainfall->addPoint('Jun', 55);
$Dataset_Rainfall->addPoint('Jul', 67);
$Dataset_Rainfall->addPoint('Aug', 65);
$Dataset_Rainfall->addPoint('Sep', 72);
$Dataset_Rainfall->addPoint('Oct', 77);
$Dataset_Rainfall->addPoint('Nov', 80);
$Dataset_Rainfall->addPoint('Dec', 68);
$Plot_Rainfall =& $Plotarea_Weather->addNew('bar', array(&$Dataset_Rainfall), IMAGE_GRAPH_AXIS_Y_SECONDARY);
$Plot_Rainfall->setLineColor('gray');
$Plot_Rainfall->setFillColor('blue@0.1');
$Plot_Rainfall->setTitle('Average rainfall');
    
$DataPreprocessor_MM =& Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%d mm');
$DataPreprocessor_DegC =& Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%d C');

$Marker_Rainfall =& $Plot_Rainfall->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);    
$Marker_Rainfall->setDataPreprocessor($DataPreprocessor_MM);
$PointingMarker_Rainfall =& $Plot_Rainfall->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker_Rainfall));
$Plot_Rainfall->setMarker($PointingMarker_Rainfall);      

$AxisX_Weather =& $Plotarea_Weather->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX_Weather->setAxisIntersection('min');

$AxisY_Weather =& $Plotarea_Weather->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY_Weather->showLabel(IMAGE_GRAPH_LABEL_ZERO);
$AxisY_Weather->setDataPreprocessor($DataPreprocessor_DegC);
$AxisY_Weather->setTitle('Temperature', 'vertical');

$AxisYsecondary_Weather =& $Plotarea_Weather->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
$AxisYsecondary_Weather->setDataPreprocessor($DataPreprocessor_MM);
$AxisYsecondary_Weather->setTitle('Rainfall', 'vertical2');
            
// output the graph
$Graph->done();
?>