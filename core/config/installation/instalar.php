<?php
session_name("aust");
session_start();

/*
 * SETUP
 *
 * Devem ser carregadas configurações de banco de dados, além de classes e o schema das tabelas
 * Se os arquivos a seguir estão comentados, é porque já está sendo carregado a partir de outro script que
 * está carregando este.
 *
 */

/*
 * Configuraçãoes
 *      include("../config/database.php");
 */

/*
 * Carrega todas as classes
 *      include("../class/Conexao.class.php");
 *      include("../class/Config.class.php");
 *      include("../class/Administrador.class.php");
 */
    
/*
 * Schema das tabelas
 *      require("config/installation/sql_para_construir_db.php");
 */
    

$conexao = new Conexao($dbConn);

include(THIS_TO_BASEURL.CORE_DIR."load_core.php");
/**
 * Verifica se banco de dados existe
 */
if($conexao->DBExiste){

    /**
     * Faz verificação do Schema
     *
     * O resultado é guardado em dbSchema::schemaStatus
     */
    $dbSchema->verificaSchema();
    //echo $dbSchema->schemaStatus;

    /**
     * Todas as tabelas instaladas
     */
    if($dbSchema->schemaStatus == 1){

        if(!empty($_POST['configurar']) AND $_POST['configurar'] == 'criar_admin'){
            require('criar_admin.inc.php');

        /**
         * Verifica se há um usuário Admin cadastrado
         */
        } else if(!$conexao->VerificaAdmin()){
            require('criar_admin.inc.php');

        } else if(isset($_GET["configurar"])) {
            require('configurar.inc.php');
        }
        /**
         * Está tudo ok, volta para a tela de login no root index.php
         */
        else {
            header('Location: '.THIS_TO_BASEURL.'index.php');
        }
    }
    /**
     * Defeitos encontrados:
     *      0: Nenhuma tabela existe, é necessário instalação completa
     *     -1: Todas as tabelas existém, mas alguns campos não
     *     -2: Algumas tabelas não existem
     */
    else if(
        $dbSchema->schemaStatus == 0 OR
        $dbSchema->schemaStatus == -1 OR
        $dbSchema->schemaStatus == -2
    ) {

        
        $page_title = 'Instalação do Aust';
        require 'cabecalho.inc.php';
        // se a variável $_GET[instalar] é vazia, ou seja, não há ordem de instalação

        /**
         * Primeiro acesso ao instalador
         *
         * Mostra uma tela com algum texto introdutório, então opções de acordo com
         * a situação encontrada.
         */
        if(empty($_GET['instalar'])){
            ?>
            <h1>Instalação do Aust</h1>
            <p>Está é a instalação fácil do Aust. Se você está vendo isto, é porque o sistema não está instalado ou encontrou defeitos.</p>
            <?php
            /**
             * Algumas tabelas foram encontradas e outras não. Provavelmente o sistema já foi instalado anteriormente.
             */
            if($dbSchema->schemaStatus == -2){
                ?>
                <h2>Instalação defeituosa...</h2>
                <p>Algumas <strong>tabelas</strong> do banco de dados necessárias ao funcionamento do sistema não foram encontradas.</p>
                <p>O que você deseja que o sistema faça?</p>
                <ul>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'];?>?instalar=tabelasinexistentes">Crie somente as tabelas que não foram encontradas</a></li>
                </ul>
                <?php
            
            }
            /**
             * Tabelas ok, mas alguns campos não encontrados
             */
            elseif($dbSchema->schemaStatus == -1){
                ?>
                <h2>Tabelas corrompidas</h2>
                <p>O seguinte erro foi encontrado: <strong style="color:red">campos necessários não encontrados.</strong></p>
                <p>
                    O seguintes campos não foram encontrados:
                </p>

                    <?php
                    //pr($dbSchema->camposInexistentes);
                    foreach($dbSchema->camposInexistentes as $tabela=>$campo){
                        echo '<strong style="margin: 0; color: green">'.$tabela.'</strong>';
                        echo '<ul>';
                        foreach($campo as $nome=>$propriedade){
                            echo '<li>';
                            echo '<strong>'.$nome.':</strong> '.$propriedade;
                            echo '</li>';
                        }
                        echo '</ul>';

                        //echo '</p>';
                    }
                    ?>
                <p>Use as informações acima para criar manualmente os campos.</p>

                <?php
            }


            /**
             * Nenhuma tabela foi encontrada
             */
            elseif($dbSchema->schemaStatus == 0){
                ?>
                <h2>Instalação Inicial</h2>
                <p>Esta parece ser sua primeira instalação do Aust.
                <strong>Nenhuma tabela</strong> necessária ao funcionamento do sistema foi encontrada na base de dados.</p>
                <p>O que você deseja que o sistema faça?</p>
                <ul>
                    <li><a href="<?php echo $_SERVER['PHP_SELF'];?>?instalar=todasastabelas">Esta é minha primeira instalação, crie todas as tabelas para mim</a></li>
                </ul>

                <?php
            }
        }
        /**
         * Executar instalação
         */
        /**
         * Faz a instalação inicial, primeira do sistema, instala todas as tabelas.
         */
        elseif($_GET['instalar'] == 'todasastabelas'){
            $dbSchema->instalarSchema();
            echo '<h1>Instalação do Aust</h1>';
            echo '<p>Está é a instalação fácil do Aust. Se você está vendo isto, é porque o sistema não está instalado ou encontrou defeitos.</p>';
            echo '<h2>Instalando Tabelas</h2>';
            echo '<p>A seguir, as tabelas que foram instaladas.</p>';
            echo '<ul>';

            foreach($dbSchema->tabelasInstaladas as $chave=>$valor){
                if($dbSchema->tabelasInstaladas[$chave]){
                    $status = '<span style="color: green">Instalado</span>';
                } else {
                    $status = '<span style="color: red">Não instalado</span>';
                }

                echo '<li>Tabela \''.$chave.'\': '.$status.'</li>';
            }
            echo '</ul>';
            echo '<p>"E agora", você pergunta. Agora você vai configurar o sistema. Falta pouco para terminar a instalação.</p>';
            echo '<p><a href="'.$_SERVER['PHP_SELF'].'">Clique aqui para prosseguir</a></p>';
        }
        /**
         * Instalar somente tabelas inexistentes
         */
        else if($_GET['instalar'] == 'tabelasinexistentes'){
            $dbSchema->instalarSchema();
            echo '<h1>Instalação tabelas</h1>';
            echo '<p>A seguir, as tabelas necessárias que foram instaladas.</p>';
            echo '<ul>';
            
            foreach($dbSchema->tabelasInstaladas as $chave=>$valor){
                if($dbSchema->tabelasInstaladas[$chave]){
                    $status = '<span style="color: green">Instalado</span>';
                } else {
                    $status = '<span style="color: red">Não instalado</span>';
                }

                echo '<li>Tabela \''.$chave.'\': '.$status.'</li>';
            }
            echo '</ul>';
            echo '<p>Pronto! Algumas tabelas precisam estar no sistema, mas não estavam. Instalamos elas e estamos prontos para seguir em frente.</p>';
            echo '<p><a href="'.$_SERVER['PHP_SELF'].'">Clique aqui para prosseguir</a></p>';
        }
        require 'rodape.inc.php';
    }
}
/**
 * Banco de dados inexistente
 */
else {
    echo 'Não existe DB. Por favor entre contato com o administrador ou disponibilize acesso a um DB.';
}
?>