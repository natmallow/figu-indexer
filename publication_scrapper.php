<?php


//require 'vendor/autoload.php';
//Fetches the content from the $url into $content
	$url = "https://en.wikipedia.org/wiki/Web_scraping";
	$content = file_get_contents($url);

$dom = new \IvoPetkov\HTML5DOMDocument();

	
//        echo $content;
//        die('dead');
	//Contructs a DOM from the $content
	//$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->loadHTML($content, \IvoPetkov\HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

	//Extracts the title using #firstHeading
	$title = $dom->getElementById("firstHeading");
	echo $title->nodeValue . "<br>";

//	//Extracts the first paragraph using #mw-content-text
	$textBlock = $dom->getElementById("mw-content-text");
	$paragraphs = $textBlock->getElementsByTagName("p");

	$paragraph = $paragraphs->item(0);

	echo $paragraph->nodeValue;

