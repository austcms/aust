<?php
/*
 * FORMULÁRIO ADMINS
 *
 * Este formulário serve tanto para edição como para criação, dependendo da variável $fm('criar','editar')
 */

$fm = (empty($_GET['fm'])) ? $fm = 'criar' : $fm = $_GET['fm'];
$w = (empty($_GET['w'])) ? $w = User::getInstance()->LeRegistro('id') : $w = $_GET['w'];

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
                admins.*,
				admins_photos.id as pid,
				(
					SELECT id FROM admins_photos WHERE image_type='secondary' AND admin_id=admins.id
				) as sid
            FROM
                admins
			LEFT JOIN
				admins_photos
			ON
				admins.id=admins_photos.admin_id
				AND admins_photos.image_type='primary'
            WHERE
                admins.id='".$w."'";
    $query = Connection::getInstance()->query($sql);
    $dados = $query[0];
    //echo $sql;
}
?>

<p style="margin-top: 15px;">
    <a href="adm_main.php?section=admins"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>
<h2>Novo Usuário</h2>
<p>
    <strong>Cadastre</strong> a seguir um novo usuário para este gerenciador.
</p>

<form method="post" action="adm_main.php?section=admins&action=gravar" enctype="multipart/form-data">
<input type="hidden" name="metodo" value="<?php echo $fm?>">
<input type="hidden" name="w" value="<?php ifisset($dados['id'])?>">
<input type="hidden" name="frmsupervisionado" value="0" />
<input type="hidden" name="frmautor" value="<?php echo User::getInstance()->LeRegistro('id');?>" />

<table cellpadding=0 cellspacing="3" class="form">
<tr>
    <td class="first" valign="top">Função:</td>
    <td class="second">
        <div class="input_painel">
            <div class="containner">
                <?php
                /*
                 * Seleciona a hierarquia do usuário
                 *
                 * Se for edição do próprio perfil, não permite modificação
                 */
                //vd( in_array(strtolower(User::getInstance()->tipo), array('root','webmaster','administrador' ) ) );
                if( User::getInstance()->LeRegistro('id') != $dados['id'] AND
                    in_array(strtolower(User::getInstance()->tipo), array('root','webmaster','administrador' ) ) )
                {
                    ?><div style=" width: 120px; display: table; float: left;"><?php

                    $sql = "SELECT
                                nome, id, descricao
                            FROM
                                admins_tipos
                            WHERE
                                publico=1";
                    $query = Connection::getInstance()->query($sql);
                    foreach($query as $result){
                        ?>
                        <input type="radio" <?php if($fm == 'editar') makechecked($result['id'], $dados['tipo']); else echo 'checked'; ?> name="frmtipo" value="<?php echo $result['id']?>" onclick="javascript: form_hierarquia(this.value);" /> <?php echo $result['nome']?><br />
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
                    $query = Connection::getInstance()->query($sql);
                    $result = $query[0];
                    ?>
                    <p>
                        <strong><?php echo $result['nome'];?></strong> do sistema.
                    </p>
                <?php
                }
                ?>
                </div>
            </div>
        </div>
    </td>
</tr>
<tr>
    <td valign="top">Nome completo:</td>
    <td>
        <input class="text" type="text" name="frmnome" value="<?php ifisset($dados['nome'])?>" <?php if($fm == 'criar'){ echo 'onKeyUp="javascript: alreadyexists(this.value, \'nome\', \'Digite o nome completo do usuário que será cadastrado.\',\'#999999\',\'Verifique se este usuário já não existe, pois este nome já foi cadastrado.\',\'red\',\'adm\');"'; } ?> />
        <p class="explanation" id="exists_nome">
            Digite o nome completo do usuário que será cadastrado.
        </p>
    </td>
</tr>
<tr>
    <td valign="top">Login (nome de usuário): </td>
    <td>
        <input class="text" type="text" name="frmlogin" value="<?php ifisset($dados['login'])?>" <?php if($fm == 'criar'){ echo ' onKeyUp="javascript: alreadyexists(this.value, \'login\', \'Este nome de usuário está disponível para cadastro.\',\'green\',\'Este nome de usuário já foi cadastrado. Escolha outro.\',\'red\',\'adm\');"'; } ?> />
        <p class="explanation" id="exists_login">
            Digite um login para este usuário. Isto nada mais é que um nome de usuário
            que será usado para poder entrar no gerenciador.
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
            <input type="password" name="frmsenha" class="text" />
            <p class="explanation" >
                Senha para acesso ao gerenciador.
            </p>
        </td>
    </tr>
<?php
/*
 * Se for para editar um usuário, mostra campo de senha na forma hidden
 */
} else if($fm == 'editar'){ ?>
    <tr>
        <td valign="top">Senha de acesso: </td>
        <td>
            <input type="password" name="frmsenha" class="text" value="" />
            <p class="explanation" >
                Insira uma nova senha para alterar a atual ou
                deixe este campo em branco para não modificá-la.
            </p>
        </td>
    </tr>
<?php } ?>
    

<tr>
    <td valign="top">Twitter: </td>
    <td>
        <input class="text" type="text" name="frmtwitter" value="<?php if( !empty($dados['twitter']) ) echo $dados['twitter'] ?>" />
        <p class="explanation">
            Você tem Twitter? Ex.: 'usuário' ou '@usuário'.
        </p>
    </td>
</tr>
<tr>
    <td valign="top">Foto: </td>
    <td>
		<?php
		$imagesPath = IMAGE_VIEWER_DIR."visualiza_foto.php?table=admins_photos&fromfile=true&thumbs=yes&minxsize=20&minysize=100&myid=";
		if( !empty($dados['pid']) ){
			?>
			<img src="<?php echo $imagesPath.$dados['pid'] ?>">
			<br />
			<?php
		}
		?>
        <input class="text" type="file" name="photo" />
        <p class="explanation" id="exists_login">
            Deixe em branco para não alterar a atual.
        </p>
    </td>
</tr>
<?php
if( Config::getInstance()->getConfig('user_has_secondary_image') ){ ?>

	<tr>
	    <td valign="top">Foto secundária: </td>
	    <td>
			<?php
			if( !empty($dados['sid']) ){
				?>
				<img src="<?php echo $imagesPath.$dados['sid'] ?>">
				<br />
				<?php
			}
			?>
		
	        <input class="text" type="file" name="secondary_photo" />
	        <p class="explanation" id="exists_login">
	            Deixe em branco para não alterar a atual.
	        </p>
	    </td>
	</tr>

<?php } ?>
<?php /*
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
 *
 */
?>
<tr>
    <td colspan="2">
        <center>
            <input type="submit" value="Salvar" class="submit">
        </center>
    </td>
</tr>
</table>

</form>
<p style="margin-top: 15px;">
    <a href="adm_main.php?section=admins"><img src="<?php echo IMG_DIR?>layoutv1/voltar.gif" border="0" /></a>
</p>