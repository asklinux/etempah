<?php echo form_open($this->uri->uri_string()); ?>

<div align="center" style="margin: 40px 0;">
    <h4>Penetapan Semula Katalaluan</h4>
    <label>Pautan untuk menetapkan semula katalaluan telah dihantar ke emel anda. Terima kasih.</label>
    <br/><br/>
    <?php echo anchor('/', '<i class="icon-arrow-left"></i> Kembali</button>', 'class="btn"'); ?>
</div>

<?php echo form_close(); ?>