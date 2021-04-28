<?php
$new_password = array(
    'name' => 'new_password',
    'id' => 'new_password',
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);
$confirm_new_password = array(
    'name' => 'confirm_new_password',
    'id' => 'confirm_new_password',
    'maxlength' => $this->config->item('password_max_length', 'tank_auth'),
    'size' => 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>

<div align="center" style="margin: 40px 0;">
    <h4>Penetapan Semula Katalaluan</h4>
    <div class="control-view"><strong><?php echo form_label('Katalaluan Baru', $new_password['id']); ?></strong></div>
    <?php echo form_password($new_password); ?>
    <?php echo form_error($new_password['name']); ?><?php echo isset($errors[$new_password['name']]) ? $errors[$new_password['name']] : ''; ?>

    <div class="control-view"><strong><?php echo form_label('Sahkan Katalaluan', $confirm_new_password['id']); ?></strong></div>
    <?php echo form_password($confirm_new_password); ?>
    <?php echo form_error($confirm_new_password['name']); ?><?php echo isset($errors[$confirm_new_password['name']]) ? $errors[$confirm_new_password['name']] : ''; ?>

    <div class="control-view">
        <button type="submit" class="btn btn-login" style="margin-right: 10px;">Log Masuk</button>
    </div>

</div>
<?php echo form_close(); ?>