<?php
if (session_id() == '') {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset ="utf-8">
        <title>eTempah - Sistem Tempahan DOSH Ibu Pejabat</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="<?php echo base_url('assets/css/normalize.css') ?>" rel="stylesheet"/>

        <link href="<?php echo base_url('assets/css/event-calendar/eventCalendar.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/event-calendar/eventCalendar_theme_responsive.css') ?>" rel="stylesheet"/>

        <link href="<?php echo base_url('assets/js/timepicker/jquery-ui-timepicker-addon.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/js/jquery-ui-1.9.1.custom/css/smoothness/jquery-ui-1.9.1.custom.min.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/bootstrap.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/jasny-bootstrap.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/bootstrap-responsive.min.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/font-awesome.css') ?>" rel="stylesheet"/>
        <link href="<?php echo base_url('assets/css/custom.css') ?>" rel="stylesheet"/>

        <script src="<?php echo base_url('assets/js/jquery.js') ?>"></script>
<!--        <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>-->
    </head>
    <body id="master-auth"> 
        <?php $current_page = !empty($this->uri->segments) ? $this->uri->segments[1] : ''; ?>
        <?php $current_subpage = (isset($this->uri->segments[2]) && !empty($this->uri->segments)) ? $this->uri->segments[2] : ''; ?>
        <?php $current_id = (isset($this->uri->rsegments[3])) ? (!is_numeric($this->uri->rsegments[3]) ? "all" : 0) : 0; ?>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <div class="row">
                        <div class="span6">
                            <div class="navbar-logo pull-left">
                                <img src="<?php echo base_url('assets/img/logo-jata-negara.png') ?>" style="width: 55px;"/>

                                <?php if ($this->config->item('display_logo', 'setting') == 'display') { ?>
                                    <?php if (is_file(FCPATH . 'assets/img/' . $this->config->item('logo_name', 'setting'))) { ?>
                                        <img src="<?php echo (is_readable(FCPATH . 'assets/img/' . $this->config->item('logo_name', 'setting'))) ? base_url('assets/img/' . $this->config->item('logo_name', 'setting')) : ''; ?>" height="40" class="agency-logo"/>
                                    <?php } ?>
                                <?php } ?>

                                <img src="<?php echo base_url('assets/img/mybooking-logo.png') ?>" style="width: 140px;"/>
                            </div>
                        </div>
                        <div class="span6">
                            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <div class="nav-collapse collapse">

                                <?php
                                $username = $this->tank_auth->get_username();
                                $fullname = $this->tank_auth->get_user_fullname();

                                $display_name = (!empty($fullname)) ? $fullname : $username;
                                ?>

                                <ul class="nav pull-right">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $display_name; ?> <b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><?php echo anchor('auth/profile/' . $this->tank_auth->get_user_id(), '<i class="icon-user"></i> Lihat Profil'); ?></li>
                                            <li><?php echo anchor(site_url() . '#myModal', '<i class="icon-off"></i> Log Keluar', ' data-toggle="modal"'); ?></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="span3">
                    <div class="well box-shadow" style="max-width: 340px; padding: 8px 0;">
                        <ul class="nav nav-list">
                            <li class="<?php echo ($current_page == '' || $current_page == 'frontpage') ? 'active' : ''; ?>""><?php echo anchor('/', '<i class="icon-home"></i> Laman Utama'); ?></li>
                            <li class="divider"></li>
                            <?php if ($this->tank_auth->is_admin()): ?>
                                <li class="nav-header">Pengurusan Rekod</li>
                                <li class="<?php echo (($current_subpage != 'profile') && $current_page == 'auth') ? 'active' : ''; ?>"><?php echo anchor('auth/users', '<i class="icon-list-alt"></i> Pengguna'); ?></li>
                                <li class="<?php echo ($current_page == 'rooms') ? 'active' : ''; ?>"><?php echo anchor('rooms', '<i class="icon-list-alt"></i> Bilik'); ?></li>
                                <li class="<?php echo ($current_page == 'equipments') ? 'active' : ''; ?>"><?php echo anchor('equipments', '<i class="icon-list-alt"></i> Peralatan'); ?></li>
                                <li class="<?php echo ($current_page == 'transports' && ($current_subpage == '' || $current_subpage == 'create')) ? 'active' : ''; ?>"><?php echo anchor('transports', '<i class="icon-list-alt"></i> Kenderaan'); ?></li>
                                <?php if ($current_page == 'transports') { ?>
                                    <ul class="nav nav-list">
                                        <li class="<?php echo ($current_subpage == 'list_type' || $current_subpage == 'create_type') ? 'active' : ''; ?>"><?php echo anchor('transports/list_type', '<i class="icon-tasks"></i> Jenis Kenderaan'); ?></li>
                                    </ul>
                                <?php } ?>
                                <li class="divider"></li>
                            <?php endif; ?>

                            <?php if ($this->tank_auth->is_subadmin()): ?>
                                <li class="nav-header">Pengurusan Rekod</li>
                                <li class="<?php echo ($current_page == 'rooms') ? 'active' : ''; ?>"><?php echo anchor('rooms', '<i class="icon-list-alt"></i> Bilik'); ?></li>
                                <li class="divider"></li>
                            <?php endif; ?>

                            <?php if ($this->tank_auth->is_headdriver()): ?>
                                <li class="nav-header">Pengurusan Rekod</li>
                                <li class="<?php echo ($current_page == 'transports' && ($current_subpage == '' || $current_subpage == 'create')) ? 'active' : ''; ?>"><?php echo anchor('transports', '<i class="icon-list-alt"></i> Kenderaan'); ?></li>
                                <?php if ($current_page == 'transports') { ?>
                                    <ul class="nav nav-list">
                                        <li class="<?php echo ($current_subpage == 'list_type' || $current_subpage == 'create_type') ? 'active' : ''; ?>"><?php echo anchor('transports/list_type', '<i class="icon-tasks"></i> Jenis Kenderaan'); ?></li>
                                    </ul>
                                <?php } ?>
                                <li class="divider"></li>
                            <?php endif; ?>

                            <li class="nav-header">Pengurusan Tempahan</li>
                            <li class="<?php echo (($current_page == 'booking_rooms')) ? 'active' : ''; ?>"><?php echo anchor('booking_rooms', '<i class="icon-list-alt"></i> Bilik'); ?></li>
                            <li class="<?php echo (($current_page == 'booking_foods')) ? 'active' : ''; ?>"><?php echo anchor('booking_foods', '<i class="icon-list-alt"></i> Makanan'); ?></li>
                            <li class="<?php echo (($current_page == 'booking_equipments')) ? 'active' : ''; ?>"><?php echo anchor('booking_equipments', '<i class="icon-list-alt"></i> Peralatan'); ?></li>
                            <li class="<?php echo (($current_page == 'booking_transports')) ? 'active' : ''; ?>"><?php echo anchor('booking_transports', '<i class="icon-list-alt"></i> Kenderaan'); ?></li>

                            <?php if (!$this->tank_auth->is_normal_user()): ?>
                                <li class="<?php echo (($current_page == 'reports')) ? 'active' : ''; ?>"><?php echo anchor('reports', '<i class="icon-list-alt"></i> Laporan'); ?></li>
                            <?php endif; ?>

                            <li class="divider"></li>
                            <li class="<?php echo (($current_subpage == 'profile') /* && ($current_id == $this->tank_auth->get_user_id()) */ ) ? 'active' : ''; ?>"><?php echo anchor('auth/profile/' . $this->session->userdata['user_id'], '<i class="icon-user"></i> Profil Pengguna'); ?></li>
                            <li class="<?php echo (($current_page == 'search')) ? 'active' : ''; ?>"><a href=""><?php echo anchor('search', '<i class="icon-search"></i> Carian Maklumat'); ?></li>
                            <?php if ($this->tank_auth->is_admin()): ?>
                                <li class="divider"></li>
                                <li class="<?php echo (($current_page == 'logs')) ? 'active' : ''; ?>"><?php echo anchor('logs', '<i class="icon-time"></i> Log Aktiviti'); ?></li>
                                <li class="<?php echo (($current_page == 'settings')) ? 'active' : ''; ?>"><?php echo anchor('settings', '<i class="icon-wrench"></i> Tetapan Utama'); ?></li>
                            <?php endif; ?>
<!--                            <li><a href="#"><i class="icon-info-sign"></i> Bantuan</a></li>-->
                        </ul>
                    </div>

                </div>

                <div class="span9">
                    <ul class="breadcrumb box-shadow">
                        <li><i class="icon-home"></i> <?php echo anchor(site_url(), 'Laman Utama'); ?></li>
                        <?php foreach ($breadcrumb as $key => $val): ?>
                            <?php if (!empty($val)): ?>
                                <li><span class="divider">/</span> <?php echo anchor($val, $key); ?></li>
                            <?php else: ?>
                                <li><span class="divider">/</span> <?php echo anchor('#', $key); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>

                    <div class="well box-shadow padding-top0 content-area">
                        <h4><?php echo $pagetitle; ?></h4>
                        <hr class="title-divider" />

                        <?php if ($this->session->userdata('error_message')): ?>

                            <div class="alert alert-error fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $this->session->userdata('error_message'); ?>
                                <?php echo $this->session->unset_userdata('error_message'); ?>
                            </div>

                        <?php endif; ?>

                        <?php if ($this->session->userdata('warning_message')): ?>

                            <div class="alert alert-warning fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $this->session->userdata('warning_message'); ?>
                                <?php $this->session->unset_userdata('warning_message'); ?>
                            </div>

                        <?php endif; ?>

                        <?php if ($this->session->userdata('action_message')): ?>

                            <div class="alert alert-success fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <?php echo $this->session->userdata('action_message'); ?>
                            </div>

                            <?php $this->session->unset_userdata('action_message') ?>
                        <?php endif; ?>

                        <?php $this->load->view($loadpage); ?>

                    </div>
                </div>
            </div>
        </div> 

        <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Pengesahan</h3>
            </div>
            <div class="modal-body">
                <p>Anda pasti untuk keluar dari sistem?</p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
                <?php echo anchor('/auth/logout/', 'Keluar', 'class="btn btn-primary"'); ?>
            </div>
        </div>

        <div id="myFormModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myFormModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myFormModalLabel">Pengesahan</h3>
            </div>
            <div class="modal-body">
                <p>Anda pasti untuk teruskan?</p>
            </div>
            <div class="modal-footer">
                <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
                <?php echo anchor('/auth/logout/', 'Keluar', 'class="btn btn-primary"'); ?>
            </div>
        </div>

        <div class="container footer">
            <hr/>
            <p>Hak Cipta © 2013. Produk OSCC, MAMPU.<br/><img src="<?php echo base_url('assets/img/mybooking-logo.png') ?>" style="width: 100px;"/></p>
        </div>

        <script src="<?php echo base_url('assets/js/event-calendar/jquery.eventCalendar.js') ?>" type="text/javascript"></script>
        <script src="<?php echo base_url('assets/js/jquery-ui-1.9.1.custom/js/jquery-ui-1.9.1.custom.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/timepicker/jquery-ui-timepicker-addon.js') ?>"></script>

        <script src="<?php echo base_url('assets/js/jquery-autocomplete/jquery.mockjax.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/jquery-autocomplete/jquery.autocomplete.js') ?>"></script>

        <script src="<?php echo base_url('assets/js/bootstrap.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/jasny-bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/general.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/validation.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/custom.js') ?>"></script>
    </body>
</html>
