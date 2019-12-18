<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT u.nama_unit as ruang,sum(jmbayar) as jmbayar 
from t_billrajal br 
join m_tarif_rs t on br.kodetarif = t.kode_tarif
join t_bayarrajal braj on braj.idxbill = br.idxbill
join m_unit u on braj.unit = u.kode_unit
left join m_cara_bayar i on i.id_cara_bayar = braj.st_carabayar
group by u.nama_unit
union 
select r.nama as ruang,sum(jmbayar) as jmbayar
from t_billranap br 
join m_tarif_rs t on br.kodetarif = t.kode_tarif
join t_bayarranap bran on bran.idxbill = br.idxbill
join m_ruang r on bran.noruang = r.no
left join m_cara_bayar i on i.id_cara_bayar = bran.st_carabayar
group by r.nama
";

$chart_data = '';
foreach($conn->query($sql) as $row) {
      $chart_data .= "{  ruang:'".$row["ruang"]."', jmbayar:".$row["jmbayar"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Ruang</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Per Tempat Layanan</h2>
            <br /><br />
            <div id="chart"></div>
      </div>
</body>
</html>

<script>
      Morris.Bar({
            element : 'chart',
            data:[<?php echo $chart_data; ?>],
            xkey:'ruang',
            ykeys: ['jmbayar'],
            labels:['Jumlah Pendapatan'],
            hideHover:'auto',
            stacked:true
      });
</script>
