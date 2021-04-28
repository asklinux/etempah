<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<?php
$status_tempahan = "";
$status_label = "";
$status_food_label = "";
$status_equipment_label = "";

if ($booking->booking_status == 0) {
    $status_tempahan = 'Tempahan bilik belum diluluskan';
    $status_label = "info";
} elseif (($booking->booking_closed) && ($booking->booking_status > 0) && ($booking->booking_mode == $booking->booking_status)) {
    $status_tempahan = 'Tempahan bilik diluluskan';
    $status_label = "success";
} elseif ((!$booking->booking_closed) && ($booking->booking_status == 1) && ($booking->booking_mode != $booking->booking_status)) {
    $status_tempahan = 'Dalam proses';
    $status_label = "warning";
} elseif (($booking->booking_closed) && ($booking->booking_status > 0) && ($booking->booking_mode != $booking->booking_status)) {
    if ($booking->booking_status == 6):
        $status_tempahan = 'Tempahan bilik ditolak';
        $status_label = "important";
    elseif ($booking->booking_status == 8):
        $status_tempahan = 'Tempahan bilik dibatalkan';
        $status_label = "default";
    elseif ($booking->room_owner == 0):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_label = "success";

    // Bilik YES, Food No
    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 3)):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_food_label = 'important';
        $status_label = "success";

    // Bilik YES, Equipment No
    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 5)):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_equipment_label = 'important';
        $status_label = "success";

    // Bilik YES, Equipment No, Food No
    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 1)):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_food_label = 'important';
        $status_equipment_label = 'important';
        $status_label = "success";

    // Bilik YES, Equipment No, Food Yes
    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 3)):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_food_label = 'success';
        $status_equipment_label = 'important';
        $status_label = "success";

    // Bilik YES, Equipment Yes, Food No
    elseif (($booking->booking_mode > $booking->booking_status) && ($booking->booking_mode == 7) && ($booking->booking_status == 5)):
        $status_tempahan = 'Tempahan bilik diluluskan';
        $status_food_label = 'important';
        $status_equipment_label = 'success';
        $status_label = "success";
    endif;
}
?>

