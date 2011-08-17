/*
 * SCRIPTS RESPONSÁVELS PELA INTERAÇÃO COM USUÁRIOS
 */


// Opções de navegação em estrutura na página principal
var current_est;
function est_options(este){
	if(current_est != este){
		$('.est_options').hide();
		current_color = $("#est_options_info_"+este).css("color");
		$(".est_options_info").css({fontWeight: "normal", color: current_color});
		$('#est_options_'+este).fadeIn('fast');
		$("#est_options_info_"+este).css({fontWeight: "bold", color: "black"});
	}
	current_est = este;


}

