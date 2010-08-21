
function AbreGaleriaInterna(sid){
	window.open('inc/galeriainterna/inc_galeriainterna_geral.php?sid=' + sid,'_blank','width=500,height=500,status=no,menubar=0,scrollbar=1,toolbar=0');	
}

var currentLbHtml = '';
var currentLbId = '';

function lightbox(){
    $('a[name=modal]').click(function(e) {

        /*
         * Cancela o comportamento padrão do link e Armazena o atributo href do link
         */
        e.preventDefault();
        var id = '#' + $(this).attr('class');
        
        currentLbId = id;
        currentLbHtml = $(id+' .lb_content').html();

        /*
         * Armazena a largura e a altura da tela
         */
        var maskHeight = $(document).height();
        var maskWidth = $(document).width();

        /*
         * Define largura e altura do div#mask iguais às dimensoes da tela
         */
        $('#mask').css({'width':maskWidth,'height':maskHeight});

        /*
         * Efeito de transição
         */
        $('#mask').fadeIn(400);
        $('#mask').fadeTo("slow", 0.9);
        
        /*
         * Armazena a largura e a altura da janela
         */
        var winH = $(window).height();
        var winW = $(window).width();

        /*
         * Centraliza na tela a janela popup
         */
        var top = (winH - $( id ).height()) / 2;
        var left = (winW - $( id ).width()) / 2;

        $( id ).css('top', top);
        $( id ).css('left', left);

        $(id).fadeIn(800);
        $(id+' input.lb_focus').focus();
        $(id).keydown( function( e ) {
            if( e.which == 27) {  // escape, close box
                e.preventDefault();
                lightboxClose();
            }
        });

    });

    /**
     * Dá comando ao botão de fechar do Lightbox
     */
    $('.window .close').click(function(e){
        e.preventDefault();
        lightboxClose();
    });

}

function lightboxClose(){
    $('#mask, .window').fadeOut("normal");
    setTimeout(function(){
        $(currentLbId+' .lb_content').html(currentLbHtml);
    }, 400);
}

// viewmode - thumbs, list
function changeViewMode(este){

	var url = 'adm_main.php?section=conteudo&action=view_items&page='+page+'&aust_node='+austNode;
	var viewMode = $(este).attr('name');
	
    $('.viewmode').removeClass('pressed');
    $(este).find('span.viewmode').addClass("pressed");
	
    $.post(
		url, {
			viewonly: 'yes',
			viewMode: viewMode
	    },
		function(txt){
	        $('#listing_table').html(txt);
	    });	
}





