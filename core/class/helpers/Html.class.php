<?php
/**
 * HELPER
 *
 * Form
 *
 * Contém gerador de elementos HTML automáticos
 *
 * @package Helpers
 * @name Form
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @version 0.1
 * @since v0.1.6, 13/07/2009
 */
class HtmlHelper
{

    /**
     *
     * @var <string> Código javascript
     */
    public $jsCode;

    function __construct(){
        
    }

    static function getInstance(){

        static $instance;

        if( !$instance ){
            $instance[0] = new HtmlHelper();
        }

        return $instance[0];
    }

    /**
     * js()
     *
     * Loads all js into one file.
     *
     * Caches if needed.
     */
    public function js(){

        $isCached = $this->_isJsCached();
        $jsCacheFile = CACHE_DIR.'javascript.js';

        if( $isCached ){
            ob_start();
            include($jsCacheFile);
            ob_end_clean();
        } else {
            ob_start();
            foreach( glob( THIS_TO_BASEURL.BASECODE_JS.'*.js' ) as $file ){
                $files[$file] = filesize($file);
                include($file);
            }
            
            $js = ob_get_contents();
            ob_end_clean();

            /*
             * Procura por
             *         ->(/*! ou /* com espaço ou /**
             *                             ->qualquer caractere
             *                                 ->*\
             */
            $pattern = '/(\/\*!|\/\*\s|\/\*\*).*(\*\/)/Us';
            $replacement = '';
            $js = preg_replace($pattern, $replacement, $js);

            /*
             * Salva cache
             */
            if( !is_file($jsCacheFile) ){
                $handle = fopen( $jsCacheFile, 'w+');
                chmod($jsCacheFile, 0777);
            } else {
                $handle = fopen( $jsCacheFile, 'w');
            }

            if( is_writable($jsCacheFile) ){
                fwrite($handle, $js);
            }
            fclose($handle);
            /*
             * Salva informações sobre quais arquivos estão com cache
             */
            if( !is_file(CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES') ){
                $handle = fopen( CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES', 'w+');
                chmod(CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES', 0777);
            } else {
                $handle = fopen( CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES', 'w');
            }

            if( is_writable(CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES') ){
                foreach ($files as $filename=>$filesize) {
                    fputcsv($handle, array($filename.';'.$filesize) );
                }
            }
            fclose($handle);
        }
        echo '<script type="text/javascript" src="'.$jsCacheFile.'"></script>';
        return true;
    }

    /**
     * _isJsCached()
     *
     * Verifica se os arquivos com cache
     * definidos em cache/CLIENTSIDE_JAVASCRIPT_FILES
     * são os mesmos existentes no diretório do javascript. Se
     * forem diferentes os arquivos ou tamanhos, retorna falso.
     *
     * @return <bool>
     */
    public function _isJsCached(){

        /*
         * Arquivos atuais
         */
        foreach( glob( THIS_TO_BASEURL.BASECODE_JS.'*.js' ) as $file ){
            $files[$file] = filesize($file);
        }

        /*
         * Verifica quais arquivos estão cached
         */
        if( file_exists(CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES') )
            $handle = fopen(CACHE_DIR.'CLIENTSIDE_JAVASCRIPT_FILES', "r"); // Open file form read.
        else
            return false;

        if( $handle ){
            while( !feof($handle) ){
                $buffer = fgets($handle, 4096); // Read a line.

                $array = explode(";", $buffer);
                if( !empty($array[3]) )
                    return false;
                
                $current[$array[0]] = trim($array[1]);
                unset($array);
                
            }
        } else
            return false;
        fclose($handle); // Close the file.

        //pr($files);
        return !array_diff_assoc($files, $current);
    }

}

?>