<?php echo form_open($this->uri->uri_string()); ?>

<div align="center" style="margin: 40px 0;">
    <h4>Penetapan Semula Katalaluan</h4>
    <label>Sila masukkan emel anda untuk menetapkan semula katalaluan. </label>
    <input type="text" class="span3 margin-bottom0" name="login" value="">
    <button type="submit" class="btn">Hantar</button>
    <?php echo anchor('/', '<i class="icon-arrow-left"></i> Kembali</button>', 'class="btn"'); ?>
</div>

<?php echo form_close(); ?>

<p style="text-align:center; color: red;">
  <?php echo isset($errors['login']) ? $errors['login'] : '' ?>
</p>