
function AbreGaleriaInterna(sid){
	window.open('inc/galeriainterna/inc_galeriainterna_geral.php?sid=' + sid,'_blank','width=500,height=500,status=no,menubar=0,scrollbar=1,toolbar=0');	
}

function lightbox(){
   	$(document).ready(function() {
		//seleciona os elementos a com atributo name="modal"
		$('a[name=modal]').click(function(e) {
		//cancela o comportamento padrão do link
			e.preventDefault();

		//armazena o atributo href do link
			var id= $(this).attr('href');

		//armazena a largura e a altura da tela
			var maskHeight = $(document).height();
			var maskWidth = $(document).width();

		//Define largura e altura do div#mask iguais ás dimensçoes da tela
			$('#mask').css({'width':maskWidth,'height':maskHeight});

		//efeito de transição
			$('#mask').fadeIn(700);
			$('#mask').fadeTo("slow", 0.9);

		//armazena a largura e a altura da janela
			var winH = $(window).height();
			var winW = $(window).width();

		//centraliza na tela a janela popup
			var top = (winH - $('#box').height()) / 2;
			var left = (winW - $('#box').width()) / 2;

			$('#box').css('top', top);
			$('#box').css('left', left);

		//efeito de transição
			$(id).fadeIn(800);
			});

		//se o botão fechar for clicado
			$('.window .close').click(function(e){

		//cancela o comportamento padrão do link
				e.preventDefault();
				$('#mask, .window').hide();
			});
		//se div#mask for clicado
				//$('#mask').click(function() {
				//$(this).hide();
				//$('window').hide();
			//});
	});

}



