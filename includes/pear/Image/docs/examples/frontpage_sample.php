<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Drivers usage
 * 
 * Other: 
 * Grids, Axis markers, Secondary Y-axis, Datapreprocessor, Multiple plotareas,
 * Axis titles
 * 
 * $Id: frontpage_sample.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

// create a new GD canvas
$Canvas =& Image_Canvas::factory('gd',
    array(
        'width' => 600,
        'height' => 400            
    )
);     

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);

$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(7);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Image_Graph Demonstration', 12)),
        Image_Graph::vertical(
            Image_Graph::vertical(
                $Plotarea_Weather = Image_Graph::factory('plotarea'),
                $Legend_Weather = Image_Graph::factory('legend'),
                85
            ),
            Image_Graph::horizontal(
                Image_Graph::vertical(
                    Image_Graph::vertical(
                        Image_Graph::factory(
                            'title', 
                            array('Demonstration of Mathematical Functions', 10)
                        ),
                        $Plotarea_SinCos = Image_Graph::factory('plotarea', 'axis'),
                        5
                    ),
                    $Legend_SinCos = Image_Graph::factory('legend'),
                    90
                ),
                $Plotarea_Car = Image_Graph::factory('plotarea'),
                50
            ),
            60
        ),
    5)
);
$Legend_Weather->setPlotarea($Plotarea_Weather);
$Legend_Weather->setFontSize(7);

$Legend_SinCos->setPlotarea($Plotarea_SinCos);
$Legend_SinCos->setFontSize(8);

$GridY_Weather =& $Plotarea_Weather->addNew(
    'line_grid', 
    null, 
    IMAGE_GRAPH_AXIS_Y
);
$GridY_Weather->setLineColor('gray@0.1');

$Marker_AverageSpan =& 
    $Plotarea_Weather->addNew(
        'Image_Graph_Axis_Marker_Area', 
        IMAGE_GRAPH_AXIS_Y
    );
$Marker_AverageSpan->setFillColor('green@0.2');
$Marker_AverageSpan->setLowerBound(3.8);
$Marker_AverageSpan->setUpperBound(11.4);

$Marker_Average =& 
    $Plotarea_Weather->addNew(
        'Image_Graph_Axis_Marker_Line', 
        IMAGE_GRAPH_AXIS_Y
    );
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
$Plot_Rainfall =& 
    $Plotarea_Weather->addNew(
        'bar',
        array(&$Dataset_Rainfall),
        IMAGE_GRAPH_AXIS_Y_SECONDARY
    );
$Plot_Rainfall->setLineColor('gray');
$Plot_Rainfall->setFillColor('yellow@0.1');
$Plot_Rainfall->setTitle('Average rainfall');

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
$Plot_TempAvg =& 
    $Plotarea_Weather->addNew(
        'Image_Graph_Plot_Smoothed_Line', 
        array(&$Dataset_TempAvg)
    );
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
$Plot_TempMin =& 
    $Plotarea_Weather->addNew(
        'Image_Graph_Plot_Smoothed_Line', 
        array(&$Dataset_TempMin)
    );
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
$Plot_TempMax =& 
    $Plotarea_Weather->addNew(
        'Image_Graph_Plot_Smoothed_Line', 
        array(&$Dataset_TempMax)
    );
$Plot_TempMax->setLineColor('red');
$Plot_TempMax->setTitle('Maximum temperature');   
    
$DataPreprocessor_MM =& 
    Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%d mm');
$DataPreprocessor_DegC =& 
    Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%d C');

$Marker_Rainfall =& 
    $Plot_Rainfall->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);    
$Marker_Rainfall->setDataPreprocessor($DataPreprocessor_MM);
$Marker_Rainfall->setFontSize(7);
$PointingMarker_Rainfall =& 
    $Plot_Rainfall->addNew(
        'Image_Graph_Marker_Pointing_Angular', 
        array(20, &$Marker_Rainfall)
    );
$Plot_Rainfall->setMarker($PointingMarker_Rainfall);      

$AxisY_Weather =& $Plotarea_Weather->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY_Weather->showLabel(IMAGE_GRAPH_LABEL_ZERO);
$AxisY_Weather->setDataPreprocessor($DataPreprocessor_DegC);
$AxisY_Weather->setTitle(
    'Temperature', 
    array('vertical' => true, 'angle' => 90)
);

