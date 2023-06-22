<?php require_once('includes/init.php'); ?>

<?php
$ada_error = false;
$result = '';

$id_karyawan = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_karyawan) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM karyawan WHERE id_karyawan = :id_karyawan');
	$query->execute(array('id_karyawan' => $id_karyawan));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}
}
?>

<?php
$judul_page = 'Detail Karyawan';
require_once('template/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template/sidebar-karyawan.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>
				
			<?php elseif(!empty($result)): ?>
			
				<h4>Nama Karyawan</h4>
				<p><?php echo $result['nama_karyawan']; ?></p>
				
				<h4>Tanggal Tes</h4>
                <p><?php
					$tgl1 = strtotime($result['tanggal_tes']);
					echo date('j F Y', $tgl1);
				?></p>
				
				<h4>Tanggal Input</h4>
				<p><?php
					$tgl2 = strtotime($result['tanggal_input']);
					echo date('j F Y', $tgl2);
				?></p>
				
				<?php
				$query2 = $pdo->prepare('SELECT nilai_karyawan.nilai AS nilai, kriteria.nama AS nama FROM kriteria 
				LEFT JOIN nilai_karyawan ON nilai_karyawan.id_kriteria = kriteria.id_kriteria 
				AND nilai_karyawan.id_karyawan = :id_karyawan ORDER BY kriteria.urutan_order ASC');
				$query2->execute(array(
					'id_karyawan' => $id_karyawan
				));
				$query2->setFetchMode(PDO::FETCH_ASSOC);
				$kriterias = $query2->fetchAll();
				if(!empty($kriterias)):
				?>
					<h3>Nilai Kriteria</h3>
					<table class="pure-table">
						<thead>
							<tr>
								<?php foreach($kriterias as $kriteria ): ?>
									<th><?php echo $kriteria['nama']; ?></th>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<?php foreach($kriterias as $kriteria ): ?>
									<th><?php echo ($kriteria['nilai']) ? $kriteria['nilai'] : 0; ?></th>
								<?php endforeach; ?>
							</tr>
						</tbody>
					</table>
				<?php
				endif;
				?>

				<p><a href="edit-karyawan.php?id=<?php echo $id_karyawan; ?>" class="button">Edit</a> &nbsp; <a href="hapus-karyawan.php?id=<?php echo $id_karyawan; ?>" class="button button-red yaqin-hapus">Hapus</a></p>
			
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template/footer.php');