<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_kriteria = isset($_GET['id']) ? trim($_GET['id']) : '';

if (!$id_kriteria) {
    $ada_error = 'Maaf, data tidak dapat diproses.';
} else {
    $query = $pdo->prepare('SELECT nk.*, k.nama_karyawan
                            FROM nilai_karyawan nk
                            JOIN karyawan k ON nk.id_karyawan = k.id_karyawan
                            WHERE nk.id_kriteria = :id_kriteria
                            ORDER BY nk.id_nilai_karyawan ASC');
    $query->execute(array('id_kriteria' => $id_kriteria));
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($result)) {
        $ada_error = 'Maaf, data tidak dapat diproses.';
    }
}

if (isset($_POST['submit'])) {
    $kriteria = isset($_POST['kriteria']) ? $_POST['kriteria'] : array();

    if (empty($errors)) {
        foreach ($kriteria as $id_karyawan => $nilai) {
            $handle = $pdo->prepare('UPDATE nilai_karyawan SET nilai = :nilai WHERE id_kriteria = :id_kriteria AND id_karyawan = :id_karyawan');
            $handle->execute(array(
                'nilai' => $nilai,
                'id_kriteria' => $id_kriteria,
                'id_karyawan' => $id_karyawan
            ));
        }
        $sukses = true;
    }
}
?>

<?php
$judul_page = 'Edit Nilai Kriteria setiap Karyawan';
require_once('template/header.php');
?>
<div class="main-content-row">
    <div class="container clearfix">
        <?php include_once('template/sidebar-kriteria.php'); ?>
        <div class="main-content the-content">
            <h1>Nilai Setiap Karyawan berdasarkan Kriteria</h1>

            <?php if (!empty($errors)) : ?>

                <div class="msg-box warning-box">
                    <p><strong>Error:</strong></p>
                    <ul>
                        <?php foreach ($errors as $error) : ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($sukses) : ?>
                <div class="msg-box">
                    <p>Data berhasil disimpan</p>
                </div>
            <?php elseif ($ada_error) : ?>
                <p><?php echo $ada_error; ?></p>
            <?php else : ?>
                <form action="everynilai-kriteria.php?id=<?php echo $id_kriteria; ?>" method="post">
                    <h3>Nilai Kriteria</h3>
                    <?php foreach ($result as $row) : ?>
                        <div class="field-wrap clearfix">
                            <label><?php echo $row['nama_karyawan']; ?></label>
                            <input type="number" name="kriteria[<?php echo $row['id_karyawan']; ?>]" value="<?php echo ($row['nilai']) ? $row['nilai'] : 0; ?>">
                        </div>
                    <?php endforeach; ?>
                    <div class="field-wrap clearfix">
                        <button type="submit" name="submit" value="submit" class="button">Simpan</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

    </div><!-- .container -->
</div><!-- .main-content-row -->


<?php
require_once('template/footer.php');
?>