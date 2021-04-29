<?php
function get_type($a){
    if($a=='number') {
        return 'type="number" required';
    } elseif ($a=='positive_number') {
        return 'type="number" min="0" required';
    } elseif ($a=='small_text') {
        return 'type="text" pattern="^[0-9a-zA-ZА-Яа-яЁё\s]{,30}" required';
    } elseif ($a=='big_text') {
        return 'type="text" pattern="^[0-9a-zA-ZА-Яа-яЁё\s]{,255}" required';
    } elseif ($a=='checkbox') {
        return 'type="checkbox"';
    } elseif ($a=='radio') {
        return 'type="radio" required';
    }
}
?>
