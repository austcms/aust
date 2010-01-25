
function AbreGaleriaInterna(sid){
	window.open('inc/galeriainterna/inc_galeriainterna_geral.php?sid=' + sid,'_blank','width=500,height=500,status=no,menubar=0,scrollbar=1,toolbar=0');	
}

/**
 * LIGHTBOX
 */
/**
 * Seleciona os elementos a com atributo name="modal"
 * e atribui evento a eles para amostragem de lightbox
 */
$('a[name=modal]').click(function(e) {
    /*
     * Cancela o comportamento padrão do link e Armazena o atributo href do link
     */
    e.preventDefault();
    var id = "#" + $(this).attr('class');

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

    //alert(id);

    $(id).fadeIn(800);

    /**
     * Focus
     */
    if( id == "#modalcarregar" ){
        $("#modalcarregar input[name=email]").focus();
    } else if( id == "#modalgravar" ) {
        $("#modalgravar input[name=email]").focus();
    }
});

/**
 * Dá comando ao botão de fechar do Lightbox
 */
$('.window .close').click(function(e){
    e.preventDefault();
    $('#mask, .window').fadeOut("normal");
});

