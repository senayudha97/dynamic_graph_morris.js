<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT nama_penjamin,COALESCE( (SELECT coalesce(sum(jumlah_bayar),0) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin AND  (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin),0) - COALESCE( (SELECT coalesce(sum(pp.jumlah_bayar),0) as berkurang
FROM t_pelunasan_piutang pp 
JOIN t_piutang p on p.id_piutang = pp.id_piutang and (p.st_piutang is null or p.st_piutang = 'LUNAS')
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
AND (status_batal IS NULL OR status_batal = 0)
GROUP BY pj.nama_penjamin), 0) as saldo_awal,

(SELECT coalesce(sum(jumlah_bayar),0) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin) as bertambah,

(SELECT coalesce(sum(pp.jumlah_bayar),0) as berkurang
FROM t_pelunasan_piutang pp 
JOIN t_piutang p on p.id_piutang = pp.id_piutang
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin 
AND (status_batal IS NULL OR status_batal = 0) and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin) as berkurang,

COALESCE( (SELECT sum(jumlah_bayar) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin 
and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin),0) - 
COALESCE( (SELECT sum(pp.jumlah_bayar) as berkurang
FROM t_pelunasan_piutang pp 
JOIN t_piutang p on p.id_piutang = pp.id_piutang
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin
AND (status_batal IS NULL OR status_batal = 0) and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin), 0) + 
COALESCE( (SELECT sum(jumlah_bayar) as bertambah
FROM t_piutang p
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin 
and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin), 0) - 
COALESCE( (SELECT sum(pp.jumlah_bayar) as berkurang
FROM t_pelunasan_piutang pp 
JOIN t_piutang p on p.id_piutang = pp.id_piutang
JOIN m_penjamin pj ON p.kode_penjamin = pj.id_penjamin 
WHERE pj.id_penjamin = m_penjamin.id_penjamin 
AND (status_batal IS NULL OR status_batal = 0) and (p.st_piutang is null or p.st_piutang = 'LUNAS')
GROUP BY pj.nama_penjamin), 0) as saldo
FROM m_penjamin
ORDER BY nama_penjamin
";

$chart_data = '';
foreach($conn->query($sql) as $row) {
      $chart_data .= "{  nama_penjamin:'".$row["nama_penjamin"]."', saldo_awal:".$row["saldo_awal"].", saldo:".$row["saldo"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Rekap Mutasi Piutang</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Mutasi Piutang</h2>
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
            ykeys: ['saldo_awal', 'saldo'],
            labels:['Saldo Awal', 'Saldo Akhir'],
            hideHover:'auto',
            stacked:false
      });
</script>
