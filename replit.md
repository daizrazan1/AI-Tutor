# AI Chat Application - Project Structure

## Database Configuration
- **Host**: localhost (Assumed for standard PHP/MySQL setup, or update if external)
- **Database**: u237055794_team06
- **Username**: u237055794_team06
- **Password**: Stored in Secrets as `DB_PASSWORD`

## Tables
### `chat_history`
Used to store prompts and AI responses.
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `prompt`: TEXT
- `response`: TEXT
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP

## Features
- Groq AI Integration (Llama 3.3 70B)
- AJAX-based chat interface (`chat.php`)
- Backend API handler (`ai_handler.php`)
