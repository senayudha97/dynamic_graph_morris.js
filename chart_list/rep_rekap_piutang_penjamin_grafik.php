<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT nama_penjamin,mp.alamat,sum(coalesce(p.jumlah_bayar,0))-sum(coalesce(t1.jumlah_bayar,0)) AS jml_piutang 
FROM t_piutang p
JOIN m_pasien pas ON p.nomr = pas.nomr
JOIN m_penjamin mp on mp.id_penjamin = p.kode_penjamin
LEFT JOIN (
select pp2.id_piutang,mp2.id_penjamin,sum(pp2.jumlah_bayar) as jumlah_bayar 
from t_pelunasan_piutang pp2 
join t_piutang p2 on pp2.id_piutang = p2.id_piutang
join m_penjamin mp2 on p2.kode_penjamin = mp2.id_penjamin
where (pp2.status_batal IS NULL OR pp2.status_batal = 0)
group by pp2.id_piutang,mp2.id_penjamin
) t1 on t1.id_piutang = p.id_piutang
WHERE (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY p.kode_penjamin,mp.alamat,nama_penjamin
";

$chart_data = '';
foreach($conn->query($sql) as $row) {
      $chart_data .= "{  nama_penjamin:'".$row["nama_penjamin"]."', jml_piutang:".$row["jml_piutang"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Rekap Piutang Penjamin</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Piutang Penjamin</h2>
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
            ykeys: ['jml_piutang'],
            labels:['Jumlah Pendapatan'],
            hideHover:'auto',
            stacked:true
      });
</script>
