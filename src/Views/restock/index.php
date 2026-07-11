<div class="flex-between mb-6 justify-end">
  <?php if (hasPermission('restock.create')): ?>
  <button class="btn btn-primary" onclick="openModal('modal-restock')">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Ajukan Permintaan Restock
  </button>
  <?php endif; ?>
</div>

<div class="restock-tab-container">
  <button id="tab-pending" onclick="switchRestockTab('pending')"
    class="btn btn-sm border-radius-8">Menunggu</button>
  <button id="tab-approved" onclick="switchRestockTab('approved')"
    class="btn btn-sm border-radius-8">Disetujui</button>
  <button id="tab-rejected" onclick="switchRestockTab('rejected')"
    class="btn btn-sm border-radius-8">Ditolak</button>
  <button id="tab-all" onclick="switchRestockTab('all')"
    class="btn btn-sm border-radius-8">Semua</button>
</div>

<div id="low-stock-banner" class="mb-6 d-none">
  <div class="alert alert-warning mb-12">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
      <strong>Barang berikut perlu perhatian!</strong> Stok di bawah batas minimum.
      <div id="low-items-list" class="low-items-list"></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Barang</th><th>Kategori</th><th>Stok Saat Ini</th>
          <th>Jumlah Diminta</th><th>Diajukan Oleh</th><th>Tanggal</th>
          <th>Status</th>
          <?php if (hasPermission('restock.approve')): ?><th>Aksi / Penyetuju</th><?php endif; ?>

        </tr>
      </thead>
      <tbody id="restock-tbody">
        <tr><td colspan="8" class="text-center p-32 text-muted">Memuat data...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="modal-restock">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">Ajukan Permintaan Restock</div>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Pilih Barang *</label>
        <select id="restock-item" class="form-control" required>
          <option value="">-- Pilih Barang --</option>
          <?php foreach ($items as $item): ?>
          <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?> [<?= htmlspecialchars($item['sku']) ?>] (<?= htmlspecialchars($item['unit']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Jumlah yang Diminta *</label>
        <input type="number" id="restock-qty" class="form-control" min="1" placeholder="Jumlah yang dibutuhkan" required/>
      </div>
      <div class="form-group">
        <label class="form-label">Catatan</label>
        <textarea id="restock-notes" class="form-control" rows="3" placeholder="Alasan atau catatan tambahan..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-restock')">Batal</button>
      <button class="btn btn-primary" onclick="submitRestock()">Kirim Permintaan</button>
    </div>
  </div>
</div>

<script>
const isAdmin = <?= json_encode(hasPermission('restock.approve')) ?>;

let activeTab = 'pending';

async function loadLowStock() {
  const data = await fetch(BASE_URL + '/api/dashboard.php?type=low_stock').then(r=>r.json());
  if (data.length) {
    document.getElementById('low-stock-banner').classList.remove('d-none');
    const list = document.getElementById('low-items-list');
    data.forEach(item => {
      list.innerHTML += `<span class="badge ${item.stock_status === 'empty' ? 'badge-danger' : 'badge-warning'}">
        ${item.item_name} (${item.total_stock} ${item.unit})</span>`;
    });
  }
}

async function loadRestockList(status = 'pending') {
  const tbody = document.getElementById('restock-tbody');
  tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted p-24">Memuat...</td></tr>';
  const data = await fetch(BASE_URL + '/api/restock.php?status=' + status).then(r=>r.json());
  if (!data.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted p-32">Tidak ada data</td></tr>';
    return;
  }
  tbody.innerHTML = '';
  data.forEach(row => {
    const statusBadge = row.status === 'pending'
      ? '<span class="badge badge-warning">Menunggu</span>'
      : row.status === 'approved'
        ? '<span class="badge badge-success">Disetujui</span>'
        : '<span class="badge badge-danger">Ditolak</span>';

    const date = new Date(row.created_at).toLocaleString('id-ID', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
    const aksiCol = isAdmin && row.status === 'pending'
      ? `<td><div class="d-flex gap-6">
          <button class="btn btn-sm btn-success-soft" onclick="approveRestock(${row.id},'approve')">Setujui</button>
          <button class="btn btn-sm btn-danger-soft" onclick="approveRestock(${row.id},'reject')">Tolak</button>
         </div></td>`
      : isAdmin ? '<td class="text-muted fs-12">' + (row.approver_name ?? '—') + '</td>' : '';

    tbody.innerHTML += `<tr>
      <td class="font-600-main">${row.item_name}</td>
      <td><span class="badge badge-primary">${row.category_name}</span></td>
      <td>${row.current_stock} ${row.unit}</td>
      <td class="font-700">${row.requested_qty} ${row.unit}</td>
      <td>${row.requester_name}</td>
      <td class="fs-12 text-muted">${date}</td>
      <td>${statusBadge}</td>
      ${aksiCol}
    </tr>`;
  });
}

function switchRestockTab(tab) {
  activeTab = tab;
  ['pending','approved','rejected','all'].forEach(t => {
    const btn = document.getElementById('tab-' + t);
    btn.classList.toggle('btn-primary', t === tab);
  });
  loadRestockList(tab);
}

async function submitRestock() {
  const itemId = document.getElementById('restock-item').value;
  const qty    = parseInt(document.getElementById('restock-qty').value);
  const notes  = document.getElementById('restock-notes').value;
  if (!itemId || !qty) { showToast('Lengkapi data terlebih dahulu', 'error'); return; }

  const res = await fetch(BASE_URL + '/api/restock.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action:'create', item_id: parseInt(itemId), requested_qty: qty, notes })
  }).then(r=>r.json());

  if (res.success) {
    closeModal('modal-restock');
    showToast('Permintaan restock berhasil dikirim!', 'success');
    loadRestockList(activeTab);
  } else {
    showToast(res.error || 'Gagal mengirim permintaan', 'error');
  }
}

async function approveRestock(id, action) {
  const label = action === 'approve' ? 'menyetujui' : 'menolak';
  const confirmed = await showConfirm(
    `Yakin ingin ${label} permintaan ini?`,
    'Konfirmasi Permintaan Restock',
    action === 'approve' ? 'Ya, Setujui' : 'Ya, Tolak',
    action === 'reject'
  );
  if (!confirmed) return;
  const res = await fetch(BASE_URL + '/api/restock.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action, id })
  }).then(r=>r.json());
  if (res.success) {
    showToast(`Permintaan berhasil ${action === 'approve' ? 'disetujui' : 'ditolak'}`, action === 'approve' ? 'success' : 'warning');
    loadRestockList(activeTab);
  }
}

switchRestockTab('pending');
loadLowStock();
</script>
