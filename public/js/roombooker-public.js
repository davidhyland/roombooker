(function( $ ) {

		'use strict';

		$(document).ready(function() {

			if($('#roombooker').length){

				var monthMessageShown = false;
				var iau = (jhub == '642105684') ? true : false;
				var $modal = $('#roombooker .modal');

				var roomData = [];
				roomData[1] = {id: 1, shortTitle: 'Room 1', longTitle: 'Room 1 (Boardroom)', color: 'blue', minBook: 2, maxBook: 15};
				roomData[2] = {id: 2, shortTitle: 'Room 2', longTitle: 'Room 2', color: 'green', minBook: 1, maxBook: 4};
				roomData[3] = {id: 3, shortTitle: 'Room 3', longTitle: 'Room 3', color: 'orange', minBook: 1, maxBook: 4};
				roomData[4] = {id: 4, shortTitle: 'Room 4', longTitle: 'Room 4 (Studio Boardroom)', color: 'red', minBook: 1, maxBook: 6};

				// instructions
				$('#roombooker .infobox h4 a').on('click', function(e){
					e.preventDefault();
					$(this).parent().next('ul').slideToggle();
				});

				function smokeAlert(msg){
					smoke.alert(msg, function(e){}, {ok: "Ok"});
				}


				function setRoomOptions(roomData){
					var $field = $("#roomSelect", $modal);
					if($('option', $field).length == 1) {
						for(var i = 1; i < roomData.length; i++) {
						   var opt = document.createElement("option");
						   opt.value = roomData[i].id;
						   opt.innerHTML = roomData[i].longTitle;
						   $field.append(opt);
						}						
					}
					$field.val('').attr('validation', 'required').show();
					$('#txtRoom', $modal).hide();
				};


				function setNumPeopleOptions(room, val){
					var $field = $("select[name=fldNumPeople]", $modal);
					$field.html('<option value="">Select...</option>');
					var min = roomData[room].minBook;
					var max = roomData[room].maxBook;
					for(var i = 1; i <= max; i++) {
					   var opt = document.createElement("option");
					   opt.value = i;
					   opt.innerHTML = i;
					   $field.append(opt);
					}
					if(val > 0) $field.val(val);
					$field.attr({disabled: false});
				};

				
				function populateNumPeople(){
					$("#roomSelect", $modal).on('change', function(){
						setNumPeopleOptions($(this).val());
					});
				}


				function eventClashResponse(clashes){
					//console.log(clashes);
					var event = clashes[0];
					var str = 'Sorry but your selected times clash with an event booked for ';
					str += '<b>' + event.organisation + '</b> on <b>' + moment(event.time_start).format('DD MMM') + '</b> in <b>' + roomData[event.room].longTitle + '</b>';
					str += ' from <b>' + moment(event.time_start).format('HH:mm') + '</b> to <b>' + moment(event.time_end).format('HH:mm') + '</b><br><br>';
					str += "If this event isn't showing on the calendar then it was created by another user since you loaded this page. ";
					str += "Either change your times or <a href='"+window.location.href+"'>reload the page</a>";
					smokeAlert(str);
					resetModal();
				}


				function initTooltip(event, element, view){
						//console.log(view.name);
				    var qtipContent = '<b>' + moment(event.start).format("ddd Do MMM YYYY") + '<br>';
				    qtipContent += moment(event.start).format("HH:mm") + ' - ' + moment(event.end).format("HH:mm") + '</b>';
				    //qtipContent += '<hr><b>' + event.title.toUpperCase()+'</b>';
				    qtipContent += '<hr><b>' + event.longtitle.toUpperCase()+'</b><hr>';

				    qtipContent += 'Booked by: <a href="mailo:'+event.email+'">' + event.name+'</a><br>'; 
				    qtipContent += 'Organisation: ' + event.organisation+'<br>';
				    qtipContent += 'Room: ' + roomData[event.resourceId].longTitle+'<br>';
				    qtipContent += 'Num people: ' + event.numpeople; 

				    element.qtip({
	            content: qtipContent,
	            position: {
				        my: 'top left',  // Position my top left...
				        at: 'top center', // at the bottom right of...
				        target: 'mouse', // my target
	            	adjust: { 
	            		y: 10,
	            		mouse: false
	            	},
				        effect: function(api, pos, viewport) {
				            // "this" refers to the tooltip
				            $(this).animate(pos, {
				                duration: 600,
				                easing: 'linear',
				                queue: false // Set this to false so it doesn't interfere with the show/hide animations
				            });
				        }
					    },
					    style: {
					        classes: 'qtip-'+roomData[event.resourceId].color
					    },
							hide: {
                  fixed: true,
                  when: {
                      event: 'mouseout'
                  }
              },
		        });   
				}

				/*
				==========================================================================================
				*/

				function openModal(){
					$modal.addClass('is-visible');
					var $modalwrapper = $modal.find('.modal-transition');
					//console.log( viewport().height);
					//console.log($modalwrapper.height());
					if( viewport().height > ($('.modal-header', $modalwrapper).height() + $('.modal-body', $modalwrapper).height() + 100) ){
						$modalwrapper.css('bottom', 'auto');						
					}
					else{
						$modalwrapper.css('bottom', '2%');
					}
				}


		    function disableModal(){
					  $modal.addClass('wait');
            $('input, select', $modal).attr('disabled', true);
		    }


		    function resetModal(){
					  $modal.removeClass('wait');
					  $('input, select', $modal).attr('disabled', false);
            $('#btnSave', $modal).val('Save');
            $('#btnUpdate', $modal).val('Update');
            $('#btnDelete', $modal).val('Delete');	
		    }

		    function clearModal(msg){
						// clear selection
						$('#roombooker-calendar').fullCalendar('unselect');
					  // hide modal
					  $modal.removeClass('wait').removeClass('is-visible');
					  // reset form
					  $('input, select', $modal).attr('disabled', false);
            $('#btnSave', $modal).off('click').val('Save').hide();
            $('#btnUpdate', $modal).off('click').val('Update').hide();
            $('#btnDelete', $modal).off('click').val('Delete').hide();
					  document.getElementById("formRoomBooker").reset();
					  // message?
					  if(msg) smokeAlert(msg);
		    }

				/*
				==========================================================================================
				*/

				function updateDragDropEvent(event, revertFunc){

						// update database
						$.ajax({
	              url: roombookerajax.ajaxurl,
	              type: 'post',
	              dataType: 'json',
	              data: {
	                  action: 'ajax_update_event',
	                  id: event.id,
	                  room: event.resourceId,
	                  time_start: moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
	                  time_end: moment(event.end).format("YYYY-MM-DD HH:mm:ss")
	              },
	              beforeSend: function(){
	                  event.className = 'wait';
	              },
	              complete: function(){
	                  //console.log('complete');
	                  event.className = '';
								},
								error: function(){
									revertFunc();
									smoke.alert("An error occured and the booking could not be updated. Please try again", function(e){}, {ok: "Ok"});
								}
	          });

				}

				/*
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				*/

				function addEvent(start, end, jsEvent, view, resource){

						//console.log(start);
						//console.log(end);

						// empty form
						$('input[name=fldId]', $modal).val('');
						$('select[name=fldType]', $modal).val('');
						$('input[name=fldName]', $modal).val('');
						$('input[name=fldOrganisation]', $modal).val('');
						$('select[name=fldNumPeople]', $modal).val('');
						$('input[name=fldEmail]', $modal).val('');
						$('input[name=fldEmailConfirm]', $modal).val('').parent().show();

						setRoomOptions(roomData);

						if(resource) {
							$('input[name=fldRoom]', $modal).val(resource.id);
							$("#roomSelect", $modal).val(resource.id);
							setNumPeopleOptions(resource.id, 0);
						}
						else{
							$("select[name=fldNumPeople]", $modal).attr({disabled: true});
						}

						$("#roomSelect", $modal).on('change', function(){
							setNumPeopleOptions($(this).val());
							$('input[name=fldRoom]', $modal).val($(this).val());
						});

						// set start time
						if(start._ambigTime === false){
							$('select[name=fldStartTime]', $modal).val(moment(start).format("HH:mm"));
						}
						else{
							$('select[name=fldStartTime]', $modal).prop('selectedIndex', 0);
						}

						// set end time
						if(end._ambigTime === false){
							$('select[name=fldEndTime]', $modal).val(moment(end).format("HH:mm"));
						}
						else{
							$('select[name=fldEndTime]', $modal).prop('selectedIndex', 0);
						}
						//console.log('1 '+start._d);
						//console.log('1 '+end._d);

						//alert(start);
						$('#txtDate', $modal).text(moment(start).format("dddd Do MMM YYYY"));

						$('input[name=fldEmailConfirm]', $modal).attr('validation', 'required email_confirm').parent().show();

						// show save button
						$('#btnSave', $modal).show()

						// open modal
						//$modal.addClass('is-visible');
						openModal();

						// validation
						$('form', $modal).validation();

				    // TYPE OTHER
						$('select[name=fldType]', $modal).on('change', function(e) {
							if($(this).val() == 'Other'){
								$('input[name=fldTypeOther]').attr('validation', 'required').parent('div').show();
							}
							else{
								$('input[name=fldTypeOther]').removeAttr('validation').parent('div').hide();
							}
						});
						// reset other
				    $('input[name=fldTypeOther]').val('').removeAttr('validation').parent('div').hide();

				    // CANCEL
						$('#btnCancel', $modal).on('click', function(e) {
								//console.log('cancel');
								clearModal();
								//$('#roombooker-calendar').fullCalendar('unselect');
								//$('#btnSave', $modal).hide();
							  //$modal.removeClass('is-visible');
						});

				    // SUBMIT MODAL
						$('#btnSave', $modal).on('click', function(e) {
						//$('form', $modal).on('submit', function(e) {

							//console.log('submit');

							// valid form
						  if($('form', $modal).validform()) {

						  	//console.log('valid');

							  // set event data
								var room = $('input[name=fldRoom]', $modal).val();
								var start_time = $('select[name=fldStartTime]', $modal).val();
								var end_time = $('select[name=fldEndTime]', $modal).val();
								//var title = $('select[name=fldTitle]', $modal).val();
								var title = ($('input[name=fldTypeOther]', $modal).val() != '') ? $('input[name=fldTypeOther]', $modal).val() : $('select[name=fldType]', $modal).val();
								var longtitle = ($('input[name=fldTypeOther]', $modal).val() != '') ? $('input[name=fldTypeOther]', $modal).val() : $('select[name=fldType] option:selected', $modal).text();
								var name = $('input[name=fldName]', $modal).val();
								var organisation = $('input[name=fldOrganisation]', $modal).val();
								var numpeople = $('select[name=fldNumPeople]', $modal).val();
								var email = $('input[name=fldEmail]', $modal).val();
								var thisData;

								//console.log('['+start_time + '] - [' + end_time + ']');

								// update start and end moments
								start = moment(start.format()).hour(start_time.substr(0, 2)).minutes(start_time.substr(3, 2));
								//end = moment(end).hour(end_time.substring(0, 2)).minutes(end_time.substring(3, 2));
								end = moment(start.format()).hour(end_time.substr(0, 2)).minutes(end_time.substr(3, 2));

								//console.log(start);
								//console.log(end);

								// check min booking
								if(roomData[room].minBook > numpeople){
									smokeAlert('This room has a minimum booking of '+roomData[room].minBook+' people');
									return false;
								}

								// save new event
								if (title && name && email) {

									thisData = {
										id: false,									
										resourceId: room,
										start: start,
										end: end,
										title: title,
										longtitle: longtitle,
										name: name,
										organisation: organisation,
										numpeople: numpeople,
										email: email,
										editable: true
									};

									//console.log(thisData);

									// save data to database
									$.ajax({
			              url: roombookerajax.ajaxurl,
			              type: 'post',
			              dataType: 'json',
			              data: {
			                  action: 'ajax_save_event',
			                  room: thisData.resourceId,
			                  room_name: roomData[thisData.resourceId].longTitle,
			                  time_start: moment(thisData.start).format("YYYY-MM-DD HH:mm:ss"),
			                  time_end: moment(thisData.end).format("YYYY-MM-DD HH:mm:ss"),
			                  title: thisData.title,
			                  organisation: thisData.organisation,
			                  numpeople: thisData.numpeople,
			                  name: thisData.name,
			                  email: thisData.email,
			                  jhubUrl: (jhubUrl != '') ? jhubUrl : window.location.href
			              },
			              beforeSend: function(){
			                  disableModal();
			                  $('#btnSave', $modal).val('Processing...');
			              },
			              success: function( result ) {
			              		//console.log(result);
			                  if(result.result == 'success'){
			                  	thisData.id = result.id;
			                  }
			                  else if(result.result == 'clash'){
			                  	eventClashResponse(result.clashes);
			                  }
			              },
			              complete: function( result ){
			              		//console.log(result);
			              		if(result.responseJSON.result == 'success'){
				              		$('#roombooker-calendar').fullCalendar('renderEvent', thisData, true); // stick? = true
				              		//console.log(thisData);
											  	clearModal('Your booking has been saved and a confirmation has been emailed to <i>'+thisData.email+'</i>');		              			
			              		}
										}
				          });
									
								}

						  } 

						  return false;

						});

				}

				/*
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				*/

				function deleteEvent(event){

						// update database
						$.ajax({
	              url: roombookerajax.ajaxurl,
	              type: 'post',
	              dataType: 'json',
	              data: {
	                  action: 'ajax_update_event',
	                  id: event.id,
	                  delete: true
	              },
	              beforeSend: function(){
	                  event.className = 'wait';
	                  disableModal();
	                  $('#btnDelete', $modal).val('Deleting...');
	              },
	              complete: function(){
	                  //console.log('complete');
	                  event.className = '';
	                  $('#roombooker-calendar').fullCalendar('removeEvents', event.id);
								  	clearModal();
								},
								error: function(){
									revertFunc();
									smoke.alert("An error occured and the booking could not be deleted. Please try again", function(e){}, {ok: "Ok"});
								}
	          });

				}


				/*
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				*/

				function updateEvent(event){

						//console.log(event);

						if(event.editable !== false) {

							if(!moment.isMoment(event.start)){
								event.start = moment(event.start).utc();
							}
							if(!moment.isMoment(event.end)){
								event.end = moment(event.end).utc();
							}

							//console.log(event);

							$('#txtDate', $modal).text(moment(event.start).format("dddd Do MMM YYYY"));

							$('input[name=fldId]', $modal).val(event.id);
							$('input[name=fldRoom]', $modal).val(event.resourceId);
							$('select[name=fldStartTime]', $modal).val(moment(event.start).format("HH:mm"));
							$('select[name=fldEndTime]', $modal).val(moment(event.end).format("HH:mm"));
							$('input[name=fldName]', $modal).val(event.name);
							$('input[name=fldOrganisation]', $modal).val(event.organisation);
							$('input[name=fldEmail]', $modal).val(event.email);
							$('input[name=fldEmailConfirm]', $modal).val(event.email).removeAttr('validation').parent().hide();

							// show buttons
							$('#btnDelete', $modal).show()
							$('#btnUpdate', $modal).show()

							// open modal
							openModal();

							// validation
							$('form', $modal).validation();

							setRoomOptions(roomData);
							setNumPeopleOptions(event.resourceId, event.numpeople);

							$("#roomSelect", $modal).val(event.resourceId).on('change', function(){
								setNumPeopleOptions($(this).val());
								$('input[name=fldRoom]', $modal).val($(this).val());
							});

							// event type
							$('input[name=fldTypeOther]', $modal).val('').removeAttr('validation').parent('div').hide();
							var typeFound = false;
							$('select[name=fldType] option', $modal).each(function(){
								if($(this).val() == event.title){
									$(this).attr('selected', true);
									typeFound = true;
								}
							});
							if(!typeFound){
								$('select[name=fldType]', $modal).val('Other');
								$('input[name=fldTypeOther]', $modal).val(event.title).attr('validation', 'required').parent('div').show();
							}

					    // TYPE change
							$('select[name=fldType]', $modal).on('change', function(e) {
								if($(this).val() == 'Other'){
									$('input[name=fldTypeOther]').attr('validation', 'required').parent('div').show();
								}
								else{
									$('input[name=fldTypeOther]').removeAttr('validation').parent('div').hide();									
								}
							});

					    // CANCEL
							$('#btnCancel', $modal).on('click', function(e) {
									clearModal();
							});

							// DELETE BOOKING
							$('#btnDelete', $modal).on('click', function(e) {
									smoke.confirm("Are you sure you want to delete this booking?", function(e){
										if (e){
											deleteEvent(event);
										} else{ }
									}, {
										ok: "Yes",
										cancel: "No",
										reverseButtons: true
									});
							});

					    // SUBMIT MODAL
							$('#btnUpdate', $modal).on('click', function(e) {

								// valid form
							  if($('form', $modal).validform()) {

							  	//console.log(event.end);
								  // set event data
									var start_time = $('select[name=fldStartTime]', $modal).val();
									var end_time = $('select[name=fldEndTime]', $modal).val();
									var room = $('input[name=fldRoom]', $modal).val();
									var title = ($('input[name=fldTypeOther]', $modal).val() != '') ? $('input[name=fldTypeOther]', $modal).val() : $('select[name=fldType]', $modal).val();
									var longtitle = ($('input[name=fldTypeOther]', $modal).val() != '') ? $('input[name=fldTypeOther]', $modal).val() : $('select[name=fldType] option:selected', $modal).text();
									var name = $('input[name=fldName]', $modal).val();
									var organisation = $('input[name=fldOrganisation]', $modal).val();
									var numpeople = $('select[name=fldNumPeople]', $modal).val();
									var email = $('input[name=fldEmail]', $modal).val();

									// console.log($('input[name=fldNumPeople]', $modal).val());
									// console.log(numpeople);
									// console.log(room);

									// update start and end moments
									event.start = moment(event.start).hour(start_time.substring(0, 2)).minutes(start_time.substring(3));
									event.end = moment(event.end).hour(end_time.substring(0, 2)).minutes(end_time.substring(3));

									// check min booking
									if(roomData[room].minBook > numpeople){
										smokeAlert('This room has a minimum booking of '+roomData[room].minBook+' people');
										return false;
									}

									// update event
									if (title && name && email) {

										event.resourceId = room;
										event.title = title;
										event.longtitle = longtitle;
										event.name = name;
										event.organisation = organisation;
										event.numpeople = numpeople;
										event.email = email;

										//console.log(event);

										// update database
										$.ajax({
					              url: roombookerajax.ajaxurl,
					              type: 'post',
					              dataType: 'json',
					              data: {
					                  action: 'ajax_update_event',
					                  id: event.id,
					                  room: event.resourceId,
					                  time_start: moment(event.start).format("YYYY-MM-DD HH:mm:ss"),
					                  time_end: moment(event.end).format("YYYY-MM-DD HH:mm:ss"),
					                  title: event.title,
					                  organisation: event.organisation,
					                  numpeople: event.numpeople,
					                  name: event.name,
					                  email: event.email
					              },
					              beforeSend: function(){
					                  disableModal();
					                  $('#btnUpdate', $modal).val('Updating...');
					              },
					              success: function( result ) {
				                  if(result.result == 'success'){
					                  smokeAlert('Your booking has been updated');
				                  }
				                  else if(result.result == 'clash'){
				                  	eventClashResponse(result.clashes);
				                  }
					              },
					              complete: function(result){
					                  //console.log('complete');
					                  //console.log(event);
					                  if(result.responseJSON.result == 'success'){
						              		$('#roombooker-calendar').fullCalendar('updateEvent', event);
													  	clearModal();
													  }
												},
												error: function(){
													smokeAlert('An error occured and the booking could not be updated. Please try again');
												}
					          });

									}

							  } 

							  return false;

							});					

						}

				}

				/*
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				https://fullcalendar.io/docs/
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				==========================================================================================
				*/

		    function renderCalendar(eventData) {

		    	var viewChangeSize = 960;
		    	var defaultDate = moment();
		    	var doEdit = false;
		    	var currentView = $(window).width() <= viewChangeSize ? 'agendaDay' : 'agendaTwoDay';

		    	// remove no-js message
		    	$('#roombooker-calendar > span').empty();

		    	// loading edit event
		    	if(jhubEdit == 1 && jhubEditDate != ''  && jhubEditId != '' && jhubEditError == 0) {
			    	currentView = 'agendaDay';
			    	defaultDate = jhubEditDate;
			    	doEdit = true;
		    	}
		    	else if(jhubEdit == 1 && jhubEditError == 1){
		    		smokeAlert('The booking you tried to edit could not be found. Please check the URL in your confirmation email.');
		    	}

					$('#roombooker-calendar').fullCalendar({

						// license purchased specifically for JHub - DO NOT REUSE!!
						schedulerLicenseKey		: '0331103659-fcs-1508783031', 

						defaultView						: currentView,
						defaultDate						: defaultDate,
						header								: {
																			left: 'prev,next today',
																			center: 'title',
																			right: 'agendaDay,agendaTwoDay,agendaWeek,month,listMonth'
																		},
						views 								: {
																			agendaTwoDay: {
																				type: 'agenda',
																				duration: { days: 2 },
																				groupByResource: true,
																				groupByDateAndResource: true
																			},
																			listMonth: { buttonText: 'list' }
																		},																
						timezone							: false,
						weekends							: false,
						weekNumbers						: false,
						contentHeight					: 'auto',
						allDaySlot						: false,
						slotLabelFormat 			: 'HH:mm',
						minTime								: '08:00',
						maxTime								: '21:00',
						slotEventOverlap 			: false,
						slotDuration					: '00:30',
						noEventsMessage 			: 'No bookings to display',
						agendaEventMinHeight	: 50,
						selectable						: true,
						selectHelper					: true,
						selectOverlap					: false,
						unselectAuto					: false,
						selectConstraint 			: {	start: '08:00', end: '21:00' },
						eventConstraint 			: {	start: '08:00', end: '21:00' },
						eventOverlap 					: false,
						displayEventTime 			: true,
						nowIndicator					: true,

						resources							: [
																			{ id: '1', title: roomData[1].shortTitle, eventColor: roomData[1].color },
																			{ id: '2', title: roomData[2].shortTitle, eventColor: roomData[2].color },
																			{ id: '3', title: roomData[3].shortTitle, eventColor: roomData[3].color },
																			{ id: '4', title: roomData[4].shortTitle, eventColor: roomData[4].color }
																		],

						events								: eventData,

						/*
						==========================================================================================
						*/

						eventRender: function(event, element, view ){
						    var desc =''; 
						    if(event.name) desc = event.name;
						    if(event.organisation) desc += ' ('+event.organisation+')';
						    element.find('.fc-content').append('<div class="fc-description">'+desc+'</div>');
						    //console.log(event.resourceId);
						    // set up QTIP
						    if(event.resourceId !== null){
						    	initTooltip(event, element, view);	
						    }
						},

						dayClick: function( date, jsEvent, view ){
							if(view.name == 'month') {
								//console.log(date);
								$('#roombooker-calendar').fullCalendar( 'select', moment(date) );
							}
						},

						windowResize: function(view) {
							//console.log(view.name);
				        if($(window).width() <= viewChangeSize && view.name == 'agendaTwoDay'){
				        	$('#roombooker-calendar').fullCalendar('changeView', 'agendaDay');
				        }
				        else if($(window).width() > viewChangeSize && view.name == 'agendaDay'){
				        	$('#roombooker-calendar').fullCalendar('changeView', 'agendaTwoDay');
				        }
				    },

						selectAllow: function(selectInfo) { 
							var duration = moment.duration(selectInfo.end.diff(selectInfo.start));
							return duration.asHours() <= 10;
				    },

						validRange: function(nowDate) {
							return {
								//start: nowDate.clone().subtract(1, 'day'),
								start: (iau) ? nowDate.clone().subtract(6, 'months') : nowDate.clone().startOf('day'),
								//start: nowDate.clone().add(1, 'hours'),
								end: nowDate.clone().add(6, 'months')
							};
						},

						eventAfterAllRender: function( view ){
							//console.log('eventAfterAllRender');
							if(doEdit !== false){
							  var eventObj = $('#roombooker-calendar').fullCalendar( 'clientEvents', jhubEditId );
							  //console.log(eventObj[0]);
							  if(event === false){
							  	smokeAlert('The booking you tried to edit could not be found. Please check the URL in your confirmation email.');
							  }
							  else{
								  updateEvent(eventObj[0]);
								  doEdit = false;
								}
							}
						},

						select : function(start, end, jsEvent, view, resource) {
							addEvent(start, end, jsEvent, view, resource);
						},

						eventClick : function(event, jsEvent, view){
							updateEvent(event);
						},

						eventDrop: function(event, delta, revertFunc, jsEvent, ui, view){
							updateDragDropEvent(event, revertFunc);
						},

						eventResize: function(event, delta, revertFunc, jsEvent, ui, view){
							updateDragDropEvent(event, revertFunc);
						}

					});


		    } // renderCalendar

		    // CLOSE MODAL
				$('#roombooker .modal-toggle').on('click', function(e) {
				  clearModal();
				});		    

				// INIT ALL THE GOODNESS
	    	renderCalendar(eventData);

			}

		});

})( jQuery );


/*
	* Viewport funciton
	* 2014 David Hyland
	* returns dimensions of current viewport
	*/
	function viewport() {
		var e = window, a = 'inner';
		if (!('innerWidth' in window )) {
			a = 'client';
			e = document.documentElement || document.body;
		}
		return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
	};