<fieldset id='booking-details'>
    <input type="hidden" id="id" name="id" value="<?php echo $booking->booking_id; ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $booking->fk_user_id; ?>">
    <input type="hidden" id="booking_ref_no" name="booking_ref_no" value="<?php echo $booking->booking_ref_no; ?>">
    <input type="hidden" id="booking_mode" name="booking_mode" value="<?php echo $booking->booking_mode; ?>">
    <input type="hidden" id="booking_closed" name="booking_closed" value="<?php echo $booking->booking_closed; ?>">
    <input type="hidden" id="booking_status" name="booking_status" value="<?php echo $booking->booking_status; ?>">
    <input type="hidden" id="room_owner" name="room_owner" value="<?php echo $booking->room_owner; ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label">Status Tempahan</label>
            <div class="control-label">
                <label class="align-left span7">
                    <span class="label label-<?php echo $status_label; ?>">
                        <i class="icon-info-sign"></i> <?php echo $status_tempahan; ?>
                    </span>
                    <?php if ($status_food_label): ?>
                        <span class="label label-<?php echo $status_food_label; ?>">
                            <i class="icon-info-sign"></i> <?php echo ($status_food_label == 'success') ? ("Tempahan makanan diluluskan") : ("Tempahan makanan ditolak"); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($status_equipment_label): ?>
                        <span class="label label-<?php echo $status_equipment_label; ?>">
                            <i class="icon-info-sign"></i> <?php echo ($status_equipment_label == 'success') ? ("Tempahan peralatan diluluskan") : ("Tempahan peralatan ditolak"); ?>
                        </span>
                    <?php endif; ?>
                </label>
            </div>
        </div>
        <?php if ($booking->booking_status_description): ?>
            <div class="control-group">
                <label class="control-label">Catatan</label>
                <div class="control-label">
                    <label class="align-left span4"><strong><?php echo ($booking->booking_status_description) ? $booking->booking_status_description : "Tiada" ?></strong></label>
                </div>
            </div>
        <?php endif; ?>
        <div class="control-group">
            <label class="control-label">Jenis Tempahan</label>
            <div class="control-label">
                <label class="align-left span4"><strong><?php echo ($booking->full_day) ? "Sepanjang Hari" : "Slot Masa" ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="booking_ref_no">No. Rujukan Tempahan</label>
            <div class="control-label">
                <label class="align-left span4" for="booking_ref_no"><strong><?php echo $booking->booking_ref_no; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Ditempah Pada</label>
            <div class="control-label">
                <label class="align-left span4" for="start_date"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->created)); ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Masa Mula</label>
            <div class="control-label">
                <label class="align-left span4" for="start_date"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->start_date)); ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="end_date">Masa Tamat</label>
            <div class="control-label">
                <label class="align-left span4" for="start_date"><strong><?php echo date('d/m/Y H:i a', strtotime($booking->end_date)); ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="room_id">Nama Bilik</label>
            <div class="control-label">
                <label class="align-left span4" for="start_date"><strong><?php echo isset($room_list[$booking->fk_room_id]) ? $room_list[$booking->fk_room_id] : 'Bilik tidak wujud'; ?></strong></label>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="room_id">Penyelia Bilik</label>
            <div class="control-label">
                <?php if ($owner): ?>
                    <label class="align-left span3"><strong><?php echo $owner->full_name; ?> 
                            <a href="<?php echo "mailto:{$owner->email}"; ?>"><?php echo "{$owner->email}"; ?></a>, <?php echo "{$owner->contact_office}" ?></strong></label>
                <?php else: ?>
                    <label class="align-left span3"><strong>Pentadbir Utama</strong></label>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#mesyuarat">Maklumat Mesyuarat</a></li>

        <?php $food_visible = empty($booking->food_type_ids) ? 'hide' : ''; ?>
        <li class="<?php echo $food_visible; ?>"><a href="#makanan">Maklumat Tempahan Makanan</a></li>

        <?php $equipment_visible = empty($booking->equipment_list) ? 'hide' : ''; ?>
        <li class="<?php echo $equipment_visible; ?>"><a href="#peralatan">Maklumat Tempahan Peralatan</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="mesyuarat">
            <div class="control-group">
                <label class="control-label" for="room_purpose">Nama Mesyuarat</label>
                <div class="control-label">
                    <label class="align-left span4" for="start_date"><strong><?php echo $booking->room_purpose; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="secretariat">Urusetia</label>
                <div class="control-label">
                    <label class="align-left span4" for="secretariat"><strong><?php echo $booking->secretariat; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="chairman">Pengerusi</label>
                <div class="control-label">
                    <label class="align-left span4" for="chairman"><strong><?php echo ($booking->chairman) ? $booking->chairman : 'Tiada'; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="total_from_agensi">Bil. Pegawai Agensi</label>
                <div class="control-label">
                    <label class="align-left span4" for="total_from_agensi"><strong><?php echo $booking->total_from_agensi; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="total_from_nonagensi">Bil. Pegawai Luar</label>
                <div class="control-label">
                    <label class="align-left span4" for="total_from_nonagensi"><strong><?php echo $booking->total_from_nonagensi; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="description">Penerangan</label>
                <div class="control-label">
                    <label class="align-left span4" for="description"><strong><?php echo!empty($booking->description) ? $booking->description : "Tiada"; ?></strong></label>
                </div>
            </div>
        </div>

        <?php $food_visible = empty($booking->food_type_ids) ? 'hide' : ''; ?>
        <div class="tab-pane fade <?php echo $food_visible; ?>" id="makanan">
            <div class="control-group">
                <label class="control-label">Jenis Tempahan</label>
                <div class="control-label">

                    <?php
                    $food_type_list = '';
                    if (!empty($booking->food_type_ids)):
                        $selected_food_types = explode(',', $booking->food_type_ids);

                        if (is_array($selected_food_types)):
                            foreach ($selected_food_types as $key => $val) {
                                $food_type_list[] = $food_types[$val];
                            }

                            $food_type_list = implode(", ", $food_type_list);
                        else:
                            $food_type_list = $food_types[$booking->food_type_ids];
                        endif;
                    endif;
                    ?>

                    <label class="align-left span4" for="total_from_nonagensi"><strong><?php echo set_value('booking_food_ids', $food_type_list); ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="total_pack">Jumlah Pegawai</label>
                <div class="control-label">
                    <label class="align-left span4" for="total_from_nonagensi"><strong><?php echo set_value('total_pack', !empty($booking->total_pack) ? $booking->total_pack : '' ); ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="booking_food_description">Catatan</label>
                <div class="control-label">
                    <label class="align-left span4" for="total_from_nonagensi"><strong><?php echo set_value('booking_food_description', !empty($booking->booking_food_description) ? $booking->booking_food_description : 'Tiada'); ?></strong></label>
                </div>
            </div>
        </div>

        <?php $equipment_visible = empty($booking->equipment_list) ? 'hide' : ''; ?>
        <div class="tab-pane fade <?php echo $equipment_visible; ?>" id="peralatan">
            <div class="control-group">
                <label class="control-label" for="equipments_type_id">Peralatan</label>
                <div class="controls">

                    <?php
                    $show_deleted_msg = false;

                    $equipment_list_tmp = $booking->equipment_list;
                    $equipment_list_tmp = explode(',', $equipment_list_tmp);

                    foreach ($equipment_list_tmp as $equipment_to_val_tmp):
                        list($key, $val) = explode(':', $equipment_to_val_tmp);
                        $equipment_list[$key] = $val;
                    endforeach;
                    ?>

                    <?php foreach ($equipments as $equipment): ?>
                        <?php if (isset($equipment_list[$equipment->equipment_id]) && $equipment_list[$equipment->equipment_id] != 0): ?>
                            <?php
                            if ($equipment->deleted):
                                $show_deleted_msg = true;
                            endif;
                            ?>
                            <div class="input-prepend input-append">
                                <span class="add-on width1-add-on"><?php echo ($equipment->deleted) ? '<i class="icon-ban-circle color-red"></i> ' : ''; ?><?php echo $equipment->name ?></span>
                                <input readonly class="span1" type="text" value="<?php echo $equipment_list[$equipment->equipment_id] ?> unit">
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($show_deleted_msg): ?>
                        <p class="help-block">Peralatan dengan tanda <i class="icon-ban-circle color-red"></i> sudah tidak disediakan lagi.</p>    
                    <?php endif; ?>

                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="require_technical_person">Bantuan Teknikal</label>
                <div class="controls">
                    <label class="checkbox">

                        <?php $set_selected_require_technical = ($booking->require_technical_person) ? 'checked' : ''; ?>

                        <input disabled <?php echo $set_selected_require_technical; ?> type="checkbox" name="require_technical_person" id="require_technical_person" value="1">
                    </label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="booking_equipment_description">Catatan</label>
                <div class="control-label">
                    <label class="align-left span4" for="description"><strong><?php echo!empty($booking->booking_equipment_description) ? $booking->booking_equipment_description : "Tiada"; ?></strong></label>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (!empty($booking->room_owner) && ($booking->room_owner == $this->tank_auth->get_user_id()) && $this->tank_auth->is_subadmin()):
        $first_process_visibility = "";
        $second_process_visibility = "disabled";
    elseif (empty($booking->room_owner)):
        $first_process_visibility = "disabled";
        $second_process_visibility = "";
    endif;
    ?>
    <div class="form-actions">

        <?php if (!in_array($booking->booking_status, array(6, 8)) && (($this->tank_auth->is_admin() || $this->tank_auth->is_subadmin()) || ($booking->fk_user_id == $this->tank_auth->get_user_id()))): ?>
            <div class="control-group">
                <label class="control-label" for="name">Status Tempahan <?php echo ($booking->booking_mode > 1) ? "Bilik" : ""; ?></label>
                <div class="controls margin-bottom10">

                    <?php
                    // TODO: check if sudah lulus tapi current logged in bukan owner tetapi admin
                    $room_approve_visibility = "";
                    if ($this->tank_auth->is_admin() && ($booking->room_owner != 0)) {
                        $room_approve_visibility = "disabled";
                    }
                    ?>

                    <select id="approve_room" name="approve_room" class="span2" <?php echo $room_approve_visibility; ?>>
                        <?php
                        if (
                                in_array($booking->booking_status, array(0, 3, 5, 7)) ||
                                ( (($booking->booking_status == $booking->booking_mode) && ($booking->booking_mode > 1)) || (($booking->booking_status == 1) && ($this->tank_auth->get_user_id() == $booking->fk_user_id)))
                        ):
                            ?>
                            <option value="">-- Sila Pilih</option>
                        <?php endif; ?>

                        <?php if ($this->tank_auth->is_admin() || $this->tank_auth->is_subadmin()): ?>
                            <?php if (in_array($booking->booking_status, array(0, 1, 3, 5, 7))): ?>
                                <option value="1" <?php echo (in_array($booking->booking_status, array(1, 3, 5, 7))) ? "selected" : "" ?>>Lulus</option>
                            <?php endif; ?>

                            <?php if (($booking->booking_status < 2) && ($booking->booking_status == 0)): ?>
                                <option value="6" <?php echo ($booking->booking_status == 6) ? "selected" : "" ?>>Tolak</option>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if (in_array($booking->booking_status, array(0, 1, 3, 5, 7, 8))): ?>
                            <option value="8" <?php echo ($booking->booking_status == 8) ? 'selected' : ''; ?> >Batal</option>    
                        <?php endif; ?>

                    </select> 

                    <?php $badge_style = ($booking->booking_status == 1) ? "badge-success" : "badges-important"; ?>

                    <?php if ($this->tank_auth->is_admin() && (in_array($booking->booking_status, array(0))) && ($booking->room_owner != 0)): ?>
                        <p class="help-block"><i>(Untuk tindakan Pentadbir Bahagian)</i></p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="booking_status_description_field" class="control-group">
                <label class="control-label" for="booking_status_description">Catatan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="booking_status_description" name="booking_status_description"><?php echo set_value('booking_status_description', $booking->booking_status_description); ?></textarea>
                </div>
            </div>

            <?php if ($booking->booking_food_id && ( ($this->tank_auth->is_admin() && ($booking->room_owner != 0)) || $this->tank_auth->is_subadmin())): ?>
                <div class="control-group margin-bottom10">
                    <label class="control-label" for="name">Status Tempahan Makanan</label>
                    <div class="controls">
                        <?php
                        $food_approve_visibility = '';
                        $food_display = '';
                        if (!$this->tank_auth->is_admin()) {
                            $food_approve_visibility = '';
                            $food_display = '';
                        } elseif (!$booking->booking_status) {
                            $food_approve_visibility = 'disabled';
                            $food_display = 'hide';
                        }

                        if ($booking->booking_closed == 1):
                            $food_approve_visibility = 'disabled';
                            $food_display = 'hide';
                        endif;
                        ?>
                        <select id="approve_food" name="approve_food" class="span2" <?php echo $food_approve_visibility; ?>>
                            <?php if ($booking->booking_closed <= 1): ?>
                                <option value="">-- Sila Pilih</option>
                            <?php endif; ?>
                            <?php if (in_array($booking->booking_status, array(0,5,1,3,7))): ?>
                                <option value="2" <?php echo ($booking->booking_status >= 3) ? "selected" : "" ?>>Lulus</option>
                            <?php endif; ?>
                            <?php if (!in_array($booking->booking_status, array(6))): ?>
                                <option value="6" <?php echo ($booking->booking_status == 6) ? "selected" : "" ?>>Tolak</option>
                                <option value="6" <?php echo ($booking->booking_status == 6) ? "selected" : "" ?>>Lulus Separa</option>
                            <?php endif; ?>
                        </select>
                        <?php if (!$this->tank_auth->is_admin() && !$booking->booking_closed): ?>
                            <!-- <p class="help-block"><i>(Untuk tindakan Pentadbir Utama)</i></p> -->
                        <?php else: ?>
                            <span class="label label-important validate-required <?php echo $food_display; ?>">Wajib</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->booking_equipment_id && ( ($this->tank_auth->is_admin() && ($booking->room_owner != 0)) || $this->tank_auth->is_subadmin())): ?>
                <div class="control-group margin-bottom10">
                    <label class="control-label" for="name">Status Tempahan Peralatan</label>
                    <div class="controls">
                        <?php
                        $equipment_approve_visibility = '';
                        $equipment_display = "";
                        if (!$this->tank_auth->is_admin()) {
                            $equipment_approve_visibility = 'disabled';
                            $equipment_display = "hide";
                        } elseif (!$booking->booking_status) {
                            $equipment_approve_visibility = 'disabled';
                            $equipment_display = "hide";
                        }

                        if ($booking->booking_closed == 1):
                            $equipment_approve_visibility = 'disabled';
                            $equipment_display = "hide";
                        endif;
                        ?>
                        <select id="approve_equipment" name="approve_equipment" class="span2" <?php echo $equipment_approve_visibility; ?>>
                            <?php if ($booking->booking_closed <= 1): ?>
                                <option value="">-- Sila Pilih</option>
                            <?php endif; ?>
                            <?php if (in_array($booking->booking_status, array(1, 5, 7))): ?>
                                <option value="4" <?php echo ($booking->booking_status == $booking->booking_mode) ? "selected" : "" ?>>Lulus</option>
                            <?php endif; ?>
                            <?php if (!in_array($booking->booking_status, array(0, 6))): ?>
                                <option value="6" <?php echo ($booking->booking_status == 6) ? "selected" : "" ?>>Tolak</option>
                            <?php endif; ?>
                        </select>
                        <?php if (!$this->tank_auth->is_admin() && !$booking->booking_closed): ?>
                            <p class="help-block"><i>(Untuk tindakan Pentadbir Utama)</i></p>
                        <?php else: ?>
                            <span class="label label-important validate-required <?php echo $equipment_display; ?>">Wajib</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            <br/>
        <?php endif; ?>

        <div class="control-group">
            <div class="controls">
                <?php echo anchor("booking_rooms", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                <?php if (!in_array($booking->booking_status, array(6, 8)) && (($this->tank_auth->is_admin() || $this->tank_auth->is_subadmin()) || ($booking->fk_user_id == $this->tank_auth->get_user_id()))): ?>
                    <input id="submit" type="submit" class="btn btn-primary <?php echo (isset($food_display) && !empty($food_display) && ($booking->booking_mode > 1) && (!$this->tank_auth->is_subadmin())) ? $food_display : ''; ?>" value="Simpan"/>
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
        <p>Adakah anda pasti untuk teruskan?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <?php echo anchor("booking_foods/view/{$booking->booking_id}", 'Teruskan', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        if ($('select').val() == 6 || $('select').val() == 8) {
            $("#booking_status_description_field").show();
        }
        else {
            $("#booking_status_description_field").hide();
        }
        
        $('select').change(function() {
            if ($(this).val() == 6 || $(this).val() == 8) {
                $("#booking_status_description_field").fadeIn();
            }
            else {
                $("#booking_status_description_field").fadeOut();
            }
        });
    });
</script>