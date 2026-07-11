/* JavaScript untuk ZaishinAI Chatbot */

let chatSessions = [];
let activeSessionId = null;
let wmsStats = null;
let aiChatOpen = sessionStorage.getItem('zaishin_ai_chat_open') === 'true';

async function fetchWMSStats() {
  try {
    const [kpiRes, zoneRes] = await Promise.all([
      fetch(BASE_URL + '/api/dashboard.php?type=kpi').then(r => r.json()),
      fetch(BASE_URL + '/api/dashboard.php?type=chart_zone').then(r => r.json())
    ]);

    let densestName = '---', densestPct = -1;
    let emptiestName = '---', emptiestPct = 999;

    if (zoneRes && zoneRes.labels) {
      for (let i = 0; i < zoneRes.labels.length; i++) {
        const pct = zoneRes.pct[i];
        const name = zoneRes.labels[i];
        if (pct > densestPct) {
          densestPct = pct;
          densestName = `${name} (${pct}%)`;
        }
        if (pct < emptiestPct) {
          emptiestPct = pct;
          emptiestName = `${name} (${pct}%)`;
        }
      }
    }

    wmsStats = {
      totalStok: kpiRes.total_stok ? parseInt(kpiRes.total_stok).toLocaleString('id-ID') : '---',
      capacity: kpiRes.kapasitas_pct ? `${kpiRes.kapasitas_pct}%` : '---',
      alertCount: parseInt(kpiRes.alert_count) || 0,
      txCount: kpiRes.tx_hari_ini ? parseInt(kpiRes.tx_hari_ini).toLocaleString('id-ID') : '---',
      densest: densestName,
      emptiest: emptiestName
    };
  } catch (e) {
    console.error("Failed to fetch WMS stats for AI:", e);
    const totalStokEl = document.getElementById('kpi-total-stok');
    const capacityEl = document.getElementById('kpi-kapasitas');
    const alertCountEl = document.getElementById('kpi-alert');
    const txCountEl = document.getElementById('kpi-tx');
    const densestEl = document.getElementById('zone-stats-densest');
    const emptiestEl = document.getElementById('zone-stats-emptiest');

    wmsStats = {
      totalStok: totalStokEl ? totalStokEl.textContent : '---',
      capacity: capacityEl ? capacityEl.textContent : '---',
      alertCount: alertCountEl ? parseInt(alertCountEl.textContent.replace(/[^0-9]/g, '')) || 0 : 0,
      txCount: txCountEl ? txCountEl.textContent : '---',
      densest: densestEl ? densestEl.textContent : '---',
      emptiest: emptiestEl ? emptiestEl.textContent : '---'
    };
  }
}

function startNewSession() {
  const newId = 'session_' + Date.now();
  const newSession = {
    id: newId,
    title: 'Obrolan Baru',
    messages: []
  };
  chatSessions.unshift(newSession);
  activeSessionId = newId;
  localStorage.setItem('zaishin_chat_sessions', JSON.stringify(chatSessions));
  localStorage.setItem('zaishin_active_session_id', activeSessionId);

  const histPanel = document.getElementById('ai-history-panel');
  if (histPanel) histPanel.style.transform = 'translateX(-100%)';

  renderCurrentSession();
}

function resetAllSessions() {
  if (confirm('Apakah Anda yakin ingin menghapus semua riwayat obrolan?')) {
    chatSessions = [];
    localStorage.removeItem('zaishin_chat_sessions');
    localStorage.removeItem('zaishin_active_session_id');
    startNewSession();

    const histPanel = document.getElementById('ai-history-panel');
    if (histPanel) histPanel.style.transform = 'translateX(-100%)';
  }
}

function resetAIChat() {
  const session = chatSessions.find(s => s.id === activeSessionId);
  if (session) {
    session.messages = [];
    session.title = 'Obrolan Baru';
    localStorage.setItem('zaishin_chat_sessions', JSON.stringify(chatSessions));
  }
  renderCurrentSession();
}

function loadSessionsFromStorage() {
  const stored = localStorage.getItem('zaishin_chat_sessions');
  if (stored) {
    try {
      chatSessions = JSON.parse(stored);
    } catch (e) {
      console.error("Failed to parse sessions:", e);
      chatSessions = [];
    }
  }
  activeSessionId = localStorage.getItem('zaishin_active_session_id');

  if (!chatSessions || chatSessions.length === 0) {
    startNewSession();
  } else {
    if (!activeSessionId || !chatSessions.find(s => s.id === activeSessionId)) {
      activeSessionId = chatSessions[0].id;
      localStorage.setItem('zaishin_active_session_id', activeSessionId);
    }
    renderCurrentSession();
  }
}

