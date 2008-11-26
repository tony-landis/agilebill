<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is not a test in itself, since it depends on another source part to
 * create the canvas. It is merely a common canvas test include, to avoid
 * redundant code in every canvas test.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Canvas
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: canvas_body.php,v 1.4 2005/08/16 21:27:58 nosey Exp $
 * @link       http://pear.php.net/pepr/pepr-proposal-show.php?id=212
 */  
 
$canvas->addText(array('x' => 5, 'y' => 5, 'text' => 'Line'));

$canvas->setLineColor('black');
$canvas->line(array('x0' => 100, 'x1' => 195, 'y0' => 5, 'y1' => 5));
$canvas->setLineColor('red');
$canvas->line(array('x0' => 200, 'x1' => 295, 'y0' => 5, 'y1' => 5));
$canvas->setLineColor('green');
$canvas->line(array('x0' => 300, 'x1' => 395, 'y0' => 5, 'y1' => 5));
$canvas->setLineColor('blue');
$canvas->line(array('x0' => 400, 'x1' => 495, 'y0' => 5, 'y1' => 5));

$canvas->setLineColor(array('blue', 'red'));
$canvas->line(array('x0' => 100, 'x1' => 195, 'y0' => 10, 'y1' => 10));

$canvas->setLineColor(array('blue', 'blue', 'transparent'));
$canvas->line(array('x0' => 200, 'x1' => 295, 'y0' => 10, 'y1' => 10));

$canvas->setLineColor('yellow');
$canvas->setLineThickness(2);
$canvas->line(array('x0' => 300, 'x1' => 395, 'y0' => 10, 'y1' => 10));

$canvas->setLineColor('red');
$canvas->setLineThickness(4);
$canvas->line(array('x0' => 400, 'x1' => 495, 'y0' => 10, 'y1' => 10));

$canvas->setLineColor('black@0.4');
$canvas->setLineThickness(4);
$canvas->line(array('x0' => 100, 'x1' => 220, 'y0' => 15, 'y1' => 15));
$canvas->setLineColor('red@0.4');
$canvas->setLineThickness(4);
$canvas->line(array('x0' => 200, 'x1' => 320, 'y0' => 15, 'y1' => 15));
$canvas->setLineColor('green@0.4');
$canvas->setLineThickness(4);
$canvas->line(array('x0' => 300, 'x1' => 420, 'y0' => 15, 'y1' => 15));
$canvas->setLineColor('blue@0.4');
$canvas->setLineThickness(4);
$canvas->line(array('x0' => 400, 'x1' => 495, 'y0' => 15, 'y1' => 15));

$canvas->addText(array('x' => 5, 'y' => 30, 'text' => 'Rectangle'));

$canvas->setLineColor('black');
$canvas->rectangle(array('x0' => 100, 'x1' => 150, 'y0' => 30, 'y1' => 80));
$canvas->setLineColor('red');
$canvas->rectangle(array('x0' => 155, 'x1' => 205, 'y0' => 30, 'y1' => 80));
$canvas->setLineColor('green');
$canvas->rectangle(array('x0' => 210, 'x1' => 260, 'y0' => 30, 'y1' => 80));
$canvas->setLineColor('blue');
$canvas->rectangle(array('x0' => 265, 'x1' => 315, 'y0' => 30, 'y1' => 80));

$canvas->setFillColor('black');
$canvas->rectangle(array('x0' => 100, 'x1' => 150, 'y0' => 85, 'y1' => 135));
$canvas->setLineColor('black');
$canvas->setFillColor('red');
$canvas->rectangle(array('x0' => 155, 'x1' => 205, 'y0' => 85, 'y1' => 135));
$canvas->setLineColor('black');
$canvas->setFillColor('green');
$canvas->rectangle(array('x0' => 210, 'x1' => 260, 'y0' => 85, 'y1' => 135));
$canvas->setLineColor('black');
$canvas->setFillColor('blue');
$canvas->rectangle(array('x0' => 265, 'x1' => 315, 'y0' => 85, 'y1' => 135));

$canvas->setLineColor('red');
$canvas->setFillColor('red@0.3');
$canvas->rectangle(array('x0' => 340, 'x1' => 400, 'y0' => 30, 'y1' => 90));
$canvas->setLineColor('green');
$canvas->setFillColor('green@0.3');
$canvas->rectangle(array('x0' => 380, 'x1' => 440, 'y0' => 50, 'y1' => 110));
$canvas->setLineColor('blue');
$canvas->setFillColor('blue@0.3');
$canvas->rectangle(array('x0' => 360, 'x1' => 420, 'y0' => 70, 'y1' => 130));

