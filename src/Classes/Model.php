<?php

namespace Classes;

use Interfaces\IModel;

class Model implements IModel{

    protected $table = __CLASS__;


    public static function all($table, $array_type = \PDO::FETCH_ASSOC){
        $query = "SELECT * FROM $table";

        $conn = app()->user()->connection();
        //Prepare our SQL statement,
        $statement = $conn->prepare($query);
    
        //Execute the statement.
        $statement->execute();
    
        //Fetch the rows from our statement.
        $data = $statement->fetchAll($array_type);

        return $data;
    
    }

    public static function get_where($field, $value , $operation = "="){
        $query = "SELECT * FROM ". self::$table ." WHERE $field $operation ?";

        $conn = app()->user()->connection();
        //Prepare our SQL statement,
        $statement = $conn->prepare($query);
    
        //Execute the statement.
        $statement->execute([$value]);
    
        //Fetch the rows from our statement.
        $data = $statement->fetchAll($array_type);

    }

    public static function insert($data){
    }

    public static function update(){


    }

    public static function delete(){
        
    }

    public static function remove($table_name){
        $query = "DELETE FROM $table_name";

        $conn = app()->user()->connection();
        //Prepare our SQL statement,
        $statement = $conn->prepare($query);
    
        //Execute the statement.
        $status = $statement->execute();

        return $status;
    
    }

}