function renderCurrentSession() {
  const body = document.getElementById('ai-chat-body');
  if (!body) return;

  const session = chatSessions.find(s => s.id === activeSessionId);
  if (!session) return;

  body.innerHTML = `
    <!-- Welcome Message -->
    <div class="ai-bot-msg-wrap">
      <div class="ai-bot-avatar-icon">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-bot-avatar-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <div class="ai-bot-bubble-text">
        Halo <strong>${escapeHTML(USER_NAME)}</strong>! Saya adalah asisten AI untuk Zaishin WMS.
        <br/><br/>
        Tanyakan apa saja seputar data gudang saat ini. Contoh:
        <div class="ai-preset-btn-group">
          <button onclick="askPreset('Berapa total stok aktif saat ini?')" class="ai-preset-btn">
            <svg fill="none" stroke="#1e40af" viewBox="0 0 24 24" class="ai-preset-btn-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Berapa total stok aktif?
          </button>
          <button onclick="askPreset('Barang apa saja yang stoknya kritis?')" class="ai-preset-btn">
            <svg fill="none" stroke="#ea580c" viewBox="0 0 24 24" class="ai-preset-btn-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Barang apa saja yang kritis?
          </button>
          <button onclick="askPreset('Bagaimana utilisasi kapasitas zona?')" class="ai-preset-btn">
            <svg fill="none" stroke="#2563eb" viewBox="0 0 24 24" class="ai-preset-btn-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Bagaimana utilisasi kapasitas zona?
          </button>
        </div>
      </div>
    </div>`;

  session.messages.forEach(msg => {
    if (msg.sender === 'user') {
      body.innerHTML += `
        <div class="ai-user-msg-wrap">
          <div class="ai-user-bubble-text">
            ${escapeHTML(msg.text)}
          </div>
          <div class="ai-user-avatar-icon">U</div>
        </div>`;
    } else {
      const reply = formatMarkdown(msg.text);
      body.innerHTML += `
        <div class="ai-bot-msg-wrap bot-subsequent">
          <div class="ai-bot-avatar-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-bot-avatar-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div class="ai-bot-bubble-text">
            ${reply}
          </div>
        </div>`;
    }
  });

  body.scrollTop = body.scrollHeight;
  renderSuggestedChips();
}

function toggleHistoryPanel() {
  const panel = document.getElementById('ai-history-panel');
  if (!panel) return;
  const isClosed = panel.style.transform === 'translateX(-100%)' || panel.style.transform === '';

  if (isClosed) {
    renderHistoryList();
    panel.style.transform = 'translateX(0)';
  } else {
    panel.style.transform = 'translateX(-100%)';
  }
}

function renderHistoryList() {
  const listEl = document.getElementById('ai-history-list');
  if (!listEl) return;

  listEl.innerHTML = '';
  if (chatSessions.length === 0) {
    listEl.innerHTML = '<div class="ai-history-empty">Tidak ada riwayat obrolan</div>';
    return;
  }

  chatSessions.forEach(session => {
    const isActive = session.id === activeSessionId;
    const activeClass = isActive ? 'active' : 'inactive';

    listEl.innerHTML += `
      <div class="ai-history-item ${activeClass}" onclick="switchSession('${session.id}')">
        <div class="ai-history-item-content">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-history-item-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          <span class="ai-history-item-text">${escapeHTML(session.title)}</span>
        </div>
        <button onclick="deleteSession(event, '${session.id}')" class="ai-history-delete-btn" title="Hapus Obrolan">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="ai-history-delete-svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    `;
  });
}

function switchSession(id) {
  activeSessionId = id;
  localStorage.setItem('zaishin_active_session_id', id);
  toggleHistoryPanel();
  renderCurrentSession();
}

function deleteSession(event, id) {
  event.stopPropagation();
  chatSessions = chatSessions.filter(s => s.id !== id);
  localStorage.setItem('zaishin_chat_sessions', JSON.stringify(chatSessions));

  if (activeSessionId === id) {
    activeSessionId = chatSessions.length > 0 ? chatSessions[0].id : null;
    if (activeSessionId) {
      localStorage.setItem('zaishin_active_session_id', activeSessionId);
    } else {
      localStorage.removeItem('zaishin_active_session_id');
    }
  }

  if (chatSessions.length === 0) {
    startNewSession();
  } else {
    renderHistoryList();
    renderCurrentSession();
  }
}

