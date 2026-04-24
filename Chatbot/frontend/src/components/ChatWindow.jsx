import { useEffect, useRef } from 'react';
import { ChatBubble } from './ChatBubble';
import { QuickActions } from './QuickActions';
import './Chatbot.css';

export function ChatWindow({ messages, isLoading, onActionSelect }) {
  const bottomRef = useRef(null);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages, isLoading]);

  return (
    <div className="chat-window">
      {messages.length === 1 && (
        <QuickActions onActionSelect={onActionSelect} />
      )}
      
      <div className="chat-messages">
        {messages.map((msg, idx) => (
          <ChatBubble key={idx} message={msg} />
        ))}
        {isLoading && (
          <div className="chat-bubble-wrapper assistant">
            <div className="chat-avatar skeleton"></div>
            <div className="chat-bubble assistant typing-indicator">
              <span></span><span></span><span></span>
            </div>
          </div>
        )}
        <div ref={bottomRef} />
      </div>
    </div>
  );
}
