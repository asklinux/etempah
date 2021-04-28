<?php echo anchor('booking_rooms/create', '<i class="icon-plus-sign"></i> Tambah Rekod', 'class="btn"'); ?>

<?php
if ($this->tank_auth->is_admin()) {
    //echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"');
}
?>

<div class="btn-group pull-right">
    <a href="<?php echo site_url(); ?>booking_rooms" class="btn">Papar Semua</a>
    <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="caret"></span></button>
    <ul class="dropdown-menu">
        <li><a href="<?php echo site_url(); ?>booking_rooms/index/not-approved-yet"><span class="label label-info"><i class="icon-exclamation-sign"></i></span> Belum Lulus</a></li>
        <li><a href="<?php echo site_url(); ?>booking_rooms/index/processing"><span class="label label-warning"><i class="icon-ok-sign"></i></span> Dalam Proses</a></li>
        <li><a href="<?php echo site_url(); ?>booking_rooms/index/approved"><span class="label label-success"><i class="icon-ok-sign"></i></span> Telah Lulus</a></li>
        <li><a href="<?php echo site_url(); ?>booking_rooms/index/rejected"><span class="label label-important"><i class="icon-remove-sign"></i></span> Ditolak</a></li>
        <li><a href="<?php echo site_url(); ?>booking_rooms/index/cancelled"><span class="label"><i class="icon-remove-sign"></i></span> Batal</a></li>
    </ul>
</div>

<br/><br/>

<table class="table table-bordered table-striped table-hover">
    <tr>
        <td>
            Petunjuk:            
            <a href="<?php echo site_url(); ?>booking_rooms/index/not-approved-yet"><span class="label label-info"><i class="icon-exclamation-sign"></i></span> Belum Lulus</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_rooms/index/processing"><span class="label label-warning"><i class="icon-ok-sign"></i></span> Dalam Proses</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_rooms/index/approved"><span class="label label-success"><i class="icon-ok-sign"></i></span> Telah Lulus</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_rooms/index/rejected"><span class="label label-important"><i class="icon-remove-sign"></i></span> Ditolak</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_rooms/index/cancelled"><span class="label"><i class="icon-remove-sign"></i></span> Batal</a>
        </td>
    </tr>
</table>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <?php if ($this->tank_auth->is_admin()): ?>
                                        <!--                <th width="5%"></th>-->
            <?php endif; ?> 
            <th width="25%">Nama Bilik</th>
            <th width="42%">Maklumat Tempahan</th>
            <th width="20%">Tarikh</th>
            <th width="8%">Status</th>
            <th class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>

        <?php if (!empty($bookings)): ?>
            <?php $current_location = ''; ?>
            <?php foreach ($bookings as $booking): ?>

                <?php
                $sign_display = '';
                $no_food = '';
                $no_equipment = '';
                if ($booking->booking_status == 0) {
                    $sign_display = '<span class="label label-big label-info"><i class="icon-exclamation-sign"></i></span>';
                } elseif (($booking->booking_closed) && ($booking->booking_status > 0) && ($booking->booking_mode == $booking->booking_status)) {
                    $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                } elseif ((!$booking->booking_closed) && ($booking->booking_status == 1) && ($booking->booking_mode != $booking->booking_status)) {
                    $sign_display = '<span class="label label-big label-warning"><i class="icon-ok-sign"></i></span>';
                } elseif (($booking->booking_closed) && ($booking->booking_status > 0) && ($booking->booking_mode != $booking->booking_status)) {
                    if ($booking->booking_status == 6):
                        $sign_display = '<span class="label label-big label-important"><i class="icon-remove-sign"></i></span>';
                    elseif ($booking->booking_status == 8):
                        $sign_display = '<span class="label label-big"><i class="icon-remove-sign"></i></span>';
                        $status_label = "default";
                    elseif ($booking->room_owner == 0):
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 3)): // makanan ditolak
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        $no_food = 'crossline';
                    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 5)): // peralatan ditolak
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        $no_equipment = 'crossline';
                    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 1)): // makanan dan peralatan ditolak
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        $no_food = 'crossline';
                        $no_equipment = 'crossline';
                    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 3)): // peralatan shj ditolak
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        $no_equipment = 'crossline';
                    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 5)): // makanan shj ditolak
                        $sign_display = '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        $no_food = 'crossline';
                    endif;
                }
                ?>

                <tr data-provides="rowlink">

                    <?php if ($this->tank_auth->is_admin()): ?>
<!--                        <td class="nolink"><input type="checkbox" name="delete[<?php echo $booking->booking_id; ?>]" value="<?php echo $booking->booking_id; ?>"></td>-->
                    <?php endif; ?> 

                    <td><?php echo "{$booking->room_name}<br/>{$booking->floor} {$booking->building}, {$booking->location} "; ?></td>
                    <td>
                        <strong>Tujuan:</strong>
                        <?php echo $booking->room_purpose; ?>
                        <br/><strong>Urusetia:</strong> <?php echo $booking->secretariat; ?>
                        <?php
                        if (!empty($booking->chairman)):
                            echo "<br/><strong>Pengerusi:</strong> {$booking->chairman}";
                        endif;
                        ?>
                        <br/><strong>Tempahan Makanan: </strong><?php echo (!empty($booking->fk_booking_food_id)) ? (!empty($no_food) ? "Ditolak" : "Ada") : "Tiada"; ?>
                        <br/><strong>Tempahan Peralatan: </strong><?php echo (!empty($booking->fk_booking_equipment_id)) ? (!empty($no_equipment) ? "Ditolak" : "Ada") : "Tiada"; ?>
                    </td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($booking->start_date)); ?>
                        <?php if ($booking->full_day == '2'): ?>
                            <br>(Sepanjang hari)
                        <?php else: ?>
                            </br>
                            <?php echo date('H:ia', strtotime($booking->start_date)); ?> - <?php echo date('H:ia', strtotime($booking->end_date)); ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center">
                        <?php echo $sign_display; ?>
                    </td>
                    <td class="hide">
                        <?php echo anchor("booking_rooms/view/{$booking->booking_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                    </td>
                </tr>

            <?php endforeach; ?>
        <?php else: ?>

            <tr>
                <td colspan="8">Tiada senarai dijumpai.</td>
            </tr>

        <?php endif; ?>

    </tbody>
</table>

<?php echo $pagination; ?>

<?php if (!empty($bookings)): ?>
    <div class="custom-legend"><?php echo $this->pagination->create_pagination_text(); ?></div>
<?php endif; ?>
<br/><br/>

<div id="deleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <?php echo form_open('booking_rooms/delete', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>