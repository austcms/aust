<?php
/**
 * Conteúdos Bootstrap
 *
 *
 * Este é o arquivo responsável por carregar os devidos formulários dos módulos.
 * Ele verifica cada ação requisitada e cada arquivo encontrado nos formulários
 * e mostra a página adequada.
 *
 * @package
 * @name Conteúdo
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.2
 * @since v0.1, 01/01/2009
 */



/*
 *  Se $_GET['action'] está setado, alguma ação foi requisitada
 */
if(!empty($_GET['action'])){

	/**
     * A seguir, o código de automação dos módulos (CRUD). São carregados os
     * formulários de cada módulo.
     *
     * $_POST|$aust_node é passado para sabermos qual estrutura deve ser
     * carregada. Aust_node é o ID da estrutura na tabela 'categorias' da
     * base de dados.
 	 */

    /**
     * AUST_NODE
     *
     * Ajusta $aust_node
     */
    if( !empty($_POST['aust_node']) ){
        $aust_node = $_POST['aust_node'];
    } else if( !empty($aust_node) ){
        $aust_node = $aust_node;
    } else if( !empty($_GET["aust_node"]) ){
        $aust_node = $_GET['aust_node'];
    }
    // @todo - Módulos devem procurar por $aust_node, não $_GET['aust_node']
    $_GET["aust_node"] = $aust_node;
    /**
     * Identifica qual é a pasta do módulo responsável por esta
     * estrutura/categoria
     */
    $modDir = $aust->LeModuloDaEstrutura($aust_node).'/';

    /**
     * Carrega arquivos principal do módulo requerido
     */
	include(MODULOS_DIR.$modDir.'index.php');

    /**
     * MVC?
     *
     * Se o módulo possui arquitetura MVC
     */
    if( !empty($modInfo['mvc']) AND $modInfo['mvc'] == true ){

        /**
         * ModController é o controller principal do módulo
         */
        include(MODULOS_DIR.$modDir.MOD_CONTROLLER);

        /*
         * JS DO MÓDULO
         *
         * Carrega Javascript de algum módulo se existir
         */
        if(!empty($aust_node)){
            //$modulo = $aust->LeModuloDaEstrutura($aust_node);
            if(is_file('modulos/'.$modDir.'js/jsloader.php')){
                $include_baseurl = WEBROOT.'modulos/'. substr($modDir, 0, strlen($modDir)-1); // necessário para o arquivo jsloader.php saber onde está fisicamente
                include_once('modulos/'.$modDir.'js/jsloader.php');
            }
        }


        /**
         * Prepara os argumentos para instanciar a classe e depois
         * chama o Controller que cuidará de toda a arquitetura MVC do módulo
         */
        
        $param = array(
            'conexao' => $conexao,
            'modulo' => $modulo,
            'administrador' => $administrador,
            'aust' => $aust,
            'action' => $_GET['action'],
            'modDir' => $modDir,
            'austNode' => $aust_node,
            'model' => $model,
        );
        $modController = new ModController($param);
    }
    /**
     * Não possui estrutura MVC
     */
    else {

        /**
         * Dependendo da ação requisitada, carrega o devido form
         */
        switch($_GET['action']){
            /**
             * NOVO conteúdo
             */
            case 'criar' :
                include( $modulos->loadInterface( $aust_node, 'new' ) );
                break;
            /**
             * EDITAR conteúdo
             */
            case 'editar' :
                /**
                 * O arquivo carregado 'inc/conteudo.inc/editar.inc.php' (arquivo do core)
                 * verfica se o módulo tem arquivo formulário específico para edição e o
                 * carrega.
                 */
                include( $modulos->loadInterface( $aust_node, 'edit' ) );
                break;
            /**
             * LISTAR conteúdo
             */
            case 'listar' :
                //include($aust->AustListar($aust_node).'/view/list_data.php'); // verifica se o módulo tem uma listagem padrão.
                include( $modulos->loadInterface( $aust_node, 'list' ) ); // verifica se o módulo tem uma listagem padrão.
                break;

            /**
             * AÇÕES ESPECIAIS
             */
            /**
             * ACTIONS
             *
             * Acima da listagem dos conteúdo, há um local com ações como excluir, por exemplo.
             * Estas são as ações especiais tratadas aqui.
             */
            case 'actions' :
                /**
                 * Inclui os actions
                 */
                include('conteudo.inc/actions.php');
                /**
                 * Após ter incluido os actions, lista o conteúdo normalmente.
                 */
                include( $modulos->loadInterface( $aust_node, 'list' ) );
                break;
            /**
             * GRAVAR
             *
             * Toma dados enviados via $_POST e carrega arquivo responsável pela gravação
             * de dados na base de dados.
             */
            case 'save' :
                /**
                 * 'gravar.php' retornou as seguintes variáveis:
                 *      - $status['classe']: contém a classe CSS referente ao resultado da operação (.sucesso|.insucesso)
                 *      - $status['mensagem']: contém a mensagem de sucesso ou erro.
                 *
                 * Ao final do código, escreve BoxMensagem
                 */
                include( $modulos->loadInterface( $aust_node, 'save' ) ); // abre o arquivo do módulo responsável pela gravação

                /**
                 * Após ter concluído a gravaçao, lista o conteúdo normalmente.
                 */
                include( $modulos->loadInterface( $aust_node, 'list' ) ); // após salvar informações, lista.
                break;
            /**
             * ELSE
             *
             * Se nenhuma ação corresponde, verifica se "$_GET['action'].'.php'"
             * existe e o carrega, senão dá erro.
             */
            default :
                $diretorio = 'modulos/'.$aust->LeModuloDaEstrutura($aust_node); // pega o endereço do diretório
                if(count(glob($diretorio.'/'.$_GET['action'].'.php')) == 1){
                    include($diretorio.'/'.$_GET['action'].'.php');
                } else {
                    echo '<h1>Ops... Erro nesta ação!</h1>';
                    echo '<p>O arquivo requisitado não existe. Entre em contato com o responsável pelo sistema.</p>';
                    echo '<p><a href="adm_main.php?section='.$_GET['section'].'"><img src="img/layoutv1/voltar.gif" border="0" /></a></p>';
                }

                break;

        }
    }
}

