<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit();
}

$action = $_POST['action'] ?? '';
$api_key = 'YOUR_GEMINI_API_KEY_HERE'; // Replace with your actual API key
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

function callGeminiAPI($prompt, $api_key, $api_url) {
    $data = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . '?key=' . $api_key);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        $result = json_decode($response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    } else {
        return 'Error: ' . $response;
    }
}

if ($action == 'customize_tour') {
    $package_id = $_POST['package_id'] ?? '';
    $budget = $_POST['budget'] ?? '';
    $requirements = $_POST['requirements'] ?? '';

    if (empty($package_id)) {
        echo json_encode(['success' => false, 'error' => 'Package ID required']);
        exit();
    }

    // Get package details
    $query = "SELECT * FROM packages WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $package_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();

    if (!$package) {
        echo json_encode(['success' => false, 'error' => 'Package not found']);
        exit();
    }

    $prompt = "Create a customized tour itinerary for the " . $package['name'] . " package to " . $package['destination'] . ". ";
    $prompt .= "Original duration: " . $package['duration'] . " days. ";
    $prompt .= "Original price: ₹" . $package['price'] . ". ";
    
    if ($budget) {
        $prompt .= "Customer's budget: ₹" . $budget . ". ";
    }
    
    if ($requirements) {
        $prompt .= "Special requirements: " . $requirements . ". ";
    }
    
    $prompt .= "Provide a detailed, customized itinerary with specific recommendations for this tour.";

    // For demonstration, return a sample response if API key is not set
    if ($api_key === 'YOUR_GEMINI_API_KEY_HERE') {
        $suggestions = "\n=== Customized Tour Itinerary ===\n\n";
        $suggestions .= "Destination: " . $package['destination'] . "\n";
        $suggestions .= "Original Duration: " . $package['duration'] . " days\n";
        if ($budget) {
            $suggestions .= "Custom Budget: ₹" . $budget . "\n";
        }
        $suggestions .= "\nSample Itinerary:\n";
        $suggestions .= "Day 1: Arrival and orientation\n";
        $suggestions .= "Day 2-3: Main attractions\n";
        $suggestions .= "Day 4+: Adventure activities and relaxation\n\n";
        $suggestions .= "Note: To get AI-powered suggestions, add your Gemini API key to config.";
    } else {
        $suggestions = callGeminiAPI($prompt, $api_key, $api_url);
    }

    echo json_encode(['success' => true, 'suggestions' => $suggestions]);
    exit();

} elseif ($action == 'ask_assistant') {
    $prompt = $_POST['prompt'] ?? '';

    if (empty($prompt)) {
        echo json_encode(['success' => false, 'error' => 'Prompt required']);
        exit();
    }

    $business_context = "You are an AI assistant for EboStay, a travel and tour booking platform. Provide helpful advice about tour planning, pricing strategies, customer management, and travel recommendations.\n\nUser question: ";
    $full_prompt = $business_context . $prompt;

    // For demonstration, return a sample response if API key is not set
    if ($api_key === 'YOUR_GEMINI_API_KEY_HERE') {
        $response = "Sample AI Response:\n\nThis is a placeholder response. To enable the AI Assistant with full Gemini API capabilities, please:\n\n1. Go to https://ai.google.dev/\n2. Get your API key\n3. Add it to the api/gemini-api.php file\n4. Restart the application\n\nThen you'll get real AI-powered suggestions for your question: \"" . $prompt . "\"";
    } else {
        $response = callGeminiAPI($full_prompt, $api_key, $api_url);
    }

    echo json_encode(['success' => true, 'response' => $response]);
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);
?>
