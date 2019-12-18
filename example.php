<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT * FROM m_tarif_rs WHERE id_tarif <= 20";
    // foreach($conn->query($sql) as $row) {
	// 	print "<br>";
	// 	print $row['id_tarif'];
	// }

//index.php
$chart_data = '';
foreach($conn->query($sql) as $row) {
	$chart_data .= "{  id_tarif:".$row["id_tarif"].", jumlah:".$row["jumlah"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Chart Template</title>
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
	<br/><br/>
	<div class="container" style="width:900px;">
		<h2 align="center">Default Chart</h2>
		<br /><br />
		<div id="chart"></div>
	</div>
</body>
</html>

<script>
	Morris.Bar({
		element : 'chart',
		data:[<?php echo $chart_data; ?>],
		xkey:'id_tarif',
		ykeys:['jumlah'],
		labels:['Jumlah'],
		hideHover:'auto',
		stacked:true
	});
</script>
