<?php

require_once('includes/init.php');

$judul_page = 'Perankingan Menggunakan Metode Fuzzy SAW';
require_once('template/header.php');

// Set jumlah digit di belakang koma
$digit = 5;

$query = $pdo->prepare('SELECT id_kriteria, nama, type, bobot
	FROM kriteria ORDER BY urutan_order ASC');
$query->execute();
$query->setFetchMode(PDO::FETCH_ASSOC);
$kriterias = $query->fetchAll();

$query2 = $pdo->prepare('SELECT id_karyawan, nama_karyawan FROM karyawan');
$query2->execute();
$query2->setFetchMode(PDO::FETCH_ASSOC);
$karyawans = $query2->fetchAll();


// >>> STEP 1 Matrix Keputusan (X)
$matriks_x = array();
$list_kriteria = array();
foreach($kriterias as $kriteria):
    $list_kriteria[$kriteria['id_kriteria']] = $kriteria;
	foreach($karyawans as $karyawan):
		$id_karyawan = $karyawan['id_karyawan'];
		$id_kriteria = $kriteria['id_kriteria'];
		// Fetch nilai dari db
		$query3 = $pdo->prepare('SELECT nilai FROM nilai_karyawan
			WHERE id_karyawan = :id_karyawan AND id_kriteria = :id_kriteria');
		$query3->execute(array(
			'id_karyawan' => $id_karyawan,
			'id_kriteria' => $id_kriteria,
		));			
		$query3->setFetchMode(PDO::FETCH_ASSOC);
		if($nilai_karyawan = $query3->fetch()) {
			// Jika ada nilai kriterianya
			$matriks_x[$id_kriteria][$id_karyawan] = $nilai_karyawan['nilai'];
		} else {			
			$matriks_x[$id_kriteria][$id_karyawan] = 0;
		}
	endforeach;
endforeach;

// >>> STEP 2 Menampilkan Bobot (W)
// >>> STEP 3 Matriks Ternormalisasi (R)
$matriks_r = array();
foreach($matriks_x as $id_kriteria => $nilai_karyawans):
	$tipe = $list_kriteria[$id_kriteria]['type'];
	foreach($nilai_karyawans as $id_alternatif => $nilai) {
		if($tipe == 'benefit') {
			$nilai_normal = $nilai / max($nilai_karyawans);
		} elseif($tipe == 'cost') {
			$nilai_normal = min($nilai_karyawans) / $nilai;
		}
		$matriks_r[$id_kriteria][$id_alternatif] = $nilai_normal;
	}
endforeach;

// >>> STEP 4 Matriks Normalisasi Terbobot (Y)
$matriks_y = array();
foreach($kriterias as $kriteria):
	foreach($karyawans as $karyawan):
		$bobot = $kriteria['bobot'];
		$id_karyawan = $karyawan['id_karyawan'];
		$id_kriteria = $kriteria['id_kriteria'];
		$nilai_r = $matriks_r[$id_kriteria][$id_karyawan];
		$matriks_y[$id_kriteria][$id_karyawan] = $bobot * $nilai_r;
	endforeach;
endforeach;

// >>> STEP 5 Solusi Ideal Positif & Negatif
$solusi_ideal_positif = array();
$solusi_ideal_negatif = array();
foreach($kriterias as $kriteria):
	$id_kriteria = $kriteria['id_kriteria'];
	$type = $kriteria['type'];
	$nilai_kriterias = array();
	foreach($karyawans as $karyawan):
		$nilai_y = $matriks_y[$id_kriteria][$karyawan['id_karyawan']];
		$nilai_kriterias[] = $nilai_y;
	endforeach;
	if($type == 'benefit') {
		$solusi_ideal_positif[$id_kriteria] = max($nilai_kriterias);
		$solusi_ideal_negatif[$id_kriteria] = min($nilai_kriterias);
	} else if($type == 'cost') {
		$solusi_ideal_positif[$id_kriteria] = min($nilai_kriterias);
		$solusi_ideal_negatif[$id_kriteria] = max($nilai_kriterias);
	}
endforeach;

// >>> STEP 6 Menghitung Fuzzy Preference Value
$fuzzy_preference = array();
foreach($karyawans as $karyawan):
	$id_karyawan = $karyawan['id_karyawan'];
	$fpv = 0;
	foreach($kriterias as $kriteria):
		$id_kriteria = $kriteria['id_kriteria'];
		$nilai_y = $matriks_y[$id_kriteria][$id_karyawan];
		$diff_positif = abs($nilai_y - $solusi_ideal_positif[$id_kriteria]);
		$diff_negatif = abs($nilai_y - $solusi_ideal_negatif[$id_kriteria]);
		$fpv += $diff_negatif / ($diff_positif + $diff_negatif);
	endforeach;
	$fuzzy_preference[$id_karyawan] = $fpv;
endforeach;

// >>> STEP 7 Menampilkan Hasil Perangkingan
$nilai_preferensi = array();
foreach($karyawans as $karyawan):
    $id_karyawan = $karyawan['id_karyawan'];
    $nilai_preferensi[$id_karyawan] = $fuzzy_preference[$id_karyawan];
endforeach;

// Mengurutkan array nilai preferensi dari yang tertinggi ke terendah
arsort($nilai_preferensi);

// Menentukan peringkat untuk setiap karyawan
$peringkat = array();
$ranking = 1;
foreach($nilai_preferensi as $id_karyawan => $nilai):
    $peringkat[$id_karyawan] = $ranking;
    $ranking++;
endforeach;

?>

<div class="main-content-row">
<div class="container clearfix">
	<div class="main-content main-content-full the-content">
    	<h1 class="mt-4"><?php echo $judul_page; ?></h1>
    	<!-- STEP 1. Matriks Keputusan(X) ==================== -->		
		<h3>Step 1: Matriks Keputusan (X)</h3>
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left">Nama Karyawan</th>
					<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
				</tr>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($karyawans as $karyawan): ?>
					<tr>
						<td><?php echo $karyawan['nama_karyawan']; ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_karyawan = $karyawan['id_karyawan'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo $matriks_x[$id_kriteria][$id_karyawan];
							echo '</td>';
						endforeach;
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
				<?php foreach($kriterias as $hasil): ?>
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
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<!-- Step 3: Matriks Ternormalisasi (R) ==================== -->
		<h3>Step 3: Matriks Ternormalisasi (R)</h3>			
		<table class="pure-table pure-table-striped">
			<thead>
				<tr class="super-top">
					<th rowspan="2" class="super-top-left">Nama Karyawan</th>
					<th colspan="<?php echo count($kriterias); ?>">Kriteria</th>
				</tr>
				<tr>
					<?php foreach($kriterias as $kriteria ): ?>
						<th><?php echo $kriteria['nama']; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach($karyawans as $karyawan): ?>
					<tr>
						<td><?php echo $karyawan['nama_karyawan']; ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_karyawan = $karyawan['id_karyawan'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo round($matriks_r[$id_kriteria][$id_karyawan], $digit);
							echo '</td>';
						endforeach;
						?>
					</tr>
				<?php endforeach; ?>				
			</tbody>
		</table>
		<!-- Step : Perangkingan ==================== -->
		<h3>Step : Perangkingan</h3>	
    	<table class="pure-table pure-table-striped">
        	<thead>
            	<tr>
                    <th class="super-top-left">Nama Karyawan</th>
            	    <th>Fuzzy Preference Value</th>
    	            <th>Peringkat</th>
    	        </tr>
    	    </thead>
	        <tbody>
            <?php 
            	$peringkat_terbaik = null;
        	    foreach($karyawans as $karyawan): 
                    $id_karyawan = $karyawan['id_karyawan'];
                    $ranking = $peringkat[$id_karyawan];
    	            if ($ranking === 1) {
                	    $peringkat_terbaik = $id_karyawan;
            	    }
        	    ?>
                <tr>
                    <td><?php echo $karyawan['nama_karyawan']; ?></td>
    	            <td><?php echo round($fuzzy_preference[$id_karyawan], $digit); ?></td>
                    <td><?php echo $ranking; ?></td>
            	</tr>
            	<?php endforeach; ?>
            	</tbody>
        </table>
	</div>
</div><!-- .container -->
</div><!-- .main-content-row -->

<?php
require_once('template/footer.php');
?>
