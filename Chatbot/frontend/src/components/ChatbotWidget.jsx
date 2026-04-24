import { useState } from 'react';
import { MessageSquare, X } from 'lucide-react';
import { useGeminiChat } from '../hooks/useGeminiChat';
import { ChatWindow } from './ChatWindow';
import { ChatInput } from './ChatInput';
import './Chatbot.css';

export function ChatbotWidget({ role = "customer" }) {
  const [isOpen, setIsOpen] = useState(false);
  const { messages, isLoading, sendMessage } = useGeminiChat(role);

  return (
    <div className="chatbot-widget-container">
      {isOpen && (
        <div className="chatbot-panel">
          <div className="chatbot-header">
            <div>
              <h3>Keshir AI Assistant</h3>
              <p>Powered by Gemini 1.5</p>
            </div>
            <button className="chatbot-close-btn" onClick={() => setIsOpen(false)}>
              <X size={20} />
            </button>
          </div>
          
          <ChatWindow 
            messages={messages} 
            isLoading={isLoading} 
            onActionSelect={sendMessage}
          />
          
          <ChatInput 
            onSendMessage={sendMessage} 
            isLoading={isLoading} 
          />
        </div>
      )}

      <button 
        className={`chatbot-fab ${isOpen ? 'open' : ''}`} 
        onClick={() => setIsOpen(!isOpen)}
      >
        {isOpen ? <X size={24} /> : <MessageSquare size={24} />}
      </button>
    </div>
  );
}
