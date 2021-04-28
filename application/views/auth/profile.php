<?php echo form_open($this->uri->uri_string(), "class='form-horizontal form-with-validate'"); ?>

<fieldset>
    <input type="hidden" id="id" name="id" value="<?php echo set_value('id', !empty($user_profile) ? $user_profile->id : 0); ?>">

    <ul class="nav nav-tabs margin-bottom0" id="myTab">
        <li class="active"><a href="#userprofile">Maklumat Pengguna</a></li>
        
        <?php if ($user_profile->id == $this->tank_auth->get_user_id()): ?>
            <li><a href="#passwordupdate">Kemaskini Kata Laluan</a></li>
        <?php endif; ?>
            
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade active in" id="userprofile">
            <div class="control-group">
                <label class="control-label" for="user_level">Status</label>
                <?php if ($this->tank_auth->is_admin() && ($user_profile->id != $this->tank_auth->get_user_id())): ?>
                    <div class="controls">
                        <select id="activated" name="activated" class="span2">
                            <option value="1" <?php echo ($user_profile->activated == 1) ? "selected" : ""; ?>>Aktif</option>
                            <option value="2" <?php echo ($user_profile->activated == 2) ? "selected" : ""; ?>>Tidak Aktif</option>
                        </select>
                    </div>
                <?php else: ?>
                    <div class="control-label">
                        <label class="align-left span4" for="user_level"><strong><?php echo $user_profile->activated ? "Aktif" : "Tidak Aktif"; ?></strong></label>
                    </div>
                <?php endif; ?>
            </div>
            <div class="control-group">
                <label class="control-label" for="user_level">Tahap Pengguna</label>
                <div class="control-label">
                    <label class="align-left span4" for="user_level"><strong><?php echo $user_levels[$user_profile->user_level]; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="Username">ID Pengguna</label>
                <div class="control-label">
                    <label class="align-left span4" for="Username"><strong><?php echo $user_profile->username; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="email">Emel Pengguna</label>
                <div class="control-label">
                    <label class="align-left span4" for="email"><strong><?php echo $user_profile->email; ?></strong></label>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="full_name">Nama Penuh</label>
                <div class="controls">
                    <input type="text" class="span4" id="full_name" name="full_name" value="<?php echo $user_profile->full_name; ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block">Nama penuh berserta gelaran.</p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="department_name">Bahagian</label>
                <div class="controls">
                    <input type="text" class="span4" id="department_name" name="department_name" value="<?php echo $user_profile->department_name; ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block"><i>"Seksyen Teknologi Maklumat"</i></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="position_id">Jawatan</label>
                <div class="controls">
                    <input type="text" class="span4" id="position_name" name="position_name" maxlength="36" value="<?php echo $user_profile->position_name; ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block"><i>"Penolong Pengarah"</i></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="position_level">Gred</label>
                <div class="controls">
                    <input type="text" class="span1" id="position_level" name="position_level" maxlength="10" value="<?php echo $user_profile->position_level; ?>">
                    <span class="label label-important validate-required">Wajib</span>
                    <p class="help-block"><i>"F41"</i></p>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="contact_office">Telefon Pejabat</label>
                <div class="controls">
                    <input type="text" class="span2" id="contact_office" name="contact_office" maxlength="20" value="<?php echo $user_profile->contact_office; ?>">
                    Sambungan
                    <input type="text" class="span1" id="contact_office_ext" name="contact_office_ext" maxlength="20" value="<?php echo $user_profile->contact_office_ext; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="contact_mobile">Telefon Bimbit</label>
                <div class="controls">
                    <input autocomplete="off" type="text" class="span2" id="contact_mobile" name="contact_mobile" maxlength="20" value="<?php echo $user_profile->contact_mobile; ?>">
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="passwordupdate">
            <div class="control-group">
                <label class="control-label" for="old_password">Kata Laluan Lama</label>
                <div class="controls">
                    <input autocomplete="off" type="password" class="span2" id="old_password" name="old_password" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password">Kata Laluan Baru</label>
                <div class="controls">
                    <input autocomplete="off" type="password" class="span2" id="password" name="password" value="">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="password2">Sahkan Kata Laluan Baru</label>
                <div class="controls">
                    <input autocomplete="off" type="password" class="span2" id="password2" name="password2" value="">
                </div>
            </div>
            <div class="control-group msg-password-error-alert hide">
                <label class="control-label"></label>
                <div class="controls">
                    <div class="alert alert-error fade in span5 margin-left0">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="icon-ok"></i> <span class="msg-password"></span> 
                    </div>
                </div>
            </div>
            <div class="control-group msg-password-success-alert hide">
                <label class="control-label"></label>
                <div class="controls">
                    <div class="alert alert-success fade in span5 margin-left0">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="icon-ok"></i> <span class="msg-password"></span> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <div class="control-group">
            <label class="control-label"></label>
            <div class="controls">
                <?php echo anchor("auth/users", '<i class="icon-arrow-left"></i> Kembali', 'class="btn"'); ?>
                <input type="submit" class="btn" value="Simpan"/>
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
        <?php echo anchor("rooms/delete/{$room->id}", 'Hapus', 'class="btn btn-primary"'); ?>
    </div>
</div>

<script type='text/javascript' language='javascript'>
    $(document).ready(function() {
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
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
    });
</script>