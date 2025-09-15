<?php
class ClassModel {
    public static function add(&$classes, $name) {
        $classes[] = $name;
    }
    public static function getAll($classes) {
        return $classes;
    }
    public static function update(&$classes, $index, $name) {
        if (isset($classes[$index])) {
            $classes[$index] = $name;
        }
    }
    public static function delete(&$classes, $index) {
        if (isset($classes[$index])) {
            array_splice($classes, $index, 1);
        }
    }
}
?>