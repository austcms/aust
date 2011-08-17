<?php
/*
 *
 * WIDGETS
 *
 */
if( !empty($_GET['userId']) ){
	$conditions = (empty($_POST['id'])) ? array() : array('id' => $_POST['id']);

	/*
	 * Loop por cada coluna
	 */
	foreach( $_GET['widgets'] as $column_nr=>$positions ){

		foreach( $positions as $position_nr=>$widget_id ){

			$position_nr++;
			$sql = "UPDATE
						widgets
					SET
						column_nr=".$column_nr.",
						position_nr=".$position_nr."
					WHERE
						admin_id='".$_GET['userId']."' AND
						id='".$widget_id."'";
			Connection::getInstance()->exec($sql);

		}

	}

}

?>
