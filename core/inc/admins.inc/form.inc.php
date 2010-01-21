<?php
/*
 * FORMULÁRIO ADMINS
 *
 * Este formulário serve tanto para edição como para criação, dependendo da variável $fm('criar','editar')
 */

$fm = (empty($_GET['fm'])) ? $fm = 'criar' : $fm = $_GET['fm'];
$w = (empty($_GET['w'])) ? $w = $administrador->LeRegistro('id') : $w = $_GET['w'];

$dados = array(
    'id' => '',
    'nome' => '',
    'titulo' => '',
    'senha' => '',
    'email' => '',
    'telefone' => '',
    'biografia' => '',
    'celular' => '',
    'sexo' => '',
    'login' => '',
);

if($fm == 'editar'){
    $sql = "SELECT
                *
            FROM
                admins
            WHERE
                id='".$w."'";
    $query = $conexao->query($sql);
    $dados = $query[0];
    //echo $sql;
}
?>

<p style="margin-top: 15px;">
    <a href="adm_main.php?section=admins"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>
<h2>Novo Usuário</h2>
<p>
    <strong>Cadastre</strong> a seguir um novo usuário para este gerenciador.
</p>

<form method="post" action="adm_main.php?section=admins&action=gravar" class="simples">
<input type="hidden" name="metodo" value="<?php echo $fm?>">
<input type="hidden" name="w" value="<?php ifisset($dados['id'])?>">
<input type="hidden" name="frmsupervisionado" value="0" />
<input type="hidden" name="frmautor" value="<?php echo $administrador->LeRegistro('id');?>" />
<table width="100%" border="0" cellpadding=0 cellspacing="3">
<col width="250">
<col>
<tr>
    <td valign="top">Hierarquia de usuário:</td>
    <td>
        <div class="input_painel">
            <div class="containner">
                <?php
                /*
                 * Seleciona a hierarquia do usuário
                 *
                 * Se for edição do próprio perfil, não permite modificação
                 */
                if( $administrador->LeRegistro('id') != $dados['id'] ){
                    ?><div style=" width: 120px; display: table; float: left;"><?php

                    $sql = "SELECT
                                nome, id, descricao
                            FROM
                                admins_tipos
                            WHERE
                                publico=1";
                    $query = $conexao->query($sql);
                    foreach($query as $result){
                        ?>
                        <input type="radio" <?php if($fm == 'editar') makechecked($result['id'], $dados['tipo']); else echo 'checked'; ?> name="frmhierarquia" value="<?php echo $result['id']?>" onclick="javascript: form_hierarquia(this.value);" /> <?php echo $result['nome']?><br />
                        <?php
                    }
                    ?>
                    </div>
                    <div style="width: 270px; min-height: 100px; float: right;">

                    <?php
                        foreach($query as $result){
                            ?>
                            <p class="admin-hierarquia" id="hierarquia<?php echo $result['id']?>"><?php echo str_replace($result['nome'], '<strong>'.$result['nome'].'</strong>', $result['descricao']);?></p>
                            <?php
                        }
                }
                /**
                 * Não pode alterar a hierarquia
                 */
                else {
                    $sql = "SELECT
                                nome, id, descricao
                            FROM
                                admins_tipos
                            WHERE
                                id='".$dados['tipo']."'";
                        //echo $sql;
                    $query = $conexao->query($sql);
                    $result = $query[0];
                    ?>
                    Sua hierarquia é como <strong><?php echo $result['nome'];?></strong> do sistema. Somente <em>administradores</em> podem
                    modificar a hierarquia de um usuário.
                <?php
                }
                ?>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td valign="top">Nome completo: <?php echo $dados['nome'];?></td>
    <td>
        <INPUT TYPE="text" NAME="frmnome" SIZE="40" value="<?php ifisset($dados['nome'])?>" <?php if($fm == 'criar'){ echo 'onKeyUp="javascript: alreadyexists(this.value, \'nome\', \'Digite o nome completo do usuário que será cadastrado.\',\'#999999\',\'Verifique se este usuário já não existe, pois este nome já foi cadastrado.\',\'red\',\'adm\');"'; } ?> />
        <p style="margin: 0; font-size: 12px; color: #999999" id="exists_nome">
            Digite o nome completo do usuário que será cadastrado.
        </p>
    </td>
