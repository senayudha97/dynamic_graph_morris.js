<?php
// konfigurasi koneksi

// script koneksi php postgree
// $link = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 


class Library{

	public function __construct(){
		$host       =  "10.10.150.40";
		$dbuser     =  "postgres";
		$dbpass     =  "123qweasd";
		$port       =  "5432";
		$dbname    =  "simrs";
		$this->db = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass); 
	}

	public function addBook($kode, $judul, $pengarang, $penerbit){
		$sql = "INSERT INTO books (kodeBuku, judulBuku, pengarang, penerbit) VALUES('$kode', '$judul', '$pengarang', '$penerbit')";
		$query = $this->db->query($sql);
		if(!$query){
			return "Failed";
		}
		else{
			return "Success";
		}
	}
	public function editBook($kode){
		$sql = "SELECT * FROM books WHERE kodeBuku='$kode'";
		$query = $this->db->query($sql);
		return $query;
	}
	public function updateBook($kode, $judul, $pengarang, $penerbit){
		$sql = "UPDATE books SET judulBuku='$judul', pengarang='$pengarang', penerbit='$penerbit' WHERE kodeBuku='$kode'";
		$query = $this->db->query($sql);
		if(!$query){
			return "Failed";
		}
		else{
			return "Success";
		}
	}
	
	public function show(){
		$sql = "SELECT * FROM m_tarif_r WHERE id_transaction < 20";
		$query = $this->db->query($sql);
		return $query;
	}
	public function deleteBook($kode){
		$sql = "DELETE FROM books WHERE kodeBuku='$kode'";
		$query = $this->db->query($sql);
	}
}
?>