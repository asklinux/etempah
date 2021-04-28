/* =
	jquery.eventCalendar.js
	version: 0.42;
	date: 28-05-2012
	authors: 
		Jaime Fernandez (@vissit)
		Nerea Navarro (@nereaestonta)
	company:	
		Paradigma Tecnologico (@paradigmate)
*/

;
$.fn.eventCalendar = function(options){
    var eventsOpts = $.extend({}, $.fn.eventCalendar.defaults, options);
        
    // define global vars for the function
    var flags = {
        wrap: "",
        directionLeftMove: "300",
        eventsJson: {}
    }
	
    // each eventCalendar will execute this function
    this.each(function(){
        flags.wrap = $(this);
        //flags.wrap.addClass('eventCalendar-wrap').append("<div class='eventsCalendar-list-wrap'><p class='eventsCalendar-subtitle'></p><span class='eventsCalendar-loading'>loading...</span><div class='eventsCalendar-list-content'><ul class='eventsCalendar-list'></ul></div></div>");
        flags.wrap.addClass('eventCalendar-wrap').append("<div class='eventsCalendar-list-wrap'></div>");
                
        if (eventsOpts.eventsScrollable) {
            flags.wrap.next().addClass('scrollable');
        }
		
        setCalendarWidth();
        $(window).resize(function(){
            setCalendarWidth();
        });
        //flags.directionLeftMove = flags.wrap.width();
		
        // show current month
        dateSlider("current");
		
        getEvents(eventsOpts.eventsLimit,false,false,false,false);
		
        changeMonth();
                
        flags.wrap.find('.eventsCalendar-day a').live('click',function(e){
            e.preventDefault();
            
            if (!$(this).data('isClicked')) {
                var link = $(this);
                
                var year = flags.wrap.attr('data-current-year'),
                month = flags.wrap.attr('data-current-month'),
                day = $(this).parent().attr('rel');
				
                getEvents(false, year, month,day, "day");
                
                link.data('isClicked', true);
                setTimeout(function() {
                    link.removeData('isClicked')
                }, 1000);
            }
        });
		
        flags.wrap.find('.monthTitle').live('click',function(e){
            e.preventDefault();
            var year = flags.wrap.attr('data-current-year'),
            month = flags.wrap.attr('data-current-month');
				
            getEvents(eventsOpts.eventsLimit, year, month,false, "month");
        });	
    });
	
    // show event description
    flags.wrap.find('.room_calendar .eventTitle').live('click',function(e){
        //flags.wrap.next.find('.eventsCalendar-list .eventTitle').live('click',function(e){
        if(!eventsOpts.showDescription) {
            e.preventDefault();
            var desc = $(this).parent().find('.eventDesc');
			
            if (!desc.find('a').size()) {
                var eventUrl = $(this).attr('href');
                var eventTarget = $(this).attr('target');
				
                // create a button to go to event url
                desc.append('<a href="' + eventUrl + '" target="'+eventTarget+'" class="bt">'+eventsOpts.txt_GoToEventUrl+'</a>')
            } 
			
            if (desc.is(':visible')) {
                desc.slideUp();
            } else {
                if(eventsOpts.onlyOneDescription) {
                    flags.wrap.find('.eventDesc').slideUp();
                }
                desc.slideDown();
            }
			
        }
    });
	
    function sortJson(a, b){  
        return a.date.toLowerCase() > b.date.toLowerCase() ? 1 : -1;  
    };

    function dateSlider(show, year, month) {
        var $eventsCalendarSlider = $("<div class='eventsCalendar-slider'></div>"),
        $eventsCalendarMonthWrap = $("<div class='eventsCalendar-monthWrap'></div>"),
        $eventsCalendarTitle = $("<div class='eventsCalendar-currentTitle'><a href='#' class='monthTitle'></a></div>"),
        $eventsCalendarArrows = $("<a href='#' class='arrow prev'><span>" + eventsOpts.txt_prev + "</span></a><a href='#' class='arrow next'><span>" + eventsOpts.txt_next + "</span></a>");
        $eventsCalendarDaysList = $("<ul class='eventsCalendar-daysList'></ul>"),
       
        date = new Date();
		
        if (!flags.wrap.find('.eventsCalendar-slider').size()) {
            flags.wrap.prepend($eventsCalendarSlider);
            $eventsCalendarSlider.append($eventsCalendarMonthWrap);
        } else {
            flags.wrap.find('.eventsCalendar-slider').append($eventsCalendarMonthWrap);
        }
		
        flags.wrap.find('.eventsCalendar-monthWrap.currentMonth').removeClass('currentMonth').addClass('oldMonth');
        $eventsCalendarMonthWrap.addClass('currentMonth').append($eventsCalendarTitle, $eventsCalendarDaysList);
		
        // if current show current month & day 
        if (show === "current") {
            day = date.getDate();
            $eventsCalendarSlider.append($eventsCalendarArrows);	
			
        } else {
            date = new Date(flags.wrap.attr('data-current-year'),flags.wrap.attr('data-current-month'),1,0,0,0); // current visible month
            day = 0; // not show current day in days list				
				
            moveOfMonth = 1;
            if (show === "prev") {
                moveOfMonth = -1;
            }
            date.setMonth( date.getMonth() + moveOfMonth );			
			
            var tmpDate = new Date();
            if (date.getMonth() === tmpDate.getMonth()) {
                day = tmpDate.getDate();
            } 
			
        }
		
        // get date portions
        var year = date.getFullYear(), // year of the events
        currentYear = (new Date).getFullYear(), // current year
        month = date.getMonth(), // 0-11
        monthToShow = month + 1;

        if (show != "current") {
            // month change
            getEvents(eventsOpts.eventsLimit, year, month,false, show);
        }
				
        flags.wrap.attr('data-current-month',month)
        .attr('data-current-year',year);
			
        // add current date info
        $eventsCalendarTitle.find('.monthTitle').html(eventsOpts.monthNames[month] + " " + year);
		
        // print all month days
        var daysOnTheMonth = 32 - new Date(year, month, 32).getDate();
        var daysList = [];
        if (eventsOpts.showDayAsWeeks) {
            $eventsCalendarDaysList.addClass('showAsWeek');
			
            // show day name in top of calendar
            if (eventsOpts.showDayNameInCalendar) {
                $eventsCalendarDaysList.addClass('showDayNames');
				
                var i = 0;
                // if week start on monday
                if (eventsOpts.startWeekOnMonday) {
                    i = 1;
                }
				
                for (; i < 7; i++) {
                    daysList.push('<li class="eventsCalendar-day-header">'+eventsOpts.dayNamesShort[i]+'</li>');
					
                    if (i === 6 && eventsOpts.startWeekOnMonday) {
                        // print sunday header
                        daysList.push('<li class="eventsCalendar-day-header">'+eventsOpts.dayNamesShort[0]+'</li>');
                    }
					
                }
            }
			
            dt=new Date(year, month, 01);
            var weekDay = dt.getDay(); // day of the week where month starts
			
            if (eventsOpts.startWeekOnMonday) {
                weekDay = dt.getDay() - 1;
            }
            if (weekDay < 0) {
                weekDay = 6;
            } // if -1 is because day starts on sunday(0) and week starts on monday
            for (i = weekDay; i > 0; i--) {
                daysList.push('<li class="eventsCalendar-day empty"></li>');
            }
        }
        for (dayCount = 1; dayCount <= daysOnTheMonth; dayCount++) {
            var dayClass = "";
			
            if (day > 0 && dayCount === day && year === currentYear) {
            //dayClass = "current";
            }
            daysList.push('<li id="dayList_' + dayCount + '" rel="'+dayCount+'" class="eventsCalendar-day '+dayClass+'"><a href="#">' + dayCount + '</a></li>');
        }
        $eventsCalendarDaysList.append(daysList.join(''));
		
        $eventsCalendarSlider.css('height',$eventsCalendarMonthWrap.height()+'px');
    }

    function num_abbrev_str(num) {
        var len = num.length, last_char = num.charAt(len - 1), abbrev
        var abbrev = 'hb'
        //                if (len === 2 && num.charAt(0) === '1') {
        //                        //abbrev = 'th'
        //                        abbrev = 'hb'
        //                } else {
        //                        if (last_char === '1') {
        //                                abbrev = 'st'
        //                        } else if (last_char === '2') {
        //                                abbrev = 'nd'
        //                        } else if (last_char === '3') {
        //                                abbrev = 'rd'
        //                        } else {
        //                                abbrev = 'th'
        //                        }
        //                }
        return num + abbrev
    }
	
    function getEvents(limit, year, month, day, direction) {
        var limit = limit || 0;
        var year = year || '';
        var day = day || date.getDate();

        // to avoid problem with january (month = 0)
		
        if (typeof month != 'undefined') {
            var month = month;
        } else {
            var month = '';
        }
                
        //var month = month || '';
        //flags.wrap.find('.eventsCalendar-loading').fadeIn();
        flags.wrap.next().find('.eventsCalendar-loading').fadeIn();
                
        if (eventsOpts.jsonData) {
            // user send a json in the plugin params
            eventsOpts.cacheJson = true;
	
            flags.eventsJson = eventsOpts.jsonData;
            getEventsData(flags.eventsJson, limit, year, month, day, direction);
			
        } else if (!eventsOpts.cacheJson || !direction) {
            //first load: load json and save it to future filters
            $.getJSON(eventsOpts.eventsjson + "?limit="+limit+"&year="+year+"&month="+month+"&day="+day, function(data) {
                flags.eventsJson = data; // save data to future filters
                getEventsData(flags.eventsJson, limit, year, month, day, direction);
            }).error(function() { 
                showError("error getting json: ");
            });
                        
        //                        $.post(eventsOpts.eventsjson, {}, 
        //                                function(data){
        //                                        flags.eventsJson = data; // save data to future filters
        //                                        getEventsData(flags.eventsJson, limit, year, month, day, direction);
        //                                },'json');
        } else {
            // filter previus saved json
            getEventsData(flags.eventsJson, limit, year, month, day, direction);
        }
    }

    function getEventsData(data, limit, year, month, day, direction){
        directionLeftMove = "-=" + flags.directionLeftMove;
        eventContentHeight = "auto";
        
        subtitle = flags.wrap.next().find('.eventsCalendar-subtitle')
        if (!direction) {
            // first load
            eventContentHeight = "auto";
            directionLeftMove = "-=0";
            subtitle.html(eventsOpts.txt_SpecificEvents_prev + date.getDate() + "/" + (date.getMonth()+1) + "/" + date.getFullYear());
        } else {
            if (day != '' && (direction == '')) {
                subtitle.html(eventsOpts.txt_SpecificEvents_prev + " " + num_abbrev_str(day) + " " +  eventsOpts.monthNames[month] + " " + eventsOpts.txt_SpecificEvents_after);
            } else {
                subtitle.html(eventsOpts.txt_SpecificEvents_prev + day + '/' + (parseInt(month)+1) + '/' + year);
            }
			
            if (direction === 'prev') {
                directionLeftMove = "+=" + flags.directionLeftMove;
            } else if (direction === 'day' || direction === 'month') {
                directionLeftMove = "+=0";
                eventContentHeight = 0;
            }
        }
		
        flags.wrap.next().find('.room_calendar').animate({
            opacity: eventsOpts.moveOpacity,
            left: directionLeftMove,
            height: eventContentHeight
        }, eventsOpts.moveSpeed, function() {
            flags.wrap.next().find('.room_calendar').css({
                'left':0, 
                'height': 'auto'
            }).hide();
			
            var events = [];

            data = $(data).sort(sortJson); // sort event by dates
            
            // each event
            if (data.length) {
                var content_template = '';
                var event_content = '';
                
                // show or hide event description
                var eventDescClass = '';
                if(!eventsOpts.showDescription) {
                    eventDescClass = 'hidden';
                }
                var eventLinkTarget = "_self";
                if(eventsOpts.openEventInNewWindow) {
                    eventLinkTarget = '_target';
                }
			
                var i = 0;
                var cur_room_id = 0;
                var cur_date = day+''+month+''+year;
                
                var room_list = new Array();
                var content_open = new Array();
                var room_list = new Array();
                
                $.each(data, function(key, event) {
                    
                    var eventDate = new Date(parseInt(event.date)),
                    eventYear = eventDate.getFullYear(),
                    eventMonth = eventDate.getMonth(),
                    eventDay = eventDate.getDate();
                    
                    var eventDateEnd = new Date(parseInt(event.end_date));
                    
                    if (cur_date == ''){
                        cur_date = eventDay + "" + eventMonth + '' + eventYear;
                    }                    
                                        
                    if (limit === 0 || limit > i) {
                        var eventMonthToShow = eventMonth + 1,
                        eventHour = eventDate.getHours(),
                        eventMinute = eventDate.getMinutes();
                        var ampmStart = eventHour >= 12 ? 'pm' : 'am';
                                                
                        eventHourEnd = eventDateEnd.getHours(),
                        eventMinuteEnd = eventDateEnd.getMinutes() == '0' ? '00' : '30';
                        var ampmEnd = eventHourEnd >= 12 ? 'pm' : 'am';
                                                
                        var eventDescription = event.description ? event.description : '';
                                                
                        if (eventMinute <= 9) {
                            eventMinute = "0" + eventMinute;
                        }
                        // if month or day exist then only show matched events
                        if ((month === false || month == eventMonth) 
                            && (day == '' || day == eventDay)
                            && (year == '' || year == eventYear) // get only events of current year
                            ) {
                            // if initial load then load only future events
                            if (month === false && eventDate < new Date()) {

                            } else {
                                eventStringDate = eventDay + "/" + eventMonthToShow + "/" + eventYear;
                                
                                if (room_list.indexOf(event.room_id) == -1){
                                    event_content = '<tr data-provides="rowlink"><td><i class="icon-time"></i> <time datetime="'+eventDate+'"><small>'+eventHour+":"+eventMinute+' '+ampmStart+ ' - '+eventHourEnd+":"+eventMinuteEnd+' '+ampmEnd+'</small></time><br/><a href="'+event.url+'" target="' + eventLinkTarget + '" class="eventTitle"><strong>' + event.title + '</strong></a><p class="eventDesc"><i class="icon-hand-right"></i> <small>' + eventDescription + '</small></p></td></tr>';
                                    
                                    content_open[event.room_id] = '<div class="accordion-group"><div class="accordion-heading"><a href="#collapse' + event.room_id + '" data-parent="#accordion2" data-toggle="collapse" class="accordion-toggle"><i class="icon-map-marker"></i> ' + event.room_name + '</a></div><div class="accordion-body collapse" id="collapse' + event.room_id + '"><div class="accordion-inner">' +
                                    '<table id="room_calendar" class="table table-bordered table-striped table-hover">' + event_content + '</table></div></div></div>';
                                    
                                    room_list.push(event.room_id);
                                    i++;
                                    
                                }
                                else {
                                    //room_list.push(event.room_id);
                                    event_content = '<tr data-provides="rowlink"><td><i class="icon-time"></i> <time datetime="'+eventDate+'"><small>'+eventHour+":"+eventMinute+' '+ampmStart+ ' - '+eventHourEnd+":"+eventMinuteEnd+' '+ampmEnd+'</small></time><br/><a href="'+event.url+'" target="' + eventLinkTarget + '" class="eventTitle"><strong>' + event.title + '</strong></a><p class="eventDesc"><i class="icon-hand-right"></i> <small>' + eventDescription + '</small></p></td></tr>';
                                    
                                    content_open[event.room_id] = content_open[event.room_id].substr(0, content_open[event.room_id].lastIndexOf('</table>')) + event_content + content_open[event.room_id].substr(content_open[event.room_id].lastIndexOf('</table>'));
                                }
                                
                            }
                        }
                    }
					
                    // add mark in the dayList to the days with events
                    if (eventYear == flags.wrap.attr('data-current-year') && eventMonth == flags.wrap.attr('data-current-month')) {
                        flags.wrap.find('.currentMonth .eventsCalendar-daysList #dayList_' + eventDay).addClass('dayWithEvents');
                    }
                 
                });
                
                content_open_string = content_open.join('');
                events.push(content_open_string);
            }
            
            // there is no events on this period
            if (!events.length || events == '') {
                events.push('<table id="room_calendar" class="table table-bordered table-striped table-hover"><tr><td>' + eventsOpts.txt_noEvents + '</td></tr></table>');
            }
            
            //flags.wrap.find('.eventsCalendar-loading').hide();
            flags.wrap.next().find('.eventsCalendar-loading').hide();
			
            //flags.wrap.find('.eventsCalendar-list')
            flags.wrap.next().find('.room_calendar')
            .html(events.join(''));
			
            //flags.wrap.find('.eventsCalendar-list').animate({
            flags.wrap.next().find('.room_calendar').animate({
                opacity: 1,
                height: "toggle"
            }, eventsOpts.moveSpeed);
				

        });
        setCalendarWidth();
    }
		
    function changeMonth() {
        flags.wrap.find('.arrow').click(function(e){
            e.preventDefault();

            if ($(this).hasClass('next')) {
                dateSlider("next");
                var lastMonthMove = '-=' + flags.directionLeftMove;
				
            } else {
                dateSlider("prev");
                var lastMonthMove = '+=' + flags.directionLeftMove;
            }
			
            flags.wrap.find('.eventsCalendar-monthWrap.oldMonth').animate({
                opacity: eventsOpts.moveOpacity,
                left: lastMonthMove
            }, eventsOpts.moveSpeed, function() {
                flags.wrap.find('.eventsCalendar-monthWrap.oldMonth').remove();
            });
        });
    }

    function showError(msg) {
        //flags.wrap.find('.eventsCalendar-list-wrap').html("<span class='eventsCalendar-loading error'>"+msg+" " +eventsOpts.eventsjson+"</span>");
        flags.wrap.next().html("<span class='eventsCalendar-loading error'>"+msg+" " +eventsOpts.eventsjson+"</span>");
    }
	
    function setCalendarWidth(){
        // resize calendar width on window resize
        flags.directionLeftMove = flags.wrap.width();
        flags.wrap.find('.eventsCalendar-monthWrap').width(flags.wrap.width() + 'px');
		
        //flags.wrap.find('.eventsCalendar-list-wrap').width(flags.wrap.width() + 'px');
        flags.wrap.next().width(flags.wrap.width() + 'px');
		
    }
};


