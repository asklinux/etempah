<?php // echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'");                                   ?>
<?php echo form_open_multipart($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>
<?php $this->load->helper('html'); ?>
<?php $flag = TRUE; ?>
<fieldset>
    <!--input type="hidden" id="id" name="id" value="<?php echo set_value('id', !empty($user_profile) ? $user_profile->id : 0); ?>"-->

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#driverinfo">Pentadbir</a></li>
        <li><a href="#agencyprofile">Maklumat Agensi</a></li>
        <li><a href="#logo">Logo Agensi</a></li>
        <li><a href="#smtp">Tetapan SMTP</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane fade active in" id="driverinfo">
            <div class="control-group msg-check-success">
                <label class="control-label"></label>
                <div class="controls padding-right20">
                    <div id="ajax-placeholder-success" class="alert margin-bottom0 alert-success"></div>
                </div>
            </div>
            <div class="control-group msg-check-error">
                <label class="control-label"></label>
                <div class="controls padding-right20">
                    <div id="ajax-placeholder-error" class="alert margin-bottom0 alert-error"></div>
                </div>
                <div>&nbsp;</div>
            </div>
            <div id="admin-container">
                <?php
                $i = 0;
                $count = count($admin);
                ?>
                <?php foreach ($admin as $key => $value) { ?>
                    <?php $i++; ?>
                    <div class="control-group" id="<?php echo $value->id; ?>">
                        <?php if ($flag == TRUE) { ?>
                            <label class="control-label" for="admin_email">Emel Pentadbir</label>
                        <?php } ?>
                        <div class="controls">
                            <input type="text" disabled name="admin_email[<?php echo $value->id; ?>]" id="admin_email[<?php echo $value->id; ?>]"  class="span3" value="<?php echo ($value->email) ? $value->email : ''; ?>" />
                            <?php if ($count != 1) { ?>
                                <button type="button" class="btn btn-mini btn-danger" value="<?php echo $value->id; ?>" name="Delete[<?php echo $value->id; ?>]"><i class="icon-minus-sign icon-white"></i><strong> Hapus</strong></button>
                            <?php } ?>
                            <?php if ($i == $count) { ?>
                                <button type="button" id="listTrigger" class="btn btn-mini btn-primary"><i class="icon-plus-sign icon-white"></i><strong> Tambah</strong></button> 
                            <?php } ?>
                        </div>
                    </div>
                    <?php $flag = FALSE; ?>

                <?php } ?>
            </div>

            <div id="adminList" class="control-group hide">
                <div class="controls">
                    <input autocomplete="off" type="text" id="adminList" name="adminList" class="span3" placeholder="Taip Emel Pengguna" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]'>
