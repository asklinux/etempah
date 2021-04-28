<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate' target='_blank'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('name', !empty($booking) ? $booking->booking_id : 0); ?>">
    <input type="hidden" id="fk_user_id" name="fk_user_id" value="<?php echo $this->tank_auth->get_user_id(); ?>">

    <div class="well well-form">
        <div class="control-group">
            <label class="control-label">Jenis Laporan</label>
            <div class="controls">
                <select name="report_type" class="span2">
                    <option value="food">Makanan</option>
                    <option value="equipment">Peralatan</option>
                    <option value="transport">Kenderaan</option>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="start_date">Tarikh Mula</label>
            <div class="controls">
                <input autocomplete="off" name="start_date" class="span2 date-only" id="start_date" type="text" placeholder="Klik untuk pilih tarikh">
                <span class="label label-important validate-required">Wajib</span>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="end_date">Tarikh Akhir</label>
            <div class="controls">
                <input autocomplete="off" name="end_date" class="span2 date-only" id="end_date" type="text" placeholder="Klik untuk pilih tarikh">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
                <button type="submit" id="btn-report" class="btn">Jana Laporan</button>
            </div>
        </div>
    </div>

</fieldset>

<?php echo form_close(); ?>

<script>
    $(document).ready(function(){
//        $('input[name=start_date]').datepicker({ 
//            dateFormat: "dd/mm/yy"
//        });
//        
//        $('input[name=end_date]').datepicker({ 
//            dateFormat: "dd/mm/yy"
//        });
//        
//        $('input[name=start_date]').click(function(){
//            $('input[name=start_date]').removeClass('field-error');
//        });
//        
//        $('input[name=end_date]').click(function(){
//            $('input[name=end_date]').removeClass('field-error');
//        });
        
        $('#btn-report').click(function(e){
            if ($('input[name=start_date]').val() == ''){
                $('input[name=start_date]').addClass('field-error');
                e.preventDefault();
            }
            
//            if ($('input[name=end_date]').val() == ''){
//                $('input[name=end_date]').addClass('field-error');
//                e.preventDefault();
//            }
        });
    });
</script>