/**
 * Abre a página inicial da interface de conteúdos
 */
else {
?>
    <h2>Gerenciar conteúdo</h2>
	<p>
		Use esta tela para gerenciar seu conteúdo. Passe o mouse sobre o conteúdo para mais opções.
	</p>
    <div class="painel_gerenciar">
        <div class="tab">
            <div class="esquerda"></div>
            <div class="titulo_site">
                <h3><a <?php MenuSelecionado($_GET['section'], "conteudo"); ?> href="adm_main.php?section=conteudo">Ecobiologia</a></h3>
            </div>
            <div class="direita"></div>
        </div>
        
        <?php
        /*
         * EXEMPLO DE COMO SERÁ SE TIVER MAIS DE UMA TAB
         */
        ?>
        <div class="tab">
            <div class="esquerda"></div>
            <div class="titulo_site">
                <h3><a <?php MenuSelecionado($_GET['section'], ""); ?> href="adm_main.php?section=">Área Restrita</a></h3>
            </div>
            <div class="direita"></div>
        </div>
        
        
        <div class="titulo_gerenc">
            <ul>
                <li class="secoes">Seções</li>
                <li class="acoes">Ações</li>
                <li class="tipo">Tipo</li>
                <li class="acesso">Último acesso por</li>
            </ul>
        </div>
        <div class="corpo_gerenc">
            <div class="listagem">

                <div class="secoes">
                    <div class="nome_secoes">
                        <a href="">ARTIGOS</a>
                    </div>
                    <div class="interrogacao">
                        <a href="#"> <img src="core/user_interface/img/interrogacao_cinza.png" /></a>
                    </div>
                </div>

                <div class="secoes">
                    <div class="nome_secoes">
                        <a href="">ENQUETE</a>
                    </div>
                    <div class="interrogacao">
                        <a href="#"> <img src="core/user_interface/img/interrogacao_cinza.png" /></a>
                    </div>
                </div>

                <div class="secoes">
                    <div class="nome_secoes">
                        <a href="">GALERIA DE FOTOS</a>
                    </div>
                    <div class="interrogacao">
                        <a href="#"> <img src="core/user_interface/img/interrogacao_cinza.png" /></a>
                    </div>
                </div>

                <div class="secoes">
                    <div class="nome_secoes">
                        <a href="">NOTÍCIAS</a>
                    </div>
                    <div class="interrogacao">
                        <a href="#"> <img src="core/user_interface/img/interrogacao_cinza.png" /></a>
                    </div>
                </div>
                
                <div class="secoes">
                    <div class="nome_secoes">
                        <a href="">PESQUISA PARA PAIS E ALUNOS</a>
                    </div>
                    <div class="interrogacao">
                        <a href="#"> <img src="core/user_interface/img/interrogacao_cinza.png" /></a>
                    </div>
                </div>
                
            </div>
            
            <ul class="listagem editar">
                <li><a href="">Nova</a>&nbsp;&nbsp;&nbsp;<a href="">Listar</a></li>
                <li><a href="">Nova</a>&nbsp;&nbsp;&nbsp;<a href="">Listar</a></li>
                <li><a href="">Nova</a>&nbsp;&nbsp;&nbsp;<a href="">Listar</a></li>
                <li><a href="">Nova</a>&nbsp;&nbsp;&nbsp;<a href="">Listar</a></li>
                <li><a href="">Nova</a>&nbsp;&nbsp;&nbsp;<a href="">Listar</a></li>
            </ul>
            <ul class="listagem tipo">
                <li><a href="">Conteúdo</a></li>
                <li><a href="">Pesquisa</a></li>
                <li><a href="">Galeria de Fotos</a></li>
                <li><a href="">Conteúdos</a></li>
                <li><a href="">Pesquisa</a></li>
            </ul>
            <ul class="listagem pessoa">
                <li><a href="">Acácio Neimar de Oliveira</a></li>
                <li><a href="">Andréia de Oliveira</a></li>
                <li><a href="">Alexandre de Oliveira</a></li>
                <li><a href="">Andréia de Oliveira</a></li>
                <li><a href="">Alexandre de Oliveira</a></li>
            </ul>
        </div>
        <div class="rodape_gerenc">
        </div>
    </div>
    <br clear="all" />
	<div class="action_options">
        <?php
        include(INC_DIR.'conteudo.inc/user_menu.php');
        ?>

	</div>
<?php } ?>