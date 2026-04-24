import './Chatbot.css';

export function QuickActions({ onActionSelect }) {
  const actions = [
    { label: '🏆 Menu Terlaris', query: 'Apa saja 5 menu paling laris di sini?' },
    { label: '☀️ Minuman Cuaca Panas', query: 'Cuaca lagi panas, ada rekomendasi minuman?' },
    { label: '📋 Detail Caramel Macchiato', query: 'Tolong jelaskan detail menu Caramel Macchiato' },
    { label: '📦 Cek Stok Kopi', query: 'Apakah stok Arabica Coffee Beans masih aman?' }
  ];

  return (
    <div className="quick-actions-container">
      <p className="quick-actions-title">Coba tanyakan:</p>
      <div className="quick-actions-list">
        {actions.map((action, idx) => (
          <button 
            key={idx} 
            className="quick-action-btn"
            onClick={() => onActionSelect(action.query)}
          >
            {action.label}
          </button>
        ))}
      </div>
    </div>
  );
}
