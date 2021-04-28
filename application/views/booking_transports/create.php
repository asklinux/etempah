<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($transport) ? $transport->id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label" for="trip_type">Jenis Perjalanan</label>
            <div class="controls">
                <select id="trip_type" name="trip_type" class="span2">
                    <option value="1">Satu hala</option>
                    <option value="2">Dua hala</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Masa Pergi</label>
            <div class="controls">
                <input readonly type="text" class="span2" id="start_date" name="start_date" autocomplete="off" value="<?php echo set_value('start_date', !empty($room) ? $room->start_date : ''); ?>">
                <span class="label label-important validate-required">Wajib</span>
            </div>
        </div>        
        <div id="trip_type_extended">
            <div class="control-group">
                <label class="control-label" for="end_date">Masa Balik</label>
                <div class="controls">
                    <input readonly type="text" class="span2" id="end_date" name="end_date" autocomplete="off" value="<?php echo set_value('end_date', !empty($room) ? $room->end_date : ''); ?>">
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
        </div>
        <!--        <div class="control-group">
                    <label class="control-label" for="trip_type">Kenderaan</label>
        
        <?php foreach ($transport_types as $key => $transport_type): ?>
            <?php foreach ($transport_lists as $transport_list): ?>
                <?php if (($transport_list->transport_type == $key)): ?>
                    
                                            <div class="controls">
                                                <select id="transports" name="transports[<?php echo $key; ?>]" class="span1">
                    
                    <?php for ($i = 0; $i <= $transport_list->total; $i++): ?>
                                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                    
                                                </select> 
                    
                    <?php //echo $transport_type[0]; ?>
                    
                                            </div><br/>
                    
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
        
                </div>-->
    </div>

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#kenderaan">Maklumat Tempahan Kenderaan</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="kenderaan">
            <div class="control-group">
                <label class="control-label" for="secretariat">Ketua Perjalanan</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="secretariat" name="secretariat" class="span4" placeholder="Taip Nama Urusetia" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]'>
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="destination_from">Destinasi Dari</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="destination_from" name="destination_from" class="span4" placeholder="Taipkan destinasi mula" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $destination_list; ?>]'>
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="destination_to">Ke</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="destination_to" name="destination_to" class="span4" placeholder="Taipkan destinasi tamat" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $destination_list; ?>]'>
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="transport_purpose">Tujuan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="transport_purpose" name="transport_purpose" maxlength="150"><?php echo set_value('transport_purpose', !empty($transport) ? $transport->transport_purpose : ''); ?></textarea>
                    <span class="label label-important validate-required">Wajib</span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="total_passenger">Bilangan Pegawai</label>
                <div class="controls">
                    <input type="text" class="span1" id="total_passenger" name="total_passenger" value="<?php echo set_value('total_passenger', !empty($transport) ? $transport->total_passenger : ''); ?>">
                    <span class="label label-important validate-number-only">Wajib</span>
                    <p class="help-block"><strong>Nombor sahaja</strong>.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="booking_transport_description">Penerangan</label>
                <div class="controls">
                    <textarea rows="2" class="span4" id="booking_transport_description" name="booking_transport_description"><?php echo set_value('booking_transport_description', !empty($transport) ? $transport->booking_transport_description : ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions padding-left0">
        <div class="control-group">
            <div class="controls">
                <?php echo anchor("booking_transports", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                <input type="submit" class="btn btn-primary" value="Simpan"/>
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
        <?php echo anchor("transports/delete/{$transport->id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#driver_status').popover().click(function(e) {e.preventDefault()});
    });
</script>