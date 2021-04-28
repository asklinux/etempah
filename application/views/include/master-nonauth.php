<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>eTempah - Sistem Tempahan Bilik Mesyuarat dan Sumber DOSH</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link href="<?php echo base_url('assets/css/normalize.css') ?>" rel="stylesheet"/>

        <link href="<?php echo base_url('assets/css/event-calendar/eventCalendar.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/event-calendar/eventCalendar_theme_responsive.css') ?>" rel="stylesheet">

        <link href="<?php echo base_url('assets/css/bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/bootstrap-responsive.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/font-awesome.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/css/style.css') ?>" rel="stylesheet">



        <link href="<?php echo base_url('assets/css/custom.css') ?>" rel="stylesheet">

        <script src="<?php echo base_url('assets/js/jquery.js') ?>"></script>
        <script src="<?php echo base_url('assets/js/event-calendar/jquery.eventCalendar.js') ?>" type="text/javascript"></script>
    </head>
    <body id="master-nonauth">
        <div class="navbar">
            <div class="navbar-inner">
                <div class="container">
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
            </div>
        </div>

        <br/>

        <?php $current_id = (isset($this->uri->rsegments[3])) ? $this->uri->rsegments[3] : 0; ?>

        <div class="container">   

            <div class="row">
                <div class="span9">
