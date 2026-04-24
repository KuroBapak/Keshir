import { useState } from 'react';
import { SendHorizontal } from 'lucide-react';
import './Chatbot.css';

export function ChatInput({ onSendMessage, isLoading }) {
  const [inputValue, setInputValue] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!inputValue.trim() || isLoading) return;
    
    onSendMessage(inputValue);
    setInputValue('');
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSubmit(e);
    }
  };

  return (
    <div className="chat-input-container">
      <form onSubmit={handleSubmit} className="chat-form">
        <textarea
          className="chat-textarea"
          placeholder="Tanya menu, stok, atau rekomendasi..."
          value={inputValue}
          onChange={(e) => setInputValue(e.target.value)}
          onKeyDown={handleKeyDown}
          disabled={isLoading}
          rows={1}
        />
        <button 
          type="submit" 
          className="chat-send-btn"
          disabled={!inputValue.trim() || isLoading}
        >
          <SendHorizontal size={20} />
        </button>
      </form>
    </div>
  );
}