async function toggleAIChat() {
  const bubble = document.getElementById('ai-chat-bubble');
  const panel = document.getElementById('ai-chat-panel');
  aiChatOpen = !aiChatOpen;

  if (aiChatOpen) {
    sessionStorage.setItem('zaishin_ai_chat_open', 'true');
    await fetchWMSStats();
    panel.classList.add('active');
    bubble.style.transform = 'scale(0) rotate(90deg)';
    bubble.style.opacity = '0';
    bubble.style.pointerEvents = 'none';
    setTimeout(() => {
      const input = document.getElementById('ai-chat-input');
      if (input) input.focus();
    }, 150);
  } else {
    sessionStorage.setItem('zaishin_ai_chat_open', 'false');
    panel.classList.remove('active');
    bubble.style.transform = 'scale(1) rotate(0deg)';
    bubble.style.opacity = '1';
    bubble.style.pointerEvents = 'auto';
  }
}

function handleAIPress(e) {
  if (e.key === 'Enter') {
    sendAIMessage();
  }
}

function askPreset(text) {
  const input = document.getElementById('ai-chat-input');
  if (input) {
    input.value = text;
    sendAIMessage();
  }
}

function formatMarkdown(text) {
  if (!text) return '';

  let html = escapeHTML(text);

  const lines = html.split('\n');
  let result = [];
  let inCodeBlock = false;
  let codeBlockContent = [];

  for (let i = 0; i < lines.length; i++) {
    let line = lines[i];
    let trimmed = line.trim();

    if (trimmed.startsWith('```')) {
      if (inCodeBlock) {

        inCodeBlock = false;
        let content = codeBlockContent.join('\n');
        result.push(`<pre class="ai-code-pre"><code>${content}</code></pre>`);
        codeBlockContent = [];
      } else {

        inCodeBlock = true;
      }
      continue;
    }

    if (inCodeBlock) {
      codeBlockContent.push(line);
      continue;
    }

    let processedLine = line
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.*?)\*/g, '<em>$1</em>')
      .replace(/`(.*?)`/g, '<code class="ai-code-inline">$1</code>');

    let trimmedProcessed = processedLine.trim();

    if (trimmed.startsWith('* ') || trimmed.startsWith('- ') || trimmed.startsWith('• ')) {

      let content = trimmedProcessed.replace(/^[\*\-\•]\s+/, '');
      result.push(`<li class="ai-md-li-disc">${content}</li>`);
    }

    else if (/^\d+\.\s/.test(trimmed)) {
      let content = trimmedProcessed.replace(/^\d+\.\s+/, '');
      let match = trimmed.match(/^(\d+)\.\s/);
      let num = match ? match[1] : '1';
      result.push(`<li class="ai-md-li-decimal" value="${num}">${content}</li>`);
    }

    else if (trimmed === '') {
      result.push('<div class="ai-md-spacing"></div>');
    }

    else {
      result.push(`<p class="ai-md-p">${processedLine}</p>`);
    }
  }

  if (inCodeBlock && codeBlockContent.length > 0) {
    let content = codeBlockContent.join('\n');
    result.push(`<pre class="ai-code-pre"><code>${content}</code></pre>`);
  }

  return result.join('');
}

