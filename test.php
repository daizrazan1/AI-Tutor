<?php
/**
 * Test Groq API connection
 * This script uses the GROQ_API_KEY environment variable.
 */

// Get API Key from environment variable
$apiKey = getenv('GROQ_API_KEY');

// For local testing if the env var isn't set globally yet
if (!$apiKey) {
    // Check if it's in the .replit file style or similar if needed, 
    // but typically we expect it in the environment.
    echo "Error: GROQ_API_KEY environment variable not set.\n";
    echo "Please ensure you have set the secret in your Replit project.\n";
    exit(1);
}

$url = 'https://api.groq.com/openai/v1/chat/completions';

// Using a current valid model
$data = [
    'model' => 'openai/gpt-oss-20b',
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Hello, are you working?'
        ]
    ]
];

$options = [
    'http' => [
        'header'  => [
            "Content-type: application/json",
            "Authorization: Bearer $apiKey"
        ],
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

echo "Testing Groq API connection...\n";
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo "Error: Failed to connect to Groq API. Check your internet connection or API URL.\n";
} else {
    $response = json_decode($result, true);
    if (isset($response['choices'][0]['message']['content'])) {
        echo "Success! Connection established.\n";
        echo "Groq Response: " . $response['choices'][0]['message']['content'] . "\n";
    } else {
        echo "API Error Received:\n";
        if (isset($response['error'])) {
            echo "Message: " . ($response['error']['message'] ?? 'Unknown error') . "\n";
            echo "Type: " . ($response['error']['type'] ?? 'N/A') . "\n";
            echo "Code: " . ($response['error']['code'] ?? 'N/A') . "\n";
        } else {
            echo "Raw response: " . $result . "\n";
        }
    }
}
