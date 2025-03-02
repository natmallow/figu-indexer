<?php
$orig = "I'll \"walk\" the. <b>dog</b> now.!:?";

$a = htmlentities($orig);

$engMatches = [];
$gerMatches = [];

//  $re = '/(.*?(?:\.|\?|!))(?: |$)/m';
$re = '/.*?[.?!:](?=(?<!\d\.|\d\s\. )\s+|\s*$)/m';


preg_match_all($re, $a, $engMatches, PREG_SET_ORDER, 0);

var_dump($engMatches);


$b = html_entity_decode($a);

echo $a; // I'll &quot;walk&quot; the &lt;b&gt;dog&lt;/b&gt; now
echo "<br>";
echo $b; // I'll "walk" the <b>dog</b> now