<?php
	$page_title = 'Configurar o Aust';
	require 'cabecalho.inc.php';

if(isset($_POST[configurar])){
				
	foreach($_POST as $key=>$valor){
		if($key <> 'configurar'){
			$mysql = mysql_query("SELECT id FROM config WHERE propriedade='$key'");
			if(!mysql_num_rows($mysql)){
                $sql = "INSERT INTO
                            config(tipo,nome,propriedade,valor,autorid)
                        VALUE
                            (
                            '','','$key','$valor','0'
                            )";
                if(mysql_query($sql))
                {?>
                    <h1 style="color: green;">Configuração terminada com sucesso!</h1>
                    <p>Pronto, novo usuário cadastrado.</p>
                    <p><a href="<?php echo $_SERVER['PHP_SELF'];?>">Próximo passo...</a></p>
                <?php
                }
                else
                {?>
                    <h1 style="color: red;">Ops... Não foi possível cadastrar o usuário.!</h1>
                    <p>Ocorreu um erro estranho. Entre em contato com o programador responsável por isto.</p>
                    <p><a href="<?php echo $_SERVER['PHP_SELF'];?>">Voltar</a></p>
                <?php
                }

    		}
        }
    }

} else {
?>

    <h1>Configurando o Aust</h1>
    <p>Não foi encontrada configuração alguma no sistema, portanto, configure o Aust a seguir.</p>
    
    <h2>Configurações Gerais</h2>
     
    <?php
	$propriedades[method] = 'post';
	$propriedades['class'] = 'simples';
    $form = new Form($propriedades);
	$form->IniciaForm();
/*
		$campos[0][label] = 'Qual o nome do site?';
		$campos[0][name] = 'sitename';
		$campos[0][value] = 'sitename';
		$campos[1][label] = 'Oi tudo bem?';
		$campos[1][name] = 'tudobem';
		$campos[1][value] = 'tudobemm';
		$campos[1][type] = 'password';
		$form->CriaCampos($campos);
	$form->FinalizaForm();
*/
	
	$campo[] = new Campo( Array('label' => 'Qual é o nome do site?',
								'name' => 'sitename' ) );
	$campo[] = new Campo( Array('type' => 'submit',
								'class' => 'submit',
								'name' => 'configurar',
								'value' => 'Enviar!' ) );
}
require 'rodape.inc.php';

?>
