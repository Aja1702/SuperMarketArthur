<?php
$pattern = '/^/productos/([0-9]+)$/';
$uri = '/productos/200';

$result = preg_match($pattern, $uri, $matches);
echo "Pattern: $pattern\n";
echo "URI: $uri\n";
echo "Result: $result\n";
print_r($matches);
