<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$ada_error = false;
$result = '';
$id_karyawan = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_karyawan) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_karyawan FROM karyawan WHERE id_karyawan = :id_karyawan');
	$query->execute(array('id_karyawan' => $id_karyawan));
	$result = $query->fetch();
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		$handle = $pdo->prepare('DELETE FROM nilai_karyawan WHERE id_karyawan = :id_karyawan');				
		$handle->execute(array(
			'id_karyawan' => $result['id_karyawan']
		));
		$handle = $pdo->prepare('DELETE FROM karyawan WHERE id_karyawan = :id_karyawan');				
		$handle->execute(array(
			'id_karyawan' => $result['id_karyawan']
		));
		redirect_to('list-karyawan.php?status=sukses-hapus');
	}
}
?>

<?php
$judul_page = 'Hapus Karyawan';
require_once('template/header.php');
?>
	<div class="main-content-row">
	<div class="container clearfix">
		<?php include_once('template/sidebar-karyawan.php'); ?>
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			<?php if($ada_error): ?>
				<?php echo '<p>'.$ada_error.'</p>'; ?>	
			<?php endif; ?>
		</div>
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template/footer.php');