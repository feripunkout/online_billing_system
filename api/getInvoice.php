<?php
session_start();
include_once 'utilities.php';
include_once 'search.php';
include_once 'authenticationUtilities.php';

if(!comparePermissions(array('read'))) {
	$error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$value = NULL;
if ( isset($_GET['InvoiceNo']) && !empty($_GET['InvoiceNo']) ) {
    $value = $_GET['InvoiceNo'];
} else {
    $error = new Error(700, "Expected InvoiceNo parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the invoice we are looking for
$table = 'Invoice';
$field = 'invoiceNo';
$values = array($value);
$rows = array('invoiceId', 'invoiceNo', 'invoiceDate', 'customerId', 'taxPayable', 'netTotal', 'grossTotal');
$joins = array();

$invoiceSearch = new EqualSearch($table, $field, $values, $rows, $joins);
$invoice = $invoiceSearch->getResults();

if (!$invoice) {
    $error = new Error(404, "Invoice not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$invoice = $invoice[0];

roundDocumentTotals($invoice);

// Fetch the invoice lines associated with the invoice found
$table = 'InvoiceLine';
$field = 'invoiceId';
$values = array($invoice['invoiceId']);
$rows = array('lineNumber', 'productCode', 'quantity', 'unitPrice', 'creditAmount' , 'Tax.taxId AS taxId', 'taxType', 'taxPercentage');
$joins = array('InvoiceLine' => array('Tax', 'Product'));

$invoiceLinesSearch = new EqualSearch($table, $field, $values, $rows, $joins);
$invoiceLines = $invoiceLinesSearch->getResults();
foreach($invoiceLines as &$invoiceLine){
    roundLineTotals($invoiceLine);
    setValuesAsArray('tax', array('taxType', 'taxPercentage'), $invoiceLine);
}

unset($invoice['invoiceId']);
$invoice['line'] = $invoiceLines;

setValuesAsArray('documentTotals', array('taxPayable', 'netTotal', 'grossTotal' ), $invoice);

echo json_encode($invoice, JSON_NUMERIC_CHECK);