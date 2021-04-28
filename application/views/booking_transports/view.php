<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<?php
$status_tempahan = "";
$status_label = "";
if ($booking->booking_status == 0) {
    $status_tempahan = 'Tempahan belum diluluskan';
    $status_label = "info";
} elseif ($booking->booking_status == 1) {
    $status_tempahan = 'Tempahan diluluskan';
    $status_label = "success";
} elseif ($booking->booking_status == 6) {
    $status_tempahan = 'Tempahan ditolak';
    $status_label = "important";
} elseif ($booking->booking_status == 8) {
    $status_tempahan = 'Tempahan dibatalkan';
    $status_label = "default";
}
?>

<fieldset id='booking-details'>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($booking) ? $booking->booking_id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">
    <input type="hidden" id="booker" name="booker" value="<?php echo $booking->fk_user_id; ?>">
    <input type="hidden" id="booking_ref_no" name="booking_ref_no" value="<?php echo $booking->booking_ref_no; ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label">Status Tempahan</label>
            <div class="control-label">
                <label class="align-left span4">
                    <span class="label label-<?php echo $status_label; ?>"><i class="icon-info-sign"></i> <?php echo ($status_tempahan); ?></span>
                </label>
            </div>
        </div>
        <?php if ($booking->booking_status_description): ?>
            <div class="control-group">
                <label class="control-label"><?php echo ($booking->booking_status == 1) ? "Pemandu/Kenderaan" : "Catatan"; ?></label>
                <div class="control-label">
                    <label class="align-left span4"><strong><?php echo ($booking->booking_status_description) ? $booking->booking_status_description : "Tiada" ?></strong></label>
                </div>
            </div>
        <?php endif; ?>
        <div class="control-group">
            <label class="control-label" for="booking_ref_no">No. Rujukan Tempahan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->booking_ref_no; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Ditempah Pada</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->created)); ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="trip_type">Jenis Perjalanan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $trip_types[$booking->trip_type]; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Masa Pergi</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->start_date)); ?></strong></label>
            </div>
        </div>
        <div id="trip_type_extended">
            <div class="control-group">
                <label class="control-label" for="end_date">Masa Balik</label>
                <div class="control-label">
                    <label class="align-left span4"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->end_date)); ?></strong></label>
                </div>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="secretariat">Ketua Perjalanan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->secretariat; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="destination_from">Dari</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->destination_from; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="destination_to">Hingga</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->destination_to; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="transport_purpose">Tujuan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->transport_purpose; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="total_passenger">Bilangan Pegawai</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo $booking->total_passenger; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="booking_transport_description">Penerangan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo!empty($booking->booking_transport_description) ? $booking->booking_transport_description : "Tiada"; ?></strong></label>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <?php if (!in_array($booking->booking_status, array(6, 8)) && (($this->tank_auth->is_admin() || $this->tank_auth->is_headdriver()) || ($booking->fk_user_id == $this->tank_auth->get_user_id()))): ?>
            <div class="control-group">
                <label class="control-label" for="name">Status Tempahan</label>
                <div class="controls margin-bottom10">
                    <select id="booking_status" name="booking_status" class="span2">
                        <option value="">-- Sila Pilih</option>

                        <?php if ($this->tank_auth->is_admin() || $this->tank_auth->is_headdriver()): ?>
                            <?php if (!in_array($booking->booking_status, array(6, 8))): ?>
                                <option value="1" <?php echo ($booking->booking_status == 1) ? 'selected' : ''; ?> >Lulus</option>
                            <?php endif; ?>
                            <?php if (!in_array($booking->booking_status, array(1))): ?>
                                <option value="6" <?php echo ($booking->booking_status == 2) ? 'selected' : ''; ?> >Tolak</option>
                            <?php endif; ?>
                        <?php endif; ?>

                        <option value="8" <?php echo ($booking->booking_status == 3) ? 'selected' : ''; ?> >Batal</option>
                    </select>
                </div>
            </div>
            <div id="booking_status_description_field" class="control-group">
                <label class="control-label" for="booking_status_description" id="booking_status_description_label">Catatan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="booking_status_description" name="booking_status_description"><?php echo set_value('booking_status_description', $booking->booking_status_description); ?></textarea>
                </div>
            </div>
            <br/>
        <?php endif; ?>
        <div class="control-group">
            <div class="controls">
                <?php echo anchor("booking_transports", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                <?php if (!in_array($booking->booking_status, array(6, 8)) && (($this->tank_auth->is_admin() || $this->tank_auth->is_headdriver()) || ($booking->fk_user_id == $this->tank_auth->get_user_id()))): ?>
                    <input id="" type="submit" class="btn btn-primary" value="Simpan"/>
                <?php endif; ?>
            </div>
        </div>
    </div>
</fieldset>
<?php echo form_close(); ?>

<div id="deleteItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <?php echo anchor("transports/delete/{$booking->booking_id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {        
        if ($('select').val() == 1 || $('select').val() == 6 || $('select').val() == 8) {
            if ($('select').val() == 1){
                $('#booking_status_description_label').html('Pemandu/Kenderaan');
            }
            else{
                $('#booking_status_description_label').html('Catatan');
            }
            $("#booking_status_description_field").show();
        }
        else {
            $("#booking_status_description_field").hide();
        }
        
        $('select').change(function() {
            if ($('select').val() == 1 || $(this).val() == 6 || $(this).val() == 8) {
                if ($('select').val() == 1){
                    $('#booking_status_description_label').html('Pemandu/Kenderaan');
                }
                else{
                    $('#booking_status_description_label').html('Catatan');
                }
                $("#booking_status_description_field").fadeIn();
            }
            else {
                $("#booking_status_description_field").fadeOut();
            }
        });
    });
</script>