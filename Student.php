<?php
class Student {
    public static function add(&$students, $name) {
        $students[] = $name;
    }
    public static function getAll($students) {
        return $students;
    }
    public static function update(&$students, $index, $name) {
        if (isset($students[$index])) {
            $students[$index] = $name;
        }
    }
    public static function delete(&$students, $index) {
        if (isset($students[$index])) {
            array_splice($students, $index, 1);
        }
    }
}
?>