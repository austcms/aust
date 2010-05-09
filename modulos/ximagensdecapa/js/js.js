/* 
 * Este arquivo contém funções JavaScript específicas deste módulo
 *
 * Requer JQuery
 */

// Função que Salva no DB que determinada estrutura pode usar galeria de imagens
function selectLiberacao(este){
    // se for relacional um-para-um
    //alert(este.name + ' - ' + este.checked);
    $.post(include_baseurl+"/js/ajax.php", {
                    action: "selectLiberacao",
                    modulo: este.value,
                    estrutura: este.name,
                    clicado: este.checked

                }, function(txt){
                    alert('Resultado: ' + txt);
                })

}

// Função que Salva no DB que determinada estrutura pode usar imagens de capa
function selectImagensDeCapa(este){
    // se for relacional um-para-um
    //alert(este.name + ' - ' + este.checked);
    $.post(include_baseurl+"/js/ajax.php", {
                    action: "selectImagensDeCapa",
                    modulo: este.value,
                    estrutura: este.name,
                    clicado: este.checked

                }, function(txt){
                    alert('Resultado: ' + txt);
                })

}



