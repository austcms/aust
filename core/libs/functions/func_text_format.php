<?php

/**
 * tooltip()
 *
 * Gera código HTML necessário para criação dinâmica de Tooltips
 *
 * @param string $str
 */
function tooltip($str = ''){
    if( !empty($str) ){
        $random = substr( sha1( rand(0, 100) ), rand(5,20));
        ?>
        <span class="hint">

            <a href="javascript: void(0)" class="tooltip-trigger" name="<?php echo $random;?>">&nbsp;</a>

            <div class="tooltip" id="<?php echo $random;?>">
                <div class="top"></div>
                <div class="text"><p><?php echo $str ?></p></div>
                <div class="bottom"></div>
            </div>
        </span>
        <?php
        return true;
    }
}// fim tooltip()

/**
 * tt()
 *
 * ALIAS PARA TOOLTIP()
 *
 * @param string $str
 * @return bool
 */
function tt($str = ''){
    return tooltip($str);
}

function lbCategoria($austNode=''){
    if( empty($austNode) )
        return false;

    $random = substr( sha1( rand(0, 100) ), rand(5,20));
    ?>

    <div id="lb_categoria_<?php echo $random; ?>" class="window lb_categoria">
        <div class="header">
            <h2>Nova Categoria</h2>
            <a href="#" class="close"></a>
        </div>
        <div class="lb_content">
            <input type="hidden" class="aust_node_hidden" value="<?php echo $_GET['aust_node']; ?>" />
            <table class="form">
                <tr>
                    <td valign="top" class="titulo">
                        <label for="lb_input_categoria_<?php echo $random; ?>">Título:</label>
                    </td>
                    <td>
                        <input name="lb[frmcategoria]" id="lb_input_categoria_<?php echo $random; ?>" class="text lb_focus" />
                        <p class="explanation">
                            Digite o nome da categoria. (Começa com letra maiúscula e não leva
                            ponto final)
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                    <center>
                        <input type="submit" value="Salvar"
                               onclick="newCategory('lb_categoria_<?php echo $random; ?>'); return false;" />
                    </center>
                    </td>
                </tr>
            </table>

        </div>
        <div class="footer">
        </div>
    </div>
    <div class="nova_categoria">
        <?php
        /*
         * Link para inserir nova categoria. O lightbox pode
         * ser encontrado no arquivo principal da UI.
         */
        ?>
        <a href="#box" name="modal" class="lb_categoria_<?php echo $random; ?>"></a>
    </div>
    <?php
}
?>