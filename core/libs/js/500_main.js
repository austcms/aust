/**
 * Arquivo principal Javascript
 *
 * @since 01/01/2009
 */

var arquivoAjax = "core/libs/ajax.php";
var privilegio_escolhido = false;

/**
 * Função de Start
 */
$(document).ready(function(){
	lightbox();

	setTimeout(function(){
		$('#notice, #failure').animate({
				opacity: 0.4
			}, 1000, 'linear');
	}, 3000);
	
	// tooltip design
	$("a.tooltip-trigger").css({'opacity': 0.5})
	$("a.tooltip-trigger").mouseenter(function(){
		$(this).stop().animate({'opacity': 1}, 150);
	}).mouseleave(function(){
		$(this).stop().animate({'opacity': 0.5});
	});
	
	// show sql debug messages
	$('a#show_sql_debug_messages_button').click(function(){
		var shouldShow = '0';
		var messages = $("#sql_debug_messages");
		
		$(this).html('Debug SQL ');
		if( messages.is(':visible') ){
			$(this).append('<span class="arrow">▼</span>');
			messages.fadeOut();
		} else {
			shouldShow = '1';
			$(this).append('<span class="arrow">▲</span>');
			messages.fadeIn().removeClass('hidden');
		}
		
		$.post(
		arquivoAjax+'?lib=show_sql_debug_messages',
		{ show: shouldShow },
				function(response){

				}
		);
	
	});
	
//	$("a[name=modal]").first().click();

	$('.check_checkboxes').attr('checked', false).click(function(){
		
		window.status = $(this).parents('table').length;
		$(this).parents('table').find('input[type=checkbox]').attr('checked', $(this).attr('checked'));
	});
	
	// changeViewMode
	$('a[class=change_viewmode]').click(function(e) {
		changeViewMode(this);
	});
	
	// Confirmation alerts
	$(".js_confirm").live('click', function(){
		return confirm('Você tem certeza desta decisão?');
	});
	
	if($('div.campooptions').length > 0){$('div.campooptions').hide();}
	if($('div.est_options').length > 0){$('div.est_options').hide();}
	if($('div#categoriacontainer_priv').length > 0) {$('div#categoriacontainer_priv').hide();}

	// Mod privilégios - formulário
	if(privilegio_escolhido != ''){
		form_privilegio('1')
	}

	/* WIDGETS - DRAG&DROP */
	$("#sortable1, #sortable2").sortable({
		connectWith: '.connectedSortable',
		handle: 'h3',
		opacity: 0.4,
		containment: 'document',
		dropOnEmpty: true,
		stop: function () {
			//alert('oi');
			var order1 = $('#sortable1').sortable('serialize', {key: 'widgets[1][]'});
			var order2 = $('#sortable2').sortable('serialize', {key: 'widgets[2][]'});

			$.ajax({
				url: arquivoAjax+'?lib=widgets&location=dashboard&userId='+userId,
				data: order1+'&'+order2,
				complete: function(response){
					//alert(response.responseText);
					//$('#permissoesAtuais').html(response.responseText);
				},
				type: "get",
				dataType: "html"
			});
		}

	});

	// .dragdrop
	
	$(".dragdrop").sortable({
		opacity: 0.4,
		dropOnEmpty: true,
		stop: function () { }
	});
	
	// Hints
	$("span.hint a").each(function(){
		$(this).tooltip({
			tip: '#'+$(this).attr('name'),
			effect: 'toggle',// whichEffect,
			opacity: 1,
			offset: [10, 10],
			events: {
				def: 'click, mouseout'
			}
		})
	});

	// Tooltips
	/*
	$(".tooltip-test").tooltip( {
		effect: 'fade',
		position: "bottom center",
		relative: false,
		events: {
			def: 'click, click'
		}
		
	});
	*/

	// Panes
	// perform JavaScript after the document is scriptable.
	$(function() {
		// setup ul.tabs to work as tabs for each div directly under div.panes
		$("ul.tabs").tabs("div.panes > div");
	});

});

/*
 * MÓDULOS
 */
/*
 * Lightbox para criação de categorias
 */
