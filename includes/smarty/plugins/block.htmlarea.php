<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.htmlarea.php
 * Type:     block
 * Name:     htmlarea
 * Purpose:  display a WYSIWYG html editor form
 * -------------------------------------------------------------
 */
function smarty_block_htmlarea($params, $resource, &$smarty)
{
    /* passed params:
    field = name of the textarea field
    width = width ie '100'
    height = height ie '200'
    */


     if(isset($resource))
     {
         if(empty($params['width']))
          $width = '550';
         else
          $width = $params['width'];

         if(empty($params['height']))
          $height = '350';
         else
          $height = $params['height'];

        echo '
        <textarea id="'.$params['field'].'" name="'.$params['field'].'">'.$resource.'</textarea>
        <script type="text/javascript" defer="1">
            var config = new HTMLArea.Config();
            config.width = \''.$width .'px\';
            config.height = \''.$height .'px\';
            config.toolbar = [
            [ "fontname", "space",
              "fontsize", "space",
              "formatblock", "space",
              "strikethrough", "subscript", "superscript", "separator",
              "copy", "cut", "paste", "space", "undo", "redo" ],

            [ "bold", "italic", "underline", "separator",
              "justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
              "insertorderedlist", "insertunorderedlist", "outdent", "indent", "separator",
              "forecolor", "hilitecolor", "textindicator", "separator",
              "inserthorizontalrule", "createlink", "insertimage", "inserttable", "htmlmode"]
            ];
            HTMLArea.replace("'.$params['field'].'", config);
        </script>';
     }
}
?>