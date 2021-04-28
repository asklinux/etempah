$(document).ready(function(){    
    // General form validation
    $('form.form-with-validate input, form.form-with-validate textarea, form.form-with-validate select').each(function() {
        var badge = $(this).next();
                
        if (badge.length > 0 && badge.attr('class')){
            if ($(this).val().trim() != '' && badge.length > 0) {
                if ((badge.attr('class').search('validate-required') >= 0) || (badge.attr('class').search('validate-number-only') >= 0)) {
                    badge.hide();
                }
            }
        }
    });
        
    // Validate dropdown as selected
    $('form.form-with-validate select').change(function() {
        var badge = $(this).next();
                
        if (badge.length > 0 && badge.attr('class')){
            if ((badge.attr('class').search('validate-required') >= 0) && (badge.html() != '')) {
                if ($(this).val() != '') {
                    badge.fadeOut();
                }
                else {
                    badge.fadeIn();
                }
            }
        }
    });
        
    // Validate only numbers
    $('form.form-with-validate input, form.form-with-validate textarea').keydown(function(event) {
        var badge = $(this).next();
                
        if (badge.length > 0 && badge.attr('class')){
            if ((badge.attr('class').search('validate-email-only') >= 0)) {
                var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                if (filter.test($(this).val())) {
                    if ((badge.attr('class').search('validate-email-only') >= 0) && (badge.html() != '')) {
                        badge.fadeOut();
                    }
                }
            }                
                
            if ((badge.attr('class').search('validate-number-only') >= 0)) {
                if(event.shiftKey)
                    event.preventDefault();
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9) {
                }
                else {
                    if (event.keyCode < 95) {
                        if (event.keyCode < 48 || event.keyCode > 57) {
                            event.preventDefault();
                        }
                        else {
                            if ((badge.attr('class').search('validate-number-only') >= 0) && (badge.html() != '')) {
                                badge.fadeOut();
                            }
                        }
                    } 
                    else {
                        if (event.keyCode < 96 || event.keyCode > 105) {
                            event.preventDefault();
                        }
                        else {
                            if ((badge.attr('class').search('validate-number-only') >= 0) && (badge.html() != '')) {
                                badge.fadeOut();
                            }
                        }
                    }
                }
            }
            else {
                if ((badge.attr('class').search('validate-required') >= 0) && (badge.html() != '')) {
                    badge.fadeOut();
                }
            } 
        }
    });
        
    $('form.form-with-validate input, form.form-with-validate textarea').blur(function() {
        var badge = $(this).next();
                
        if (badge.length > 0) {
            if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val().trim() == '') && (badge.css('display') == 'none')) {
                badge.fadeIn();
            }
            else if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val().trim() != '') && (badge.css('display') != 'none')) {
                badge.fadeOut();
            }
                
            if ((badge.attr('class').search('validate-number-only') >= 0) && ($(this).val() == '') && (badge.css('display') == 'none')) {
                badge.fadeIn();
            }
        }
                
    });
        
        
    if ($('form.form-with-validate').length > 0) {
        // Datetimepicker restriction
        var startDateTextBox = $('#start_date');
        var endDateTextBox = $('#end_date');

        if (($('#start_date').attr('class').length > 0) && $('#start_date').attr('class').search('date-only') >= 0){
            
            startDateTextBox.datepicker({ 
                dateFormat: "dd/mm/yy",
                onClose: function(dateText, inst) {
//                    if (endDateTextBox) {
//                        if (endDateTextBox.val() != '') {
//                            var testStartDate = startDateTextBox.datetimepicker('getDate');
//                            var testEndDate = endDateTextBox.datetimepicker('getDate');
//                                        
//                            if (testStartDate > testEndDate)
//                                endDateTextBox.datetimepicker('setDate', testStartDate);
//                        }
//                        else {
//                            endDateTextBox.val(dateText);
//                                        
//                            var badge = endDateTextBox.next();
//                            if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val() != '') && (badge.css('display') != 'none')) {
//                                badge.fadeOut();
//                            }
//                        }
//                    }
                },
                onSelect: function (selectedDateTime){
                    if (endDateTextBox) {
                        endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
                        startDateTextBox.next().fadeOut();
                        
                    }                        
                }
            });
            endDateTextBox.datepicker({ 
                dateFormat: "dd/mm/yy",
                onClose: function(dateText, inst) {
                    if (endDateTextBox) {
                        if (endDateTextBox.val() != '') {
                            var testStartDate = startDateTextBox.datetimepicker('getDate');
                            var testEndDate = endDateTextBox.datetimepicker('getDate');
                                        
                            if (testStartDate > testEndDate)
                                endDateTextBox.datetimepicker('setDate', testStartDate);
                        }
                        else {
                            endDateTextBox.val(dateText);
                                        
                            var badge = endDateTextBox.next();
                            if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val() != '') && (badge.css('display') != 'none')) {
                                badge.fadeOut();
                            }
                        }
                    }
                },
                onSelect: function (selectedDateTime){
                    if (endDateTextBox) {
                        endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
                        startDateTextBox.next().fadeOut();
                    }                        
                }
            });
        }
        else {
            startDateTextBox.datetimepicker({ 
                dateFormat: "dd/mm/yy",
                timeFormat: 'hh:mm:ss tt',
                stepHour: 1,
                showMinute: false,
                minDate: 0,
                hourMin: 0,
                minuteMax: 0,
                secondMax: 0,
                onClose: function(dateText, inst) {
                    if (startDateTextBox.val() != ''){
                        badge = startDateTextBox.next();
                        if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val() != '') && (badge.css('display') != 'none')) {
                            badge.fadeOut();
                        }
                    }
                    if (endDateTextBox) {
                        if (endDateTextBox.val() != '') {
                            var testStartDate = startDateTextBox.datetimepicker('getDate');
                            var testEndDate = endDateTextBox.datetimepicker('getDate');
                                        
                            if (testStartDate > testEndDate)
                                endDateTextBox.datetimepicker('setDate', testStartDate);
                        }
                        else {
                            endDateTextBox.val(dateText);
                            
//                            badge = endDateTextBox.next();
//                            if ((badge.attr('class').search('validate-required') >= 0) && ($(this).val() != '') && (badge.css('display') != 'none')) {
//                                badge.fadeOut();
//                            }
                        }
                    }
                },
                onSelect: function (selectedDateTime){
                    if (endDateTextBox) {
                        endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
                    }                        
                }
            });
            endDateTextBox.datetimepicker({ 
                dateFormat: "dd/mm/yy",
                timeFormat: 'hh:mm:ss tt',
                stepHour: 1,
                showMinute: false,
                minDate: 0,
                hourMin: 0,
                minuteMax: 0,
                secondMax: 0,
                onClose: function(dateText, inst) {
                    if (startDateTextBox.val() != '') {
                        var testStartDate = startDateTextBox.datetimepicker('getDate');
                        var testEndDate = endDateTextBox.datetimepicker('getDate');
                        if (testStartDate > testEndDate)
                            startDateTextBox.datetimepicker('setDate', testEndDate);
                    }
                    else {
                        startDateTextBox.val(dateText);
                    }
                },
                onSelect: function (selectedDateTime){
                    startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
                }
            });
        }
        
        $('#trip_type_extended').hide();
        $('select[name=trip_type]').change(function() {
            if ($(this).val() == 2) {
                $('#trip_type_extended').fadeIn();
            }
            else {
                $('#trip_type_extended').fadeOut();
            }
        });
    }
    
    $('.validate-numberonly').keydown(function(event){
		
        if(event.shiftKey)
            event.preventDefault();
        if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 189 || event.keyCode == 109) {
        }
        else {
            if (event.keyCode < 95) {
                if (event.keyCode < 48 || event.keyCode > 57) {
                    event.preventDefault();
                }
            } 
            else {
                if (event.keyCode < 96 || event.keyCode > 105) {
                    event.preventDefault();
                }
            }
        }
		
		
    });

    $('.validate-numberonly').keyup(function(event){
        var input = $(this).val();
        $(this).val(input.replace(/-+/g,"-"));
		
		
    });
    
    $('.validate-quantity').keydown( function (e) {
        var code = e.which,
        chr = String.fromCharCode(code), // key pressed converted to s string
        //cur = $grade.val(),
        item_quantity = $(this).prev().html().replace(" unit", "");
        newVal = parseFloat($(this).val() + chr); // what the input box will contain after this key press

        // Only allow numbers, periods, backspace, tabs and the enter key
        if (code !== 190 && code !== 8 && code !== 9 && code !== 13  && !/[0-9]/.test(chr)) {
            return false;
        }

        // If this keypress would make the number
        // out of bounds, ignore it
        if ( newVal > item_quantity) {
            return false;
        }

    });
    
});

