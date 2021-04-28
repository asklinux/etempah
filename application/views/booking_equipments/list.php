<?php echo anchor('booking_equipments/create', '<i class="icon-plus-sign"></i> Tambah Rekod', 'class="btn"'); ?> 
<?php
if ($this->tank_auth->is_admin()) {
    //echo anchor('#deleteModal', '<i class="icon-trash"></i> Hapus Rekod', 'id="delete-list" class="btn disabled" data-toggle="modal"');
}
?>

<div class="btn-group pull-right">
    <a href="<?php echo site_url(); ?>booking_equipments" class="btn">Papar Semua</a>
    <button data-toggle="dropdown" class="btn dropdown-toggle"><span class="caret"></span></button>
    <ul class="dropdown-menu">
        <li><a href="<?php echo site_url(); ?>booking_equipments/index/not-approved-yet"><span class="label label-info"><i class="icon-exclamation-sign"></i></span> Belum Lulus</a></li>
        <li><a href="<?php echo site_url(); ?>booking_equipments/index/approved"><span class="label label-success"><i class="icon-ok-sign"></i></span> Telah Lulus</a></li>
        <li><a href="<?php echo site_url(); ?>booking_equipments/index/rejected"><span class="label label-important"><i class="icon-remove-sign"></i></span> Ditolak</a></li>
        <li><a href="<?php echo site_url(); ?>booking_equipments/index/cancelled"><span class="label"><i class="icon-remove-sign"></i></span> Batal</a></li>
    </ul>
</div>

<br/><br/>

<table class="table table-bordered table-striped table-hover">
    <tr>
        <td>
            Petunjuk:            
            <a href="<?php echo site_url(); ?>booking_equipments/index/not-approved-yet"><span class="label label-info"><i class="icon-exclamation-sign"></i></span> Belum Lulus</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_equipments/index/approved"><span class="label label-success"><i class="icon-ok-sign"></i></span> Telah Lulus</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_equipments/index/rejected"><span class="label label-important"><i class="icon-remove-sign"></i></span> Ditolak</a>&nbsp;
            <a href="<?php echo site_url(); ?>booking_equipments/index/cancelled"><span class="label"><i class="icon-remove-sign"></i></span> Batal</a>
        </td>
    </tr>
</table>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <?php if ($this->tank_auth->is_admin()): ?>
<!--                <th width="5%"></th>-->
            <?php endif; ?> 
            <th width="25%">Tempat</th>
            <th width="42%">Maklumat Tempahan</th>
            <th width="20%">Tarikh</th>
            <th width="8%">Status</th>
            <th class="hide">Tindakan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $booking): ?>

                <tr data-provides="rowlink">
<!--                    <td class="nolink"><input type="checkbox" name="delete[<?php echo $booking->booking_id; ?>]" value="<?php echo $booking->booking_id; ?>"></td>-->
                    <td><?php echo $booking->place; ?></td>
                    <td>

                        <strong>Peralatan:</strong><br/>
                        <?php
                        $selected_equipments = explode(',', $booking->equipment_list);
                        $equipment_list = array();
                        foreach ($selected_equipments as $selected_equipment):
                            foreach ($equipments as $equipment):
                                list($equipment_id, $the_total) = explode(':', $selected_equipment);
                                if (($equipment_id == $equipment->equipment_id) && $the_total != 0):
                                    $equipment_list[] = $equipment->name . " ($the_total unit)";
                                endif;
                            endforeach;
                        endforeach;

                        $equipment_list = implode("<br>", $equipment_list);
                        echo $equipment_list;
                        ?>
                        <br/><strong>Bantuan Teknikal:</strong> <?php echo ($booking->require_technical_person) ? "Ya" : "Tidak"; ?>

                    </td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($booking->start_date)); ?>
                        </br>
                        <?php echo date('H:ia', strtotime($booking->start_date)); ?> - <?php echo date('H:ia', strtotime($booking->end_date)); ?>
                    </td>
                    <td style="text-align:center">
                        <?php
                        if ($booking->booking_status == 0):
                            echo '<span class="label label-big label-info"><i class="icon-exclamation-sign"></i></span>';
                        elseif ($booking->booking_status == 1):
                            echo '<span class="label label-big label-success"><i class="icon-ok-sign"></i></span>';
                        elseif ($booking->booking_status == 6):
                            echo '<span class="label label-big label-important"><i class="icon-remove-sign"></i></span>';
                        elseif ($booking->booking_status == 8):
                            echo '<span class="label label-big"><i class="icon-remove-sign"></i></span>';
                        endif;
                        ?>
                    </td>
                    <td class="hide">
                        <?php echo anchor("booking_equipments/view/{$booking->booking_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
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
        <?php echo form_open('booking_equipments/delete', 'id="form-delete-list"'); ?>
        <input type="hidden" name="ids"/>
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <input type="submit" class="btn btn-primary" value="Hapus Rekod"/>
        <?php echo form_close(); ?>
    </div>
</div>