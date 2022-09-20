<?php

namespace Interfaces;

interface IModel{

    public static function all($table);

    public static function get_where($field, $value);

    public static function insert($data);

    public static function update();

    public static function delete();
}