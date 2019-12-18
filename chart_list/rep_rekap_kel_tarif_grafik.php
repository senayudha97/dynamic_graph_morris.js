<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT kelompok_tarif,sum(jmbayar) as jmbayar from (SELECT t.kelompok_tarif,sum(jmbayar) as jmbayar 
      from t_billrajal br 
      join m_tarif_rs t on br.kodetarif = t.kode_tarif
      join t_bayarrajal braj on braj.idxbill = br.idxbill
      left join m_cara_bayar i on i.id_cara_bayar = braj.st_carabayar
      group by t.kelompok_tarif
      union 
      select t.kelompok_tarif,sum(jmbayar) as jmbayar
      from t_billranap br 
      join m_tarif_rs t on br.kodetarif = t.kode_tarif
      join t_bayarranap bran on bran.idxbill = br.idxbill
      left join m_cara_bayar i on i.id_cara_bayar = bran.st_carabayar
      group by t.kelompok_tarif) as rekap_kel_tarif group by kelompok_tarif
      ";

$chart_data = '';
foreach($conn->query($sql) as $row) {
      $chart_data .= "{  kelompok_tarif:'".$row["kelompok_tarif"]."'    , jmbayar:".$row["jmbayar"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Kelompok Tarif</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Kelompok Tarif</h2>
            <br /><br />
            <div id="chart"></div>
      </div>
</body>
</html>

<script>
      Morris.Bar({
            element : 'chart',
            data:[<?php echo $chart_data; ?>],
            xkey:'kelompok_tarif',
            ykeys: ['jmbayar'],
            labels:['Jumlah Bayar'],
            hideHover:'auto',
            stacked:true
      });
</script>
