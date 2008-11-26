<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is a visual test case, testing canvas support for text output.
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
 * @version    CVS: $Id: text.php,v 1.2 2005/08/03 21:17:48 nosey Exp $
 * @link       http://pear.php.net/pepr/pepr-proposal-show.php?id=212
 */

require_once 'Image/Canvas.php';

$canvas =& Image_Canvas::factory(
    'png',
    array('width' => 300, 'height' => 200)
);

$canvas->setLineColor('black');
$canvas->rectangle(array('x0' => 0, 'y0' => 0, 'x1' => $canvas->getWidth() - 1, 'y1' => $canvas->getHeight() - 1));

$canvas->setLineColor('lightgrey@0.3');
$canvas->rectangle(array('x0' => 10, 'y0' => 10, 'x1' => 290, 'y1' => 190));
$canvas->setLineColor('lightgrey@0.3');
$canvas->line(array('x0' => 10, 'y0' => 100, 'x1' => 290, 'y1' => 100));
$canvas->setLineColor('lightgrey@0.3');
$canvas->rectangle(array('x0' => 150, 'y0' => 10, 'x1' => 150, 'y1' => 190));

$font = array('name' => 'Verdana', 'size' => 10);

$align = array(
    array(
        array('horizontal' => 'left', 'vertical' => 'top'),
        array('horizontal' => 'center', 'vertical' => 'top'),
        array('horizontal' => 'right', 'vertical' => 'top')
    ),
    array(
        array('horizontal' => 'left', 'vertical' => 'center'),
        array('horizontal' => 'center', 'vertical' => 'center'),
        array('horizontal' => 'right', 'vertical' => 'center')
    ),
    array(
        array('horizontal' => 'left', 'vertical' => 'bottom'),
        array('horizontal' => 'center', 'vertical' => 'bottom'),
        array('horizontal' => 'right', 'vertical' => 'bottom')
    )
);

for ($row = 0; $row < 3; $row++) {
    for ($col = 0; $col < 3; $col++) {
        $x = 10 + $col * 140;
        $y = 10 + $row * 90;

        switch ($row) {
            case 0:
                $text = 'Top';
                break;
            case 1:
                $text = 'Center';
                break;
            case 2:
                $text = 'Bottom';
                break;
        }
        switch ($col) {
            case 0:
                $text .= "\n" . 'Left';
                break;
            case 1:
                if ($row !== 1) {
                    $text .= "\n" . 'Center';
                }
                break;
            case 2:
                $text .= "\n" . 'Right';
                break;
        }

        $canvas->setLineColor('red');
        $canvas->line(array('x0' => $x - 5, 'y0' => $y, 'x1' => $x + 5, 'y1' => $y));
        $canvas->setLineColor('red');
        $canvas->line(array('x0' => $x, 'y0' => $y - 5, 'x1' => $x, 'y1' => $y + 5));

        $canvas->setFont($font);
        $canvas->addText(array('x' => $x, 'y' => $y, 'text' => $text, 'alignment' => $align[$row][$col]));
    }
}

$canvas->show();

?>