async function sendAIMessage() {
  const input = document.getElementById('ai-chat-input');
  const msgText = input.value.trim();
  if (!msgText) return;

  input.value = '';

  const body = document.getElementById('ai-chat-body');
  if (!body) return;

  body.insertAdjacentHTML('beforeend', `
    <div class="chat-message-bubble" style="display:flex; align-items:flex-start; gap:8px; max-width:85%; margin-left:auto; justify-content:flex-end;">
      <div style="background:linear-gradient(135deg, var(--primary-light), var(--primary)); color:#fff; padding:10px 14px; border-radius:12px 4px 12px 12px; font-size:12.5px; line-height:1.5; box-shadow:0 4px 12px rgba(30,64,175,0.12);">
        ${escapeHTML(msgText)}
      </div>
      <div style="width:28px; height:28px; border-radius:50%; background:#e2e8f0; color:#475569; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; margin-top:2px;">U</div>
    </div>`);

  body.scrollTo({ top: body.scrollHeight, behavior: 'smooth' });

  const session = chatSessions.find(s => s.id === activeSessionId);
  if (session) {
    const isFirstMsg = session.messages.length === 0;
    session.messages.push({ sender: 'user', text: msgText });
    if (isFirstMsg) {
      session.title = msgText.length > 25 ? msgText.substring(0, 22) + '...' : msgText;
    }
    localStorage.setItem('zaishin_chat_sessions', JSON.stringify(chatSessions));
  }

  const typingId = 'typing-' + Date.now();
  body.insertAdjacentHTML('beforeend', `
    <div id="${typingId}" class="chat-message-bubble" style="display:flex; align-items:flex-start; gap:10px; max-width:85%;">
      <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--primary-light), var(--primary)); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; box-shadow:0 2px 4px rgba(30,64,175,0.2);">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      </div>
      <div style="background:#fff; border:1px solid rgba(0,0,0,0.06); padding:10px 14px; border-radius:4px 14px 14px 14px; font-size:12.5px; color:var(--text-secondary); box-shadow:0 2px 6px rgba(0,0,0,0.02); display:flex; align-items:center; gap:4px;">
        <span style="width:5px; height:5px; border-radius:50%; background:#94a3b8; animation: bounce 1.4s infinite ease-in-out;"></span>
        <span style="width:5px; height:5px; border-radius:50%; background:#94a3b8; animation: bounce 1.4s infinite ease-in-out 0.2s;"></span>
        <span style="width:5px; height:5px; border-radius:50%; background:#94a3b8; animation: bounce 1.4s infinite ease-in-out 0.4s;"></span>
      </div>
    </div>`);

  body.scrollTo({ top: body.scrollHeight, behavior: 'smooth' });

  let rawReply = '';
  try {
    const response = await fetch(BASE_URL + '/api/gemini.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        message: msgText,
        history: session ? session.messages : [],
        stats: wmsStats
      })
    });
    const resData = await response.json();
    if (resData && resData.reply) {
      rawReply = resData.reply;
    } else if (resData && resData.fallback) {
      rawReply = generateAIResponse(msgText);
    } else {
      rawReply = "Maaf, terjadi masalah koneksi dengan layanan AI.";
    }
  } catch (err) {
    console.warn("Failed to contact Gemini proxy, falling back to mock:", err);
    rawReply = generateAIResponse(msgText);
  }

  const typingEl = document.getElementById(typingId);
  if (typingEl) {
    typingEl.classList.add('chat-fade-out');
    setTimeout(() => {
      typingEl.remove();
      const reply = formatMarkdown(rawReply);

      body.insertAdjacentHTML('beforeend', `
        <div class="chat-message-bubble" style="display:flex; align-items:flex-start; gap:10px; max-width:85%;">
          <div style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--primary-light), var(--primary)); color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px; box-shadow:0 2px 4px rgba(30,64,175,0.2);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          </div>
          <div style="background:#fff; border:1px solid rgba(0,0,0,0.06); padding:12px 16px; border-radius:4px 14px 14px 14px; font-size:12.5px; color:var(--text-secondary); line-height:1.5; box-shadow:0 2px 6px rgba(0,0,0,0.02);">
            ${reply}
          </div>
        </div>`);

      body.scrollTo({ top: body.scrollHeight, behavior: 'smooth' });

      if (session) {
        session.messages.push({ sender: 'ai', text: rawReply });
        localStorage.setItem('zaishin_chat_sessions', JSON.stringify(chatSessions));
      }
    }, 200);
  }
}

