<?php echo form_open($this->uri->uri_string()); ?>

<fieldset>
    <div class="input-append">
        <input autocomplete="off" name="search_text"  id="search_text" class="span3" id="appendedInputButtons" type="text" maxlength="255">
        <select name="search_filter" id="search_filter" class="span3" style="margin-left:-1px; border-radius:0;">
            <option value="1">Nama Mesyuarat</option>  
            <option value="2">No Rujukan Tempahan</option>
        </select>
        <button class="btn" type="submit"><i class="icon-search" style="font-size:1.2em;"></i></button>
    </div>
</fieldset>

<?php echo form_close(); ?>

<div class="alert alert-error search-error hide">
    Sila masukkan sekurang-kurangnya 3 aksara.
</div>

<?php if ($this->input->post() && count($result_rooms) > 0): ?>
    <label>Hasil carian bagi - <strong><?php echo $this->input->post('search_text'); ?></strong> - ialah <?php echo count($result_rooms); ?> rekod.</label>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th width="20%">Nama Bilik</th>
                <th width="23%">Nama Mesyuarat</th>
                <th width="15%" class="hidden-tablet hidden-phone">Urusetia</th>
                <th width="10%">Lain-lain</th>
                <th width="17%">Tarikh</th>
                <th width="10%">Tindakan</th>
            </tr>
        </thead>
        <tbody>

            <?php if (!empty($result_rooms)): ?>
                <?php $current_location = ''; ?>
                <?php foreach ($result_rooms as $result_room): ?>          

                    <tr>
                        <td><?php echo $result_room->room_name; ?></td>
                        <td><?php echo $result_room->room_purpose; ?></td>
                        <td class="hidden-tablet hidden-phone"><?php echo $result_room->secretariat; ?></td>
                        <td>
                            <?php
                            if (!empty($result_room->fk_booking_equipment_id) && !empty($result_room->fk_booking_food_id)):
                                echo "Makanan<br>Peralatan";
                            elseif (!empty($result_room->fk_booking_food_id)):
                                echo "Makanan";
                            elseif (!empty($result_room->booking_fk_equipment_id)):
                                echo "Peralatan";
                            else:
                                echo "Tiada";
                            endif;
                            ?>
                        </td>
                        <td>
                            <?php echo date('d/m/Y', strtotime($result_room->start_date)); ?>
                            <?php if ($result_room->full_day): ?>
                                <br>(Sepanjang hari)
                            <?php else: ?>
                                </br>
                                <?php echo date('H:ia', strtotime($result_room->start_date)); ?> - <?php echo date('H:ia', strtotime($result_room->end_date)); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo anchor("booking_rooms/view/{$result_room->booking_id}", '<i class="icon-edit"></i>', 'class="btn btn-mini"'); ?>
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
<?php elseif ($this->input->post() && empty($result_rooms)): ?>
    <label>Carian anda - <strong><?php echo $this->input->post('search_text'); ?></strong> - tidak dijumpai.</label>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function(){
        $('form').submit(function(){
            if ($('input[name=search_text]').val() < 4) {
                $('.search-error').fadeIn();
                return false;
            }
        });
    });
</script>