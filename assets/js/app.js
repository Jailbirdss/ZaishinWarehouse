

(function() {
    const descriptor = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
    if (descriptor && descriptor.set) {
        const originalSet = descriptor.set;
        Object.defineProperty(HTMLSelectElement.prototype, 'value', {
            get() {
                return descriptor.get.call(this);
            },
            set(val) {
                originalSet.call(this, val);
                this.dispatchEvent(new CustomEvent('valuechange', { bubbles: true }));
            },
            configurable: true
        });
    }
})();

function convertNativeSelects() {
    document.querySelectorAll('select.form-control, select.form-select').forEach(select => {
        if (select.dataset.converted || select.style.display === 'none') return;

        select.style.display = 'none';
        select.dataset.converted = 'true';

        const wrapper = document.createElement('div');
        wrapper.className = 'custom-select-wrapper';
        
        let hasWidthClass = false;
        for (let i = 0; i < select.classList.length; i++) {
            const cls = select.classList[i];
            if (cls.indexOf('w-') === 0 || cls === 'so-filter-select' || cls === 'select-bg-white') {
                wrapper.classList.add(cls);
                if (cls.indexOf('w-') === 0 || cls === 'so-filter-select') {
                    hasWidthClass = true;
                }
            }
        }

        wrapper.style.width = select.style.width || (hasWidthClass ? '' : '100%');
        if (select.style.flex) wrapper.style.flex = select.style.flex;

        const trigger = document.createElement('div');
        trigger.className = 'custom-select-trigger';
        trigger.innerHTML = `
            <span class="cs-text"></span>
            <svg class="custom-select-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        `;

        const dropdown = document.createElement('div');
        dropdown.className = 'custom-select-dropdown';

        wrapper.appendChild(trigger);
        wrapper.appendChild(dropdown);

        rebuildCustomSelect(select, trigger, dropdown);

        ['change', 'valuechange'].forEach(ev => {
            select.addEventListener(ev, () => {
                syncCustomSelectUI(select, trigger, dropdown);
            });
        });

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('open');
            document.querySelectorAll('.custom-select-dropdown.open').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('open');
                    d.previousElementSibling?.classList.remove('open');
                }
            });
            if (isOpen) {
                dropdown.classList.remove('open');
                trigger.classList.remove('open');
            } else {
                dropdown.classList.add('open');
                trigger.classList.add('open');
            }
        });

        const observer = new MutationObserver(() => {
            rebuildCustomSelect(select, trigger, dropdown);
        });
        observer.observe(select, { childList: true });

        select.parentNode.insertBefore(wrapper, select.nextSibling);
    });
}

