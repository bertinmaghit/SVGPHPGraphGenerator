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
		'Avril' => 0,
		'Mai' => 167,
		'Juin' => 117);

	$data2 = array(
		'01' => 890,
		'02' => 354,
		'03' => 879,
		'04' => 412,
		'05' => 932,
		'06' => 457,
		'07' => 456,
		'08' => 879,
		'09' => 567,
		'10' => 590,
		'11' => 690,
		'12' => 750,
		'13' => 982,
		'14' => 287,
		'15' => 980,
		'16' => 577,
		'17' => 467,
		'18' => 687,
		'19' => 678,
		'20' => 566);

	$graph = new graph(700, 300, $data);
	echo $graph->graphLignes();
	?>
	<script src="jquery-2.1.0.min.js"></script>
	<script src="tooltip.js"></script>
	</body>
</html>