/*
 * JS MÓDULO PRIVILÉGIOS
 *
 *
 */

var privilegio_escolhido = false;


function form_privilegio(este){
    if(este == 0){
        $('#categoriacontainer_priv').html('<div id="categoriacontainer"><input type="hidden" name="frmcategorias_id" value="" /></div>');
        $('#categoriacontainer_priv').slideUp();
    } else {
        $('#categoriacontainer_priv').html('<div id="categoriacontainer">'+$('#categoriaselect').html()+'</div>');
        $('#categoriacontainer_priv').slideDown();
    }
}