function newCategory(lbId){

	var givenAN = $('#'+lbId+' .aust_node_hidden').val();
	var urlAN = $.getURLParam('aust_node');
	var catName = $('#'+lbId+' input[name=lb[frmnode_id]]').val();
	var categoryInput = $('#'+lbId+' input[name=category_input]').val();

	if( catName == '' ){
		alert('Você não especificou o título da categoria a ser criada.');
		return false;
	}

	if( urlAN != givenAN )
		return false;

	$.ajax({
		url: arquivoAjax+'?lib=new_category',
		data: "urlAN="+urlAN+'&givenAN='+givenAN+'&catName='+catName,
		complete: function(response){

			if( response.responseText > 0 ){
				$('#'+lbId+' .lb_content').hide();
				$('#'+lbId+' .lb_content').html('<div class="sucesso">Dados salvos com sucesso.</div>');
				$('#'+lbId+' .lb_content').fadeIn('fast');

				updateCategorySelect( categoryInput, givenAN, response.responseText);

				setTimeout(function(){
					lightboxClose();
				}, 2500);

			} else {
				$('#'+lbId+' .lb_content').hide();
				$('#'+lbId+' .lb_content').html('<div class="insucesso">Ocorreu um erro inesperado.</div>');
				$('#'+lbId+' .lb_content').fadeIn('fast');
				setTimeout(function(){
					lightboxClose();
				}, 2000);
			}
		},
		beforeSend: function(){
			$('#'+lbId+' .lb_content').html('Processando...');
		},
		error: function(){
			$('#'+lbId+' .lb_content').hide();
			$('#'+lbId+' .lb_content').html('<div class="insucesso">Ocorreu um erro inesperado.</div>');
			$('#'+lbId+' .lb_content').fadeIn('fast');
			setTimeout(function(){
				lightboxClose();
			}, 2000);

		},
		type: "post",
		dataType: "html"
	});
	
	return false;
}

function updateCategorySelect(id, node, selected){

	$.ajax({
		url: arquivoAjax+'?lib=update_category_select',
		data: "node="+node+"&selected="+selected,
		complete: function(response){

			if( response.responseText != '' ){
				$("select[id='"+id+"']").html(response.responseText);
			} else {
				location.reload();
			}
		},
		beforeSend: function(){
			$("[id='"+id+"']").html('<option value="">Processando...</option>');
		},
		error: function(){
			location.reload();
		},
		type: "post",
		dataType: "html"
	});

	return false;
}

/**
 * Responsável pelo gerenciamento das configurações de permissões de usuários
 */
function mostraPermissoes(id, tipo){
	// Carrega dados via Ajax

	$('#permissoesAtuais').html('<p style="text-align: center;"><img src="core/user_interface/img/loading.gif" /><br />Carregando...</p>');
	$.ajax({
		url: arquivoAjax+'?lib=user_permissoes',
		data: "id="+id+"&tipo="+tipo,
		complete: function(response){
			//alert(response.responseText);
			$('#permissoesAtuais').html(response.responseText);
		},
		type: "post",
		dataType: "html"
	});
	return false;
}

/**
 * Altera permissões de usuários
 *
 * @param posted O que será gravado
 * @param este =this
 * @return bool
 */
function alteraPermissao(posted, este){
	//alert('soijarg');
	$.ajax({
		url: arquivoAjax+'?lib=user_permissoes&action=altera_permissao',
		data: posted+'&value='+este.checked,
		complete: function(response){
			//alert(response.responseText);
			//$('#permissoesAtuais').html(response.responseText);
		},

		type: "post",
		dataType: "html"
	});
	return false;
}

function form_supervisionado(svalor){
	if(svalor == "sim"){
		$("p#supervisionadosim").css({
			color: "green"
		});
		$("p#supervisionadonao").css({
			color: "#999999"
		});
	} else if(svalor == "nao"){
		$("p#supervisionadosim").css({
			color: "#999999"
		});
		$("p#supervisionadonao").css({
			color: "green"
		});
	}
}

