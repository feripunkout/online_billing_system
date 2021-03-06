<?php
function isValidLargeTextField($textField) {
    $pattern = '/^[a-zA-Z0-9 \x{00A0}-\x{018F} &$%!@,\'#.-]{1,200}$/u';
    return(preg_match($pattern, $textField));
}

function isValidTextField($textField) {
    $pattern = '/^[a-zA-Z0-9 \x{00A0}-\x{018F} &$%!@,\'#.-]{1,50}$/u';
    return(preg_match($pattern, $textField));
}

function isValidUsername($username) {
    $pattern = '/^[A-Za-z][A-Za-z0-9]{4,29}$/';
    return(preg_match($pattern, $username));
}

function isValidEmail($email) {
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        return false;
    else
        return true;
}

function isValidNumber($number) {
    return is_numeric($number);
}

function isValidPositiveNumber($number) {
    return is_numeric($number) && $number > 0;
}

function isValidInvoiceNo($invoiceNo) {
    $pattern = '/^FT SEQ\/[0-9]{1,}$/';
    return(preg_match($pattern, $invoiceNo));
}
?>