$canvas->addText(array('x' => 90, 'y' => 140, 'text' => "Circle / Ellipse", 'alignment' => array('horizontal' => 'right')));

$canvas->setLineColor('black');
$canvas->ellipse(array('x' => 130, 'y' => 170, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('red');
$canvas->ellipse(array('x' => 195, 'y' => 170, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('blue');
$canvas->ellipse(array('x' => 250, 'y' => 170, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('green');
$canvas->ellipse(array('x' => 305, 'y' => 170, 'rx' => 30, 'ry' => 30));

$canvas->setFillColor('black');
$canvas->ellipse(array('x' => 130, 'y' => 235, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('black');
$canvas->setFillColor('red');
$canvas->ellipse(array('x' => 195, 'y' => 235, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('black');
$canvas->setFillColor('blue');
$canvas->ellipse(array('x' => 250, 'y' => 235, 'rx' => 30, 'ry' => 30));
$canvas->setLineColor('black');
$canvas->setFillColor('green');
$canvas->ellipse(array('x' => 305, 'y' => 235, 'rx' => 30, 'ry' => 30));

$canvas->setLineColor('brown');
$canvas->setFillColor('brown@0.3');
$canvas->ellipse(array('x' => 400, 'y' => 200, 'rx' => 40, 'ry' => 40));
$canvas->setLineColor('orange');
$canvas->setFillColor('orange@0.3');
$canvas->ellipse(array('x' => 430, 'y' => 220, 'rx' => 30, 'ry' => 40));
$canvas->setLineColor('purple');
$canvas->setFillColor('purple@0.3');
$canvas->ellipse(array('x' => 390, 'y' => 230, 'rx' => 40, 'ry' => 20));

$canvas->addText(array('x' => 5, 'y' => 270, 'text' => 'Pie slices'));

$c = 0;
for ($i = 360; $i >= 45; $i -= 30) {
    $canvas->setLineColor('black');
    $canvas->setFillColor('blue@' . sprintf('%0.1f', ((360 - $i) / 360)));
    $canvas->pieslice(
        array(
            'x' => 130 + $c * 55,
            'y' => 295,
            'rx' => 25,
            'ry' => 25,
            'v1' => 0,
            'v2' => $i
        )
    );
    $c++;
}

$canvas->addText(array('x' => 5, 'y' => 325, 'text' => 'Polygon'));

$canvas->setLineColor('green');
for ($i = 0; $i < 8; $i++) {
    $canvas->addVertex(array('x' => 115 + $i * 50, 'y' => 330));
    $canvas->addVertex(array('x' => 100 + $i * 50, 'y' => 325));
    $canvas->addVertex(array('x' => 125 + $i * 50, 'y' => 350));
}
$canvas->polygon(array('connect' => false));

$canvas->setLineColor('purple');
$canvas->setFillColor('purple@0.3');
for ($i = 0; $i < 8; $i++) {
    $canvas->addVertex(array('x' => 100 + $i * 50, 'y' => 355));
    $canvas->addVertex(array('x' => 125 + $i * 50, 'y' => 380 + 2 * $i));
}
$canvas->addVertex(array('x' => 550, 'y' => 355));
for ($i = 4; $i >= 0; $i--) {
    $canvas->addVertex(array('x' => 120 + $i * 100, 'y' => 430 + $i * 5));
    $canvas->addVertex(array('x' => 110 + $i * 100, 'y' => 405 - $i * 5));
}
$canvas->polygon(array('connect' => true));

$canvas->addText(array('x' => 5, 'y' => 455, 'text' => 'Splines'));

$points = array();
$points[] = array(
    'x' => 100, 'y' => 470,
    'p1x' => 120, 'p1y' => 455,
    'p2x' => 150, 'p2y' => 460
);

$points[] = array(
    'x' => 170, 'y' => 490,
    'p1x' => 190, 'p1y' => 500,
    'p2x' => 200, 'p2y' => 510
);

$points[] = array(
    'x' => 210, 'y' => 540,
    'p1x' => 200, 'p1y' => 550,
    'p2x' => 160, 'p2y' => 560
);

$points[] = array(
    'x' => 120, 'y' => 480
);

// draw control points! not directly a canvas test!
foreach ($points as $point) {
    if (isset($last)) {
        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $last['p2x'], 'y0' => $last['p2y'], 'x1' => $point['x'], 'y1' => $point['y']));
    }

    $canvas->setLineColor('red');
    $canvas->ellipse(array('x' => $point['x'], 'y' => $point['y'], 'rx' => 3, 'ry' => 3));

    if (isset($point['p1x'])) {
        $canvas->setLineColor('green');
        $canvas->ellipse(array('x' => $point['p1x'], 'y' => $point['p1y'], 'rx' => 2, 'ry' => 2));
        $canvas->setLineColor('green');
        $canvas->ellipse(array('x' => $point['p2x'], 'y' => $point['p2y'], 'rx' => 2, 'ry' => 2));

        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $point['x'], 'y0' => $point['y'], 'x1' => $point['p1x'], 'y1' => $point['p1y']));
        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $point['p1x'], 'y0' => $point['p1y'], 'x1' => $point['p2x'], 'y1' => $point['p2y']));

        $last  = $point;
    }
}

foreach ($points as $point) {
    if (isset($point['p1x'])) {
        $canvas->addSpline($point);
    } else {
        $canvas->addVertex($point);
    }
}

$canvas->setLineColor('black');
$canvas->polygon(array('connect' => false));

$points = array();
$points[] = array(
    'x' => 220, 'y' => 470,
    'p1x' => 240, 'p1y' => 455,
    'p2x' => 270, 'p2y' => 460
);

$points[] = array(
    'x' => 240, 'y' => 490,
    'p1x' => 310, 'p1y' => 460,
    'p2x' => 320, 'p2y' => 470
);

$points[] = array(
    'x' => 330, 'y' => 500,
    'p1x' => 320, 'p1y' => 550,
    'p2x' => 280, 'p2y' => 560
);

$points[] = array(
    'x' => 240, 'y' => 520,
    'p1x' => 230, 'p1y' => 490,
    'p2x' => 225, 'p2y' => 490
);

$points[] = array(
    'x' => 220, 'y' => 470
);

unset($last);
// draw control points! not directly a canvas test!
foreach ($points as $point) {
    if (isset($last)) {
        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $last['p2x'], 'y0' => $last['p2y'], 'x1' => $point['x'], 'y1' => $point['y']));
    }

    $canvas->setLineColor('red');
    $canvas->ellipse(array('x' => $point['x'], 'y' => $point['y'], 'rx' => 3, 'ry' => 3));

    if (isset($point['p1x'])) {
        $canvas->setLineColor('green');
        $canvas->ellipse(array('x' => $point['p1x'], 'y' => $point['p1y'], 'rx' => 2, 'ry' => 2));
        $canvas->setLineColor('green');
        $canvas->ellipse(array('x' => $point['p2x'], 'y' => $point['p2y'], 'rx' => 2, 'ry' => 2));

        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $point['x'], 'y0' => $point['y'], 'x1' => $point['p1x'], 'y1' => $point['p1y']));
        $canvas->setLineColor('gray@0.2');
        $canvas->line(array('x0' => $point['p1x'], 'y0' => $point['p1y'], 'x1' => $point['p2x'], 'y1' => $point['p2y']));

        $last  = $point;
    }
}

foreach ($points as $point) {
    if (isset($point['p1x'])) {
        $canvas->addSpline($point);
    } else {
        $canvas->addVertex($point);
    }
}

$canvas->setLineColor('black');
$canvas->setFillColor('red@0.2');
$canvas->polygon(array('connect' => true));

$canvas->addText(array('x' => 375, 'y' => 455, 'text' => 'Image'));

$canvas->image(array('x' => 445, 'y' => 455, 'filename' => './pear-icon.png', 'url' => 'http://pear.veggerby.dk/', 'target' => '_blank'));

$canvas->image(array('x' => 445, 'y' => 495, 'filename' => './pear-icon.png', 'width' => 20, 'height' => 20));

$canvas->image(array('x' => 445, 'y' => 523, 'filename' => './pear-icon.png', 'width' => 40, 'height' => 40));

//$canvas->show();
$type = basename($_SERVER['SCRIPT_NAME'], '.php');
$canvas->toHtml(
    array(
        'filename' => 'test' . $type . '.' . $type, 
        'urlpath' => '', 
        'filepath' => './', 
        'width' => '100%', 
        'height' => '100%'
    )
); 

?>