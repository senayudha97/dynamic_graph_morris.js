<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT nama_penjamin,
COALESCE((SELECT sum(jumlah_bayar) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
AND st_billing = 'IRJA'
GROUP BY pj.nama_penjamin),0) as IRJA,

COALESCE((SELECT sum(jumlah_bayar) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
AND st_billing = 'IRNA'
GROUP BY pj.nama_penjamin),0) as IRNA,

COALESCE( (SELECT sum(jumlah_bayar) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
AND st_billing in ('IRJA','IRNA')
GROUP BY pj.nama_penjamin), 0) as grandtotal
FROM m_penjamin
ORDER BY nama_penjamin
";

$chart_data = '';
foreach($conn->query($sql) as $row) {
  $chart_data .= "{  nama_penjamin:'".$row["nama_penjamin"]."', irna:".$row["irna"].", irja:".$row["irja"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Tambah Piutang</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Tambah Piutang</h2>
            <br /><br />
            <div id="chart"></div>
      </div>
</body>
</html>

<script>
      Morris.Bar({
            element : 'chart',
            data:[<?php echo $chart_data; ?>],
            xkey:'nama_penjamin',
            ykeys: ['irna','irja'],
            labels:['IRNA','IRJA'],
            hideHover:'auto',
            stacked:true
      });
</script>
