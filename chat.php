<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Chat with AI</h1>
        
        <div id="chat-container" class="space-y-4 mb-6 max-h-96 overflow-y-auto border-b pb-4">
            <div class="text-gray-500 italic text-center">Your conversation will appear here...</div>
        </div>

        <form id="chat-form" class="flex flex-col space-y-3">
            <textarea 
                id="user-input" 
                class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" 
                placeholder="Enter your prompt here..."
                rows="3"
                required
            ></textarea>
            <button 
                type="submit" 
                id="send-button"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400"
            >
                Send to AI
            </button>
        </form>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatContainer = document.getElementById('chat-container');
        const userInput = document.getElementById('user-input');
        const sendButton = document.getElementById('send-button');

        function appendMessage(role, content) {
            const div = document.createElement('div');
            div.className = `p-3 rounded-lg ${role === 'user' ? 'bg-blue-50 ml-8 text-right' : 'bg-gray-50 mr-8 text-left'}`;
            
            const roleLabel = document.createElement('div');
            roleLabel.className = 'text-xs font-bold uppercase mb-1 text-gray-400';
            roleLabel.textContent = role === 'user' ? 'You' : 'AI';
            
            const text = document.createElement('div');
            text.className = 'text-gray-800 whitespace-pre-wrap';
            text.textContent = content;
            
            div.appendChild(roleLabel);
            div.appendChild(text);
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = userInput.value.trim();
            if (!prompt) return;

            // Clear placeholder on first message
            if (chatContainer.querySelector('.italic')) {
                chatContainer.innerHTML = '';
            }

            appendMessage('user', prompt);
            userInput.value = '';
            userInput.disabled = true;
            sendButton.disabled = true;
            sendButton.textContent = 'Thinking...';

            try {
                const response = await fetch('ai_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt })
                });

                const data = await response.json();
                
                if (data.error) {
                    appendMessage('ai', 'Error: ' + data.error);
                } else if (data.response) {
                    appendMessage('ai', data.response);
                } else {
                    appendMessage('ai', 'Something went wrong. Please try again.');
                }
            } catch (err) {
                appendMessage('ai', 'Network error. Please check your connection.');
                console.error(err);
            } finally {
                userInput.disabled = false;
                sendButton.disabled = false;
                sendButton.textContent = 'Send to AI';
                userInput.focus();
            }
        });
    </script>
</body>
</html>