</tr>
<tr>
    <td valign="top">Nome de usuário: </td>
    <td>
        <INPUT TYPE="text" NAME="frmlogin" value="<?php ifisset($dados['login'])?>" <?php if($fm == 'criar'){ echo ' onKeyUp="javascript: alreadyexists(this.value, \'login\', \'Este nome de usuário está disponível para cadastro.\',\'green\',\'Este nome de usuário já foi cadastrado. Escolha outro.\',\'red\',\'adm\');"'; } ?> SIZE="30" />
        <p style="margin: 0; font-size: 12px; color: #999999" id="exists_login">
            Digite um nome de usuário.
        </p>
    </td>
</tr>
<?php
/*
 * Se for para criar novo usuário, mostra campo de senha
 */
if($fm == 'criar'){ ?>
    <tr>
        <td valign="top">Senha de acesso: </td>
        <td>
            <INPUT TYPE="password" NAME="frmsenha" SIZE="30">
            <p style="margin: 0; font-size: 12px; color: #999999">
                Digite uma senha (ela pode ser substituída depois).
            </p>
        </td>
    </tr>
<?php
/*
 * Se for para editar um usuário, mostra campo de senha na forma hidden
 */
} else if($fm == 'editar'){ ?>
    <INPUT TYPE="hidden" NAME="frmsenha" value="<?php echo $dados['senha']?>" />
<?php } ?>
<tr>
    <td valign="top">Email para contato: </td>
    <td>
        <INPUT TYPE="text" NAME="frmemail" SIZE="30" value="<?php ifisset($dados['email'])?>" <?php if($fm == 'criar'){ echo 'onKeyUp="javascript: alreadyexists(this.value, \'email\', \'Digite um email para contato.\',\'#999999\',\'Este email já foi cadastrado. Escolha outro.\',\'red\',\'adm\');"'; } ?> />
        <p style="margin: 0; font-size: 12px; color: #999999" id="exists_email">
            Digite um email para contato.
        </p>
    </td>
</tr>
<tr>
    <td>Telefone para contato: </td>
    <td>
        <INPUT TYPE="text" NAME="frmtelefone" value='<?php ifisset($dados['telefone'])?>' SIZE="30" />
    </td>
</tr>
<tr>
    <td>Celular para contato: </td>
    <td>
        <INPUT TYPE="text" NAME="frmcelular" value='<?php ifisset($dados['celular'])?>' SIZE="30" />
    </td>
</tr>
<tr>
    <td>Sexo: </td>
    <td>
        <select name="frmsexo">
            <option <?php makeselected('masculino', $dados['sexo']) ?> value="masculino">Masculino</option>
            <option <?php makeselected('feminino', $dados['sexo']) ?> value="feminino">Feminino</option>
        </select>
    </td>
</tr>
<tr>
    <td valign="top">Biografia: </td>
    <td style="padding-bottom: 30px;">
        <textarea name="frmbiografia" cols="50" rows="5"><?php ifisset($dados['biografia'])?></textarea>
        <p class="explanation">
            Fale um pouco sobre você.
        </p>
    </td>
</tr>
<!--
<tr>
    <td valign="top">
        <p style="margin: 0;">Este usuário será supervisionado?</p>

    </td>
    <td valign="top">
        <select name="frmsupervisionado" onChange="javascript: form_supervisionado(this.value);">
            <option value="sim">Sim</option>
            <option value="nao">Não</option>
        </select>
        <p style="margin: 0; font-size: 12px; color: green;" id="supervisionadosim">
            <strong>Sim</strong> significa que todo conteúdo inserido por este usuário
            precisará da aprovação de um moderador ou administrador.
        </p>
        <p style="margin: 0; font-size: 12px; color: #999999; border-top: 1px dashed silver" id="supervisionadonao">
            <strong>Não</strong> supervisionado significa que este usuário pode
            adicionar conteúdos a vontade.
        </p>
        <p class="explanation">
            Exceto artigos. Colunistas têm livre acesso. Você pode, contudo, bloquear o
            usuário a qualquer momento.
        </p>

    </td>
</tr>
-->
<tr>
<td colspan="2"><center><INPUT TYPE="submit" VALUE="Entrar"></center></td>
</tr>
</table>

</form>
<p style="margin-top: 15px;">
    <a href="adm_main.php?section=admins"><img src="img/layoutv1/voltar.gif" border="0" /></a>
</p>