<?php
/*
 * Somente webmasters tem acesso a esta página
 */
if($administrador->LeRegistro('tipo') == 'Webmaster'){

/*
 * MIGRATIONS
 *
 * Verificações de Migrations de módulos
 */
    $migrationsMods = new MigrationsMods( $conexao );
    /*
     * INSTALA MÓDULO
     */
    if( !empty($_GET['instalar_modulo'])
        AND is_dir($_GET['instalar_modulo']) )
    {
        $path = $_GET['instalar_modulo'];

        /**
         * Carrega arquivos dos módulos
         */
        include_once($path.'/index.php');
        include_once($path.'/'.MOD_CONFIG);

        $modName = $migrationsMods->getModNameFromPath($path);

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
        if( $migrationsMods->hasMigration($path) ){
            $installStatus = $migrationsMods->updateMigration($path);
            $isInstalled = $migrationsMods->isActualVersion($path);

            $param = array(
                'tipo' => 'módulo',
                'chave' => 'dir',
                'valor' => $modName,
                'pasta' => $path,
                'modInfo' => $modInfo,
                'autor' => $administrador->LeRegistro('id'),
            );
            $modulo->configuraModulo($param);

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
    $migrationsStatus = $migrationsMods->status();

    //pr($migrationsStatus);
    $modulesStatus = $modulos->getModuleInformation( array_keys($migrationsStatus) );

    //pr($modulesStatus);


/*
 * JS DO MÓDULO
 * 
 * Carrega Javascript de algum módulo se existir
 */
if(!empty($_GET['aust_node'])){
    $modulo = $aust->LeModuloDaEstrutura($_GET['aust_node']);
    if(is_file('modulos/'.$modulo.'/js/jsloader.php')){
        $include_baseurl = 'modulos/'.$modulo; // necessário para o arquivo jsloader.php saber onde está fisicamente
        include_once('modulos/'.$modulo.'/js/jsloader.php');
    }
} elseif($_GET['action'] == 'configurar_modulo' AND !empty($_GET['modulo'])){
    if(is_file($_GET['modulo'].'/js/jsloader.php')){
        $include_baseurl = $_GET['modulo']; // necessário para o arquivo jsloader.php saber onde está fisicamente
        include_once($_GET['modulo'].'/js/jsloader.php');
    }
}

/**
 * CONFIGURAR ESTRUTURA
 *
 * Se o usuário desejar configurar a estrutura. Carrega configurar_estrutura.php
 */
if($_GET['action'] == 'configurar'){
    $diretorio = 'modulos/'.$aust->LeModuloDaEstrutura($_GET['aust_node']); // pega o endereço do diretório
    foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
        if(is_file($pastas.'/configurar_estrutura.php')){
            //include($pastas.'/modulo.class.php');
            include($pastas.'/index.php');
            include($pastas.'/configurar_estrutura.php');
        }
    }
}
/**
 * CONFIGURAR MÓDULO
 *
 * Configuração do módulo e não estrutura. Carrega configurar_modulo.php
 */
    // @todo
else if($_GET['action'] == 'configurar_modulo'){
    //$diretorio = 'modulos/'.$aust->LeModuloDaEstrutura($_GET['aust_node']); // pega o endereço do diretório
    $pastas = $_GET['modulo'];
    //foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
        if(is_file($pastas.'/configurar_modulo.php')){
            include($pastas.'/index.php');
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
            ( is_file( 'modulos/'.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER ) )
            OR ( is_file( 'modulos/'.$_POST['modulo'].'/setup.php' ) )
        )
){ 

    /**
     * MVC ON
     *
     * Se o padrão MVC está ativado carrega SetupController
     */
    if( is_file( 'modulos/'.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER ) ){
        /**
         * JAVASCRIPT
         * 
         * Carrega scripts javascript
         */
        if(is_file('modulos/'.$_POST['modulo'].'/js/jsloader.php')){
            $include_baseurl = 'modulos/'.$_POST['modulo']; // necessário para o arquivo jsloader.php saber onde está fisicamente
            include_once('modulos/'.$_POST['modulo'].'/js/jsloader.php');
        }
        /**
         * Carrega o SetupController
         */
        include('modulos/'.$_POST['modulo'].'/'.MOD_SETUP_CONTROLLER);

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
            'administrador' => $administrador,
            'modDir' => $_POST['modulo'],
            'action' => $setupAction,
            'exPOST' => $_POST,
        );
        $setup = new SetupController( $params );
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
    if(!empty($_POST['inserirestrutura'])  AND !is_file('modulos/'.$_POST['modulo'].'/setup.php')) {
        $result = $aust->gravaEstrutura(
                        array(
                            'nome'              => $_POST['nome'],
                            'categoriaChefe'    => $_POST['categoria_chefe'],
                            'estrutura'         => 'estrutura',
                            'publico'           => $_POST['publico'],
                            'moduloPasta'       => $_POST['modulo'],
                            'autor'             => $administrador->LeRegistro('id')
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
    <h1>Configuração: Módulos e Estruturas</h1>
    <?php
    if( !empty($status) AND is_array($status)){
        EscreveBoxMensagem($status);
    }
    ?>

    <div class="painel-metade">
        <?php
        /*
         * LISTAGEM DAS ESTRUTURAS CRIADAS
         */
        ?>
        <div class="painel">
            <div class="titulo">
                <h2>Estruturas instaladas</h2>
            </div>
            <div class="corpo">
                <p>Abaixo, as estruturas instaladas.</p>
                <ul>
                <?php
                $aust->LeEstruturas(Array('id', 'nome', 'tipo'), '<li><strong>&%nome</strong> (módulo &%tipo) &%options</li>', '', '', 'ORDER BY tipo DESC', 'options');
                ?>
                </ul>
            </div>
            <div class="rodape"></div>
        </div>


        <?php
        /*
         * FORM INSTALAR NOVAS ESTRUTURAS
         */
        ?>
        <div class="painel">
            <div class="titulo">
                <h2>Instalar Estrutura</h2>
            </div>
            <div class="corpo">
                <p>
                    Selecione abaixo a categoria-chefe, o nome da estrutura (ex.: Notícias, Artigos, Arquivos) e o módulo adequado.
                </p>
                <form action="adm_main.php?section=<?php echo $_GET['section'];?>" method="post" class="simples pequeno">
                    <input type="hidden" value="1" name="publico" />
                    <div class="campo">
                        <label>Categoria-chefe: </label>
                            <select name="categoria_chefe">
                                <?php
                                $aust->LeCategoriaChefe(Array('id', 'nome'), '<option value="&%id">&%nome</option>', '', '');
                                ?>
                            </select>
                    </div>
                    <br />
                    <div class="campo">
                        <label>Módulo: </label>
                            <?php
                            $modulosList = $modulos->LeModulos();
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
            <div class="rodape"></div>
        </div>
    </div>


    <?php

    /**
     * Loop por cada diretório de módulos
     */
    foreach (glob($diretorio."*", GLOB_ONLYDIR) as $pastas) {
        break;
        if(is_dir($pastas) AND is_file($pastas.'/index.php')) {

            /**
             * Carrega arquivos dos módulos
             */
            include_once($pastas.'/index.php');
            include_once($pastas.'/'.MOD_CONFIG);

            /**
             * Se o módulo possui uma classe própria com métodos próprios,
             * podemos continuar
             */
            if(!empty($modulo)){

                $conteudo.= '';
                // escreve o nome do módulo
                if(is_file($pastas.'/configurar_modulo.php')){
                    $conteudo.= '<a href="adm_main.php?section=conf_modulos&action=configurar_modulo&modulo='.$pastas.'" style="text-decoration: none;">'.$modInfo['nome'].'</a><br>';
                } else {
                    $conteudo.= '<strong>'.$modInfo['nome'].'</strong>';
                }
                $conteudo.= '<div>'.$modInfo['descricao'].'</div>';

                /*
                 * INSTALAR MÓDULO
                 *
                 * faz a instalação do módulo, criando as tabelas e gravando informações na tabela módulo
                 */
                if(!empty($_GET['instalar_modulo']) and
                    $_GET['instalar_modulo'] == $pastas){

                    $pasta_dir = array_reverse( explode('/', $pastas));

                    /**
                     * Ajusta variáveis para gravação
                     */
                    /**
                     * [embedownform] indica se este módulo possui habilidade para acoplar-se em formulários de outros módulos
                     * com seu próprio <form></form>
                     */
                    $modInfo['embedownform'] = (empty($modInfo['embedownform'])) ? false : $modInfo['embedownform'];
                    /**
                     * [embed] indica se este módulo possui habilidade para acoplar-se em formulários de outros módulos
                     */
                    $modInfo['embed'] = (empty($modInfo['embed'])) ? false : $modInfo['embed'];
                    /**
                     * [somenteestrutura] indica se a estrutura conterá categorias ou não.
                     */
                    $modInfo['somenteestrutura'] = (empty($modInfo['somenteestrutura'])) ? false : $modInfo['somenteestrutura'];

                    /**
                     * DBSCHEMA
                     *
                     * A partir será criado o banco de dados
                     */
                    include($pastas.'/'.MOD_DBSCHEMA);
                    
                    $migrationsMods = new MigrationsMods($conexao);

                    /*
                     * Caso o módulo não tenha migrations, faz a verificação normal das tabelas
                     * a partir de schemas, o que não é recomendado.
                     */
                    if( $migrationsMods->hasMigration($pastas) ){
                        $installStatus = $migrationsMods->updateMigration($pastas);
                        $isInstalled = $migrationsMods->isActualVersion($pastas);
                    } else {
                        $installStatus = $thisDbSchema->instalarSchema();
                        $isInstalled = $modulo->verificaInstalacaoTabelas();
                    }

                    /*
                     * Instalou?
                     */
                    if( $installStatus == true
                        OR $isInstalled )
                    {
                        /**
                         * Guarda configurações do módulo na base de dados
                         *
                         * Chama função InstalarTabelas para criação oficial do módulo
                         */
                        $param = array(
                            'tipo' => 'módulo',
                            'chave' => 'dir',
                            'valor' => $pasta_dir[0],
                            'pasta' => $pastas,
                            'modInfo' => $modInfo,
                            'autor' => $administrador->LeRegistro('id'),
                        );
                        $modulo->configuraModulo($param);


                        $conteudo.= '<div style="color: green;">Instalado com sucesso!</div>';
                    }
                    /*
                     * Não foi possível instalar o módulo.
                     */
                    else {
                        $conteudo.= '<div style="color: red;">Não foi possível instalar o módulo</div>';
                    }

                }
                /*
                 * Amostragem normal dos módulos.
                 */
                else {
                    
                    if( $modulo->verificaInstalacaoTabelas()
                        AND $modulo->verificaInstalacaoRegistro(array("pasta"=>$pastas)) )
                    {
                        $conteudo.= '<div style="color: green;">Instalado</div>';

                    } else if( $modulo->verificaInstalacaoTabelas() ){
                        $conteudo.= '<div style="color: orange;">Tabela instalada, registro no DB não.<br />';
                        $conteudo.= '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$pastas.'">Tentar instalar</a></div>';
                    } else if( $modulo->verificaInstalacaoRegistro(array("pasta"=>$pastas)) ){
                        $conteudo.= '<div style="color: orange;">Tabela não instalada, registro no DB sim.</div>';
                    } else {
                        $conteudo.= '<div style="color: red;">Não Instalado, ';
                        $conteudo.= '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$pastas.'">clique para instalar</a></div>';
                    }
                }
                $conteudo.= '<br />';

            }
            unset($modulo);
            unset($modDbSchema);
            unset($modInfo);
        }
    }
    ?>
    <div class="painel-metade painel-dois">

        <?php
        /*
         * INSTALAÇÃO DE MÓDULOS
         */
        ?>
        <div class="painel">
            <div class="titulo">
                <h2>Módulos disponíveis</h2>
            </div>
            <div class="corpo">

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
                    if(is_file($modulo['path'].'/configurar_modulo.php')){
                        echo '<a href="adm_main.php?section=conf_modulos&action=configurar_modulo&modulo='.$modulo['path'].'" style="text-decoration: none;">'.$modulo['config']['nome'].'</a>';
                    } else {
                        echo $modulo['config']['nome'];
                    }
                    ?>
                    </strong>
                    <br />
                    <?php echo $modulo['config']['descricao']?>
                    <?php

                    /*
                     * STATUS DE INSTALAÇÃO
                     */
                    /*
                     * Totalmente Atualizado.
                     */
                    //var_dump($migrationsMods->isActualVersion($path));
                    if( $migrationsMods->isActualVersion($path)
                        AND $modulos->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                    {
                        echo '<div style="color: green;">Instalado</div>';
                    } elseif( $migrationsMods->isActualVersion($path)
                        AND !$modulos->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                    {
                        echo '<div style="color: orange;">Migration atualizado, mas não há registro do módulo no DB.<br />';
                        echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Tentar registrar agora</a></div>';
                    }
                    /*
                     * Não atualizado,
                     * contém alguma versão no DB.
                     */
                    elseif( $migrationsMods->hasSomeVersion($path)
                            AND $modulos->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                    {
                        echo '<div style="color: orange;">Tabela instalada, mas requer atualização.<br />';
                        echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
                    } elseif( $migrationsMods->hasSomeVersion($path)
                            AND !$modulos->verificaInstalacaoRegistro(array("pasta"=>$path)) )
                    {
                        echo '<div style="color: orange;">Tabela instalada, mas requer atualização e registro do módulo no DB.<br />';
                        echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">Rodar Migration</a></div>';
                    } else {
                        echo '<div style="color: red;">Não Instalado, ';
                        echo '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$path.'">instalar agora</a></div>';
                    }
                    /*
                    if( $modulo->verificaInstalacaoTabelas()
                        AND $modulo->verificaInstalacaoRegistro(array("pasta"=>$pastas)) )
                    {
                        $conteudo.= '<div style="color: green;">Instalado</div>';

                    } else if( $modulo->verificaInstalacaoTabelas() ){
                        $conteudo.= '<div style="color: orange;">Tabela instalada, registro no DB não.<br />';
                        $conteudo.= '<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&instalar_modulo='.$pastas.'">Tentar instalar</a></div>';
                    } else if( $modulo->verificaInstalacaoRegistro(array("pasta"=>$pastas)) ){
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
            <div class="rodape"></div>
        </div>

        <div class="painel">
            <div class="titulo">
                <h2>Versões dos Módulos</h2>
            </div>
            <div class="corpo">

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
            <div class="rodape"></div>
        </div>

    </div>


    <?php

}

/**
 * Somente webmasters tem acesso a esta página
 */
}
?>