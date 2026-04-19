import { useState, useCallback, useEffect } from 'react';

export function useGeminiChat(role = 'customer') {
  const [messages, setMessages] = useState([
    {
      role: 'assistant',
      content: 'Halo! Saya Keshir AI Assistant 👋🏻 Ada yang bisa saya bantu terkait menu atau kafe kami?',
      isFunctionCall: false
    }
  ]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);

  // Auto-scroll logic happens in ChatWindow, this just manages state.
  const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api/v1/chatbot';

  const sendMessage = useCallback(async (content) => {
    if (!content.trim()) return;

    // Add user message
    const userMessage = { role: 'user', content, isFunctionCall: false };
    setMessages((prev) => [...prev, userMessage]);
    setIsLoading(true);
    setError(null);

    try {
      // Prepare history (excluding the very first greeting and formatting for Laravel API)
      const conversationHistory = messages.filter((m, i) => i !== 0).map(m => ({
        role: m.role,
        content: m.content
      }));

      const response = await fetch(`${apiUrl}/message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          message: content,
          conversation_history: conversationHistory,
          role: role
        })
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || data.error || 'Terjadi kesalahan pada server');
      }

      const isFunction = !!data.data.function_called;

      setMessages((prev) => [
        ...prev,
        {
          role: 'assistant',
          content: data.data.message,
          isFunctionCall: isFunction,
          functionName: data.data.function_called
        }
      ]);

    } catch (err) {
      console.error('Chat error:', err);
      setError(err.message);
      setMessages((prev) => [
        ...prev,
        {
          role: 'assistant',
          content: 'Maaf, terjadi masalah koneksi ke server. Silakan coba lagi nanti.',
          isError: true
        }
      ]);
    } finally {
      setIsLoading(false);
    }
  }, [messages, apiUrl]);

  return {
    messages,
    isLoading,
    error,
    sendMessage
  };
}
