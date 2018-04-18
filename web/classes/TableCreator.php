<?php
/**
 * Created by PhpStorm.
 * User: Vincent
 * Date: 5-5-2015
 * Time: 14:53
 */

class TableCreator {
    public static function createTable($arrayOfObjects) {
        $arrayOfArrays = array_map(function($s) { return $s->toArray(); }, $arrayOfObjects);

        return TableCreator::createTableWithClass($arrayOfArrays, "outer");
    }

    private static function createTableWithClass($array, $class) {
        if (count($array) && $class == "outer") {
            $header = array_keys(reset($array));
        }

        return "<table class='$class'>"
        . ($header ? TableCreator::createRow($header, true) : "")
        . implode('', array_map(function($r) { return TableCreator::createRow($r, false); }, $array))
        . "</table>";
    }

    private static function createRow($row, $isHeader) {
        return "<tr>"
        . implode('', array_map(function($c) use ($isHeader) { return TableCreator::createCell($c, $isHeader); }, $row))
        . "</tr>";
    }

    private static function createCell($cell, $isHeader) {
        $td = $isHeader ? "th" : "td";

        if (is_array($cell)) {
            return "<$td>" . TableCreator::createTableWithClass($cell, "inner") . "</$td>";
        } else {
            return "<$td>$cell</$td>";
        }
    }
/*
    public static function foo($array) {
        $subresults = array();
        $height = 1;
        foreach ($array as $column) {
            if (is_array($column)) {
                $subresult = foo($column);
                $height = max($height, count($subresult));
                $subresults[] = $subresult;
            }
        }

        $result = array();
        for ($i = 0; $i < $height; $i++) $result[$i] = array();

        foreach ($array as $column) {
            if (is_array($column)) {
                for ($i = 0; $i < count($column); $i++) {
                    $result[$i] = array_merge($result[$i], $column[$i]);
                }
            } else {
                $result[0][] = new Cell($height, $column);
            }
        }
    }*/
}

class Cell {
    private $height;
    private $value;

    function __construct($height, $value)
    {
        $this->height = $height;
        $this->value = $value;
    }
}