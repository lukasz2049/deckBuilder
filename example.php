<!DOCTYPE html>
<html dir="ltr">
<head>
	<meta charset="utf-8" />
	<title>Deck Generator - example</title>
	<link rel="stylesheet" type="text/css" href="styleExample.css">
</head>
<body>
<div id="Deck">
<?php
// Deck Builder
require_once 'deckBuilder.php';
$deck = new deckBuilder;
$deck->tplDir = 'exampleTpl/';
$deck->cardTpl = 'cardExample';
$deck->type = 'exampleType';

// Example 1: local file, Polish language, display only the first card
$deck->loadSheet('Example deck data.csv');
$deck->lang = 'PL';
echo $deck->render(TRUE)[0]; // $renderAsArray is set TRUE

echo '<hr>';

// Example 2: local file, English language
$deck->lang = 'EN';
echo $deck->render(); // $renderAsArray is set FALSE (default)

echo '<hr>';

// Example 3: remote file, English language
$deck->loadSheet('https://docs.google.com/spreadsheets/u/0/d/1dIJYT-7GwbX3SKnIa52gBdmc58AdB-qtEoMjCxZLDWQ/export?format=csv&id=1dIJYT-7GwbX3SKnIa52gBdmc58AdB-qtEoMjCxZLDWQ&gid=0');
print_r ($deck->render());

?>
</div>
</body>
</html>