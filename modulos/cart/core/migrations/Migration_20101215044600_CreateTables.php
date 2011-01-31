<?php
/**
 * MOD MIGRATION
 * 
 * Migration de um módulos
 * 
 */
class Migration_20101215044600_CreateTables extends Migrations
{
    function up(){


        $schema['cart'] = array(
            "id" => "int auto_increment",
            "node_id" => "int",
            "transaction_nr" => "varchar(200) COMMENT 'unique number.'",
            "client_id" => "int",
            "paid" => "int DEFAULT '0' COMMENT 'is paid?'",
            "process" => "int DEFAULT '0' COMMENT 'should be processed?'",
            "pending" => "int DEFAULT '1' COMMENT 'this cart has pending tasks'",
            "sent" => "int DEFAULT '0' COMMENT 'sent to the customer?'",
            "total_price" => "decimal(13,2)",
            "deadline_days" => "int COMMENT 'how many days were given as deadline.'",
            "deadline" => "datetime",
            "scheduled_on" => "datetime",
            "created_on" => "datetime",
            "updated_on" => "datetime",
            "admin_id" => "int",
            "dbSchemaTableProperties" => array(
                "PRIMARY KEY" => "(id)",
                "INDEX" => "(client_id)",
            )
        );
	    $schema['cart_line_items'] = array(
	        "id" => "int auto_increment",
	        "cart_id" => "int",
	        "transaction_nr" => "varchar(200) COMMENT 'unique number.'",
	        "product_id" => "int",
	        "product_title" => "varchar(240)",
	        "product_description" => "text",
	        "price" => "decimal(13,2)",
	        "quantity" => "decimal(10,2)",
	        "quantity_unit" => "varchar(20) COMMENT 'units? weight? liters? usually, the product title already depicts this.'",
	        "created_on" => "datetime",
	        "updated_on" => "datetime",
	        "admin_id" => "int",
	        "dbSchemaTableProperties" => array(
	            "PRIMARY KEY" => "(id)",
	            "INDEX" => "(product_id)",
	        )
	    );
        $this->createTable( $schema );

        return true;
    }

    function down(){
        $this->dropTable('cart');
        $this->dropTable('cart_line_items');
        return true;

    }
}
?>