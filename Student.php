<?php
class Student {
    public static function add(&$students, $name) {
        $students[] = $name;
    }
    public static function getAll($students) {
        return $students;
    }
}
?>