// define the parameters with the default values of the function
$.fn.eventCalendar.defaults = {
    eventsjson: 'js/events.json',
    eventsLimit: '',
    monthNames: [ "Januari", "Februari", "Mac", "April", "Mei", "Jun",
    "Julai", "Ogos", "September", "Oktober", "November", "Disember" ],
    dayNames: [ 'Ahad','Isnin','Selasa','Rabu',
    'Khamis','Jumaat','Sabtu' ],
    dayNamesShort: [ 'Ahad','Isnin','Selasa','Rabu', 'Khamis','Jumaat','Sabtu' ],
    txt_noEvents: "Tiada rekod tempahan dijumpai.",
    txt_SpecificEvents_prev: "Paparan Tempahan Pada ",
    txt_SpecificEvents_after: "",
    txt_next: "selepas",
    txt_prev: "sebelum",
    txt_NextEvents: "Senarai Tempahan:",
    txt_GoToEventUrl: "Lihat tempahan",
    showDayAsWeeks: true,
    startWeekOnMonday: true,
    showDayNameInCalendar: true,
    showDescription: true,
    onlyOneDescription: true,
    openEventInNewWindow: false,
    eventsScrollable: false,
    moveSpeed: 10,	// speed of month move when you clic on a new date
    moveOpacity: 1, // month and events fadeOut to this opacity
    jsonData: "", 	// to load and inline json (not ajax calls) 
    cacheJson: true	// if true plugin get a json only first time and after plugin filter events
// if false plugin get a new json on each date change
};

