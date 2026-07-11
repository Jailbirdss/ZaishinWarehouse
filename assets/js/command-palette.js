

(function () {
    'use strict';

    const DEFAULT_MENU_ITEMS = [
        { id: 'dashboard', label: 'Dashboard', desc: 'Ringkasan & KPI gudang', url: 'index.php?page=dashboard', icon: 'dashboard', keywords: ['dashboard', 'beranda', 'utama', 'kpi'] },
        { id: 'zones', label: 'Peta Gudang', desc: 'Visualisasi zona & slot rak', url: 'index.php?page=zones', icon: 'map', keywords: ['peta', 'gudang', 'zona', 'rak', 'slot', 'area'] },
        { id: 'stock', label: 'Ketersediaan Stok', desc: 'Cek stok semua barang', url: 'index.php?page=stock', icon: 'stock', keywords: ['stok', 'stock', 'ketersediaan', 'barang', 'inventaris'] },
        { id: 'inbound', label: 'Barang Masuk', desc: 'Penerimaan barang & scan QR', url: 'index.php?page=inbound', icon: 'inbound', keywords: ['masuk', 'inbound', 'penerimaan', 'po', 'purchase order'] },
        { id: 'outbound', label: 'Barang Keluar', desc: 'Pengiriman & picking list', url: 'index.php?page=outbound', icon: 'outbound', keywords: ['keluar', 'outbound', 'pengiriman', 'picking', 'so'] },
        { id: 'relocation', label: 'Mutasi Stok', desc: 'Pindah barang antar rak/zona', url: 'index.php?page=relocation', icon: 'relocation', keywords: ['mutasi', 'pindah', 'relokasi', 'transfer', 'stok'] },
        { id: 'opname', label: 'Stock Opname', desc: 'Penghitungan & audit stok fisik', url: 'index.php?page=opname', icon: 'opname', keywords: ['opname', 'audit', 'stock', 'fisik', 'hitung', 'cek'] },
        { id: 'restock', label: 'Permintaan Restock', desc: 'Buat & kelola permintaan restock', url: 'index.php?page=restock', icon: 'restock', keywords: ['restock', 'pembelian', 'request', 'minta', 'permintaan'] },
        { id: 'sales-orders', label: 'Sales Order (SO)', desc: 'Daftar & status sales order', url: 'index.php?page=sales-orders', icon: 'sales', keywords: ['so', 'sales', 'order', 'penjualan', 'daftar'] },
        { id: 'items', label: 'Master Barang', desc: 'Kelola data barang & kategori', url: 'index.php?page=items', icon: 'items', keywords: ['master', 'barang', 'item', 'produk', 'kategori', 'sku'] },
        { id: 'reports', label: 'Laporan Transaksi', desc: 'Export laporan masuk/keluar', url: 'index.php?page=reports', icon: 'reports', keywords: ['laporan', 'report', 'export', 'csv', 'excel', 'cetak', 'print', 'transaksi'] },
        { id: 'notifications', label: 'Notifikasi & Alarm', desc: 'Pusat notifikasi & alarm stok', url: 'index.php?page=notifications', icon: 'notif', keywords: ['notif', 'notifikasi', 'alarm', 'stok', 'rendah', 'kritis'] },
        { id: 'users', label: 'Manajemen User', desc: 'Kelola akun & hak akses', url: 'index.php?page=users', icon: 'users', keywords: ['user', 'pengguna', 'akun', 'manajemen', 'akses', 'admin'] },
    ];

    const ICONS = {
        dashboard: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>`,
        map: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>`,
        stock: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>`,
        inbound: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>`,
        outbound: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>`,
        relocation: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>`,
        opname: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>`,
        restock: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>`,
        sales: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>`,
        items: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/></svg>`,
        reports: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
        notif: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`,
        users: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>`,
    };

    const RECENT_KEY = 'zaishin_recent_pages';
    const MAX_RECENT = 4;
    let activeIndex = -1;
    let flatItems = [];
    let paletteOpen = false;

    function _buildPaletteHTML() {
        const isMac = navigator.platform.toUpperCase().includes('MAC');
        const shortcut = isMac ? '⌘K' : 'Ctrl+K';

        const overlay = document.createElement('div');
        overlay.id = 'command-palette-overlay';
        overlay.innerHTML = `
        <div id="command-palette-box">
            <div id="cp-input-row">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                </svg>
                <input id="cp-search-input" type="text" placeholder="Cari menu, fitur, halaman..." autocomplete="off" spellcheck="false"/>
                <span class="cp-kbd">Esc</span>
            </div>
            <div id="cp-results"></div>
            <div id="cp-footer">
                <span class="cp-hint"><span class="cp-kbd">↑↓</span> Navigasi</span>
                <span class="cp-hint"><span class="cp-kbd">↵</span> Buka</span>
                <span class="cp-hint"><span class="cp-kbd">Esc</span> Tutup</span>
                <span class="ml-auto fs-xs text-light">${shortcut} untuk membuka</span>
            </div>
        </div>
    `;
        document.body.appendChild(overlay);

        overlay.addEventListener('mousedown', (e) => {
            if (e.target === overlay) closePalette();
        });

        const input = document.getElementById('cp-search-input');
        input.addEventListener('input', () => _renderResults(input.value.trim()));

        input.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                _moveActive(1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                _moveActive(-1);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0 && flatItems[activeIndex]) {
                    const targetUrl = flatItems[activeIndex].url;
                    closePalette();
                    window.location.href = targetUrl;
                }

            } else if (e.key === 'Escape') {
                e.preventDefault();
                closePalette();
            }
        });
    }

    function openPalette() {
        if (!document.getElementById('command-palette-overlay')) _buildPaletteHTML();
        const overlay = document.getElementById('command-palette-overlay');
        overlay.classList.add('open');
        paletteOpen = true;
        document.body.style.overflow = 'hidden';

        const input = document.getElementById('cp-search-input');
        input.value = '';
        _addRecentPage();
        _renderResults('');

        setTimeout(() => input.focus(), 60);
    }

    function closePalette() {
        const overlay = document.getElementById('command-palette-overlay');
        if (overlay) overlay.classList.remove('open');
        paletteOpen = false;
        activeIndex = -1;
        flatItems = [];
        document.body.style.overflow = '';
    }

    function _getRecentPages() {
        try { return JSON.parse(sessionStorage.getItem(RECENT_KEY) || '[]'); }
        catch { return []; }
    }

    function _addRecentPage() {
        const params = new URLSearchParams(window.location.search);
        const pageId = params.get('page') || 'dashboard';
        const item = DEFAULT_MENU_ITEMS.find(m => m.id === pageId);
        if (!item) return;

        let recent = _getRecentPages().filter(r => r.id !== pageId);
        recent.unshift({ id: pageId, label: item.label, icon: item.icon, url: item.url });
        if (recent.length > MAX_RECENT) recent = recent.slice(0, MAX_RECENT);
        try { sessionStorage.setItem(RECENT_KEY, JSON.stringify(recent)); } catch { }
    }

    function _fuzzyMatch(item, query) {
        if (!query) return true;
        const q = query.toLowerCase();
        return item.label.toLowerCase().includes(q)
            || (item.desc && item.desc.toLowerCase().includes(q))
            || (item.keywords && item.keywords.some(k => k.toLowerCase().includes(q)));
    }

    function _renderResults(query) {
        const container = document.getElementById('cp-results');
        if (!container) return;

        const allowedIds = (typeof CP_ALLOWED_PAGES !== 'undefined') ? CP_ALLOWED_PAGES : null;
        const menuItems = DEFAULT_MENU_ITEMS.filter(m => !allowedIds || allowedIds.includes(m.id));

        flatItems = [];
        activeIndex = -1;
        container.innerHTML = '';

        if (!query) {

            const recent = _getRecentPages()
                .map(r => menuItems.find(m => m.id === r.id))
                .filter(Boolean);

            if (recent.length > 0) {
                _appendSection(container, 'Terakhir Dikunjungi', recent);
            }

            const recentIds = new Set(recent.map(r => r.id));
            const rest = menuItems.filter(m => !recentIds.has(m.id));
            _appendSection(container, 'Semua Menu', rest);

        } else {
            const matched = menuItems.filter(m => _fuzzyMatch(m, query));
            if (matched.length === 0) {
                container.innerHTML = `
                <div class="cp-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                    </svg>
                    Tidak ada hasil untuk "<strong>${_esc(query)}</strong>"
                </div>`;
                return;
            }
            _appendSection(container, `${matched.length} Hasil Ditemukan`, matched);
        }
    }

    function _appendSection(container, label, items) {
        if (items.length === 0) return;

        const labelEl = document.createElement('div');
        labelEl.className = 'cp-section-label';
        labelEl.textContent = label;
        container.appendChild(labelEl);

        items.forEach(item => {
            const flatIdx = flatItems.length;
            flatItems.push(item);

            const el = document.createElement('a');
            el.className = 'cp-result-item';
            el.href = item.url;
            el.dataset.flatIdx = flatIdx;
            el.innerHTML = `
            <div class="cp-result-icon">${ICONS[item.icon] || ICONS.stock}</div>
            <div class="flex-1 min-w-0">
                <div class="cp-item-name">${_esc(item.label)}</div>
                <div class="cp-item-desc">${_esc(item.desc)}</div>
            </div>
            <svg class="cp-item-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        `;

            el.addEventListener('mouseenter', () => _highlightEl(el, flatIdx));
            el.addEventListener('mouseleave', () => {

                if (activeIndex !== flatIdx) el.classList.remove('active');
            });

            el.addEventListener('click', (e) => {
                closePalette();

            });

            container.appendChild(el);
        });
    }

    function _moveActive(delta) {
        if (flatItems.length === 0) return;
        const next = Math.max(0, Math.min(flatItems.length - 1, activeIndex + delta));
        _setActive(next);
    }

    function _setActive(idx) {

        document.querySelectorAll('.cp-result-item').forEach(el => el.classList.remove('active'));

        activeIndex = idx;
        const target = document.querySelector(`.cp-result-item[data-flat-idx="${idx}"]`);
        if (!target) return;

        target.classList.add('active');

        const results = document.getElementById('cp-results');
        if (results) {
            const containerTop = results.scrollTop;
            const containerBottom = containerTop + results.clientHeight;
            const elTop = target.offsetTop;
            const elBottom = elTop + target.offsetHeight;

            if (elTop < containerTop) {
                results.scrollTop = elTop - 8;
            } else if (elBottom > containerBottom) {
                results.scrollTop = elBottom - results.clientHeight + 8;
            }
        }
    }

    function _highlightEl(el, flatIdx) {
        document.querySelectorAll('.cp-result-item').forEach(e => e.classList.remove('active'));
        el.classList.add('active');

        activeIndex = flatIdx;
    }

    function _esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.addEventListener('keydown', (e) => {
        const isMac = navigator.platform.toUpperCase().includes('MAC');
        if ((isMac ? e.metaKey : e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            paletteOpen ? closePalette() : openPalette();
        }
        if (e.key === 'Escape' && paletteOpen) {
            closePalette();
        }
    });

    window.openCommandPalette = openPalette;
    window.closeCommandPalette = closePalette;

})();
