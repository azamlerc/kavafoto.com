<?php
if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', array('GET', 'HEAD'), true)) {
    header('Allow: GET, HEAD');
    http_response_code(405);
    exit('This archive is read-only.');
}
// These archive pages accept scalar query values only.
foreach ($_GET as $key => $value) {
    if (!is_string($value)) {
        http_response_code(400);
        exit('Invalid query parameter.');
    }
}
foreach (array('page', 'number', 'column') as $key) {
    if (isset($_GET[$key]) && (!ctype_digit($_GET[$key]) || (int) $_GET[$key] > 100000)) {
        http_response_code(400);
        exit('Invalid page number.');
    }
}
