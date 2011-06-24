<?php
/*
 * Somente webmasters tem acesso a esta página
 */
if(User::getInstance()->LeRegistro('tipo') == 'Webmaster'):

/*
 * MIGRATIONS
 *
 * Verificações de Migrations de módulos
 */
    $migrationsMods = new MigrationsMods( Connection::getInstance() );

    /*
     * INSTALA MÓDULO
     */
    if( !empty($_GET['instalar_modulo'])
        AND is_dir(MODULES_DIR.$_GET['instalar_modulo']) )
    {

        $path = $_GET['instalar_modulo'];
        /**
         * Carrega arquivos dos módulos
         */
     	include_once(MODULES_DIR.$path.'/'.MOD_CONFIG);

        $modName = MigrationsMods::getInstance()->getModNameFromPath(MODULES_DIR.$path);
        /**
         * Ajusta variáveis para gravação
         */
            /**
             * [embedownform] indica se este módulo possui habilidade
             * para acoplar-se em formulários de outros módulos
             * com seu próprio <form></form>
             */
            $modInfo['embedownform'] = (empty($modInfo['embedownform'])) ? false : $modInfo['embedownform'];
            /**
             * [embed] indica se este módulo possui
             * habilidade para acoplar-se em formulários de outros módulos
             */
            $modInfo['embed'] = (empty($modInfo['embed'])) ? false : $modInfo['embed'];
            /**
             * [somenteestrutura] indica se a estrutura conterá categorias ou não.
             */
            $modInfo['somenteestrutura'] = (empty($modInfo['somenteestrutura'])) ? false : $modInfo['somenteestrutura'];

        /*
         * Caso o módulo não tenha migrations, faz a verificação normal das tabelas
         * a partir de schemas, o que não é recomendado.
         */
        if( MigrationsMods::getInstance()->hasMigration($path) ){
            $installStatus = MigrationsMods::getInstance()->updateMigration($path);
            $isInstalled = MigrationsMods::getInstance()->isActualVersion($path);

            $param = array(
                'tipo' => 'módulo',
                'chave' => 'dir',
                'valor' => $modName,
                'pasta' => $path,
                'modInfo' => $modInfo,
                'autor' => User::getInstance()->LeRegistro('id'),
            );
            ModulesManager::getInstance()->configuraModulo($param);

            $status['classe'] = 'sucesso';
            $status['mensagem'] = '<strong>Sucesso: </strong> Migration executado com sucesso!';
        } else {
            $status['classe'] = 'insucesso';
            $status['mensagem'] = 'Este módulo não possui Migrations.';
        }
    }
    /*
     * Migration Status
     */
    $migrationsStatus = MigrationsMods::getInstance()->status();

    $modulesStatus = ModulesManager::getInstance()->getModuleInformation( array_keys($migrationsStatus) );

    /*
     * Module's JS
     */
    if(!empty($_GET['aust_node'])){
        $modulo = Aust::getInstance()->LeModuloDaEstrutura($_GET['aust_node']);
        if(is_file(MODULES_DIR.$modulo.'/js/jsloader.php')){
            $include_baseurl = MODULES_DIR.$modulo;
            include_once(MODULES_DIR.$modulo.'/js/jsloader.php');
        }
    } elseif($_GET['action'] == 'configurar_modulo' AND !empty($_GET['modulo'])){
        if(is_file($_GET['modulo'].'/js/jsloader.php')){
            $include_baseurl = $_GET['modulo'];
            include_once($_GET['modulo'].'/js/jsloader.php');
        }
    }

    /*
     * Configure a structure
     */
    if($_GET['action'] == 'configurar'){
        $diretorio = MODULES_DIR.Aust::getInstance()->LeModuloDaEstrutura($_GET['aust_node']); // pega o endereço do diretório
        foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
            if(is_file($pastas.'/configurar_estrutura.php')){
				$module = ModulesManager::getInstance()->modelInstance($_GET["aust_node"]);
				include($pastas.'/configurar_estrutura.php');
            }
        }
    }
    /*
     * Configure a module
     */
    else if($_GET['action'] == 'configurar_modulo'){
        $pastas = $_GET['modulo'];
        if(is_file($pastas.'/configurar_modulo.php')){
            include($pastas.'/configurar_modulo.php');
        }
    }

    /*
     * INSTALAR/CRIAR ESTRUTURA COM SETUP PRÓPRIO
     *
     * Se instalar uma estrutura a partir de um módulo com setup próprio, faz
     * includes neste arquivo para configuração.
     */
    elseif( !empty($_POST['inserirestrutura'])
            AND (
                ( is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER ) )
                OR ( is_file( MODULES_DIR.$_POST['modulo'].'/setup.php' ) )
            )
    ){

        /**
         * MVC ON
         *
         * Se o padrão MVC está ativado carrega SetupController
         */
        if( is_file( MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER ) ){

            /*
             * Instancia Módulo para começar o Setup
             */
                $modDir = $_POST['modulo'].'/';
                include(MODULES_DIR.$modDir.MOD_CONFIG);
                /**
                 * Carrega classe do módulo e cria objeto
                 */
                $moduloNome = (empty($modInfo['className'])) ? 'Classe' : $modInfo['className'];
                include_once(MODULES_DIR.$modDir.$moduloNome.'.php');

                $param = array(
                    'config' => $modInfo,
                    'user' => $administrador,
                    //'modDbSchema' => $modDbSchema,
                );
                $modulo = new $moduloNome($param);
                //unset( $modDbSchema );

            /**
             * JAVASCRIPT
             *
             * Carrega scripts javascript
             */
            if(is_file(MODULES_DIR.$_POST['modulo'].'/js/jsloader.php')){
                $include_baseurl = MODULES_DIR.$_POST['modulo']; // necessário para o arquivo jsloader.php saber onde está fisicamente
                include_once(MODULES_DIR.$_POST['modulo'].'/js/jsloader.php');
            }
            /**
             * Carrega o SetupController
             */
            include(MODULES_DIR.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER);

            /**
             * SetupController
             *
             * Chama o controller que vai carregar o setup
             */
            $setupAction = ( empty( $_POST['setupAction'] ) ) ? '' : $_POST['setupAction'];
            /**
             * 'action': $setupAction contém informações de $_POST['setupAction']
             * 'exPOST': possui $_POST enviados anteriormente.
             */
            $params = array(
                'conexao' => $conexao,
                'aust' => $aust,
                'modulo' => $modulo,
                'administrador' => $administrador,
                'modDir' => $_POST['modulo'],
                'action' => $setupAction,
                'exPOST' => $_POST,
            );
            $setup = new SetupController( $params );
            unset($modulo);
        }
    }

    /*
     * NENHUMA DAS OPÇÕES ACIMA, CARREGAR A PÁGINA NORMALMENTE
     *
     * Carrega toda a interface normalmente
     */
    else {

        /*
         * INSTALAR ESTRUTURA SEM SETUP.PHP PRÓPRIO VIA CORE DO AUST
         *
         * Se instalar uma estrutura a partir de um módulo com setup.php próprio, faz include neste arquivo para configuração
         */
        if(!empty($_POST['inserirestrutura'])  AND !is_file(MODULES_DIR.$_POST['modulo'].'/setup.php')) {
            $result = Aust::getInstance()->gravaEstrutura(
                            array(
                                'nome'              => $_POST['nome'],
                                'categoriaChefe'    => $_POST['categoria_chefe'],
                                'estrutura'         => 'estrutura',
                                'publico'           => $_POST['publico'],
                                'moduloPasta'       => $_POST['modulo'],
                                'autor'             => User::getInstance()->LeRegistro('id')
                            )
                        );
            if($result){
                $status['classe'] = 'sucesso';
                $status['mensagem'] = '<strong>Sucesso: </strong> Item inserido com sucesso!';
            } else {
                $status['classe'] = 'insucesso';
                $status['mensagem'] = '<strong>Erro: </strong> Ocorreu um erro desconhecido, tente novamente.';
            }
        }

        //
        ?>
        <h2>Configuração: Módulos e Estruturas</h2>
        <?php
        if( !empty($status) AND is_array($status)){
            EscreveBoxMensagem($status);
        }
        ?>

        <div class="widget_group">
            <?php
            /*
             * LISTAGEM DAS ESTRUTURAS CRIADAS
             */
            ?>
            <div class="widget">
                <div class="titulo">
                    <h3>Estruturas instaladas</h3>
                </div>
                <div class="content">
                    <p>Abaixo, as estruturas instaladas.</p>
                    <ul>
                    <?php
                    /**
                     * @todo - refatorar LeEstruturas()
                     */
                    Aust::getInstance()->LeEstruturas(Array('id', 'nome', 'tipo'), '<li><strong>&%nome</strong> (módulo &%tipo) &%options</li>', '', '', 'ORDER BY tipo DESC', 'options');
                    ?>
                    </ul>
                </div>
                <div class="footer"></div>
            </div>


            <?php
            /*
             * FORM INSTALAR NOVAS ESTRUTURAS
             */
            ?>
            <div class="widget">
                <div class="titulo">
                    <h3>Instalar Estrutura</h3>
                </div>
                <div class="content">
                    <p>
                        Selecione abaixo a categoria-chefe, o nome da estrutura (ex.: Notícias, Artigos, Arquivos) e o módulo adequado.
                    </p>
                    <form action="adm_main.php?section=<?php echo $_GET['section'];?>" method="post" class="simples pequeno">
                        <input type="hidden" value="1" name="publico" />
                        <div class="campo">
                            <label>Categoria-chefe: </label>
                                <select name="categoria_chefe">
                                    <?php
                                    Aust::getInstance()->getSite(Array('id', 'nome'), '<option value="&%id">&%nome</option>', '', '');
                                    ?>
                                </select>
                        </div>
                        <br />
                        <div class="campo">
                            <label>Módulo: </label>
                                <?php
                                $modulosList = ModulesManager::getInstance()->LeModulos();
                                //pr($modulosList);
                                ?>
                                <select name="modulo">
                                    <?php
                                    foreach( $modulosList  as $moduloDB ){

                                        ?>
                                        <option value="<?php echo $moduloDB["valor"] ?>">
                                            <?php echo $moduloDB["nome"] ?>
                                        </option>
                                        <?
                                    }

                                    unset($moduloDB);
                                    ?>
                                </select>
                        </div>
                        <br />
                        <div class="campo">
                            <label>Nome da estrutura:</label>
                            <div class="input">
                                <input type="text" name="nome" class="input" />
                                <p class="explanation">Ex.: Notícias, Artigos</p>
                            </div>
                        </div>
                        <div class="campo">
                            <input type="submit" name="inserirestrutura" value="Enviar!" class="submit" />
                        </div>

                    </form>
                </div>
                <div class="footer"></div>
            </div>
        </div>



        <div class="widget_group">

            <?php
            /*
             * INSTALAÇÃO DE MÓDULOS
             */
            ?>
            <div class="widget">
                <div class="titulo">
                    <h3>Módulos disponíveis</h3>
                </div>
                <div class="content">

                    <div style="margin-bottom: 10px;">
                    <?php
                    foreach( $modulesStatus as $modulo) {
                        $path = $modulo['path'];
                        ?>
                        <div>

                        <strong>
                        <?php
                        /*
                         * Tem configurador?
                         */
                        if(is_file(MODULES_DIR.$modulo['path'].'/configurar_modulo.php')){
                            echo '<a href="adm_main.php?section=conf_modulos&action=configurar_modulo&modulo='.$modulo['path'].'" style="text-decoration: none;">'.$modulo['config']['nome'].'</a>';
                        } else {
                            echo $modulo['config']['nome'];
                        }
                        ?>
                        </strong>
                        <br />
                        <?php echo $modulo['config']['descricao'];
                        /*
                         * Totalmente Atualizado.
                         */
                        if( MigrationsMods::getInstance()->isActualVersion($path)
                            AND ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                        {
                            echo '<br /><span class="green">Instalado</span><br />';
                        } elseif( MigrationsMods::getInstance()->isActualVersion($path)
                            AND !ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                        {
                            echo '<div style="color: orange;">Migration atualizado, mas não há registro do módulo no DB.<br />';
                            echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Tentar registrar agora</a></div>';
                        }
                        /*
                         * Não atualizado,
                         * contém alguma versão no DB.
                         */
                        elseif( MigrationsMods::getInstance()->hasSomeVersion($path)
                                AND ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                        {
                            echo '<div style="color: orange;">Tabela instalada, mas requer atualização.<br />';
                            echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
                        } elseif( MigrationsMods::getInstance()->hasSomeVersion($path)
                                AND !ModulesManager::getInstance()->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                        {
                            echo '<div style="color: orange;">Tabela instalada, mas requer atualização e registro do módulo no DB.<br />';
                            echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
                        } else {
                            echo '<br /><span class="red">Não Instalado,</span> ';
                            echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">instalar agora</a><br />';
                        }
                        /*
                        if( $module->verificaInstalacaoTabelas()
                            AND $module->verificaInstalacaoRegistro(array("pasta"=>$pastas)) )
                        {
                            $conteudo.= '<div style="color: green;">Instalado</div>';

                        } else if( $module->verificaInstalacaoTabelas() ){
                            $conteudo.= '<div style="color: orange;">Tabela instalada, registro no DB não.<br />';
                            $conteudo.= '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$pastas.'">Tentar instalar</a></div>';
                        } else if( $module->verificaInstalacaoRegistro(array("pasta"=>$pastas)) ){
                            $conteudo.= '<div style="color: orange;">Tabela não instalada, registro no DB sim.</div>';
                        } else {
                            $conteudo.= '<div style="color: red;">Não Instalado, ';
                            $conteudo.= '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$pastas.'">clique para instalar</a></div>';
                        }
                         *
                         */
                        ?>

                        <br />
                        </div>
                        <?php
                    }
                    ?>
                    </div>

                </div>
                <div class="footer"></div>
            </div>

            <div class="widget">
                <div class="titulo">
                    <h3>Versões dos Módulos</h3>
                </div>
                <div class="content">

                    <div style="margin-bottom: 10px;">
                        <ul>
                        <?php
                        foreach( $migrationsStatus as $modName=>$status ){
                            ?>
                            <li>
                            <?php
                            if( $status ){
                                echo $modName.': Ok.';
                            } else {
                                echo $modName.': Requer atualização.';
                            }
                            ?>
                            </li>
                            <?php
                        }
                        ?>
                        </ul>
                    </div>


                </div>
                <div class="footer"></div>
            </div>

        </div>


        <?php

    }

endif;

?>