function rebuildCustomSelect(select, trigger, dropdown) {
    dropdown.innerHTML = '';
    const selectedOpt = select.options[select.selectedIndex] || select.options[0];
    const selectedText = selectedOpt ? selectedOpt.textContent : '';
    const selectedVal = selectedOpt ? selectedOpt.value : '';

    trigger.querySelector('.cs-text').textContent = selectedText;

    Array.from(select.options).forEach(opt => {
        const isSelected = opt.value === selectedVal;
        const optEl = document.createElement('div');
        optEl.className = `custom-select-option${isSelected ? ' selected' : ''}`;
        optEl.dataset.value = opt.value;
        optEl.textContent = opt.textContent;

        optEl.addEventListener('click', (e) => {
            e.stopPropagation();
            if (select.value !== opt.value) {
                select.value = opt.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
            dropdown.classList.remove('open');
            trigger.classList.remove('open');
        });
        dropdown.appendChild(optEl);
    });
}

function syncCustomSelectUI(select, trigger, dropdown) {
    const selectedOpt = select.options[select.selectedIndex];
    const selectedText = selectedOpt ? selectedOpt.textContent : '';
    const selectedVal = selectedOpt ? selectedOpt.value : '';

    trigger.querySelector('.cs-text').textContent = selectedText;

    dropdown.querySelectorAll('.custom-select-option').forEach(optEl => {
        if (optEl.dataset.value === selectedVal) {
            optEl.classList.add('selected');
        } else {
            optEl.classList.remove('selected');
        }
    });
}

document.addEventListener('click', () => {
    document.querySelectorAll('.custom-select-dropdown.open').forEach(d => {
        d.classList.remove('open');
        d.previousElementSibling?.classList.remove('open');
    });
});

function countUp(el, target, duration = 1200) {
    const start     = performance.now();
    const isDecimal = target % 1 !== 0;
    const from      = 0;

    function update(now) {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);

        const eased = 1 - Math.pow(1 - progress, 3);
        const value = from + (target - from) * eased;
        el.textContent = isDecimal
            ? value.toFixed(1)
            : Math.round(value).toLocaleString('id-ID');
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

function initCountUps() {
    document.querySelectorAll('[data-countup]').forEach(el => {
        const target = parseFloat(el.dataset.countup);
        if (!isNaN(target)) countUp(el, target);
    });
}

function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('open');
    document.body.style.overflow = 'hidden';

    if (id === 'notif-modal') {
        loadNotifications();
    }
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.remove('open');
    document.body.style.overflow = '';
    if (id === 'modal-slot-detail') {
        document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
    }
}
function closePermissionsModal() {
    closeModal('modal-permissions');
    document.querySelectorAll('.role-item-card').forEach(card => card.classList.remove('active'));
    document.querySelectorAll('.btn-role-action').forEach(btn => {
        btn.className = 'btn-role-action edit-trigger';
        btn.innerHTML = 'Kelola Izin';
    });
}

function loadNotifications() {
    const listEl = document.getElementById('notif-list');
    const loadingEl = document.getElementById('notif-loading');
    if (!listEl || !loadingEl) return;

    loadingEl.style.display = 'block';
    listEl.innerHTML = '';

    fetch(BASE_URL + '/api/notifications.php?type=list')
        .then(r => r.json())
        .then(items => {
            loadingEl.style.display = 'none';
            if (!items || items.length === 0) {
                listEl.innerHTML = `
                    <div class="p-5 text-center text-muted fs-base">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="notif-empty-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tidak ada notifikasi baru saat ini.
                    </div>
                `;
                return;
            }

            items.forEach(item => {
                const isUnread = item.is_read == 0;
                let typeClass = 'system';
                let typeLabel = 'Sistem';

                if (item.type === 'low_stock' || item.type === 'restock_rejected') {
                    typeClass = 'danger';
                    typeLabel = item.type === 'low_stock' ? 'Alarm Stok' : 'Restock Ditolak';
                } else if (item.type === 'restock_submitted') {
                    typeClass = 'warning';
                    typeLabel = 'Permintaan Restock';
                } else if (item.type === 'restock_approved') {
                    typeClass = 'success';
                    typeLabel = 'Restock Disetujui';
                } else if (item.type.startsWith('opname')) {
                    typeClass = 'info';
                    typeLabel = 'Stock Opname';
                }

                const timeFormatted = new Date(item.created_at).toLocaleString('id-ID', {day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'});

                const readBadge = isUnread
                    ? `<span class="notif-badge-new ${typeClass}">
                         <span class="notif-badge-dot"></span> Baru
                       </span>`
                    : `<span class="notif-badge-read">
                         <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Dibaca
                       </span>`;

                const typePill = `<span class="notif-type-pill ${typeClass}">${typeLabel}</span>`;

                listEl.innerHTML += `
                    <div class="notif-card ${isUnread ? 'unread ' + typeClass : ''}">
                        <div class="notif-card-header">
                            ${typePill}
                            ${readBadge}
                        </div>
                        <div class="fs-base ${isUnread ? 'fw-700 text-main' : 'fw-600 text-secondary'} letter-spacing-sm">${item.title}</div>
                        <div class="fs-md text-secondary">${item.message}</div>
                        <div class="fs-xs text-muted text-right mt-1">${timeFormatted}</div>
                    </div>
                `;
            });

            listEl.innerHTML += `
                <div class="mt-2 text-center">
                    <a href="index.php?page=notifications" onclick="closeModal('notif-modal')" class="btn btn-outline btn-sm w-full justify-center fw-700">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            `;
        })
        .catch(err => {
            console.error(err);
            loadingEl.style.display = 'none';
            listEl.innerHTML = '<div style="padding:20px; text-align:center; color:var(--danger); font-size:13px;">Gagal memuat data notifikasi.</div>';
        });
}

document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        if (e.target.id === 'custom-confirm-modal') {
            closeConfirmModal(false);
            return;
        }
        e.target.classList.remove('open');
        document.body.style.overflow = '';
        document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const confirmM = document.getElementById('custom-confirm-modal');
        if (confirmM && confirmM.classList.contains('open')) {
            closeConfirmModal(false);
            return;
        }
        document.querySelectorAll('.modal-overlay.open').forEach(m => {
            m.classList.remove('open');
            document.body.style.overflow = '';
        });
        document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
    }
});

