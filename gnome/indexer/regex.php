<?php
    
   $text = "bobby like to eat apples on the corner of apple town street. Samstag, 22. August 2020, 00.17 h la la la Samstag,  22.  August  2020,  00.17 h ehehehe";
   $patterns = '/(\b\w*\,\s*?\d*)(\.)(\s*?\w*\s*?\d*\,\s*?\d{2})(\.)(\d{2}?)/m';
   $pattern = '/(\b\w*\,\s*?\d*\.\s*?\w*\s*?\d*\,\s*?\d{2}\.\d{2}?\s*?h)$/mD';
   // $replacements = ';';
   $replacements = ['${1}',';','${3}',';','${5}'];
//'/\b\w*\,\s*?\d*(\.)\s*?\w*\s*?\d*\,\s*?\d{2}(\.)\d{2}?\s*?h/m'



$names = [];

$match_count = preg_match_all($patterns, $text, $names);

$new_text = preg_replace($patterns, "$1;$3;$5", $text);

echo '<pre>';
echo $text;
echo '<br>';
echo $new_text;
echo '<br>';
var_dump($names);

?>
