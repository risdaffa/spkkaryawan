<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$judul_page = 'List Kriteria';
require_once('template/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
		<?php include_once('template/sidebar-kriteria.php'); ?>
		<div class="main-content the-content">
			<?php
			$status = isset($_GET['status']) ? $_GET['status'] : '';
			$msg = '';
			switch($status):
				case 'sukses-baru':
					$msg = 'Kriteria baru berhasil dibuat';
					break;
				case 'sukses-hapus':
					$msg = 'Kriteria behasil dihapus';
					break;
				case 'sukses-edit':
					$msg = 'Kriteria behasil diedit';
					break;
			endswitch;
			if($msg):
				echo '<div class="msg-box msg-box-full">';
				echo '<p>'.$msg.'</p>';
				echo '</div>';
			endif;
			?>
			<h1>List Kriteria</h1>
			<?php
			$query = $pdo->prepare('SELECT * FROM kriteria ORDER BY urutan_order ASC');			
			$query->execute();
			// menampilkan berupa nama field
			$query->setFetchMode(PDO::FETCH_ASSOC);
			if($query->rowCount() > 0):
			?>
			
			<table class="pure-table pure-table-striped">
				<thead>
					<tr>
						<th>Nama Kriteria</th>
						<th>Type</th>
						<th>Bobot</th>
						<th>Detail</th>
						<th>Value</th>
						<th>Edit</th>
						<th>Hapus</th>
					</tr>
				</thead>
				<tbody>
					<?php while($hasil = $query->fetch()): ?>
						<tr>
							<td><?php echo $hasil['nama']; ?></td>
							<td>
							<?php
							if($hasil['type'] == 'benefit') {
								echo 'Benefit';
							} elseif($hasil['type'] == 'cost') {
								echo 'Cost';
							}
							?>
							</td>
							<td><?php echo $hasil['bobot']; ?></td>
							<td><a href="single-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>">Detail</a></td>
							<td><a href="everynilai-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>">Set Nilai</a></td>
							<td><a href="edit-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>">Edit</a></td>
							<td><a href="hapus-kriteria.php?id=<?php echo $hasil['id_kriteria']; ?>" class="red yaqin-hapus">Hapus</a></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>

			<?php else: ?>
				<p>Maaf, belum ada data untuk kriteria.</p>
			<?php endif; ?>
		</div>
	</div><!-- .container -->
	</div><!-- .main-content-row -->

<?php
require_once('template/footer.php');