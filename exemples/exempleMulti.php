<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>Exemple multi courbe - SVGPHPGraphGenerator</title>
        <link href="../style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
	<?php
	require('../svgraph.class.php');

	$data = array(
		'Lundi' => array(123, 765),
		'Mardi' => array(432, 612),
		'Mercredi' => array(69, 234),
		'Jeudi' => array(451, 90),
		'Vendredi' => array(54, 80)
		);

	$graph = new graph(700, 300, $data, false, array('red', 'green'));
	echo $graph->graphLignes();
	?>
	<script src="../jquery-2.1.0.min.js"></script>
	<script src="../tooltip.js"></script>
	</body>
</html>