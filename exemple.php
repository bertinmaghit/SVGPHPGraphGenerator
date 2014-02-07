<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Exemple - SVGPHPGraphGenerator</title>
        <link href="style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
	<?php
	require('svgraph.class.php');

	$data = array(
		'Janvier' => 200,
		'Février' => 120,
		'Mars' => 235,
		'Avril' => 100,
		'Mai' => 167,
		'Juin' => 117);

	$data2 = array(
		'Lundi' => array(123, 765),
		'Mardi' => array(432, 612),
		'Mercredi' => array(69, 234),
		'Jeudi' => array(451, 90),
		'Vendredi' => array(54, 80)
		);

	$graph = new graph(700, 300, $data, true);
	echo $graph->graphLignes();

	$graph = new graph(700, 300, $data2, false, array('red', 'green'));
	echo $graph->graphLignes();
	?>
	<script src="jquery-2.1.0.min.js"></script>
	<script src="tooltip.js"></script>
	</body>
</html>