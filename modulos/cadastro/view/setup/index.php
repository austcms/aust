<?php
/**
 * Controller principal deste módulo
 *
 * @package SetupController
 * @name nome
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 06/07/2009
 */



?>
<h2>Setup de cadastro: configuração inicial</h2>
<form action="" method="post" class="normal">

<?php
/**
 * Escreve cada exPOST
 */
foreach($exPOST as $chave=>$valor){
    echo '<input type="hidden" name="'.$chave.'" value="'.$valor.'" />';
}
?>
<input type="hidden" name="setupAction" value="criarcampos" />

<p>Aqui nós configuraremos esta estrutura.</p>
<div class="campo">
    <label>Quantos campos terá seu cadastro?</label>
    <div class="input">
    <select name="qtd_campos" style="width: 70px;">
        <?php
        // cria um select com 20 números
        for($i = 1; $i <= 100; $i++){
        ?>
            <option value="<?php echo $i;?>"><?php echo $i;?></option>
        <?php
        }
        ?>
    </select>
    </div>
</div>
<div class="campo">
    <label>Será necessário aprovação para completar cadastro?</label>
    <div class="input">
        <input type="radio" name="aprovacao" value="1" /> Sim, será necessária aprovação de um administrador após cadastro<br />
        <input type="radio" checked="checked" name="aprovacao" value="0" /> Não, qualquer usuário poderá se cadastrar
    </div>
</div>
<div class="campo">
    <label>Se for necessário que o usuário digite uma senha para poder se cadastrar, especifique:</label>
    <div class="input">
    <input type="text" name="pre_senha" value="" />
    </div>
</div>
<div class="campo">
    <label>Parágrafo introdutório ao formulário:</label>
    <div class="input">
    <textarea name="descricao" cols="50" rows="3"></textarea>
    </div>
</div>
<div class="campo">
    <input type="submit" value="Enviar!" class="submit" />
</div>

</form>