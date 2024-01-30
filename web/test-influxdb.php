<?php

// Retrieve input variables from $_GET
$url = $_GET['server'];
$org = $_GET['org'];
$bucket = $_GET['bucket'];
$token = $_GET['token'];

// adds default influxdb port if the url doesn't contain it.
if (strpos($url, ':') === false) {
    $url .= ':8086';
}

// Sample data to write
$data = [
    [
        "measurement" => "temperature",
        "tags" => [
            "location" => "New York"
        ],
        "fields" => [
            "value" => 25.5
        ],
        "timestamp" => time() * 1000000000  // Convert to nanoseconds
    ],
    [
        "measurement" => "humidity",
        "tags" => [
            "location" => "New York"
        ],
        "fields" => [
            "value" => 60
        ],
        "timestamp" => time() * 1000000000  // Convert to nanoseconds
    ]
];

// Convert data to line protocol format
$lines = '';
foreach ($data as $point) {
    $lines .= sprintf("%s,%s %s %s\n", $point['measurement'], http_build_query($point['tags']), http_build_query($point['fields']), $point['timestamp']);
}

//echo "$url/api/v2/write?org=$org&bucket=$bucket&precision=ns<br />";
// Prepare cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$url/api/v2/write?org=$org&bucket=$bucket&precision=ns");
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

// Check response status
if ($status_code === 204) {
    echo "Success: Got write access to InfluxDB.";
} else if ($status_code === 404) {
    echo "Error: Failed with status code: 404. Be sure that the bucket and exists.";
} else if ($status_code === 401) {
    echo "Error: Unauthorized. Is the token correct and does it have write access to the bucket '$bucket'";
} else if (str_contains($url, 'localhost') || str_contains($url, '127.0.0.1')) {
    echo "Error: No Response. Your db hostname points to the container itself and not a InfluxDB container/server.";
} else if ($status_code === 0) {
    echo "Error: No Response. Status code: $status_code";
} else {
    echo "Error: Failed to write data to InfluxDB. Status code: $status_code";
}
?>