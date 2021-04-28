<?php if (isset($_SESSION['show_activated']) && $_SESSION['show_activated']): ?>
    <div class="alert alert-success fade in">
        <i class="icon-ok-sign"></i> Akaun anda telah diaktifkan.
    </div>
    <?php session_destroy(); ?>
<?php endif; ?>


<?php if (isset($_SESSION['action_message']) && $_SESSION['action_message']): ?>
    <div class="alert alert-success fade in"><?php echo $_SESSION['action_message']; ?></div>
    <?php session_destroy(); ?>
<?php endif; ?>

<?php echo form_open($this->uri->uri_string(), "class='navbar-form'"); ?>
<div class="control-view"><strong>ID Pengguna</strong></div>
<input type="text" id="login" name="login" class="" placeholder="ID Pengguna" value="<?php echo ($this->input->post('login') != '') ? $this->input->post('login') : ''; ?>">

<div class="control-view"><strong>Kata Laluan</strong></div>
<input type="password" id="password" name="password" class="" placeholder="Katalaluan">
<?php echo anchor('auth/forgot_password', 'Anda terlupa kata laluan?'); ?>
<div class="control-view">
    <button type="submit" class="btn btn-login" style="margin-right: 10px;">Log Masuk</button>
<!--    <input type="checkbox" style="margin-top: 0;"> Kekal log masuk-->

</div>
<?php echo form_close(); ?>

<?php
$login_error = $this->tank_auth->get_error_message('login');
if (!empty($login_error)):
    ?>
    <div class="control-view">
        <div class="alert alert-error fade in">
            <i class="icon-exclamation-sign"></i> <?php echo $this->tank_auth->get_error_message('login'); ?>
        </div>
    </div>
<?php endif; ?>