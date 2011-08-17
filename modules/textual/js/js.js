/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */

// Função que mostra os cadastros no formato <select>
function agendaShowCalendar(este){
	$(".agenda_datetime").show();

	$.post(include_baseurl+"/js/calendar/for_form.php", {
					action: "create"
				}, function(txt){
					//alert(txt);
					//$('#agenda_start_datetime').html(txt);
					//SetupCampoRelacionalCampos($('#campooptions_tabela option:selected').val(), id, inc);
				})

}

/*
 * Calendário especifica o dia de início e o dia final
 */
function agendaSetFromEndDate() {
	var dates = $('#start_date, #end_date').datepicker({
		defaultDate: "+1w",
		//changeMonth: true,
		numberOfMonths: 1,
		onSelect: function(selectedDate) {
			var option = this.id == "start_date" ? "minDate" : "maxDate";
			var instance = $(this).data("datepicker");
			var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
			dates.not(this).datepicker("option", option, date);
		}
	});
}

/*
 * Calendário de apenas um dia
 */
function agendaSetOneDay() {
	var dates = $('#start_date').datepicker({
		defaultDate: "+1w",
		//changeMonth: true,
		numberOfMonths: 1,
		onSelect: function(selectedDate) {
			var option = this.id == "start_date" ? "minDate" : "maxDate";
			var instance = $(this).data("datepicker");
			var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
			dates.not(this).datepicker("option", option, date);
		}
	});
}

function agendaSetTimeAllDay(este){
	if( $(este).is(':checked') ){
		$("#agendaTime").hide();
	} else {
		$("#agendaTime").show();
	}
}

function agendaAdjustTime(select){

	//alert( $('#start_time').val() + ' - ' + $('#end_time').val());
	if( select == 'start' ){
		// start_time maior que end_time
		if( $('#start_time').val() > $('#end_time').val() ){
			$('#end_time option[value='+$('#start_time').attr('value')+']').attr( 'selected', true );
		}
	} else if( select == 'end' ){
		// start_time maior que end_time
		if( $('#start_time').val() > $('#end_time').val() ){
			$('#start_time option[value='+$('#end_time').attr('value')+']').attr( 'selected', true );
		}
	}

}
