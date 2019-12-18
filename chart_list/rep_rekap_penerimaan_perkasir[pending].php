<?php 
$host       =  "10.10.150.40";
$dbuser     =  "postgres";
$dbpass     =  "123qweasd";
$port       =  "5432";
$dbname     =  "simrs";

$conn = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


$sql = "SELECT nama_pegawai,sum(jmbayar) as jmbayar from (SELECT nama_pegawai,sum(jmbayar) as jmbayar 
FROM t_billrajal br 
join m_tarif_rs t on br.kodetarif = t.kode_tarif
join t_bayarrajal braj on braj.idxbill = br.idxbill
join m_login l on braj.nip = l.nip
left join m_cara_bayar i on i.id_cara_bayar = braj.st_carabayar
where  i.id_cara_bayar = 1
group by nama_pegawai
union 
select nama_pegawai,sum(jmbayar) as jmbayar
from t_billranap br 
join m_tarif_rs t on br.kodetarif = t.kode_tarif
join t_bayarranap bran on bran.idxbill = br.idxbill
join m_login l on bran.nip = l.nip
left join m_cara_bayar i on i.id_cara_bayar = bran.st_carabayar
where  i.id_cara_bayar = 1
group by nama_pegawai) as rekap_kas group by session_name()a_pegawai
";

$chart_data = '';
foreach($conn->query($sql) as $row) {
      $chart_data .= "{ nama_pegawai:'".$row["nama_pegawai"]."', jmbayar:".$row["jmbayar"]."}, ";
}

$chart_data = substr($chart_data, 0, -2);
?>


<!DOCTYPE html>
<html>
<head>
      <title>Chart Rekap Penerimaan Per Kasir</title>
      <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
      <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

</head>
<body>
      <br/><br/>
      <div class="container" style="width:900px;">
            <h2 align="center">Grafik Laporan Rekap Penerimaan Per Kasir</h2>
            <br /><br />
            <div id="chart"></div>
      </div>
</body>
</html>

<script>
      Morris.Bar({
            element : 'chart',
            data:[<?php echo $chart_data; ?>],
            xkey:'nama_pegawai',
            ykeys: ['jmbayar'],
            labels:['JUMLAH PEMBAYARAN'],
            hideHover:'auto',
            stacked:true
      });
</script>
