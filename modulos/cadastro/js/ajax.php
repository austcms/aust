<?php
/**
 * Ajax do Módulo
 *
 * @package ModCadastro
 * @name adm_main.php
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.6 25/07/2009
 */
/**
 * Cria SESSION
 */
session_name("aust");
session_start();

/**
 * Se não está definido o endereço deste arquivo até o root
 */
if(!defined('THIS_TO_BASEURL')){
    define('THIS_TO_BASEURL', '../../../');
}

/**
 * Variáveis constantes contendo comportamentos e Paths
 */
include_once(THIS_TO_BASEURL."core/config/variables.php");


/**
 * Classes do sistema
 */
include(THIS_TO_BASEURL.CLASS_DIR."_carrega_classes.inc.php");
/**
 * Propriedades editáveis do sistema. Carrega todas as configurações da aplicação
 */
/**
 * Configurações de conexão do banco de dados
 */
include(THIS_TO_BASEURL.CONFIG_DIR."database.php");

include(THIS_TO_BASEURL.LIB_DIR."aust/aust_func.php");
/**
 * Conexão principal
 */
$conexao = Connection::getInstance();

/**
 * Configurações do core do sistema
 */
    include(THIS_TO_BASEURL.CONFIG_DIR."core.php");
/**
 * Permissões de tipos de usuários relacionados à navegação
 */
    include(THIS_TO_BASEURL.CONFIG_DIR."nav_permissions.php");
/**
 * Carrega o CORE
 */
    include(THIS_TO_BASEURL.CORE_DIR.'load_core.php');

    
include('../index.php');

$modulo = new Cadastro;
$permissoes = StructurePermissions::getInstance();


header("Content-Type: text/html; charset=".$aust_charset['view'],true);

/**
 * Função para retornar os cadastros do sistema no formato para <select>
 */
/**
 * LER TABELAS
 */
if($_POST['action'] == 'LeCadastros'){
    $sql = "SELECT
                *
            FROM
                categorias
            WHERE
                tipo='cadastro'";
    //echo $sql;
    $arraytmp = $conexao->query('SHOW TABLES');
    //$arraytmp = $conexao->listaTabelasDoDBParaArray();
    
    foreach($arraytmp AS $valor){
        $valor = reset($valor);
        echo '<option value="'.$valor.'">'.$valor.'</option>';
    }
    
}
/**
 * Ler campos
 */
elseif($_POST['action'] == 'LeCampos'){

    /**
     * Lê os campos da tabela e depois mostra um html <select> para o usuário
     * escolher o relacionamento de tabelas
     */
    $query = $conexao->query('DESCRIBE '.$_POST['tabela']);
    foreach ( $query as $chave=>$valor ){
        echo '<option value="'.$valor['Field'].'">'.$valor['Field'].'</option>';
    }

}

/*
 * LISTING SEARCH
 */

elseif($_POST['action'] == 'search'){

    /**
     *
     */
    //print_r($_POST);
    //exit();
    $austNode = $_POST['austNode'];
    $aust = Aust::getInstance();

    $resultado = array();
    $categorias = $aust->LeCategoriasFilhas('',$_GET['aust_node']);
    $categorias[$austNode] = 'Estrutura';

    $searchField = '';
    if( !empty($_POST["field"]) AND
        $_POST["field"] != "&all&" )
    {
        $searchField = $_POST["field"];
    }

//	$_POST['query'] = str_replace(" ", "%", $_POST['query']);
    $param = array(
        'categorias' => $categorias,
        'metodo' => 'listing',
        'search' => $_POST['query'],
        'search_field' => $searchField
    );

    $sql = $modulo->loadSql($param);
//    echo '<br><br>'.$sql .'<br>';

    $resultado = $modulo->connection->query($sql, "ASSOC");

    $fields = count($resultado);

    include($modulo->getIncludeFolder().'/view/mod/listing_table.php');

    //$query = $conexao->query('DESCRIBE '.$_POST['tabela']);
    //foreach ( $query as $chave=>$valor ){
        //echo '<option value="'.$valor['Field'].'">'.$valor['Field'].'</option>';
    //}
}
/*
 * PESQUISA: relational one-to-many
 */
elseif($_POST['action'] == 'search1n'){

    /**
     *
     */
    $austNode = $_POST['austNode'];

	// checked_boxes
	$get = $_GET;
	$ids = array();
	$queryCheckedBoxes = '';
	if( !empty($get['data']) ){
		$get = reset($_GET['data'] );
		if( !empty($get) ){
			$get = reset($get);
			$ids = $get;
			$queryCheckedBoxes = " AND r.id NOT IN ('".implode("','", $ids)."')";
			
		}
	}

    $sql = "SELECT
                r.id AS ref_id,
				r.".$ref_field." as ref_value
            FROM
                ".$relational_table." AS t
			RIGHT JOIN
				".$ref_table." AS r
			ON
				r.id=t.".$childField."
			WHERE
				r.".$ref_field." LIKE '%".$query."%' AND
				(
					t.".$childField." NOT IN
					(
						SELECT s.".$childField."
						FROM ".$relational_table." AS s
						WHERE s.".$parentField."='$w'
					)
					OR
					t.".$childField." IS NULL
				)
				AND
				r.id!='".$w."'
				$queryCheckedBoxes
			GROUP BY
				r.id
            ORDER BY
                t.order_nr ASC, t.id ASC
			LIMIT 10
            ";
	$results = $modulo->connection->query($sql);
	
	foreach( $results as $result ){
		?>
		<div>
		<div class="input_checkbox_each">
			<input type="checkbox" value="<?php echo $result['ref_id'] ?>" name="<?php echo $_POST['inputName']?>">
			<?php echo $result['ref_value'] ?>
		</div>
		</div>
		<?php
	}

}

?>