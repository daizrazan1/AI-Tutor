<?php
/**
 * Handles AI requests from the chat interface
 */

header('Content-Type: application/json');

require_once 'db.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$prompt = $input['prompt'] ?? '';
$context = $input['context'] ?? [];

if (empty($prompt)) {
    echo json_encode(['error' => 'Prompt is required']);
    exit;
}

// Get API Key from environment variable
$apiKey = getenv('GROQ_API_KEY');

if (!$apiKey) {
    echo json_encode(['error' => 'GROQ_API_KEY not configured on server']);
    exit;
}

$url = 'https://api.groq.com/openai/v1/chat/completions';

// Build system prompt from context
$eduLevel = $context['eduLevel'] ?? 'High School';
$assignmentType = $context['assignmentType'] ?? 'Persuasive';
$focusMode = $context['focusMode'] ?? 'Hook & Thesis';

$systemPrompt = "You are an AI essay tutor. 
Education Level: $eduLevel
Assignment Type: $assignmentType
Focus Mode: $focusMode

Instructions:
1. Guide the student, do NOT write for them.
2. Ask ONE probing question at a time.
3. Tailor vocabulary to the education level.
4. Stick to the focus mode unless it's a critical error.
5. If text is provided in chunks, acknowledge and wait for the next part if needed.";

$data = [
    'model' => 'llama-3.3-70b-versatile',
    'messages' => [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => $prompt]
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

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(['error' => 'Failed to connect to AI service']);
} else {
    $response = json_decode($result, true);
    if (isset($response['choices'][0]['message']['content'])) {
        $aiResponse = $response['choices'][0]['message']['content'];
        
        // Log to database
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("INSERT INTO chat_history (prompt, response) VALUES (?, ?)");
            $stmt->execute([$prompt, $aiResponse]);
        } catch (Exception $e) {
            // Silence DB errors for now or log them internally
        }

        echo json_encode(['response' => $aiResponse]);
    } else {
        $errorMessage = $response['error']['message'] ?? 'Unknown API error';
        echo json_encode(['error' => $errorMessage]);
    }
}