const TOAST_QUEUE    = [];
const TOAST_ACTIVE   = [];
const TOAST_MAX      = 3;

const TOAST_CONFIG = {
    success: {
        bg: '#f0fdf4', border: '#bbf7d0', iconBg: '#dcfce7', iconColor: '#16a34a', progressColor: '#16a34a',
        icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`,
        defaultTitle: 'Berhasil'
    },
    error: {
        bg: '#fff5f5', border: '#fecaca', iconBg: '#fee2e2', iconColor: '#dc2626', progressColor: '#dc2626',
        icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`,
        defaultTitle: 'Error'
    },
    warning: {
        bg: '#fffbeb', border: '#fde68a', iconBg: '#fef3c7', iconColor: '#d97706', progressColor: '#d97706',
        icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>`,
        defaultTitle: 'Perhatian'
    },
    info: {
        bg: '#eff6ff', border: '#bfdbfe', iconBg: '#dbeafe', iconColor: '#1e40af', progressColor: '#1e40af',
        icon: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
        defaultTitle: 'Informasi'
    }
};

function showToast(message, type = 'success', duration = 3800, options = {}) {
    const cfg = TOAST_CONFIG[type] || TOAST_CONFIG.info;
    const id  = 'toast-' + Date.now() + Math.random().toString(36).slice(2,6);

    const data = {
        id, message, type, duration,
        title:  options.title  || cfg.defaultTitle,
        action: options.action || null,
        icon:   options.icon   || cfg.icon,
        cfg
    };

    if (TOAST_ACTIVE.length >= TOAST_MAX) {
        TOAST_QUEUE.push(data);
        return id;
    }
    _renderToast(data);
    return id;
}

function _renderToast(data) {
    const container = _getToastContainer();
    const { id, message, type, duration, title, action, icon, cfg } = data;

    const el = document.createElement('div');
    el.className = 'toast-item';
    el.id = id;
    el.style.cssText = `background:${cfg.bg};border-color:${cfg.border}`;

    el.innerHTML = `
        <div class="toast-icon-wrap" style="background:${cfg.iconBg};color:${cfg.iconColor}">
            ${icon}
        </div>
        <div class="toast-body">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
            ${action ? `<button class="toast-action-btn" style="background:${cfg.iconBg};color:${cfg.iconColor};" id="${id}-action">${action.label}</button>` : ''}
        </div>
        <button class="toast-close-btn" onclick="_dismissToast('${id}')" title="Tutup">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:12px;height:12px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="toast-progress" id="${id}-progress" style="background:${cfg.progressColor};width:100%;transition:width ${duration}ms linear"></div>
    `;

    container.appendChild(el);
    TOAST_ACTIVE.push(id);

    if (action) {
        setTimeout(() => {
            const btn = document.getElementById(`${id}-action`);
            if (btn) btn.addEventListener('click', () => { action.callback(); _dismissToast(id); });
        }, 50);
    }

    setTimeout(() => {
        const prog = document.getElementById(`${id}-progress`);
        if (prog) prog.style.width = '0%';
    }, 60);

    const timer = setTimeout(() => _dismissToast(id), duration);
    el.dataset.timer = timer;
}

function _dismissToast(id) {
    const el = document.getElementById(id);
    if (!el) return;
    clearTimeout(parseInt(el.dataset.timer));
    el.classList.add('removing');
    setTimeout(() => {
        el.remove();
        const idx = TOAST_ACTIVE.indexOf(id);
        if (idx > -1) TOAST_ACTIVE.splice(idx, 1);

        if (TOAST_QUEUE.length > 0) {
            _renderToast(TOAST_QUEUE.shift());
        }
    }, 300);
}

function _getToastContainer() {
    let c = document.getElementById('toast-container');
    if (!c) {
        c = document.createElement('div');
        c.id = 'toast-container';
        document.body.appendChild(c);
    }
    return c;
}

function createToastContainer() { return _getToastContainer(); }

function initProgressBars() {
    document.querySelectorAll('.progress-fill[data-width]').forEach(bar => {
        const target = bar.dataset.width;
        bar.style.width = '0%';
        setTimeout(() => { bar.style.width = target + '%'; }, 100);
    });
}

function initGridTooltip() {
    const tooltip = document.getElementById('grid-tooltip');
    if (!tooltip) return;
    document.querySelectorAll('.wh-slot[data-info]').forEach(slot => {
        slot.addEventListener('mouseenter', (e) => {
            tooltip.innerHTML = slot.dataset.info;
            tooltip.classList.remove('d-none');
        });
        slot.addEventListener('mousemove', (e) => {
            tooltip.style.left = (e.clientX + 14) + 'px';
            tooltip.style.top  = (e.clientY - 10) + 'px';
        });
        slot.addEventListener('mouseleave', () => {
            tooltip.classList.add('d-none');
        });
    });
}

function setActiveNav() {
    const params = new URLSearchParams(window.location.search);
    const page   = params.get('page') || 'dashboard';
    document.querySelectorAll('.nav-item[data-page]').forEach(item => {
        item.classList.toggle('active', item.dataset.page === page);
    });
}

let html5QrCode = null;

function isPermissionError(err) {
    if (!err) return false;
    const name = err.name || '';
    const message = err.message || '';
    return name === 'NotAllowedError' || 
           name === 'PermissionDeniedError' || 
           message.toLowerCase().includes('permission') || 
           message.toLowerCase().includes('allowed') ||
           message.toLowerCase().includes('denied');
}

function startQRScanner(elementId, onResult, onError) {
    if (html5QrCode) {
        if (html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                html5QrCode = null;
                initQRScannerDevice(elementId, onResult, onError);
            }).catch(() => {
                html5QrCode = null;
                initQRScannerDevice(elementId, onResult, onError);
            });
        } else {
            html5QrCode = null;
            initQRScannerDevice(elementId, onResult, onError);
        }
    } else {
        initQRScannerDevice(elementId, onResult, onError);
    }
}

function initQRScannerDevice(elementId, onResult, onError) {
    html5QrCode = new Html5Qrcode(elementId);

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length > 0) {
            let cameraId = devices[0].id;
            for (const device of devices) {
                if (device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('environment') ||
                    device.label.toLowerCase().includes('rear')) {
                    cameraId = device.id;
                    break;
                }
            }

            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: (width, height) => {
                        const minDim = Math.min(width, height);
                        const size = Math.floor(minDim * 0.7);
                        return { width: size, height: size };
                    }
                },
                (text) => {
                    stopQRScanner();
                    onResult(text);
                },
                () => {}
            ).catch(err => {
                console.warn("Failed with preferred cameraId", err);
                if (isPermissionError(err)) {
                    showToast("Akses kamera ditolak. Silakan berikan izin kamera atau unggah gambar QR.", "error");
                    stopQRScanner();
                    if (onError) onError(err);
                } else {
                    try {
                        html5QrCode = new Html5Qrcode(elementId);
                    } catch (e) {
                        console.warn("Failed to recreate Html5Qrcode", e);
                    }
                    startWithFacingMode(elementId, onResult, onError);
                }
            });
        } else {
            startWithFacingMode(elementId, onResult, onError);
        }
    }).catch(err => {
        console.warn("getCameras failed", err);
        if (isPermissionError(err)) {
            showToast("Akses kamera ditolak. Silakan berikan izin kamera atau unggah gambar QR.", "error");
            stopQRScanner();
            if (onError) onError(err);
        } else {
            try {
                html5QrCode = new Html5Qrcode(elementId);
            } catch (e) {
                console.warn("Failed to recreate Html5Qrcode", e);
            }
            startWithFacingMode(elementId, onResult, onError);
        }
    });
}

function startWithFacingMode(elementId, onResult, onError) {
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode(elementId);
    }
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 220, height: 220 } },
        (text) => {
            stopQRScanner();
            onResult(text);
        },
        () => {}
    ).catch(err => {
        console.warn("Failed to start with facingMode environment", err);
        if (isPermissionError(err)) {
            showToast("Akses kamera ditolak. Silakan berikan izin kamera atau unggah gambar QR.", "error");
            stopQRScanner();
            if (onError) onError(err);
        } else {
            try {
                html5QrCode = new Html5Qrcode(elementId);
            } catch (e) {
                console.warn("Failed to recreate Html5Qrcode", e);
            }
            html5QrCode.start(
                { facingMode: 'user' },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (text) => {
                    stopQRScanner();
                    onResult(text);
                },
                () => {}
            ).catch(fallbackErr => {
                console.warn("Failed to start with facingMode user", fallbackErr);
                showToast("Gagal mengakses kamera. Silakan unggah gambar QR.", "error");
                stopQRScanner();
                if (onError) onError(fallbackErr);
            });
        }
    });
}

function stopQRScanner() {
    if (html5QrCode) {
        if (html5QrCode.isScanning) {
            try {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                }).catch(err => {
                    console.warn("Failed to stop scanner", err);
                    html5QrCode = null;
                });
            } catch (e) {
                console.warn("Synchronous error stopping scanner", e);
                html5QrCode = null;
            }
        } else {
            html5QrCode = null;
        }
    }
}

function initMobileSidebar() {
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        document.body.classList.toggle('sidebar-open');
    });

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            document.body.classList.remove('sidebar-open');
        });
    }

    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
        item.addEventListener('click', () => {
            document.body.classList.remove('sidebar-open');
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    convertNativeSelects();
    initCountUps();
    initProgressBars();
    initGridTooltip();
    setActiveNav();
    initMobileSidebar();

    // Zone Page event handlers (statis)
    const addZoneBtn = document.getElementById('btn-add-zone');
    if (addZoneBtn) {
        addZoneBtn.addEventListener('click', () => {
            openModal('modal-zone');
        });
    }
    const cancelZoneBtn = document.getElementById('btn-cancel-zone');
    if (cancelZoneBtn) {
        cancelZoneBtn.addEventListener('click', () => {
            closeModal('modal-zone');
        });
    }
    const deleteSlotBtn = document.getElementById('btn-delete-slot-trigger');
    if (deleteSlotBtn) {
        deleteSlotBtn.addEventListener('click', async () => {
            const slotId = deleteSlotBtn.dataset.id;
            const loc = deleteSlotBtn.dataset.loc;
            const confirmed = await showConfirm(
                `Apakah Anda yakin ingin menghapus "${loc}"? Jumlah kapasitas total rak akan berkurang 1 slot. Tindakan ini tidak dapat dibatalkan.`,
                'Hapus Slot Rak',
                'Ya, Hapus',
                true
            );
            if (confirmed) {
                const zoneId = window.activeZoneId || '';
                window.location.href = `index.php?page=zones&action=delete_slot&id=${slotId}&zone_id=${zoneId}`;
            }
        });
    }
    const cancelAdjustBtn = document.getElementById('btn-cancel-adjust');
    if (cancelAdjustBtn) {
        cancelAdjustBtn.addEventListener('click', () => {
            closeModal('modal-adjust-slots');
        });
    }

    // Tabs Navigation scroll & toggle handler
    const tabsContainer = document.getElementById('tabs-nav-container');
    const scrollLeftBtn = document.getElementById('btn-scroll-left');
    const scrollRightBtn = document.getElementById('btn-scroll-right');
    
    if (tabsContainer) {
        if (scrollLeftBtn) {
            scrollLeftBtn.addEventListener('click', () => {
                tabsContainer.scrollBy({ left: -150, behavior: 'smooth' });
            });
        }
        if (scrollRightBtn) {
            scrollRightBtn.addEventListener('click', () => {
                tabsContainer.scrollBy({ left: 150, behavior: 'smooth' });
            });
        }
        
        // Tab switching
        const tabButtons = tabsContainer.querySelectorAll('.tab-nav-btn');
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                // Deactivate all buttons
                tabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Hide all panes
                const target = btn.dataset.target;
                const panes = document.querySelectorAll('.tab-content-pane');
                panes.forEach(pane => {
                    pane.classList.add('d-none');
                    pane.classList.remove('active');
                });
                
                // Show target pane
                const targetPane = document.getElementById(target);
                if (targetPane) {
                    targetPane.classList.remove('d-none');
                    targetPane.classList.add('active');
                }
            });
        });
    }

    // Client-side Pagination for Roles List
    const rolesContainer = document.getElementById('role-cards-container');
    if (rolesContainer) {
        const roleCards = Array.from(rolesContainer.querySelectorAll('.role-item-card'));
        const prevBtn = document.getElementById('btn-prev-role-page');
        const nextBtn = document.getElementById('btn-next-role-page');
        const pageIndicator = document.getElementById('role-page-indicator');
        
        const itemsPerPage = 5;
        let currentPage = 1;
        const totalPages = Math.ceil(roleCards.length / itemsPerPage);
        
        function showPage(page) {
            currentPage = page;
            const startIdx = (currentPage - 1) * itemsPerPage;
            const endIdx = startIdx + itemsPerPage;
            
            roleCards.forEach((card, index) => {
                if (index >= startIdx && index < endIdx) {
                    card.style.setProperty('display', 'flex', 'important');
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });
            
            // Hide prevBtn if first page, show otherwise
            if (prevBtn) {
                if (currentPage === 1) {
                    prevBtn.style.setProperty('display', 'none', 'important');
                } else {
                    prevBtn.style.setProperty('display', 'inline-block', 'important');
                }
            }
            
            // Hide nextBtn if last page, show otherwise
            if (nextBtn) {
                if (currentPage === totalPages || totalPages === 0) {
                    nextBtn.style.setProperty('display', 'none', 'important');
                } else {
                    nextBtn.style.setProperty('display', 'inline-block', 'important');
                }
            }
            
            // Update indicator
            if (pageIndicator) {
                pageIndicator.textContent = `Halaman ${currentPage} dari ${totalPages || 1}`;
            }

            // Hide entire pagination block if only 1 page or empty
            const pagBlock = document.getElementById('role-pagination');
            if (pagBlock) {
                if (totalPages <= 1) {
                    pagBlock.style.setProperty('display', 'none', 'important');
                } else {
                    pagBlock.style.setProperty('display', 'flex', 'important');
                }
            }
        }
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) showPage(currentPage - 1);
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) showPage(currentPage + 1);
            });
        }
        
        // Show first page on load
        showPage(1);
    }
});

const toastStyle = document.createElement('style');
toastStyle.textContent = `
@keyframes toastIn  { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
@keyframes toastOut { from{opacity:1;transform:translateX(0)} to{opacity:0;transform:translateX(20px)} }
`;
document.head.appendChild(toastStyle);

let confirmResolve = null;

function showConfirm(message, title = 'Konfirmasi Tindakan', confirmText = 'Ya, Lanjutkan', danger = false) {
  return new Promise((resolve) => {
    confirmResolve = resolve;

    document.getElementById('confirm-modal-title').textContent = title;
    document.getElementById('confirm-modal-message').innerHTML = message;

    const confirmBtn = document.getElementById('btn-confirm-yes');
    confirmBtn.textContent = confirmText;

    if (danger) {
      confirmBtn.style.background = 'var(--danger)';
      confirmBtn.style.borderColor = 'var(--danger)';
    } else {
      confirmBtn.style.background = 'var(--primary)';
      confirmBtn.style.borderColor = 'var(--primary)';
    }

    document.getElementById('custom-confirm-modal').classList.add('open');
  });
}

function closeConfirmModal(result) {
  document.getElementById('custom-confirm-modal').classList.remove('open');
  if (confirmResolve) {
    confirmResolve(result);
    confirmResolve = null;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const confirmBtn = document.getElementById('btn-confirm-yes');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      closeConfirmModal(true);
    });
  }
});

function playSuccessFeedback() {

    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.08, ctx.currentTime);
            osc.start();
            osc.stop(ctx.currentTime + 0.12);
        }
    } catch (e) {
        console.warn("AudioContext failed to start:", e);
    }

    if (navigator.vibrate) {
        navigator.vibrate(80);
    }
}

function playErrorFeedback() {

    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            const ctx = new AudioContext();

            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.type = 'triangle';
            osc1.frequency.setValueAtTime(220, ctx.currentTime);
            gain1.gain.setValueAtTime(0.12, ctx.currentTime);
            osc1.start();
            osc1.stop(ctx.currentTime + 0.15);

            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.type = 'triangle';
            osc2.frequency.setValueAtTime(220, ctx.currentTime + 0.2);
            gain2.gain.setValueAtTime(0.12, ctx.currentTime + 0.2);
            osc2.start(ctx.currentTime + 0.2);
            osc2.stop(ctx.currentTime + 0.35);
        }
    } catch (e) {
        console.warn("AudioContext failed to start:", e);
    }

    if (navigator.vibrate) {
        navigator.vibrate([150, 80, 150]);
    }
}

// Event delegation untuk aksi hapus Seksi/Zona dan Rak (tanpa inline JS)
document.addEventListener('click', async function(e) {
    // 1. Hapus Zona
    const deleteZoneBtn = e.target.closest('.btn-delete-zone-trigger');
    if (deleteZoneBtn) {
        e.preventDefault();
        e.stopPropagation();
        const zoneId = deleteZoneBtn.dataset.id;
        const zoneName = deleteZoneBtn.dataset.name;

        const confirmed = await showConfirm(
            `Apakah Anda yakin ingin menghapus seksi/zona "${zoneName}"? Tindakan ini tidak dapat dibatalkan.`,
            'Hapus Seksi/Zona',
            'Ya, Hapus',
            true
        );
        if (confirmed) {
            window.location.href = `index.php?page=zones&action=delete_zone&id=${zoneId}`;
        }
    }

    // 2. Hapus Rak
    const deleteRackBtn = e.target.closest('.btn-delete-rack');
    if (deleteRackBtn) {
        e.preventDefault();
        e.stopPropagation();
        const rackId = deleteRackBtn.dataset.id;
        const rackCode = deleteRackBtn.dataset.code;

        const confirmed = await showConfirm(
            `Apakah Anda yakin ingin menghapus rak "${rackCode}" beserta seluruh slotnya? Tindakan ini tidak dapat dibatalkan.`,
            'Hapus Rak',
            'Ya, Hapus',
            true
        );
        if (confirmed) {
            const zoneId = window.activeZoneId || '';
            window.location.href = `index.php?page=zones&action=delete_rack&id=${rackId}&zone_id=${zoneId}`;
        }
    }

    // 3. Atur Jumlah Slot Rak
    const adjustSlotsBtn = e.target.closest('.btn-adjust-slots');
    if (adjustSlotsBtn) {
        e.preventDefault();
        e.stopPropagation();
        const rackId = adjustSlotsBtn.dataset.id;
        const rackCode = adjustSlotsBtn.dataset.code;
        const slotsCount = adjustSlotsBtn.dataset.slots;
        
        document.getElementById('adjust-rack-id').value = rackId;
        document.getElementById('adjust-zone-id').value = window.activeZoneId || '';
        document.getElementById('adjust-rack-code').value = rackCode;
        document.getElementById('adjust-rack-slots').value = slotsCount;
        
        openModal('modal-adjust-slots');
    }

    // 4. Hapus Peran (Role)
    const deleteRoleBtn = e.target.closest('.btn-delete-role');
    if (deleteRoleBtn) {
        e.preventDefault();
        e.stopPropagation();
        const roleKey = deleteRoleBtn.dataset.key;
        const roleName = deleteRoleBtn.dataset.name;

        // Compile warnings list of permissions that will be lost
        let warningsList = '';
        if (typeof rolePermissions !== 'undefined' && typeof allPermissionsMap !== 'undefined') {
            const perms = rolePermissions[roleKey] || [];
            if (perms.length > 0) {
                warningsList = `<div style="margin-top: 14px; text-align: left; background: #fff5f5; border: 1px solid #fee2e2; padding: 12px 16px; border-radius: 10px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                    <div style="font-weight: 800; color: #b91c1c; font-size: 12px; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Akses fitur yang akan hilang:</div>
                    <ul style="margin: 0; padding-left: 18px; font-size: 11.5px; color: #991b1b; display: flex; flex-direction: column; gap: 4px; line-height: 1.4;">`;
                perms.forEach(p => {
                    const desc = allPermissionsMap[p] || p;
                    warningsList += `<li><strong>${p}</strong>: ${desc}</li>`;
                });
                warningsList += `</ul></div>`;
            }
        }

        const confirmed = await showConfirm(
            `Apakah Anda yakin ingin menghapus peran kustom <strong>"${roleName}"</strong>? Data staf dengan peran ini akan kehilangan akses masuk.<br/>${warningsList}`,
            'Hapus Peran',
            'Ya, Hapus Peran',
            true
        );
        if (confirmed) {
            window.location.href = `index.php?page=roles&action=delete_role&key=${roleKey}`;
        }
    }

    // 5. Kelola Izin Peran (Popup Modal)
    const editTriggerBtn = e.target.closest('.edit-trigger, .active-edit');
    if (editTriggerBtn) {
        e.preventDefault();
        e.stopPropagation();
        const roleKey = editTriggerBtn.dataset.key;
        const roleName = editTriggerBtn.dataset.name;

        // Reset active edit highlights in roles list
        document.querySelectorAll('.role-item-card').forEach(card => card.classList.remove('active'));
        document.querySelectorAll('.btn-role-action').forEach(btn => {
            btn.className = 'btn-role-action edit-trigger';
            btn.innerHTML = 'Kelola Izin';
        });

        // Set current as active in UI
        const activeCard = document.getElementById('role-card-' + roleKey);
        if (activeCard) activeCard.classList.add('active');
        editTriggerBtn.className = 'btn-role-action active-edit';
        editTriggerBtn.innerHTML = '<span class="pulse-indicator"></span> Mengonfigurasi';

        // Populate modal data
        document.getElementById('modal-role-key').value = roleKey;
        document.getElementById('modal-role-title').textContent = 'Izin Akses Peran: ' + roleName;

        // Reset all checkboxes inside modal
        const checkboxes = document.querySelectorAll('#form-edit-permissions input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);

        // Check the ones mapped to roleKey
        if (typeof rolePermissions !== 'undefined' && rolePermissions[roleKey]) {
            rolePermissions[roleKey].forEach(p => {
                const cb = document.getElementById('p-' + p);
                if (cb) cb.checked = true;
            });
        }

        // Open modal
        openModal('modal-permissions');
    }

    // 6. Klik Hapus Peran Bawaan
    const deleteBuiltinBtn = e.target.closest('.btn-delete-builtin');
    if (deleteBuiltinBtn) {
        e.preventDefault();
        e.stopPropagation();
        const roleName = deleteBuiltinBtn.dataset.name;
        playErrorFeedback();
        showToast(`Peran bawaan sistem ("${roleName}") tidak dapat dihapus demi keamanan data.`, 'error');
    }
});

// Enforce role_key input field guidelines in real-time
document.addEventListener('DOMContentLoaded', () => {
    const roleKeyInput = document.querySelector('input[name="role_key"]');
    if (roleKeyInput) {
        roleKeyInput.addEventListener('input', () => {
            const cursorPosition = roleKeyInput.selectionStart;
            const originalLength = roleKeyInput.value.length;
            
            // Clean value: lowercase and replace spaces with underscores, strip invalid characters
            let cleanVal = roleKeyInput.value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
            roleKeyInput.value = cleanVal;
            
            // Maintain cursor position
            const diff = cleanVal.length - originalLength;
            roleKeyInput.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });
    }
});

