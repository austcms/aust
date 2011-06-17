<?php
/**
 * Dispatches requests to the appropriated place.
 *
 * @author Alexandre de Oliveira <chavedomundo@gmail.com>
 * @since v0.2.0, 17/06/2011
 */
class Dispatcher {
	
	public function __construct(){
		
	}
	
	public function controller(){
		if( empty($_GET["section"]) )
			return "content";

		return $_GET["section"];
	}

	public function action(){
		if( empty($_GET["action"]) )
			return "index";

		return $_GET["action"];
	}
	
	public function dispatch(){

        $_GET['action'] = $this->action();
        $_GET['section'] = $this->controller();

        ob_start();
        include($this->controllerFile());
        $content_for_layout = ob_get_contents();
        ob_end_clean();

		// show only view?
		$viewOnly = false;
		if( (
				!empty($_GET['viewonly'])
				AND $_GET['viewonly'] == 'yes'
			)
			OR 
			(
				!empty($_POST['viewonly'])
				AND $_POST['viewonly'] == 'yes'
			)
		)
		{
			$viewOnly = true;
		}

        if( $viewOnly == false
			AND (
				empty($_GET['theme'])
            	OR $_GET['theme'] != 'blank'
			)
		)
        {
            include(UI_STANDARD_FILE);
        } else {
            echo $content_for_layout;
        }

	}
	
	function callController(){
		
	}
	
	function controllerFile(){
        if( UiPermissions::getInstance()->isPermittedSection() )
            return INC_DIR . $this->controller() . '.inc.php';
        else
            return MSG_DENIED_ACCESS;
	}
	
}
?>