<!--                    <select class="span3" name="adminList">
                        <option value="">--Sila Pilih</option>
                    <?php foreach ($all as $key => $value) { ?>
                        <?php if ($value->user_level == 0) { ?>
                                                <option value="<?php echo $value->id; ?>"><?php echo $value->email ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>-->
                    <button id="adminCancel" type="button" class="btn btn-mini btn-danger"><i class="icon-remove icon-white"></i><strong> Batal</strong></button>
                    <button id="adminAdd" type="button" class="btn btn-mini btn-success"><i class="icon-plus-sign icon-white"></i><strong> Simpan</strong></button>
                </div>
            </div>
 
            <div class="control-group">
                <label class="control-label" for="driver_email">Emel Ketua Pemandu</label>
                <div class="controls">
                    <input autocomplete="off" type="text" id="driver_email" name="driver_email" class="span3" placeholder="<?php echo ($headdriver) ? $headdriver->email : 'Taip Emel Pengguna';?>" style="margin: 0 auto;" data-provide="typeahead" data-items="8" data-source='[<?php echo $user_list; ?>]' value="<?php echo ($headdriver) ? $headdriver->email : '';?>">
<!--                    <select class="span3" name="driver_email" id="driver_email">
                        <option value="">-- Sila Pilih</option>
                    <?php foreach ($all as $key => $value) { ?>
                        <?php if ($value->user_level != 1 && $value->user_level != 2) { ?>
                                                <option value="<?php echo $value->id ?>" <?php echo ($headdriver) ? (($headdriver->id == $value->id ) ? 'selected' : '') : ''; ?> > <?php echo $value->email ?> </option>
                        <?php } ?>
                    <?php } ?>
                    </select>-->
                </div>
            </div>
            <input type="hidden" name="driver_id" value="<?php echo ($headdriver) ? $headdriver->id : 0; ?>" />

        </div>

        <div class="tab-pane fade" id="logo">

            <?php if ($this->config->item('logo_name', 'setting')) { ?>

                <div class="control-group">
                    <label class="control-label" for="current_logo">Papar Logo</label>
                    <div class="controls" style="padding-top:5px;">
                        <input type="radio" name="display_logo" value='display' <?php echo ($this->config->item('display_logo', 'setting') == 'display') ? 'checked' : ''; ?>> Ya&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="display_logo" value='undisplay' <?php echo ($this->config->item('display_logo', 'setting') == 'undisplay') ? 'checked' : ''; ?>> Tidak
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="current_logo">Prebiu Logo</label>
                    <div class="controls">
                        <img src="<?php echo (is_readable(FCPATH . 'assets/img/' . $this->config->item('logo_name', 'setting'))) ? base_url('assets/img/' . $this->config->item('logo_name', 'setting')) : ''; ?>" height="40" class="agency-logo"/><br>
                    </div>
                </div>

            <?php } ?>
            
            <div class="control-group"> 
                <div class="controls">
                    <div class="alert span5 margin-left0"><strong>Perhatian!</strong> Logo yang diterima pakai untuk dimuat naik adalah dari jenis <strong>JPEG</strong> dan <strong>PNG</strong> sahaja dengan ukuran dimensi tidak melebihi 2000 pixel. Ukuran logo yang disarankan adalah 200px (lebar) x 80px (tinggi) </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="new_logo">Muat Naik Logo</label>
                <div class="controls">
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                        <div class="input-append">
                            <div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-file"><span class="fileupload-new">Pilih fail</span><span class="fileupload-exists">Change</span><input type="file" name="new_logo" id="new_logo"/></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Remove</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="agencyprofile">

            <div class="control-group">
                <label class="control-label" for="agency_name">Nama Agensi</label>
                <div class="controls">
                    <input type="text" name="agency_name" id="agency_name" class="span5" value="<?php echo ($this->config->item('agency_name', 'setting')) ? $this->config->item('agency_name', 'setting') : ''; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="agency_address">Alamat Agensi</label>
                <div class="controls">
                    <textarea name="agency_address" id="agency_address" rows="3" class="span4"><?php echo ($this->config->item('agency_address', 'setting')) ? $this->config->item('agency_address', 'setting') : ''; ?></textarea>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="agency_phone">Telefon</label>
                <div class="controls">
                    <input type="text" name="agency_phone" id="agency_phone" class="span2 validate-numberonly" value="<?php echo ($this->config->item('agency_phone', 'setting')) ? $this->config->item('agency_phone', 'setting') : ''; ?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="agency_fax">Fax</label>
                <div class="controls">
                    <input type="text" name="agency_fax" id="agency_fax" class="span2 validate-numberonly" value="<?php echo ($this->config->item('agency_fax', 'setting')) ? $this->config->item('agency_fax', 'setting') : ''; ?>" />
                </div>
            </div>


        </div>

        <div class="tab-pane fade" id="smtp">

            <div class="control-group">
                <label class="control-label" for="smtp_host">Perumah SMTP</label>
                <div class="controls">
                    <input type="text" name="smtp_host" id="smtp_host" class="span3" value="<?php echo ($this->email->smtp_host) ? $this->email->smtp_host : ''; ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="smtp_user">ID Pengguna SMTP</label>
                <div class="controls">
                    <input type="text" name="smtp_user" id="smtp_user" class="span3" value="<?php echo ($this->email->smtp_user) ? $this->email->smtp_user : ''; ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="smtp_pass">Kata Laluan SMTP</label>
                <div class="controls">
                    <input type="text" name="smtp_pass" id="smtp_pass" class="span3" value="<?php echo ($this->email->smtp_pass) ? $this->email->smtp_pass : ''; ?>" />
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="smtp_port">Port SMTP</label>
                <div class="controls">
                    <input type="text" name="smtp_port" id="smtp_port" class="span3" value="<?php echo ($this->email->smtp_port) ? $this->email->smtp_port : ''; ?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <?php echo anchor("auth/users", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
        <input type="submit" class="btn" value="Simpan"/>
    </div>
</fieldset>
<?php echo form_close(); ?>

<!--<div id="deleteItemModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Pengesahan</h3>
    </div>
    <div class="modal-body">
        <p>Hapus rekod dari sistem?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
<?php // echo anchor("rooms/delete/{$room->id}", 'Hapus', 'class="btn btn-primary"');     ?>
    </div>
</div>-->

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#myTab a').click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('.msg-check-success').hide();
        $('.msg-check-error').hide();

        $('input[name=password2]').blur(function() {

            var password = $('input[name=password]').val();

            if ($(this).val() != password) {
                $('.msg-password').html('Kata Laluan Baru dan pengesahannya tidak tepat. Sila cuba lagi.');
                $('.msg-password-success-alert').hide();
                $('.msg-password-error-alert').fadeIn();
            }
            else {
                $('.msg-password').html('Kata Laluan Baru dan pengesahannya tepat. Sila teruskan.');
                $('.msg-password-error-alert').hide();
                $('.msg-password-success-alert').fadeIn();
            }

        });
        $(document).on("click", 'button[type=button][name^=Delete]', function() {
            var idHapus = $(this).val();
            $.post('<?php echo site_url('ajaxify/setRecordAdmin'); ?>', {id: idHapus, action: 'delete'}, function(result) {
                if (result['result'] == true) {

                    var firstchild = $("#admin-container div.control-group:first-child").attr('id');
                    var lastchild = $("#admin-container div.control-group:last-child").attr('id');

                    $('#' + result['id'] + '').remove();

                    if (firstchild == result['id']) {
                        var nextchild = $("#admin-container div:first-child").attr('id');
                        $("#" + nextchild).prepend('<label class="control-label" for="admin_email">Emel Pentadbir</label>').fadeIn();
                    }

                    if (lastchild == result['id']) {
                        //			var nextchild = $('#admin-container div:last-child');
                        $('#admin-container div:last').append('<button type="button" id="listTrigger" class="btn btn-mini btn-primary"><i class="icon-plus-sign icon-white"></i><strong> Tambah</strong></button>').fadeIn();
                    }

                    var onlychild = $('#admin-container div.control-group:only-child').attr('id');

                    if (onlychild) {
                        $('button[name="Delete[' + onlychild + ']"]').remove();
                    }

                    $('select[name=adminList]').html(result['data']);
                    $('#driver_email').html('');
                    $('#driver_email').append(result['driver_email']);
                    $('#ajax-placeholder-success').html('<i class="icon-ok"></i> Senarai Pentadbir Utama telah berjaya dikemaskinikan.');
                    $('.msg-check-success').fadeIn().fadeOut(5000);
                } else {
                    $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Senarai Pentadbir Utama gagal diubah.');
                    $('.msg-check-error').fadeIn().fadeOut(5000);
                }
            }, 'json');
        });

        $(document).on('click', '#listTrigger', function() {
            $('#adminList').fadeIn().removeClass('hide');
            $('#listTrigger').fadeOut();
        });

        $('#adminCancel').click(function() {
            $('#adminList').fadeOut().addClass('hide');
            $('#listTrigger').fadeIn().removeClass('hide');
        });

        $('#adminAdd').click(function() {
            var id = $('input[name=adminList]').val();
            $.post("<?php echo site_url('ajaxify/setRecordAdmin'); ?>", {id: id, action: 'add'}, function(result) {
                if (result['result'] == true) {
                    //		    $('#listTrigger').remove();
                    var listTrigger = $('#listTrigger').detach();
                    var onlychildDel = $('#admin-container div.control-group:only-child').attr('id');

                    if (onlychildDel) {
                        $('#' + onlychildDel + ' > div.controls').append(' <button type="button" class="btn btn-mini btn-danger" value="' + onlychildDel + '" name="Delete[' + onlychildDel + ']"><i class="icon-minus-sign icon-white"></i><strong> Hapus</strong></button>').fadeIn();
                    }

                    var controlGroup = $('<div class="control-group" id="' + id + '"></div>');
                    var controls = $('<div class="controls">' + result['admin'] + '</div>');
                    $('#adminList').fadeOut().addClass('hide');

                    controlGroup.append(controls).fadeIn(1500).appendTo('#admin-container');
                    listTrigger.appendTo(controls).fadeIn().removeClass('hide');

                    $('select[name=adminList]').html('');
                    $('select[name=adminList]').append(result['data']);
		    
                    $('#driver_email').html('');
                    $('#driver_email').append(result['driver_email']);

                    $('#ajax-placeholder-success').html('<i class="icon-ok"></i> Pentadbir utama berjaya ditambah.');
                    $('.msg-check-success').fadeIn().fadeOut(5000);
                }
                else {
                    $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Maaf, pentadbir utama tidak dapat ditambah.');
                    $('.msg-check-error').fadeIn().fadeOut(5000);
                }
            }, 'json');
        });
    });
</script>