<!--                    <h4><i class="icon-calendar"></i> Kalendar Tempahan</h4>
                    <hr class="title-divider" />

                    <div id="event-calendar" class="span4 margin-left0"></div>

                    <div class="eventsCalendar-list-wrap span6 padding-top10">
                        <div class="btn-group" data-toggle="buttons-radio">
                            <a href="<?php echo site_url(); ?>auth/login/" class="btn btn-info <?php echo ($current_id == 0) ? "active" : ''; ?>">Bilik</a>
                            <a href="<?php echo site_url(); ?>auth/login/3" class="btn btn-info <?php echo ($current_id == 3) ? "active" : ''; ?>">Peralatan</a>
                            <a href="<?php echo site_url(); ?>auth/login/4" class="btn btn-info <?php echo ($current_id == 4) ? "active" : ''; ?>">Kenderaan</a>
                        </div>
                        <p class='eventsCalendar-subtitle'></p>
                        <span class='eventsCalendar-loading calendar-pos'><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></span>
                        <div class='eventsCalendar-list-content'>
                            <ul class='eventsCalendar-list'></ul>
                        </div>
                    </div>       -->
                    <ul class="nav nav-tabs margin-bottom0" id="myTab">
                        <li class="active"><a href="#kalendarTempahan">Kalendar Tempahan Bilik</a></li>
                        <?php if (!empty($equipments)): ?>
                            <li><a href="#equipmentList">Senarai Peralatan</a></li>
                        <?php endif; ?>
                    </ul>

                    <div class="tab-content" style="overflow-y: visible;">
                        <div class="tab-pane fade active in" id="kalendarTempahan" style="margin: 0 20px 20px;">
                            <div class="row">
                                <div id="event-calendar" class="span4">
                                    <br/>
                                    <p>Petunjuk:</p>
                                    <div style="border-radius:3px; float:left; background:none repeat scroll 0 0 #89B814; width: 20px; height: 20px;"></div>&nbsp;&nbsp;Ada tempahan
                                    <div style="clear:both;"></div><br/>
                                    <div style="border-radius:3px; float:left; background:none repeat scroll 0 0 #E0E0E0; width: 20px; height: 20px;"></div>&nbsp;&nbsp;Tiada tempahan
                                </div>
                                <div class="eventsCalendar-list-wrap span6 padding-top10 front-calendar">
                                    <p class='eventsCalendar-subtitle'></p>
                                    <span class='eventsCalendar-loading calendar-pos'><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></span>
                                    <div class='eventsCalendar-list-content'>
                                        <div id="accordion2" class="room_calendar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($equipments)): ?>
                            <div class="tab-pane fade in padding-left30" id="equipmentList" style="margin: 0 20px 20px; min-height: 325px;">
                                <div class="row">

                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th width="90%">Nama Peralatan</th>
                                                <th width="10%">Kuantiti</th>
                                                <th width="10%" class="hide">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($equipments)): ?>
                                                <?php foreach ($equipments as $equipment): ?>
                                                    <?php if (!$equipment->deleted): ?>
                                                        <?php $row_type = ($equipment->status == 0) ? 'class="error"' : ''; ?>
                                                        <tr data-provides="rowlink" <?php echo $row_type ?>>
                                                            <td><?php echo $equipment->name; ?></td>
                                                            <td><?php echo $equipment->quantity; ?></td>
                                                            <td class="hide">
                                                                <?php echo anchor("equipments/create/{$equipment->equipment_id}", '<i class="icon-eye-open"></i> Lihat', 'class="btn btn-mini"'); ?>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8">Tiada senarai peralatan dijumpai.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="span3">
                    <div class="well box-shadow padding-top0">
                        <h4><i class="icon-lock"></i> Log Masuk</h4>
                        <hr class="title-divider" />
                        <?php $this->load->view($loadpage); ?>
                    </div>

                    <?php if ($this->config->item('agency_name', 'setting') || $this->config->item('agency_address', 'setting') || $this->config->item('agency_phone', 'setting') || $this->config->item('agency_fax', 'setting')) { ?>
                        <div class="well box-shadow padding-top0">
                            <div>
                                <?php if ($this->config->item('display_logo', 'setting') == 'display') { ?>
                                    <?php if (is_file(FCPATH . 'assets/img/' . $this->config->item('logo_name', 'setting'))) { ?>
                                        <br/>
                                        <img src="<?php echo (is_readable(FCPATH . 'assets/img/' . $this->config->item('logo_name', 'setting'))) ? base_url('assets/img/' . $this->config->item('logo_name', 'setting')) : ''; ?>" height="80" class="agency-logo"/>
                                    <?php } ?>
                                <?php } ?>
                                <br/>
                                <strong><?php echo ($this->config->item('agency_name', 'setting')) ? $this->config->item('agency_name', 'setting') : ''; ?></strong>
                            </div>
                            <br/>
                            <?php echo ($this->config->item('agency_address', 'setting')) ? nl2br($this->config->item('agency_address', 'setting')) : ''; ?> <br/>
                            <br/>
                            <?php echo ($this->config->item('agency_phone', 'setting')) ? '<i class="icon-home"></i>  Telefon :' . $this->config->item('agency_phone', 'setting') : ''; ?> <br/>
                            <?php echo ($this->config->item('agency_fax', 'setting')) ? '<i class="icon-print"></i>  Fax :' . $this->config->item('agency_fax', 'setting') : ''; ?> <br/>
                            <?php echo ($this->config->item('admin_email', 'setting')) ? '<i class="icon-envelope"></i> <a href="mailto://' . $this->config->item('admin_email', 'setting') . '" >Hubungi Admin</a>' : ''; ?>
                        </div>
                    <?php } ?>

                </div>

            </div>

            <script>
                $(document).ready(function() {
                    $('.msg-check-success').hide();
                    $('.msg-check-error').hide();

                    $('input[type=submit]').attr('disabled', true);

                    $('#check_availability').click(function() {
                        $('.msg-check-success').hide();
                        $('.msg-check-error').hide();

                        var start_date = $('input[name=start_date]').val();
                        var end_date = $('input[name=end_date]').val();

                        if ((start_date != '') && (end_date != '')) {
                            if (start_date == end_date) {
                                $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Sila pilih tetapkan tarikh tamat.');
                                $('.msg-check-error').fadeIn();
                            }
                            else {
                                $('#ajax-placeholder').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');

                                $.ajax({
                                    type: "POST",
                                    url: "<?php echo site_url('ajaxify/check_availability/1'); ?>",
                                    data: "start_date=" + start_date + "&end_date=" + end_date,
                                    dataType: 'json',
                                    success: function(msg) {

                                        if (msg['result'] == true) {
                                            $('input[type=submit]').attr('disabled', false);
                                            $('#ajax-placeholder-success').html('<i class="icon-ok"></i> Ada kekosongan. Sila teruskan tempahan.');

                                            $('select[name=fk_room_id]').html('');
                                            $('select[name=fk_room_id]').append('<option>-- Sila Pilih</option>');
                                            $('select[name=fk_room_id]').append(msg['data']);
                                            $('.msg-check-success').fadeIn();

                                            var start_date = $('input[name=start_date]').val();
                                            var end_date = $('input[name=end_date]').val();

                                            if ((start_date != '') && (end_date != '')) {
                                                //$('#ajax-placeholder-equipment').html('<p><img src="<?php echo base_url('assets/img/ajax-loader.gif') ?>" width="145" height="19" /></p>');

                                                $.ajax({
                                                    type: "POST",
                                                    url: "<?php echo site_url('ajaxify/check_availability/3'); ?>",
                                                    data: "start_date=" + start_date + "&end_date=" + end_date,
                                                    dataType: 'json',
                                                    success: function(msg) {
                                                        for (var e in msg) {
                                                            $('#equipment' + e).html(msg[e] + ' unit').fadeIn();
                                                        }

                                                        $('input[type=submit]').attr('disabled', false);
                                                        $('#ajax-placeholder-equipment-success').html('<i class="icon-ok"></i> Senarai kekosongan peralatan telah dikemaskinikan.');
                                                        $('.msg-check-equipment-success').fadeIn();

                                                    }
                                                });

                                                $('#form-details').fadeIn();
                                            }
                                            else {
                                                $('#form-details').hide();
                                                $('input[type=submit]').attr('disabled', true);
                                            }
                                        }
                                        else {
                                            $('input[type=submit]').attr('disabled', true);
                                            $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Tiada kekosongan. Sila pilih masa atau tarikh lain.');
                                            $('.msg-check-error').fadeIn();
                                        }
                                    }
                                });
                            }

                        }
                        else {
                            $('input[type=submit]').attr('disabled', true);
                            $('#ajax-placeholder-error').html('<i class="icon-info-sign"></i> Sila pilih tarikh.');
                            $('.msg-check-error').fadeIn();
                        }
                    });

                    $('input[name=start_date], input[name=end_date]').focus(function() {
                        $('.msg-check-success').fadeOut();
                        $('.msg-check-error').fadeOut();
                    });
                    $("#event-calendar").eventCalendar({
                        eventsjson: '<?php echo site_url(); ?>ajaxify/getTempahanList<?php echo (!empty($status_id)) ? "/$status_id" : ''; ?>'
                    });

                    $('#myTab a').click(function(e) {
                        e.preventDefault();
                        $(this).tab('show');
                    });

                });
            </script>
        </div>

        <div class="container">
            <div class="nav footer">
                <hr/><p>Hak Cipta © 2013. Produk OSCC, MAMPU.<br/><img src="<?php echo base_url('assets/img/mybooking-logo.png') ?>" style="width: 100px;"/></p>
            </div>
        </div>

        <style  type="text/css">
            .front-calendar {width: 320px !important;}
        </style>
        <script src="<?php echo base_url('assets/js/bootstrap.js') ?>"></script>
    </body>
</html>
