
<?php
class AIChat {
    private $conn;
    private $table_name = "chat_history";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function sendMessage($message, $user_id) {
        // Save user message
        $this->saveMessage($user_id, $message, 'user');
        
        // Get AI response
        $ai_response = $this->getAIResponse($message);
        
        // Save AI response
        $this->saveMessage($user_id, $ai_response, 'assistant');
        
        return $ai_response;
    }

    private function getAIResponse($message) {
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful AI assistant.'
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ],
            'max_tokens' => 1000,
            'temperature' => 0.7
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, OPENAI_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENAI_API_KEY
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';
        } else {
            return 'Sorry, there was an error connecting to the AI service.';
        }
    }

    private function saveMessage($user_id, $message, $role) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, message=:message, role=:role";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':role', $role);
        
        return $stmt->execute();
    }

    public function getChatHistory($user_id, $limit = 50) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