function form_insert_data(identification,dia,mes,ano){
	$("div#" + identification).slideUp("fast");
	setTimeout(function(){
		$("div#" + identification).html('<input type="text" name="frmdia" value="'+ dia +'" maxlength="2" size="1" />/<input type="text" name="frmmes" value="'+ mes +'" maxlength="2" size="1" />/<input type="text" name="frmano" value="'+ ano +'" maxlength="4" size="4" />' +
			'<p class="explanation">O conteúdo terá a data acima.</p>' +
			'<p class="explanation">O formato da data deve ser dd/mm/aaaa. Não esqueça, dia e mês dois dígitos e ano quatro dígitos</p>');
		$("div#" + identification).slideDown("fast");
	}, 300);
//	$("p#hierarquia" + svalor).css({color: "green"});
}

function form_bannerstartwhen(startwhen){
	//alert(startwhen);
	if(startwhen == "now"){
		$("p#infoinicio").html("O banner será mostrado no site assim que você cadastrá-lo.");
	} else if(startwhen == "date"){
		$("p#infoinicio").html('Caso queira especificar uma data para que o banner comece a ser mostrado no ' +
			'site, digite o DIA, MÊS e ANO abaixo.' +
			'<br />' +
			'<INPUT TYPE="text" NAME="frmstart_dia" SIZE="1" /> / ' +
			'<INPUT TYPE="text" NAME="frmstart_mes" SIZE="1" /> / ' +
			'<INPUT TYPE="text" NAME="frmstart_ano" SIZE="3" />');
	}
}

function form_verifyresumo(valor){
	if(valor == "Depoimento"){
		$("div#resumo").hide();
		$("div#resumo").html('<textarea name="frmresumo" rows="5" cols="49"></textarea>' +
			'<p style="margin: 0; font-size: 12px; color: #999999">' +
			'Um resumo que aparecerá na página principal. Deve ser curto e suscinto.'+
			'</p>');
		$("div#resumo").slideDown("slow");
	} else {
		$("div#resumo").slideUp("slow");
		$("div#resumo").html('<input type="hidden" value="" name="frmresumo" />');
		$("div#resumo").slideDown("slow");
	}
	if(valor == "Menu"){
		$("#exists_titulo").html('Exemplo: <em>Quem somos?</em>');
	} else {
		$("#exists_titulo").html('Exemplo: <em>Brasil vence Holanda nos Estados Unidos</em>');
	}
	
}


function verifytwovars(var1, var2){
	if(var1 == var2)
		return true;
	else
		return false;
}

function changestatus(local, text){
	$('' + local).html('<strong style="color: orange">Explicação:</strong><br />' + text);
//	alert('oi2');
}

// executes an onchange function after 750ms (or specified delay)
function categoriasub(dados) {
	clearTimeout( soc_id );
	soc_id = setTimeout( 'buscacategoriasub('+dados.value+');', 500 );
} 
function buscacategoriasub(dados){
	$.post("ajax.php", {
		acao: "subordinado",
		father_id: dados
	}, function(txt){
		//alert(txt);
		if(txt > 0){
			alert(txt);
		//									$("p#exists_" + scampo).html(iffalse);
		//									$("p#exists_" + scampo).css({color: iffalsecolor});
		} else {
			alert(txt);
		//									$("p#exists_" + scampo).html(iftrue);
		//									$("p#exists_" + scampo).css({color: iftruecolor});
		}
	})
}
// global timer ID for the safeOnChange function.
var soc_id = null;

/*
 * GERENCIAMENTO DE USUÁRIOS
 */
function form_hierarquia(svalor){
	//alert(svalor);
	//$("admin-hierarquia").css({color: "red"});
	$(".admin-hierarquia").fadeOut().animate({opacity: 1.0}, 400);
	$('p#hierarquia' + svalor).fadeIn();
	//$("p#hierarquia" + svalor).css({color: "green"});
}

// verifica se um campo já existe
function alreadyexists(svalor, scampo, iftrue, iftruecolor, iffalse, iffalsecolor, inwhatdbtable){
	if(svalor != ""){
		$.post("ajax.php", {
			acao: "verifyifexists",
			valor: svalor,
			campo: scampo,
			dbtable: inwhatdbtable
		}, function(txt){
			//alert(txt);
			if(txt > 0){
				$("p#exists_" + scampo).html(iffalse);
				$("p#exists_" + scampo).css({
					color: iffalsecolor
				});
			} else {
				$("p#exists_" + scampo).html(iftrue);
				$("p#exists_" + scampo).css({
					color: iftruecolor
				});
			}
		})
	}

//	return false;
}




