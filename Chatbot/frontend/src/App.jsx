import { ChatbotWidget } from './components/ChatbotWidget'
import './App.css'

function App() {
  return (
    <>
      <div style={{ textAlign: 'center', marginTop: '50px', fontFamily: 'sans-serif' }}>
        <h1>Keshir POS Frontend</h1>
        <p>This is a simulated host application.</p>
        <p>Look at the bottom right corner for the chatbot widget! 🚀</p>
      </div>

      {/* Plug and Play Widget with role prop */}
      <ChatbotWidget role="customer" />
    </>
  )
}

export default App
