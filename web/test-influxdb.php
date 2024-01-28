<?php

// Retrieve input variables from $_GET
$server = $_GET['server'];
$org = $_GET['org'];
$bucket = $_GET['bucket'];
$token = $_GET['token'];

// Sample data to write
$data = [
        "measurement" => "temperature",
        "tags" => [
            "location" => "New York"
        ],
        "fields" => [
            "value" => 25.5
        ]
];

// Convert data to line protocol format
$lines = '';
foreach ($data as $point) {
    $lines .= sprintf("%s,%s %s %s\n", $point['measurement'], http_build_query($point['tags']), http_build_query($point['fields']), $point['timestamp']);
}

// Prepare cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url/api/v2/write?org=$org&bucket=$bucket");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $lines);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $token",
]);

// Execute cURL request
$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
?>