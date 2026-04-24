import ReactMarkdown from 'react-markdown';
import { Bot, User } from 'lucide-react';
import './Chatbot.css';

export function ChatBubble({ message }) {
  const isAssistant = message.role === 'assistant';

  return (
    <div className={`chat-bubble-wrapper ${isAssistant ? 'assistant' : 'user'}`}>
      <div className="chat-avatar">
        {isAssistant ? <Bot size={18} /> : <User size={18} />}
      </div>
      <div className={`chat-bubble ${isAssistant ? 'assistant' : 'user'} ${message.isError ? 'error' : ''}`}>
        <div className="markdown-content">
          <ReactMarkdown children={message.content || ''} />
        </div>
      </div>
    </div>
  );
}

