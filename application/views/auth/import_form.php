<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal form-with-validate" enctype="multipart/form-data"'); ?>

<fieldset>
    <div class="control-group">
        <label class="control-label"></label>
        <div class="controls">
            Sila pastikan fail yang akan dimuat-naik memenuhi syarat dan kehendak sistem. Kegagalan berbuat demikian boleh menyebabkan muat naik gagal.
            <ul>
                <li>Sila gunakan templat yang disediakan sebagai panduan.</li>
                <li>Pastikan maklumat di dalam fail yang dimuat-naik adalah bebas dari duplikasi bagi maklumat <strong>username</strong> dan <strong>email</strong>.</li>
                <li>Sila pastikan fail dalam format .XLS</li>
                <li>Sila pastikan baris pertama di dalam templat tersebut tidak dibuang. Ianya digunakan di dalam sistem sebagai rujukan.</li>
            </ul>
            <?php echo anchor('auth/download', '<i class="icon-download"></i> Muat Turun Templat <span class="badge badge-warning">1</span>', 'class="btn btn-primary"'); ?>
            
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="file_upload">Fail</label>
        <div class="controls">
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="input-append">
                    <div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview"></span></div><span class="btn btn-file"><span class="fileupload-new">Pilih fail <span class="badge badge-warning">2</span></span> <span class="fileupload-exists">Tukar</span><input type="file" name="file_upload" /></span><a href="#" class="btn fileupload-exists" data-dismiss="fileupload">Batal</a> 
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
	<?php echo anchor("auth/users", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
        <input name="submit" type="submit" class="btn" value="Simpan"/>
    </div>
</fieldset>
<?php echo form_close(); ?>

