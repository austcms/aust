/*
 * JS MÓDULO PRIVILÉGIOS
 *
 *
 */
function form_privilegio(este){
    if(este == 0){
        $('#categoriacontainer_priv').html('<div id="categoriacontainer"><input type="hidden" name="categorias_id" value="" /></div>');
        $('#categoriacontainer_priv').slideUp();
    } else {
        $('#categoriacontainer_priv').html('<div id="categoriacontainer">'+$('#categoriaselect').html()+'</div>');
        $('#categoriacontainer_priv').slideDown();
    }
}


function alteraRelacionamentos(posted, este){
    $.ajax({
        url: include_baseurl+"/js/ajax.php",
        data: posted+'&value='+este.checked+'&action=altera_relacionamentos',
        complete: function(response){
            //alert(response.responseText);
            //$('#permissoesAtuais').html(response.responseText);
        },

        type: "post",
        dataType: "html"
    });
    return false;
}

