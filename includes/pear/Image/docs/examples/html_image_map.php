<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Negative values
 * (more a test really)
 * 
 * Other: 
 * None specific
 * 
 * $Id: html_image_map.php,v 1.2 2005/08/23 21:01:45 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */


require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('png', array('width' => 400, 'height' => 300, 'usemap' => true));

// This is how you get the ImageMap object, fx. to save map to file (using toHtml())      
$Imagemap = $Canvas->getImageMap();

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);
 // add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        $Plotarea = Image_Graph::factory('plotarea'),
        $Legend = Image_Graph::factory('legend'),
        90
    )
);
$Legend->setPlotarea($Plotarea);

$Dataset =& Image_Graph::factory('dataset');
$Dataset2 =& Image_Graph::factory('dataset');
$Dataset3 =& Image_Graph::factory('dataset');

$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

foreach($months as $month) {
    
    $Dataset->addPoint(
        $month,
        rand(0, 10),
        array(
            'url' => 'http://pear.php.net/?month=' . $month,
            'alt' => $month,
            'target' => '_blank'
        )
    );
    
    $Dataset2->addPoint(
        $month,
        rand(11, 16),
        array(
            'url' => 'http://pear.veggerby.dk/' . $month . '/',
            'alt' => 'Downloads for ' . $month,
            'target' => '_blank'
        )
    );
    
    $Dataset3->addPoint(
        $month,
        rand(-1, -10),
        array(
            'url' => 'http://pear.veggerby.dk/' . $month . '/',
            'alt' => $month . ' 2005',
            'target' => '_blank',
            'htmltags' => array(
                'onMouseOver' => 'alert("Hello, World!");'
            )
        )
    );
}
 
$Plot =& $Plotarea->addNew('bar', array(&$Dataset));
$Plot->setFillColor('blue@0.2');

$Plot2 =& $Plotarea->addNew('line', array(&$Dataset2));
$Plot2->setLineColor('red');

$Plot3 =& $Plotarea->addNew('area', array(&$Dataset3));
$Plot3->setFillColor('yellow@0.2');

// output the Graph
$output = $Graph->done(
    array(
        'tohtml' => true,
        'border' => 0,
        'filename' => 'imagemap.png',
        'filepath' => './',
        'urlpath' => ''
    )
);

print $output . '<pre>' . htmlspecialchars($output) . '</pre>';
?>