$AxisYsecondary_Weather =& 
    $Plotarea_Weather->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
$AxisYsecondary_Weather->setDataPreprocessor($DataPreprocessor_MM);
$AxisYsecondary_Weather->setTitle(
    'Rainfall', 
    array('vertical' => true, 'angle' => 270)
);
    
$GridX_SinCos =& 
    $Plotarea_SinCos->addNew('line_grid', null, IMAGE_GRAPH_AXIS_X);
$GridY_SinCos =& 
    $Plotarea_SinCos->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
$Dataset_Sin =& 
    Image_Graph::factory(
        'Image_Graph_Dataset_Function', 
        array(-2*pi(), 2*pi(), 'sin', 50)
    );
$Dataset_Cos =& 
    Image_Graph::factory(
        'Image_Graph_Dataset_Function', 
        array(-2*pi(), 2*pi(), 'cos', 50)
    );
$Plot_Sin =& $Plotarea_SinCos->addNew('line', $Dataset_Sin);
$Plot_Cos =& $Plotarea_SinCos->addNew('line', $Dataset_Cos);
$Plot_Sin->setLineColor('red');
$Plot_Cos->setLineColor('blue');
$Plot_Sin->setTitle('sin(x)');
$Plot_Cos->setTitle('cos(x)');
$AxisX_SinCos =& $Plotarea_SinCos->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX_SinCos->setLabelInterval(array(-6, -4, -2, 2, 4, 6));
$AxisY_SinCos =& $Plotarea_SinCos->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY_SinCos->forceMinimum(-1.1);
$AxisY_SinCos->forceMaximum(1.1);
$AxisY_SinCos->setLabelInterval(array(-1, -0.5, 0.5, 1));

function carLabel($value) {
    return 2000+$value;
}

$DataPreprocessor_Car =& 
    Image_Graph::factory('Image_Graph_DataPreprocessor_Function', 'carLabel');

$GridX_Car =& $Plotarea_Car->addNew('line_grid', null, IMAGE_GRAPH_AXIS_X);
$GridY_Car =& $Plotarea_Car->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
$GridX_Car->setLineColor('gray@0.2');
$GridY_Car->setLineColor('gray@0.2');
$Dataset_Car =& Image_Graph::factory('random', array(10, 10, 100, true));
$Fill_Car =& 
    Image_Graph::factory('Image_Graph_Fill_Image', './images/audi-tt-coupe.jpg');
$Plotarea_Car->setFillStyle($Fill_Car);
$Plot_Car =& 
    $Plotarea_Car->addNew('Image_Graph_Plot_Smoothed_Area', $Dataset_Car);
$Plot_Car->setLineColor('gray');
$Plot_Car->setFillColor('white@0.7');

$AxisX_Car =& $Plotarea_Car->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX_Car->setDataPreprocessor($DataPreprocessor_Car);
$AxisX_Car->setFontSize(6);
$AxisY_Car =& $Plotarea_Car->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY_Car->forceMaximum(100);
    
// output the graph using the GD canvas
$Graph->done(array('filename' => './output/frontpage_sample.png'));

// create a new SVG canvas
$Canvas =& Image_Canvas::factory('svg',
    array(
        'width' => 600,
        'height' => 400
    )
); 
// make the graph use this now instead
$Graph->setCanvas($Canvas);

// 're'-output the graph, but not using the SVG canvas
$Graph->done(array('filename' => './output/frontpage_sample.svg'));

/*
// create a new PDF canvas
$Canvas =& Image_Canvas::factory('pdflib',
    array(
        'page' => 'A4',
        'align' => 'center',
        'orientation' => 'landscape',           
        'width' => 600,
        'height' => 400
    )
); 
// make the graph use this now instead
$Graph->setCanvas($Canvas);

// 're'-output the graph, but not using the PDF canvas
$Graph->done(array('filename' => './output/frontpage_sample.pdf'));

// create a new SWF canvas
$Canvas =& Image_Canvas::factory('swf',
    array(
        'width' => 600,
        'height' => 400
    )
); 
// make the graph use this now instead
$Graph->setCanvas($Canvas);

// 're'-output the graph, but not using the SWF canvas
$Graph->done(array('filename' => './output/frontpage_sample.swf'));
*/
?>