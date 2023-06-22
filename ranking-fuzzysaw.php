<?php

require_once('includes/init.php');

$judul_page = 'Perankingan Menggunakan Metode FuzzySAW';
require_once('template/header.php');

$digit = 5;
$query = $pdo->prepare('SELECT id_kriteria, nama, type, bobot
    FROM kriteria ORDER BY urutan_order ASC');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();

// Calculate nilai_min and nilai_max
$nilai_min = array();
$nilai_max = array();

foreach ($kriterias as $kriteria) {
    $id_kriteria = $kriteria['id_kriteria'];
    $query2 = $pdo->prepare('SELECT nilai FROM nilai_karyawan WHERE id_kriteria = :id_kriteria');
    $query2->execute(array('id_kriteria' => $id_kriteria));
    $query2->setFetchMode(PDO::FETCH_ASSOC);
    $nilai_karyawans = $query2->fetchAll(PDO::FETCH_COLUMN);

    if ($kriteria['type'] == 'benefit') {
        $nilai_min[$id_kriteria] = min($nilai_karyawans);
        $nilai_max[$id_kriteria] = max($nilai_karyawans);
    } elseif ($kriteria['type'] == 'cost') {
        $nilai_min[$id_kriteria] = max($nilai_karyawans);
        $nilai_max[$id_kriteria] = min($nilai_karyawans);
    }
}

$query3 = $pdo->prepare('SELECT id_karyawan, nama_karyawan FROM karyawan');
$query3->execute();
$query3->setFetchMode(PDO::FETCH_ASSOC);
$karyawans = $query3->fetchAll();

// >>> STEP 1 Matrix Keputusan (X)
$matriks_x = array();
$list_kriteria = array();
foreach ($kriterias as $kriteria) {
    $list_kriteria[$kriteria['id_kriteria']] = $kriteria;
    foreach ($karyawans as $karyawan) {
        $id_karyawan = $karyawan['id_karyawan'];
        $id_kriteria = $kriteria['id_kriteria'];
        // Fetch nilai from db
        $query4 = $pdo->prepare('SELECT nilai FROM nilai_karyawan
            WHERE id_karyawan = :id_karyawan AND id_kriteria = :id_kriteria');
        $query4->execute(array(
            'id_karyawan' => $id_karyawan,
            'id_kriteria' => $id_kriteria,
        ));
        $query4->setFetchMode(PDO::FETCH_ASSOC);
        if ($nilai_karyawan = $query4->fetch()) {
            // If there is a nilai for the kriteria
            $matriks_x[$id_kriteria][$id_karyawan] = $nilai_karyawan['nilai'];
        } else {
            $matriks_x[$id_kriteria][$id_karyawan] = 0;
        }
    }
}

// >>> STEP 2 Menampilkan Bobot (W)
// >>> STEP 3 Matriks Ternormalisasi (R)
$matriks_r = array();
foreach ($matriks_x as $id_kriteria => $nilai_karyawans) {
    $tipe = $list_kriteria[$id_kriteria]['type'];
    foreach ($nilai_karyawans as $id_alternatif => $nilai) {
        if ($tipe == 'benefit') {
            $nilai_normal = ($nilai - $nilai_min[$id_kriteria]) / ($nilai_max[$id_kriteria] - $nilai_min[$id_kriteria]);
        } elseif ($tipe == 'cost') {
            $nilai_normal = ($nilai_max[$id_kriteria] - $nilai) / ($nilai_max[$id_kriteria] - $nilai_min[$id_kriteria]);
        }
        $matriks_r[$id_kriteria][$id_alternatif] = $nilai_normal;
    }
}

// >>> STEP 4 Perangkingan
$ranks = array();
foreach ($karyawans as $karyawan) {
    $total_nilai = 0;
    foreach ($list_kriteria as $kriteria) {
        $bobot = $kriteria['bobot'];
        $id_karyawan = $karyawan['id_karyawan'];
        $id_kriteria = $kriteria['id_kriteria'];
        $nilai_r = $matriks_r[$id_kriteria][$id_karyawan];
        $total_nilai += ($bobot * $nilai_r);
    }
    $ranks[$karyawan['id_karyawan']]['id_karyawan'] = $karyawan['id_karyawan'];
    $ranks[$karyawan['id_karyawan']]['nama_karyawan'] = $karyawan['nama_karyawan'];
    $ranks[$karyawan['id_karyawan']]['nilai'] = $total_nilai;
}

?>
<div class="main-content-row">
    <div class="container clearfix">
        <div class="main-content main-content-full the-content">
            <h1><?php echo $judul_page; ?></h1>
            <!-- STEP 1. Matriks Keputusan(X) ==================== -->
            <h3>Step 1: Matriks Keputusan (X)</h3>
            <table class="pure-table pure-table-striped">
                <thead>
                    <tr class="super-top">
                        <th rowspan="2" class="super-top-left">Nama Karyawan</th>
                        <th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
                    </tr>
                    <tr>
                        <?php foreach ($kriterias as $kriteria) : ?>
                            <th><?php echo $kriteria['nama']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($karyawans as $karyawan) : ?>
                        <tr>
                            <td><?php echo $karyawan['nama_karyawan']; ?></td>
                            <?php
                            foreach ($kriterias as $kriteria) {
                                $id_karyawan = $karyawan['id_karyawan'];
                                $id_kriteria = $kriteria['id_kriteria'];
                                echo '<td>';
                                echo $matriks_x[$id_kriteria][$id_karyawan];
                                echo '</td>';
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- STEP 2. Bobot Preferensi (W) ==================== -->
            <h3>Step 2: Bobot Preferensi (W)</h3>
            <table class="pure-table pure-table-striped">
                <thead>
                    <tr>
                        <th>Nama Kriteria</th>
                        <th>Type</th>
                        <th>Bobot (W)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kriterias as $hasil) : ?>
                        <tr>
                            <td><?php echo $hasil['nama']; ?></td>
                            <td>
                                <?php
                                if ($hasil['type'] == 'benefit') {
                                    echo 'Benefit';
                                } elseif ($hasil['type'] == 'cost') {
                                    echo 'Cost';
                                }
                                ?>
                            </td>
                            <td><?php echo $hasil['bobot']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- STEP 3. Matriks Ternormalisasi (R) ==================== -->
            <h3>Step 3: Matriks Ternormalisasi (R)</h3>
            <table class="pure-table pure-table-striped">
                <thead>
                    <tr class="super-top">
                        <th rowspan="2" class="super-top-left">Nama Karyawan</th>
                        <th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
                    </tr>
                    <tr>
                        <?php foreach ($kriterias as $kriteria) : ?>
                            <th><?php echo $kriteria['nama']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($karyawans as $karyawan) : ?>
                        <tr>
                            <td><?php echo $karyawan['nama_karyawan']; ?></td>
                            <?php
                            foreach ($kriterias as $kriteria) {
                                $id_karyawan = $karyawan['id_karyawan'];
                                $id_kriteria = $kriteria['id_kriteria'];
                                echo '<td>';
                                echo round($matriks_r[$id_kriteria][$id_karyawan], $digit);
                                echo '</td>';
                            }
                            ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- STEP 4. Perangkingan ==================== -->
			<h3>Step 4: Perangkingan</h3>
            <table class="pure-table pure-table-striped">
                <thead>
                    <tr>
						<th class="super-top-left">Nama Karyawan</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($ranks as $rank) :
                    ?>
                        <tr>
							<td><?php echo $rank['nama_karyawan']; ?></td>
                            <td><?php echo round($rank['nilai'], $digit); ?></td>
                        </tr>
                    <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div><!-- .main-content .the-content -->
    </div><!-- .container -->
</div><!-- .main-content-row -->
<?php require_once('template/footer.php');
?>
