
<p>Pengguna <?php echo $display_name ?> telah membuat tempahan bilik seperti berikut:</p>

<table width="80%" border="0">
    <tr><td width="150">Tarikh Mula</td><td>:</td><td><?php echo $start_date ?></td></tr>
    <tr><td>Tarikh Tamat</td><td>:</td><td></td><?php echo $end_date ?></tr>
    <tr><td>Nama Bilik</td><td>:</td><td><?php echo $fk_room_id ?></td></tr>
    <tr><td>Jenis Tempahan</td><td>:</td><td><?php echo $full_day ?></td></tr>

    <tr><td>Nama Mesyuarat</td><td>:</td><td><?php echo $room_purpose ?></td></tr>
    <tr><td>Urusetia</td><td>:</td><td><?php echo $secretariat ?></td></tr>
    <tr><td>Pengerusi</td><td>:</td><td><?php echo $chairman ?></td></tr>
    <tr><td>Bil. Pegawai Agensi</td><td>:</td><td><?php echo $total_from_agensi ?></td></tr>
    <tr><td>Bil. Pegawai Luar</td><td>:</td><td><?php echo $total_from_nonagensi ?></td></tr>
    <tr><td>Penerangan</td><td>:</td><td><?php echo $description ?></td></tr>

    <tr><td>Tempahan Makanan</td><td>:</td><td><?php echo isset($food) ? "Ada" : "Tiada"; ?></td></tr>
    <tr><td>Tempahan Peralatan</td><td>:</td><td><?php echo isset($equipment) ? "Ada" : "Tiada"; ?></td></tr>
</table>