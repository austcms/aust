<?php
/**
 * Configurações do Core do sistema vão aqui. A classe estática CoreConfig é
 * responsável por guardar todas as variáveis de configuração.
 *
 * @package Configurações
 * @name core
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.1.5 24/06/2009
 */
/**
 * Opções de Collate
 * 
 * @global array $GLOBALS['aust_charset']
 * @name $aust_charset
 */
//$aust_charset['view'] = 'iso-8859-1';
//$aust_charset['db'] = 'latin1';
//$aust_charset['db_collate'] = 'latin1_general_ci';
$aust_charset['view'] = 'utf-8';
$aust_charset['db'] = 'utf8';
$aust_charset['db_collate'] = 'utf8_general_ci';

/**
 * CONFIGURAÇÕES GERAIS
 *
 * Todas as configurações do sistema
 */

    CoreConfig::write('austTable','categorias');

    /*
     * CONFIG
     */
    /*
     * Tipo de configuração padrão, visível a todos os usuários.
     */
    CoreConfig::write('configStandardType','Geral');
    CoreConfig::write('defaultTheme','classic_blue');

    /*
     * Configurações que devem ser instaladas automaticamente.
     */
        $neededConfig = array(
            array(
                'tipo' => 'Geral',
                'local' => '',
                'nome' => 'Nome do site',
                'propriedade' => 'site_name',
                'valor' => 'Modifique o nome do site',
                'explanation' => 'Este nome aparecerá no título do gerenciador',
            ),
        );
        CoreConfig::write('neededConfig', $neededConfig);

/*
 * Acid Framework
 *
 * Opções para uso com o Acid Framework
 */
$isAcid = true;
if( $isAcid ){
    define('WEBROOT', str_replace("adm_main.php", "", $_SERVER["SCRIPT_NAME"]) );

} else {
    define('WEBROOT', '' );
}

/*************************************************************
*
*	ENDEREÇOES: Configurações dos endereços gerais do site
*
*************************************************************/
// endereço dos relatórios de estatística
$relatorio_estatistica = "";

/**
 * AUST
 *
 * Configurações do Sistema AUST
 */
/**
 * Tabela padrão onde são guardadas estruturas e categorias.
 *
 * O formato dos dados salvos é como a de um organograma.
 */
//Aust::set("austTable", "categorias");



//$aust_conf["upload"] = "/";
$aust_conf["aust_directory"] = "admin/";
$aust_conf["where_gallery"] = Array('Textos','Arquivos','Agenda de Eventos','Menu');
$areas_restritas = array('Arquivos');


/*************************************************************
*
*	FRONT-END: Configurações do website
*
*************************************************************/
// Número de itens mostrados em resultado SQL por padrão
$standard_sql_limit = 10;
// Seções que serão restritas a usuários com senha
$em_teste = 0;
$siteconf["site_title"] = "acgrupo";
$siteconf["site_title_small"] = "acgrupo";
$siteconf["site_title_admin"] = "Gerenciamento do site acgrupo";
/*************************************************************
*
*	ADMINISTRAÇÃO: Configurações do Gerenciador do website
*
*************************************************************/
$admin_title = "Centro de Gerenciamento do site acgrupo";
$admin_site_name = "acgrupo";
$show_content_resume_on_front_page = "sim"; //sim ou nao
/*************************************************************
*
*	ARQUIVOS: Configurações dos ícones por extensão/tipo dos arquivos
*
*************************************************************/

// função que diz qual é o nome da imagem
function PegaIcone($arquivo){
// Documentos, textos e planilhas
    $icone["pps"] = "texto.jpg";
    $icone["ppt"] = "texto.jpg";
    $icone["xls"] = "texto.jpg";
    $icone["doc"] = "texto.jpg";
    $icone["txt"] = "texto.jpg";
    $icone["pdf"] = "texto.jpg";
    $icone["rtf"] = "texto.jpg";
    // Áudio e vídeo
    $icone["wma"] = "audio.jpg";
    $icone["wmv"] = "video.jpg";
    $icone["mp2"] = "audio.jpg";
    $icone["mp3"] = "audio.jpg";
    $icone["mp4"] = "video.jpg";
    $icone["mpg"] = "video.jpg";
    $icone["mpeg"] = "video.jpg";
    $icone["mov"] = "video.jpg";
    $icone["wav"] = "audio.jpg";
    $icone["avi"] = "video.jpg";
    // Imagens
    $icone["jpg"] = "imagens.jpg";
    $icone["jpeg"] = "imagens.jpg";
    $icone["gif"] = "imagens.jpg";
    $icone["bmp"] = "imagens.jpg";
    $icone["tiff"] = "imagens.jpg";
    $icone["tga"] = "imagens.jpg";
    $icone["png"] = "imagens.jpg";
    $icone["gif"] = "imagens.jpg";
    // Executáveis, compactados e outros
    $icone["rar"] = "application.jpg";
    $icone["jar"] = "application.jpg";
    $icone["zip"] = "application.jpg";
    $icone["exe"] = "application.jpg";

    $tam = strlen($arquivo);

    if( $arquivo[($tam)-4] == '.' ){
        $extensao = substr($arquivo,-3);
    } elseif( $arquivo[($tam)-5] == '.' ){
        $extensao = substr($arquivo,-4);
    } elseif( $arquivo[($tam)-3] == '.' ){
        $extensao = substr($arquivo,-2);
    } else {
        $extensao = NULL;
    }
    $extensao = strtolower($extensao);
    $imgurl = ($extensao == "" OR empty($icone[$extensao]) ) ? "arquivo.jpg" : $icone[$extensao];
    return $imgurl;
}

?>