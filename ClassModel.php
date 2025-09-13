<?php
class ClassModel {
    public static function add(&$classes, $name) {
        $classes[] = $name;
    }
    public static function getAll($classes) {
        return $classes;
    }
}
?>