function escapeHTML(str) {
  return str.replace(/[&<>'"]/g,
    tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
  );
}

function generateAIResponse(text) {
  const cleanText = text.toLowerCase();

  if (!wmsStats) {
    wmsStats = {
      totalStok: '---',
      capacity: '---',
      alertCount: 0,
      txCount: '---',
      densest: '---',
      emptiest: '---'
    };
  }

  const totalStok = wmsStats.totalStok;
  const capacity = wmsStats.capacity;
  const alertCount = wmsStats.alertCount;
  const txCount = wmsStats.txCount;
  const densest = wmsStats.densest;
  const emptiest = wmsStats.emptiest;

  if (cleanText.includes('rangkuman') || cleanText.includes('langkah') || cleanText.includes('analisis') || cleanText.includes('rekomendasi')) {
    let advice = '';
    if (alertCount > 0) {
      advice = `1. **Segera Proses Restock**: Ada **${alertCount} barang kritis** di bawah reorder point. Segera setujui permintaan restock untuk menghindari kekosongan stok.<br/>`;
    } else {
      advice = `1. **Stok Aman**: Tidak ada stok barang kritis di reorder point. Tetap pantau ketersediaan berkala.<br/>`;
    }

    return `Berikut adalah **rangkuman data & analisis taktis WMS** untuk hari ini:
            <br/><br/>
            - **Volume Stok**: ${totalStok} unit aktif tersimpan.<br/>
            - **Kapasitas Terpakai**: Gudang terpakai sebesar **${capacity}**.<br/>
            - **Arus Transaksi**: Terjadi **${txCount} pergerakan barang** hari ini.<br/>
            - **Peta Lokasi**: Zona terpadat adalah **${densest}**, sedangkan zona terlonggar adalah **${emptiest}**.
            <br/><br/>
            **Rekomendasi Langkah Taktis Anda:**
            <br/>
            ${advice}
            2. **Optimalisasi Slot Rak**: Dikarenakan **${densest}** telah mencapai tingkat kepadatan tinggi, Anda disarankan menginstruksikan petugas gudang melakukan *mutasi internal* beberapa barang fast-moving ke **${emptiest}** untuk menyeimbangkan rak.<br/>
            3. **Pantau Log Pengeluaran**: Pastikan tidak ada delay picking pada transaksi hari ini guna menjaga performa pengiriman Sales Order tetap tepat waktu.`;
  }

  if (cleanText.includes('stok') && (cleanText.includes('total') || cleanText.includes('aktif') || cleanText.includes('berapa'))) {
    return `Total stok aktif yang tersimpan di seluruh zona gudang saat ini adalah **${totalStok} unit** barang cetak.`;
  }

  if (cleanText.includes('kritis') || cleanText.includes('perhatian') || cleanText.includes('alert') || cleanText.includes('low') || cleanText.includes('habis')) {
    if (alertCount === 0) {
      return `Kabar baik! Saat ini **tidak ada barang** yang berada di bawah batas minimum (Reorder Point). Semua level stok berada dalam kondisi aman.`;
    } else {
      return `Ada **${alertCount} tipe barang** yang berada di bawah batas minimum stok kritis. Anda disarankan segera menyetujui permintaan restock untuk item-item tersebut di halaman Restock.`;
    }
  }

  if (cleanText.includes('utilisasi') || cleanText.includes('kapasitas') || cleanText.includes('zona') || cleanText.includes('rak')) {
    return `Saat ini kapasitas total gudang terpakai sebesar **${capacity}**.
            <br/><br/>
            Berdasarkan utilisasi rak:<br/>
            - **Zona Terpadat**: ${densest}<br/>
            - **Zona Terlonggar**: ${emptiest}`;
  }

  if (cleanText.includes('transaksi') || cleanText.includes('hari ini') || cleanText.includes('arus')) {
    return `Jumlah transaksi hari ini tercatat sebanyak **${txCount} transaksi** (inbound + outbound).`;
  }

  if (cleanText.includes('halo') || cleanText.includes('hai') || cleanText.includes('siapa') || cleanText.includes('pagi') || cleanText.includes('siang') || cleanText.includes('sore') || cleanText.includes('malam')) {
    return `Halo! Saya adalah **Zaishin AI**. Saya bisa membantu Anda menganalisis pergerakan stok, menyajikan data kapasitas gudang secara cepat, dan mendeteksi stok yang kritis di WMS. Silakan ajukan pertanyaan Anda!`;
  }

  return `Maaf, saya belum memahami pertanyaan tersebut secara spesifik. Sebagai model demonstrasi, saya dapat menjawab tentang:<br/>
          - **Rangkuman hari ini** (contoh: *"Rangkuman data hari ini bagaimana?"*)<br/>
          - **Total stok aktif** (contoh: *"Berapa total stok?"*)<br/>
          - **Stok kritis/alert** (contoh: *"Barang apa yang kritis?"*)<br/>
          - **Utilisasi & kapasitas zona** (contoh: *"Bagaimana kapasitas zona?"*)<br/>
          - **Jumlah transaksi** (contoh: *"Berapa transaksi hari ini?"*)`;
}

document.addEventListener('DOMContentLoaded', async () => {
  loadSessionsFromStorage();

  if (sessionStorage.getItem('zaishin_ai_chat_open') === 'true') {
    const panel = document.getElementById('ai-chat-panel');
    if (panel) {
      await fetchWMSStats();
      renderSuggestedChips();
      setTimeout(() => {
        panel.style.transition = '';
      }, 100);
    }
  }
});

function renderSuggestedChips() {
  const chipsContainer = document.getElementById('ai-chat-chips');
  if (!chipsContainer) return;

  const urlParams = new URLSearchParams(window.location.search);
  const page = urlParams.get('page') || 'dashboard';

  const svgChart = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:var(--primary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>`;
  const svgBox = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:#ca8a04;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>`;
  const svgMap = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:#16a34a;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>`;
  const svgInbound = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:#10b981;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>`;
  const svgOutbound = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:#ef4444;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>`;
  const svgDoc = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:var(--primary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`;
  const svgSearch = `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:13px;height:13px;color:var(--primary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>`;
  const svgAlert = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:#dc2626;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
  const svgUser = `<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:13px;height:13px;color:var(--text-secondary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>`;

  let suggestions = [];
  if (page === 'zones') {
    suggestions = [
      { text: 'Bagaimana utilisasi kapasitas zona?', icon: svgChart, label: 'Kapasitas Zona' },
      { text: 'Rak mana saja yang penuh?', icon: svgBox, label: 'Rak Penuh' },
      { text: 'Bagaimana status okupansi Zona A?', icon: svgMap, label: 'Okupansi Zona A' },
      { text: 'Berapa jumlah slot kosong di Zona B?', icon: svgMap, label: 'Slot Kosong Zona B' },
      { text: 'Tampilkan daftar semua rak di Zona C', icon: svgDoc, label: 'Daftar Rak Zona C' }
    ];
  } else if (page === 'inbound') {
    suggestions = [
      { text: 'Tampilkan transaksi inbound hari ini', icon: svgInbound, label: 'Inbound Hari Ini' },
      { text: 'Cek status PO Restock', icon: svgDoc, label: 'Status PO' },
      { text: 'Berapa banyak barang masuk minggu ini?', icon: svgInbound, label: 'Inbound Mingguan' },
      { text: 'Tampilkan log masuk item Kertas HVS', icon: svgSearch, label: 'Log Kertas HVS' },
      { text: 'Siapa saja yang menerima inbound hari ini?', icon: svgUser, label: 'Staf Penerima' }
    ];
  } else if (page === 'outbound') {
    suggestions = [
      { text: 'Daftar Sales Order aktif saat ini', icon: svgDoc, label: 'Sales Order Aktif' },
      { text: 'Tampilkan transaksi outbound terbaru', icon: svgOutbound, label: 'Outbound Terbaru' },
      { text: 'Apa saja barang fast moving?', icon: svgChart, label: 'Fast Moving Items' },
      { text: 'Tampilkan log pengeluaran minggu ini', icon: svgOutbound, label: 'Log Outbound Mingguan' }
    ];
  } else if (page === 'opname') {
    suggestions = [
      { text: 'Tampilkan riwayat stock opname terbaru', icon: svgSearch, label: 'Riwayat Opname' },
      { text: 'Apakah ada selisih stock opname?', icon: svgAlert, label: 'Selisih Stok' },
      { text: 'Siapa yang melakukan audit opname terakhir?', icon: svgUser, label: 'Auditor Terakhir' },
      { text: 'Daftar sesi opname yang masih aktif', icon: svgDoc, label: 'Sesi Opname Aktif' }
    ];
  } else {

    suggestions = [
      { text: 'Berapa total stok aktif saat ini?', icon: svgChart, label: 'Total Stok Aktif' },
      { text: 'Barang apa saja yang stoknya kritis?', icon: svgAlert, label: 'Stok Kritis' },
      { text: 'Cari ketersediaan stok Kertas HVS', icon: svgSearch, label: 'Cek Stok Kertas' },
      { text: 'Tampilkan rangkuman data hari ini', icon: svgChart, label: 'Rangkuman Hari Ini' },
      { text: 'Cek stok barang di Zona E', icon: svgMap, label: 'Stok Zona E' }
    ];
  }

  chipsContainer.innerHTML = '';
  suggestions.forEach(s => {
    const chip = document.createElement('button');
    chip.className = 'ai-chip';
    chip.innerHTML = `${s.icon}<span>${s.label}</span>`;
    chip.title = s.text;
    chip.addEventListener('click', () => {
      askPreset(s.text);
    });
    chipsContainer.appendChild(chip);
  });
}
