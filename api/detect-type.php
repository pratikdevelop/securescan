<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../ai/LocalAIApi.php';

$input = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $input = $data['code'] ?? '';
}

if (empty($input)) {
    echo json_encode(['error' => 'No input provided.']);
    exit;
}

// Simple detection for URL
if (filter_var($input, FILTER_VALIDATE_URL)) {
    $detected_language = 'URL';
    $suggestion = 'Check for common web vulnerabilities';
} else {
    $ai_prompt = "Classify the following code snippet and return only the language name (e.g., 'JavaScript', 'Python', 'SQL', 'PHP', 'HTML'). If it's not a recognizable language, return 'Text'. Snippet: 

" . $input;

    $resp = LocalAIApi::createResponse([
        'input' => [ ['role' => 'system', 'content' => 'You are a code classification assistant.'],
            ['role' => 'user', 'content' => $ai_prompt],
        ],
    ]);

    $detected_language = 'Text'; // Default
    if (!empty($resp['success'])) {
        $text = LocalAIApi::extractText($resp);
        if (!empty($text)) {
            $detected_language = trim($text);
        }
    }

    $suggestion = 'Check for common vulnerabilities in ' . $detected_language;
    switch ($detected_language) {
        case 'JavaScript':
            $suggestion = 'Check for XSS (Cross-Site Scripting) vulnerabilities';
            break;
        case 'SQL':
            $suggestion = 'Check for SQL Injection vulnerabilities';
            break;
        case 'PHP':
            $suggestion = 'Check for file inclusion and remote code execution risks';
            break;
        case 'HTML':
            $suggestion = 'Check for broken HTML structure and insecure forms';
            break;
    }
}

echo json_encode([
    'language' => $detected_language,
    'suggestion' => $suggestion,
]);
