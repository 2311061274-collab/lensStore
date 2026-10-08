<?php
echo "cURL Version: " . curl_version()['version'] . "\n";
echo "SSL Version: " . curl_version()['ssl_version'] . "\n";

$ch = curl_init('https://test-payment.momo.vn');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);

if ($result === false) {
    echo "SSL cURL Error: " . curl_error($ch) . "\n";
} else {
    echo "SSL Connection Successful!\n";
}
curl_close($ch);
