<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>eTempah - Sistem Tempahan Bilik Mesyuarat dan Sumber Dosh</title>
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
            <div class="well"><?php $this->load->view($loadpage); ?></div>
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
