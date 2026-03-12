<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Essay Tutor MVP</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-4 md:p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <header class="bg-blue-600 p-6 text-white">
            <h1 class="text-2xl font-bold">AI Essay Tutor</h1>
            <p class="text-blue-100 mt-1">Personalized writing guidance for every student.</p>
        </header>

        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Setup Sidebar -->
            <div id="setup-panel" class="space-y-4 border-r pr-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Education Level</label>
                    <select id="edu-level" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="Middle School">Middle School</option>
                        <option value="High School">High School</option>
                        <option value="AP / IB">AP / IB Level</option>
                        <option value="College">College/University</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assignment Type</label>
                    <select id="assignment-type" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="Persuasive">Persuasive / Argumentative</option>
                        <option value="Expository">Expository / Informative</option>
                        <option value="Narrative">Narrative / Personal Statement</option>
                        <option value="Literary Analysis">Literary Analysis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Coaching Focus</label>
                    <select id="focus-mode" class="w-full p-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                        <option value="Hook & Thesis">The "Hook" & Thesis</option>
                        <option value="Evidence Auditor">The "Evidence Auditor"</option>
                        <option value="Flow Check">The "Flow" Check</option>
                        <option value="Tone Polisher">The "Tone" Polisher</option>
                    </select>
                </div>
                <button id="start-btn" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">Start Session</button>
            </div>

            <!-- Chat Area -->
            <div class="md:col-span-2 flex flex-col h-[600px]">
                <div id="chat-window" class="flex-grow overflow-y-auto space-y-4 p-4 bg-gray-50 rounded-lg border">
                    <div class="text-center text-gray-400 text-sm mt-10">Select your options and click "Start Session" to begin.</div>
                </div>

                <form id="input-form" class="mt-4 flex gap-2">
                    <textarea id="user-msg" class="flex-grow p-3 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Paste your essay chunk or ask a question..." rows="2" disabled></textarea>
                    <button type="submit" id="send-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-300" disabled>Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chatWindow = document.getElementById('chat-window');
        const inputForm = document.getElementById('input-form');
        const userMsg = document.getElementById('user-msg');
        const sendBtn = document.getElementById('send-btn');
        const startBtn = document.getElementById('start-btn');
        
        let sessionData = null;

        startBtn.onclick = () => {
            sessionData = {
                eduLevel: document.getElementById('edu-level').value,
                assignmentType: document.getElementById('assignment-type').value,
                focusMode: document.getElementById('focus-mode').value
            };
            
            chatWindow.innerHTML = '';
            addMessage('ai', `System Initialized. I am ready to help you with your ${sessionData.assignmentType} essay at the ${sessionData.eduLevel} level. We are focusing on: ${sessionData.focusMode}. How can I help you get started?`);
            
            userMsg.disabled = false;
            sendBtn.disabled = false;
            startBtn.textContent = "Restart Session";
        };

        function addMessage(role, text) {
            const div = document.createElement('div');
            div.className = `flex ${role === 'user' ? 'justify-end' : 'justify-start'}`;
            const inner = document.createElement('div');
            inner.className = `max-w-[80%] p-3 rounded-xl shadow-sm ${role === 'user' ? 'bg-blue-600 text-white' : 'bg-white border text-gray-800'}`;
            inner.innerHTML = `<div class="text-[10px] font-bold uppercase opacity-60 mb-1">${role === 'user' ? 'You' : 'Tutor'}</div><div class="whitespace-pre-wrap">${text}</div>`;
            div.appendChild(inner);
            chatWindow.appendChild(div);
            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        inputForm.onsubmit = async (e) => {
            e.preventDefault();
            const msg = userMsg.value.trim();
            if (!msg) return;

            addMessage('user', msg);
            userMsg.value = '';
            userMsg.disabled = true;
            sendBtn.disabled = true;

            try {
                const response = await fetch('ai_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        prompt: msg,
                        context: sessionData
                    })
                });
                const data = await response.json();
                addMessage('ai', data.response || data.error || "Sorry, I encountered an error.");
            } catch (err) {
                addMessage('ai', "Network error. Please try again.");
            } finally {
                userMsg.disabled = false;
                sendBtn.disabled = false;
                userMsg.focus();
            }
        };
    </script>
</body>
</html>
