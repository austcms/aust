<?php
/**
 * HELPER
 *
 * Form
 *
 * Contém gerador de elementos HTML automáticos
 *
 * @since v0.1.6, 13/07/2009
 */
class HtmlHelper
{

	/**
	 *
	 * @var <string> Código javascript
	 */
	public $jsCode;

	public $jsCacheFilesize = 0;
	public $cssCacheFilesize = 0;

	function __construct(){
		
	}

	static function getInstance(){

		static $instance;

		if( !$instance ){
			$instance[0] = new HtmlHelper();
		}

		return $instance[0];
	}

	/*
	 * 
	 * CSS
	 * 
	 */
	/**
	 * css()
	 *
	 * Loads all js into one file.
	 *
	 * Caches if needed.
	 */
	public function css(){

		$isCached = $this->_isCssCached();
		$cacheFilePath = CACHE_CSS_CONTENT;

		if( !$isCached OR
			!is_readable($cacheFilePath) )
		{
			ob_start();
			foreach( glob( CSS_PATH.'*.css' ) as $file ){
				$files[$file] = filesize($file);
				/*
				 * Para criar cache
				 */
				include($file);
			}
			
			$cachedFileStream= ob_get_contents();
			ob_end_clean();

			/*
			 * Procura por
			 *		 ->(/*! ou /* com espaço ou /**
			 *							 ->qualquer caractere
			 *								 ->*\
			 */
			$pattern = '/(\/\*!|\/\*\s|\/\*\*).*(\*\/)|\n|\s\s\s\s|\n\s\{|\t/Us';
			$replacement = ' ';
			$cachedFileStream = preg_replace($pattern, $replacement, $cachedFileStream);
			/*
			 * Salva cache
			 */
			if( !is_writable($cacheFilePath) ){
				foreach( $files as $file=>$size ){
					echo '<link rel="stylesheet" href="'.$file.'?v='.$size.'" type="text/css" />';
				}
				return false;
			}
			if( !is_file($cacheFilePath) ){
				$handle = fopen( $cacheFilePath, 'w+');
				chmod($cacheFilePath, 0777);
			} else {
				$handle = fopen( $cacheFilePath, 'w');
			}

			if( is_writable($cacheFilePath) ){
				fwrite($handle, $cachedFileStream);
			}
			fclose($handle);
			/*
			 * Salva informações sobre quais arquivos estão com cache
			 */
			if( !is_file(CACHE_CSS_FILES) ){
				$handle = fopen( CACHE_CSS_FILES, 'w+');
				chmod(CACHE_CSS_FILES, 0777);
			} else {
				$handle = fopen( CACHE_CSS_FILES, 'w');
			}

			if( is_writable(CACHE_CSS_FILES) ){
				foreach ($files as $filename=>$filesize) {
					fputcsv($handle, array($filename.';'.$filesize) );
				}
			}
			fclose($handle);
		}
		if( is_readable($cacheFilePath) )
			echo '<link rel="stylesheet" href="'.$cacheFilePath.'?v='.$this->cssCacheFilesize.'" type="text/css" />';
		
		return true;
	}

	/**
	 * _isCssCached()
	 *
	 * Verifica se os arquivos com cache
	 * definidos em cache/CLIENTSIDE_CSS_FILES
	 * são os mesmos existentes no diretório do javascript. Se
	 * forem diferentes os arquivos ou tamanhos, retorna falso.
	 *
	 * @return <bool>
	 */
	public function _isCssCached(){


		$this->cssCacheFilesize = 0;
		/*
		 * Arquivos atuais
		 */
		foreach( glob( CSS_PATH.'*.css' ) as $file ){
			$size = filesize($file);
			$files[$file] = $size;
			$this->cssCacheFilesize+= (int) $size;
		}

		if( !is_file(CACHE_CSS_CONTENT) OR
			filesize(CACHE_CSS_CONTENT) == 0 )
			return false;
		/*
		 * Verifica quais arquivos estão cached
		 */
		if( file_exists(CACHE_CSS_FILES) )
			$handle = fopen(CACHE_CSS_FILES, "r"); // Open file form read.
		else
			return false;

		if( $handle ){
			while( !feof($handle) ){
				$buffer = fgets($handle, 4096); // Read a line.
				$array = explode(";", $buffer);

				if( !empty($array[3]) )
					return false;

				if( !empty( $array[1] ) )
					$current[$array[0]] = trim($array[1]);
					
				unset($array);
				
			}
		} else
			return false;
		fclose($handle); // Close the file.

		//pr($files);
		return !array_diff_assoc($files, $current);
	}
	
	/*
	 *
	 * JAVASCRIPT
	 *
	 */
	/**
	 * js()
	 *
	 * Loads all js into one file.
	 *
	 * Caches if needed.
	 */
	public function js(){

		$isCached = $this->_isJsCached();
		$jsCacheFile = CACHE_JS_CONTENT;
		
		if( !$isCached ){
			ob_start();
			foreach( glob( BASECODE_JS.'*.js' ) as $file ){
				$files[$file] = filesize($file);
				/*
				 * Para criar cache
				 */
				include($file);
			}
			
			$js = ob_get_contents();
			ob_end_clean();

			/*
			 * Permissões negadas nos diretórios de cache
			 */
			if( !is_writable($jsCacheFile) ){
				foreach( $files as $file=>$size ){
					echo '<script type="text/javascript" src="'.$file.'?v='.$size.'"></script>';
				}
				return false;
			}
			/*
			 * Procura por
			 *		 ->(/*! ou /* com espaço ou /**
			 *							 ->qualquer caractere
			 *								 ->*\
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
			if( !is_file(CACHE_JS_FILES) ){
				$handle = fopen( CACHE_JS_FILES, 'w+');
				chmod(CACHE_JS_FILES, 0777);
			} else {
				$handle = fopen( CACHE_JS_FILES, 'w');
			}

			if( is_writable(CACHE_JS_FILES) ){
				foreach ($files as $filename=>$filesize) {
					fputcsv($handle, array($filename.';'.$filesize) );
				}
			}
			fclose($handle);
		}
		echo '<script type="text/javascript" src="'.$jsCacheFile.'?v='.$this->jsCacheFilesize.'"></script>';
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


		$this->jsCacheFilesize = 0;
		/*
		 * Arquivos atuais
		 */
		foreach( glob( BASECODE_JS.'*.js' ) as $file ){
			$size = filesize($file);
			$files[$file] = $size;
			$this->jsCacheFilesize+= (int) $size;
		}

		if( !is_file(CACHE_JS_CONTENT) OR
			filesize(CACHE_JS_CONTENT) == 0 )
			return false;
		/*
		 * Verifica quais arquivos estão cached
		 */
		if( file_exists(CACHE_JS_FILES) )
			$handle = fopen(CACHE_JS_FILES, "r"); // Open file form read.
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