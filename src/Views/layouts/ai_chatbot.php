<div id="ai-chat-bubble" onclick="toggleAIChat()" class="ai-chat-bubble-base" title="Tanya ZaishinAI">
  <svg fill="none" stroke="#ffffff" viewBox="0 0 24 24" class="ai-bubble-svg">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
  </svg>
  <div class="ai-bubble-dot"></div>
</div>

<div id="ai-chat-panel">
  <script>
    if (sessionStorage.getItem('zaishin_ai_chat_open') === 'true') {
      document.getElementById('ai-chat-panel').style.transition = 'none';
      document.getElementById('ai-chat-panel').classList.add('active');
      const bubble = document.getElementById('ai-chat-bubble');
      if (bubble) {
        bubble.style.transform = 'scale(0) rotate(90deg)';
        bubble.style.opacity = '0';
        bubble.style.pointerEvents = 'none';
      }
    }
  </script>

  <div id="ai-history-panel" class="ai-history-panel-base">
    <div class="ai-history-header">
      <div class="ai-history-title">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-history-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Riwayat Obrolan
      </div>
      <button onclick="toggleHistoryPanel()" class="ai-history-back-btn" title="Kembali ke Obrolan">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="ai-history-back-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </button>
    </div>
    <div id="ai-history-list" class="ai-history-list-container">

    </div>
    <div class="ai-history-footer">
      <button onclick="resetAllSessions()" class="ai-history-reset-btn">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-history-reset-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        Hapus Semua Riwayat
      </button>
    </div>
  </div>

  <div class="ai-chat-header">
    <div class="ai-header-info">
      <div class="ai-header-avatar">
        <svg fill="none" stroke="#ffffff" viewBox="0 0 24 24" class="ai-header-avatar-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <div>
        <div class="ai-header-title">ZaishinAI</div>
        <div class="ai-header-subtitle">
          <span class="ai-header-status-dot"></span> Online
        </div>
      </div>
    </div>

    <div class="ai-header-controls">

      <button onclick="startNewSession()" class="ai-header-control-btn" title="Mulai Obrolan Baru">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="ai-header-control-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
      </button>

      <button onclick="toggleHistoryPanel()" class="ai-header-control-btn" title="Riwayat Obrolan">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-header-control-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </button>

      <button onclick="toggleAIChat()" class="ai-header-control-btn" title="Tutup Chat">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-header-control-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </div>

  <div id="ai-chat-body" class="ai-chat-body-container">

  </div>

  <div id="ai-chat-chips" class="ai-chat-chips-container">

  </div>

  <div class="ai-chat-footer">
    <input type="text" id="ai-chat-input" onkeypress="handleAIPress(event)" placeholder="Tanya AI..." class="ai-chat-text-input"/>
    <button onclick="sendAIMessage()" class="ai-send-btn">Kirim</button>
  </div>
</div>
