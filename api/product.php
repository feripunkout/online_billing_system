<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';

function getProduct($productCode) {
    // Fetch the product we are looking for
    $table = 'Product';
    $field = 'ProductCode';
    $values = array($productCode);
    $rows = array('ProductCode','ProductDescription', 'UnitPrice', 'UnitOfMeasure');
    $joins = array();

    $search = new EqualSearch($table, $field, $values, $rows, $joins);
    $result = $search->getResults();

    if (!$result) {
        $error = new Error(404, "Product not found");
        return $error->getInfo();
        //die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
    }

    $result = $result[0];

    roundProductTotals($result);

    return $result;
}

function updateProduct($productInfo) {

// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Product';
    $field = 'ProductCode';
    $productCode = $productInfo['ProductCode'];
    if ($productCode == NULL) {
        $productCode = getLastProductCode() + 1;
        $productInfo['ProductCode'] = $productCode;
        new Insert('Product', $productInfo);
    } else
        new Update($table, $productInfo, $field, $productCode);

    return getProduct($productCode);
}

function getLastProductCode(){
    $table = 'Product';
    $field = 'ProductCode';
    $values = array();
    $rows = array('ProductCode');
    $max = new MaxSearch($table, $field, $values, $rows);
    $results = $max->getResults();
    if(isSet($results[0])) {
        return $results[0]['ProductCode'];
    }
    return 0;
}