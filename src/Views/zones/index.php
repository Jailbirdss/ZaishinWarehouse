<?php if (!empty($_GET['saved'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Rak baru berhasil ditambahkan beserta slotnya.
</div>
<?php endif; ?>
<?php if (!empty($_GET['saved_desc'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Deskripsi seksi berhasil diperbarui.
</div>
<?php endif; ?>
<?php if (!empty($_GET['saved_zone'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Seksi/Zona baru berhasil ditambahkan.
</div>
<?php endif; ?>
<?php if (!empty($_GET['deleted_zone'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Seksi/Zona berhasil dihapus.
</div>
<?php endif; ?>
<?php if (!empty($_GET['deleted_rack'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Rak berhasil dihapus beserta slotnya.
</div>
<?php endif; ?>
<?php if (!empty($_GET['deleted_slot'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Slot berhasil dihapus dari rak.
</div>
<?php endif; ?>
<?php if (!empty($_GET['adjusted_slots'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Jumlah slot rak berhasil disesuaikan.
</div>
<?php endif; ?>


<?php if (!empty($error)): ?>
<div class="alert alert-danger mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
  <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<div class="flex-between mb-20 align-center flex-wrap gap-12">
  <div>
    <?php if (hasPermission('zones.create')): ?>
    <button class="btn btn-primary d-flex align-center gap-6" id="btn-add-zone">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
      Tambah Zona
    </button>
    <?php endif; ?>
  </div>
  <div class="d-flex align-center gap-12 fs-12-5">
    <span class="d-inline-flex align-center gap-6">
      <span class="legend-indicator-box empty"></span> Kosong
    </span>
    <span class="d-inline-flex align-center gap-6">
      <span class="legend-indicator-box loaded"></span> Terisi
    </span>
    <span class="d-inline-flex align-center gap-6">
      <span class="legend-indicator-box selected"></span> Dipilih
    </span>
  </div>
</div>

<div class="zone-cards-grid" id="zone-cards"></div>

<div class="card mb-6 bg-surface d-none" id="zone-detail-panel">
  <div class="card-header p-header">
    <div>
      <div class="card-title fs-22 fw-800 text-main" id="zone-detail-title">Nama Area</div>
      <div class="fs-12-5 text-muted mt-4" id="zone-detail-sub"></div>
    </div>

    <div class="zone-header-actions d-flex align-center gap-12 ml-auto">
      <?php if (hasPermission('racks.create')): ?>
      <button class="btn btn-primary h-38px d-flex align-center gap-6" onclick="openAddRackModal()">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Rak
      </button>
      <?php endif; ?>

      <label class="view-toggle-switch" title="Toggle tampilan 3D isometrik">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16 text-muted">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
        </svg>
        <span class="fs-12 text-secondary">3D</span>
        <div class="toggle-track" id="view-3d-track" onclick="toggle3DView()">
          <div class="toggle-thumb"></div>
        </div>
      </label>

      <button onclick="closeZoneDetail()" class="modal-close" title="Tutup">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
  </div>
  <div class="card-body p-body">
    <div class="zone-detail-grid-layout">

      <div id="zone-grid-area" class="zone-visualizer-container">

        <div id="zone-grid" class="wh-grid d-flex flex-column align-center w-full"></div>

        <div id="zone-3d-wrapper" class="w-full d-none">
          <div class="d-flex align-center justify-between mb-10">
            <div class="d-flex align-center gap-6 fs-11-5 text-muted">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14 text-primary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618V15.38a1 1 0 01-1.447.894L15 14m0 0H5a2 2 0 01-2-2V8a2 2 0 012-2h10v8z"/></svg>
              Drag untuk memutar &bull; Scroll untuk zoom
            </div>
            <button onclick="reset3DCamera()" class="btn-reset-camera">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
              Reset Kamera
            </button>
          </div>
          <canvas id="rack-3d-canvas" class="rack-3d-canvas-element"></canvas>

          <div class="d-flex gap-16 mt-10 justify-center flex-wrap">
            <span class="d-flex align-center gap-5 fs-11-5 text-muted"><span class="legend-color-square-3d empty"></span>Kosong</span>
            <span class="d-flex align-center gap-5 fs-11-5 text-muted"><span class="legend-color-square-3d low"></span>Stok Rendah</span>
            <span class="d-flex align-center gap-5 fs-11-5 text-muted"><span class="legend-color-square-3d mid"></span>Stok Sedang</span>
            <span class="d-flex align-center gap-5 fs-11-5 text-muted"><span class="legend-color-square-3d high"></span>Stok Tinggi</span>
          </div>
        </div>
      </div>

      <div class="circular-progress-card" id="zone-circular-card">
        <div class="circular-progress-title">Kapasitas Terpakai</div>
        <div class="circular-progress-container">
          <svg class="circular-progress-ring" width="160" height="160">
            <circle class="ring-bg" stroke="rgba(255,255,255,0.12)" stroke-width="12" fill="transparent" r="66" cx="80" cy="80"/>
            <circle class="ring-fill" id="circle-ring-fill" stroke="#10b981" stroke-width="12" fill="transparent" r="66" cx="80" cy="80"
              stroke-dasharray="414.69" stroke-dashoffset="414.69" stroke-linecap="round"/>
          </svg>
          <div class="circular-progress-text">
            <span class="progress-pct text-white" id="circle-pct">0%</span>
            <span class="progress-label" id="circle-status">OPTIMAL</span>
          </div>
        </div>
        <div class="circular-progress-meta">
          <div class="meta-item">
            <div class="meta-label">Slot Terisi</div>
            <div class="meta-val" id="circle-loaded">0 Slot</div>
          </div>
          <div class="meta-item">
            <div class="meta-label">Slot Kosong</div>
            <div class="meta-val" id="circle-free">0 Slot</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-6">
  <div class="card-header p-header pb-14">
    <div class="card-title fs-18 fw-800">Daftar Seksi & Kapasitas Gudang</div>
  </div>
  <div class="card-body p-0">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Nama Area</th>
            <th>Kode</th>
            <th>Deskripsi</th>
            <th>Kapasitas Digunakan</th>
            <th>Keterangan Slot</th>
            <th>Kondisi Kepadatan</th>
          </tr>
        </thead>
        <tbody id="list-sections-tbody">

        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-slot-detail">
  <div class="modal-box max-w-420px">
    <div class="modal-header modal-header-primary" id="msd-header">
      <div class="modal-title text-white" id="msd-title">Info Detail Slot</div>
    </div>
    <div class="modal-body p-24">
      <div class="fs-11 text-muted text-uppercase letter-spacing-05 mb-6">Keterangan Lokasi</div>
      <div class="fw-800 fs-20 text-main mb-14" id="msd-loc"></div>

      <div id="msd-status-badge" class="mb-20"></div>

      <div id="msd-item-detail" class="d-none border-top-solid pt-16">
        <div class="fs-11 text-muted text-uppercase letter-spacing-05 mb-4">Nama Barang</div>
        <div class="fw-700 fs-16 text-main mb-4" id="msd-item-name"></div>
        <div class="font-monospace fs-12 bg-f1f5f9 p-3-8 border-radius-4 d-inline-block mb-16 fw-600" id="msd-item-sku"></div>

        <div class="grid-2-col mt-12">
          <div>
            <div class="fs-11 text-muted">Jumlah Stok</div>
            <div class="fs-20 fw-800 text-primary" id="msd-item-qty"></div>
          </div>
          <div>
            <div class="fs-11 text-muted">Kategori</div>
            <div class="fs-14 fw-700 text-secondary mt-4" id="msd-item-cat"></div>
          </div>
        </div>
      </div>

      <div id="msd-empty-detail" class="text-center py-20-0 text-muted d-none">
        <svg fill="none" viewBox="0 0 24 24" class="icon-48-primary-opacity75">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V11"/>
        </svg>
        <div class="fs-13-5 fw-500">Slot ini kosong dan siap diisi barang masuk (inbound) baru.</div>
      </div>
    </div>
    <div class="modal-footer bg-f8fafc border-top-solid">
      <?php if (hasPermission('racks.delete')): ?>
      <button type="button" class="btn btn-danger d-none" id="btn-delete-slot-trigger">Hapus Slot ini</button>
      <?php endif; ?>
      <button class="btn btn-secondary" onclick="closeSlotDetailModal()">Tutup Detail</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-rack">
  <div class="modal-box max-w-450px">
    <div class="modal-header">
      <div class="modal-title">Tambah Rak Baru</div>
    </div>
    <form method="POST" action="index.php?page=zones" id="form-rack">
      <input type="hidden" name="action" value="add_rack"/>
      <input type="hidden" name="zone_id" id="rack-zone-id"/>

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama / Kode Rak *</label>
          <input name="rack_code" id="rack-code" class="form-control" placeholder="Contoh: ZA-R05" required/>
          <div class="fs-11 text-muted mt-4">Gunakan format yang konsisten (misal: ZA-R01, ZB-R01)</div>
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Baris Posisi (Grid) *</label>
            <input name="row_num" id="rack-row" type="number" class="form-control" min="1" value="1" required/>
          </div>
          <div class="form-group">
            <label class="form-label">Kolom Posisi (Grid) *</label>
            <input name="col_num" id="rack-col" type="number" class="form-control" min="1" value="1" required/>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Jumlah Slot *</label>
          <input name="total_slots" id="rack-slots" type="number" class="form-control" min="1" max="24" value="8" required/>
          <div class="fs-11 text-muted mt-4">Setiap slot otomatis dibuat dengan status 'Kosong'</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-rack')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Rak</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-adjust-slots">
  <div class="modal-box max-w-450px">
    <div class="modal-header">
      <div class="modal-title">Atur Jumlah Slot Rak</div>
    </div>
    <form method="POST" action="index.php?page=zones" id="form-adjust-slots">
      <input type="hidden" name="action" value="adjust_slots"/>
      <input type="hidden" name="rack_id" id="adjust-rack-id"/>
      <input type="hidden" name="zone_id" id="adjust-zone-id"/>

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama / Kode Rak</label>
          <input id="adjust-rack-code" class="form-control form-control-readonly" readonly />
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah Slot Baru *</label>
          <input name="new_total_slots" id="adjust-rack-slots" type="number" class="form-control" min="1" max="24" required/>
          <div class="fs-11 text-muted mt-4">Masukkan total slot baru (1 - 24). Menambahkan slot akan membuat slot baru berstatus 'Kosong'. Pengurangan slot hanya diizinkan untuk slot kosong di ujung kanan rak.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btn-cancel-adjust">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-zone-desc">
  <div class="modal-box max-w-450px">
    <div class="modal-header">
      <div class="modal-title">Edit Deskripsi Seksi / Zona</div>
    </div>
    <form method="POST" action="index.php?page=zones" id="form-zone-desc">
      <input type="hidden" name="action" value="update_zone_description"/>
      <input type="hidden" name="zone_id" id="desc-zone-id"/>

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Seksi</label>
          <input id="desc-zone-name" class="form-control form-control-readonly" readonly />
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi Seksi / Zona *</label>
          <textarea name="description" id="desc-zone-text" class="form-control" rows="4" placeholder="Masukkan deskripsi area..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btn-cancel-desc">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Deskripsi</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="modal-zone">
  <div class="modal-box max-w-450px">
    <div class="modal-header">
      <div class="modal-title">Tambah Seksi / Zona Baru</div>
    </div>
    <form method="POST" action="index.php?page=zones" id="form-zone">
      <input type="hidden" name="action" value="add_zone"/>

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Seksi / Zona *</label>
          <input name="name" id="zone-name" class="form-control" placeholder="Contoh: Zona F - Packaging" required/>
        </div>
        <div class="form-group">
          <label class="form-label">Kode Seksi *</label>
          <input name="code" id="zone-code" class="form-control" placeholder="Contoh: ZF" required/>
          <div class="fs-11 text-muted mt-4">Gunakan 2 huruf kapital unik (misal: ZA, ZB, ZF)</div>
        </div>
        <div class="form-group">
          <label class="form-label">Deskripsi Seksi / Zona</label>
          <textarea name="description" id="zone-desc" class="form-control" rows="3" placeholder="Masukkan deskripsi area..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="btn-cancel-zone">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Seksi</button>
      </div>
    </form>
  </div>
</div>

<script>
const CAN_EDIT_ZONE = <?= hasPermission('zones.create') ? 'true' : 'false' ?>;
const CAN_CREATE_ZONE = <?= hasPermission('zones.create') ? 'true' : 'false' ?>;
const CAN_DELETE_ZONE = <?= hasPermission('zones.delete') ? 'true' : 'false' ?>;
const CAN_DELETE_RACK = <?= hasPermission('racks.delete') ? 'true' : 'false' ?>;
let zonesData = [];
let activeZoneId = null;

async function initPage() {
  await loadZones();
  const cancelBtn = document.getElementById('btn-cancel-desc');
  if (cancelBtn) {
    cancelBtn.addEventListener('click', () => {
      closeModal('modal-zone-desc');
    });
  }
}

async function loadZones() {
  const zones = await fetch(BASE_URL + '/api/warehouse.php').then(r=>r.json());
  zonesData = zones;

  const container = document.getElementById('zone-cards');
  container.innerHTML = '';

  const tbody = document.getElementById('list-sections-tbody');
  tbody.innerHTML = '';

  zones.forEach((z, i) => {
    const pct = parseFloat(z.usage_pct) || 0;

    let densityLabel = 'Optimal';
    let densityBadgeClass = 'badge-success';
    let colorClass = 'optimal';

    if (pct >= 85) {
      densityLabel = 'Kritis';
      densityBadgeClass = 'badge-danger';
      colorClass = 'critical';
    } else if (pct >= 60) {
      densityLabel = 'Cukup Padat';
      densityBadgeClass = 'badge-warning';
      colorClass = 'warning';
    }

    const el = document.createElement('div');
    el.className = 'card zone-selection-card';
    el.id = `zone-card-${z.zone_id}`;
    el.innerHTML = `
      <div class="fw-700 fs-14 text-main mb-4">${z.zone_name}</div>
      <div class="fs-12 text-muted mb-14">Kode: ${z.code}</div>
      <div class="fs-26 fw-800 mb-4 text-usage-${colorClass}">${pct}%</div>
      <div class="progress-track mb-6">
        <div class="progress-fill bg-usage-${colorClass}" data-width="${pct}"></div>
      </div>
      <div class="fs-11 text-muted">${z.loaded_slots} terisi · ${z.free_slots} kosong</div>`;
    el.addEventListener('click', () => selectZone(z));
    container.appendChild(el);
    const row = document.createElement('tr');
    row.classList.add('cursor-pointer');
    row.addEventListener('click', () => scrollToVisual(z.zone_id));

    row.innerHTML = `
      <td class="fw-600 text-main">${z.zone_name}</td>
      <td><span class="font-monospace fs-12 bg-f1f5f9 p-2-6 border-radius-4 fw-600">${z.code}</span></td>
      <td class="fs-13 text-muted max-w-250px">
        <div class="d-flex align-center justify-between gap-8">
          <span class="text-ellipsis flex-1">${z.description || '—'}</span>
          <div class="d-flex gap-4">
            ${CAN_EDIT_ZONE ? `
              <button type="button" class="btn-edit-desc-trigger" data-id="${z.zone_id}" data-name="${z.zone_name}" data-desc="${z.description || ''}" title="Edit Deskripsi">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </button>
            ` : ''}
            ${CAN_DELETE_ZONE ? `
              <button type="button" class="btn-delete-zone-trigger text-danger" data-id="${z.zone_id}" data-name="${z.zone_name}" title="Hapus Seksi/Zona">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            ` : ''}
          </div>
        </div>
      </td>
      <td class="w-280px">
        <div class="d-flex align-center gap-12">
          <span class="fs-12-5 text-muted min-w-55px">Terpakai</span>
          <div class="progress-track flex-1 h-8px">
            <div class="progress-fill bg-usage-${colorClass}" data-width="${pct}"></div>
          </div>
          <span class="fw-700 fs-14 min-w-45px text-right text-usage-${colorClass}">${pct}%</span>
        </div>
      </td>
      <td class="fs-12-5">${z.loaded_slots} terisi / ${z.total_slots} total</td>
      <td>
        <span class="badge ${densityBadgeClass}">${densityLabel}</span>
      </td>
    `;
    tbody.appendChild(row);
  });

  if (CAN_EDIT_ZONE) {
    tbody.querySelectorAll('.btn-edit-desc-trigger').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const zoneId = btn.dataset.id;
        const zoneName = btn.dataset.name;
        const zoneDesc = btn.dataset.desc;

        document.getElementById('desc-zone-id').value = zoneId;
        document.getElementById('desc-zone-name').value = zoneName;
        document.getElementById('desc-zone-text').value = zoneDesc;

        openModal('modal-zone-desc');
      });
    });
  }

  initProgressBars();

  const urlParams = new URLSearchParams(window.location.search);
  const paramZoneId = parseInt(urlParams.get('zone_id'));
  let defaultZone = zones[0];
  if (paramZoneId) {
    const matchedZone = zones.find(z => z.zone_id === paramZoneId);
    if (matchedZone) {
      defaultZone = matchedZone;
    }
  }

  if (defaultZone) {
    selectZone(defaultZone);
  }
}

function openAddRackModal() {
  if (!activeZoneId) return;
  document.getElementById('form-rack').reset();
  document.getElementById('rack-zone-id').value = activeZoneId;
  openModal('modal-rack');
}

function selectZone(z) {
  activeZoneId = z.zone_id;

  zonesData.forEach(item => {
    const card = document.getElementById(`zone-card-${item.zone_id}`);
    if (card) {
      if (item.zone_id === z.zone_id) {
        card.classList.add('selected');
      } else {
        card.classList.remove('selected');
      }
    }
  });

  loadZoneDetail(z.zone_id, z.zone_name, z);
}

function scrollToVisual(zoneId) {
  window.scrollTo({ top: 0, behavior: 'smooth' });
  const zone = zonesData.find(z => z.zone_id == zoneId);
  if (zone) {
    selectZone(zone);
  }
}

async function loadZoneDetail(zoneId, zoneName, summary) {
  const panel = document.getElementById('zone-detail-panel');
  panel.classList.remove('d-none');

  panel.classList.remove('animate-fade-in');
  void panel.offsetWidth;
  panel.classList.add('animate-fade-in');

  document.getElementById('zone-detail-title').textContent = zoneName;
  document.getElementById('zone-detail-sub').textContent =
    `Tata letak fisik slot rak dan keterisian untuk ${zoneName}`;

  const pct = parseFloat(summary.usage_pct) || 0;

  let usageColor = '#4ade80';
  let statusText = 'OPTIMAL';
  if (pct >= 85) {
    usageColor = '#f87171';
    statusText = 'KRITIS';
  } else if (pct >= 60) {
    usageColor = '#fbbf24';
    statusText = 'PADAT';
  }

  document.getElementById('circle-pct').textContent = pct + '%';
  document.getElementById('circle-status').textContent = statusText;
  document.getElementById('circle-status').style.color = usageColor;

  document.getElementById('circle-loaded').textContent = summary.loaded_slots + ' Slot';
  document.getElementById('circle-free').textContent = summary.free_slots + ' Slot';

  const ring = document.getElementById('circle-ring-fill');
  ring.style.stroke = usageColor;

  const circumference = 2 * Math.PI * 66;
  ring.style.strokeDasharray = `${circumference} ${circumference}`;
  const offset = circumference - (pct / 100) * circumference;
  ring.style.strokeDashoffset = offset;

  const racks = await fetch(BASE_URL + '/api/warehouse.php?zone_id=' + zoneId).then(r=>r.json());
  lastRackData = racks;
  const grid  = document.getElementById('zone-grid');
  grid.innerHTML = '';

  if (is3DView) {
    _init3DScene(lastRackData);
  }

  racks.forEach(rack => {

    const rackContainer = document.createElement('div');
    rackContainer.className = 'rack-row-card';

    const rLabel = document.createElement('div');
    rLabel.className = 'rack-label-side';
    rLabel.innerHTML = `
      <span class="fs-10 fw-700 text-primary-light text-uppercase letter-spacing-05 mb-2">RAK</span>
      <span class="fs-14 fw-800 text-primary-dark font-monospace letter-spacing-neg-05">${rack.rack_code}</span>
    `;
    rackContainer.appendChild(rLabel);

    const slotGrid = document.createElement('div');
    slotGrid.className = 'wh-grid-row';

    rack.slots.forEach(slot => {
      const el = document.createElement('div');
      el.className = 'wh-slot ' + slot.status;
      el.textContent = slot.slot_number;

      const info = slot.status === 'loaded'
        ? `<strong>${slot.item_name ?? 'Item'}</strong><br>SKU: ${slot.sku ?? '-'}<br>Stok: ${slot.quantity ?? 0} ${slot.unit ?? ''}<br>Rak: ${rack.rack_code} Slot ${slot.slot_number}`
        : `<span class="text-light">Slot kosong</span><br>Rak: ${rack.rack_code} Slot ${slot.slot_number}`;
      el.dataset.info = info;

      el.addEventListener('click', () => openSlotPopup(slot, rack, el));
      slotGrid.appendChild(el);
    });

    rackContainer.appendChild(slotGrid);

    if (CAN_DELETE_RACK) {
      const adjustBtn = document.createElement('button');
      adjustBtn.type = 'button';
      adjustBtn.className = 'btn-adjust-slots';
      adjustBtn.dataset.id = rack.id;
      adjustBtn.dataset.code = rack.rack_code;
      adjustBtn.dataset.slots = rack.total_slots;
      adjustBtn.title = 'Atur Jumlah Slot';
      adjustBtn.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
      `;
      rackContainer.appendChild(adjustBtn);

      const deleteBtn = document.createElement('button');
      deleteBtn.type = 'button';
      deleteBtn.className = 'btn-delete-rack';
      deleteBtn.dataset.id = rack.id;
      deleteBtn.dataset.code = rack.rack_code;
      deleteBtn.title = 'Hapus Rak';
      deleteBtn.innerHTML = `
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
      `;
      rackContainer.appendChild(deleteBtn);
    }

    grid.appendChild(rackContainer);
  });

  initGridTooltip();
}

function openSlotPopup(slot, rack, el) {

  document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));

  if (el) el.classList.add('selected');

  document.getElementById('msd-loc').textContent = `Rak ${rack.rack_code} · Slot ${slot.slot_number}`;

  const badge = document.getElementById('msd-status-badge');
  const itemDetail = document.getElementById('msd-item-detail');
  const emptyDetail = document.getElementById('msd-empty-detail');

  if (slot.status === 'loaded') {
    badge.innerHTML = `<span class="badge badge-primary-soft-outline">Terisi</span>`;

    document.getElementById('msd-item-name').textContent = slot.item_name ?? '—';
    document.getElementById('msd-item-sku').textContent = slot.sku ?? '—';
    document.getElementById('msd-item-qty').textContent = `${slot.quantity ?? 0} ${slot.unit ?? ''}`;
    document.getElementById('msd-item-cat').textContent = slot.category ?? '—';

    itemDetail.classList.remove('d-none');
    emptyDetail.classList.add('d-none');
  } else {
    badge.innerHTML = `<span class="badge badge-success badge-success-padded">Kosong / Tersedia</span>`;
    itemDetail.classList.add('d-none');
    emptyDetail.classList.remove('d-none');
  }

  // Handle delete slot button state
  const deleteSlotBtn = document.getElementById('btn-delete-slot-trigger');
  if (deleteSlotBtn) {
    if (slot.status === 'free' && CAN_DELETE_RACK) {
      deleteSlotBtn.classList.remove('d-none');
      deleteSlotBtn.dataset.id = slot.id;
      deleteSlotBtn.dataset.loc = `Rak ${rack.rack_code} · Slot ${slot.slot_number}`;
    } else {
      deleteSlotBtn.classList.add('d-none');
    }
  }

  openModal('modal-slot-detail');
}

function closeSlotDetailModal() {
  closeModal('modal-slot-detail');
  document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
}

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-overlay')) {
    document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
  }
});
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.wh-slot').forEach(s => s.classList.remove('selected'));
  }
});

function closeZoneDetail() {
  document.getElementById('zone-detail-panel').classList.add('d-none');
  activeZoneId = null;

  zonesData.forEach(item => {
    const card = document.getElementById(`zone-card-${item.zone_id}`);
    if (card) {
      card.classList.remove('selected');
    }
  });
}

const style = document.createElement('style');
style.textContent = `
.animate-fade-in {
  animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
`;
document.head.appendChild(style);

let is3DView       = false;
let threeScene     = null;
let threeRenderer  = null;
let threeCamera    = null;
let threeAnimId    = null;
let lastRackData   = [];

function toggle3DView() {
  is3DView = !is3DView;
  const track     = document.getElementById('view-3d-track');
  const grid      = document.getElementById('zone-grid');
  const wrapper3d = document.getElementById('zone-3d-wrapper');

  if (is3DView) {
    if (track) track.classList.add('on');
    grid.classList.add('d-none');
    grid.classList.remove('d-flex');
    wrapper3d.classList.remove('d-none');
    _init3DScene(lastRackData);
    showToast('Tampilan 3D interaktif aktif — drag untuk memutar', 'info', 3000);
  } else {
    if (track) track.classList.remove('on');
    grid.classList.remove('d-none');
    grid.classList.add('d-flex');
    wrapper3d.classList.add('d-none');
    _destroy3DScene();
    showToast('Kembali ke tampilan 2D', 'info', 2000);
  }
}

function reset3DCamera() {
  if (!threeCamera || !threeScene || !threeScene._controls) return;
  const ctrl = threeScene._controls;
  ctrl.target.x = 0;
  ctrl.target.y = 1.0;
  ctrl.target.z = 0;
  ctrl.spherical.theta = Math.PI / 4;
  ctrl.spherical.phi   = Math.PI / 3;
  ctrl.spherical.radius = 12;
  ctrl.update();
}

function _destroy3DScene() {
  if (threeAnimId) { cancelAnimationFrame(threeAnimId); threeAnimId = null; }
  if (threeRenderer) { threeRenderer.dispose(); threeRenderer = null; }
  threeScene    = null;
  threeCamera   = null;
}

function _init3DScene(racks) {
  _destroy3DScene();
  if (!racks || racks.length === 0) return;

  const canvas = document.getElementById('rack-3d-canvas');
  const W = canvas.clientWidth  || 600;
  const H = canvas.clientHeight || 440;
  canvas.width  = W * window.devicePixelRatio;
  canvas.height = H * window.devicePixelRatio;

  function _doInit() {
    const THREE = window.THREE;

    const scene    = new THREE.Scene();
    threeScene     = scene;
    scene.background = new THREE.Color(0x0f172a);
    scene.fog        = new THREE.Fog(0x0f172a, 20, 60);

    const cam = new THREE.PerspectiveCamera(45, W / H, 0.1, 100);
    cam.position.set(7, 6, 9);
    cam.lookAt(0, 0.8, -1.0);
    threeCamera = cam;

    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: false });
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(W, H);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type    = THREE.PCFSoftShadowMap;
    threeRenderer = renderer;

    scene.add(new THREE.AmbientLight(0xffffff, 0.6));
    const dir = new THREE.DirectionalLight(0xffffff, 1.2);
    dir.position.set(8, 12, 8);
    dir.castShadow = true;
    dir.shadow.mapSize.setScalar(1024);
    scene.add(dir);
    const fill = new THREE.DirectionalLight(0x94c8ff, 0.4);
    fill.position.set(-6, 4, -4);
    scene.add(fill);

    const gridHelper = new THREE.GridHelper(24, 24, 0x1e3a5f, 0x1e3a5f);
    gridHelper.position.y = -0.01;
    scene.add(gridHelper);

    const floorGeo = new THREE.PlaneGeometry(24, 24);
    const floorMat = new THREE.MeshLambertMaterial({ color: 0x0d1b2e });
    const floor    = new THREE.Mesh(floorGeo, floorMat);
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    const COLORS = {
      empty:    0x475569,
      low:      0x10b981,
      mid:      0xf59e0b,
      high:     0xef4444,
    };
    const SHELF_COLOR  = 0x334155;
    const POST_COLOR   = 0x1e293b;
    
    const SHELF_LEVELS = 3;
    const LEVEL_GAP    = 0.95;
    const SLOT_H       = 0.52;
    
    const RACK_W       = 0.65;
    const RACK_D       = 0.68;
    const SLOT_W       = 0.52;
    const SLOT_D       = 0.52;
    const POST_R       = 0.035;
    
    const RACK_SPACING = 2.0;
    const SLOT_SPACING = 0.85;

    const rackGroup = new THREE.Group();
    scene.add(rackGroup);

    const numRacks = racks.length;
    const numSlots = 8;

    const totalX   = (numRacks - 1) * RACK_SPACING;
    const totalZ   = (numSlots - 1) * SLOT_SPACING;
    const offsetX  = -totalX / 2;
    const offsetZ  = -totalZ / 2;

    racks.forEach((rack, ri) => {
      const rx = offsetX + ri * RACK_SPACING;

      for (let k = 0; k <= 8; k += 2) {
        const postZ = offsetZ - SLOT_SPACING / 2 + k * SLOT_SPACING;
        
        [[-RACK_W/2, postZ], [RACK_W/2, postZ]].forEach(([px, pz]) => {
          const postGeo = new THREE.CylinderGeometry(POST_R, POST_R, SHELF_LEVELS * LEVEL_GAP + 0.1, 8);
          const postMat = new THREE.MeshLambertMaterial({ color: POST_COLOR });
          const post    = new THREE.Mesh(postGeo, postMat);
          post.position.set(rx + px, (SHELF_LEVELS * LEVEL_GAP) / 2, pz);
          post.castShadow = true;
          rackGroup.add(post);
        });
      }

      for (let level = 0; level < SHELF_LEVELS; level++) {
        const shelfY = level * LEVEL_GAP;

        const shelfGeo = new THREE.BoxGeometry(RACK_W, 0.06, totalZ + SLOT_SPACING);
        const shelfMat = new THREE.MeshLambertMaterial({ color: SHELF_COLOR });
        const shelf    = new THREE.Mesh(shelfGeo, shelfMat);
        shelf.position.set(rx, shelfY, 0);
        shelf.castShadow    = true;
        shelf.receiveShadow = true;
        rackGroup.add(shelf);

        for (let colIdx = 0; colIdx < numSlots; colIdx++) {
          const rz = offsetZ + colIdx * SLOT_SPACING;
          
          const slot = rack.slots && rack.slots[colIdx] ? rack.slots[colIdx] : null;
          const isLoaded = slot && slot.status === 'loaded';
          const qty      = isLoaded ? (slot.quantity || 0) : 0;

          const outerGeo = new THREE.BoxGeometry(SLOT_W, SLOT_H, SLOT_D);
          const outerMat = new THREE.MeshLambertMaterial({
            color: 0xa1a1aa,
            transparent: true,
            opacity: 0.28,
            depthWrite: false
          });
          const outerMesh = new THREE.Mesh(outerGeo, outerMat);
          outerMesh.position.set(rx, shelfY + 0.03 + SLOT_H / 2, rz);
          outerMesh.userData = { slot, rack };
          rackGroup.add(outerMesh);

          const edges = new THREE.EdgesGeometry(outerGeo);
          const lineMat = new THREE.LineBasicMaterial({
            color: 0x71717a,
            transparent: true,
            opacity: 0.40
          });
          const line = new THREE.LineSegments(edges, lineMat);
          line.position.copy(outerMesh.position);
          rackGroup.add(line);

          if (isLoaded) {
            let slotColor;
            if (qty <= 30) {
              slotColor = COLORS.low;
            } else if (qty <= 70) {
              slotColor = COLORS.mid;
            } else {
              slotColor = COLORS.high;
            }

            const fillPct = Math.min(1.0, Math.max(0.35, qty / 100));
            const innerW = SLOT_W * 0.90;
            const innerD = SLOT_D * 0.90;
            const innerH = SLOT_H * fillPct * 0.90;

            const boxGeo = new THREE.BoxGeometry(innerW, innerH, innerD);
            const boxMat = new THREE.MeshLambertMaterial({
              color:       slotColor,
              transparent: false,
              emissive:    new THREE.Color(slotColor).multiplyScalar(0.12),
            });
            const box = new THREE.Mesh(boxGeo, boxMat);
            box.position.set(
              rx,
              shelfY + 0.03 + innerH / 2 + 0.005,
              rz
            );
            box.castShadow    = true;
            box.receiveShadow = true;
            box.userData = { slot, rack };
            rackGroup.add(box);
          }
        }
      }

      const topGeo = new THREE.BoxGeometry(RACK_W + 0.05, 0.08, totalZ + SLOT_SPACING + 0.05);
      const topMat = new THREE.MeshLambertMaterial({ color: 0x0f172a });
      const top    = new THREE.Mesh(topGeo, topMat);
      top.position.set(rx, SHELF_LEVELS * LEVEL_GAP, 0);
      rackGroup.add(top);

      const labelCanvas = document.createElement('canvas');
      labelCanvas.width  = 160;
      labelCanvas.height = 60;
      const ctx = labelCanvas.getContext('2d');
      
      ctx.fillStyle = '#1e293b';
      ctx.beginPath();
      ctx.roundRect(0, 0, 160, 60, 10);
      ctx.fill();
      ctx.strokeStyle = '#60a5fa';
      ctx.lineWidth = 4;
      ctx.stroke();

      ctx.fillStyle    = '#ffffff';
      ctx.font         = 'bold 28px sans-serif';
      ctx.textAlign    = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(rack.rack_code || `R${ri+1}`, 80, 30);

      const tex      = new THREE.CanvasTexture(labelCanvas);
      const spriteMat = new THREE.SpriteMaterial({ map: tex });
      const sprite   = new THREE.Sprite(spriteMat);
      sprite.scale.set(1.2, 0.45, 1.0);
      sprite.position.set(rx, SHELF_LEVELS * LEVEL_GAP + 0.65, 0);
      rackGroup.add(sprite);
    });

    const controls = _makeOrbitControls(cam, canvas);
    scene.controls = controls;
    scene._controls = controls;

    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    let mouseDownTime = 0;
    let mouseDownPos = { x: 0, y: 0 };

    canvas.addEventListener('mousedown', (e) => {
      mouseDownTime = Date.now();
      mouseDownPos = { x: e.clientX, y: e.clientY };
    });

    canvas.addEventListener('click', (e) => {
      const clickDuration = Date.now() - mouseDownTime;
      const dx = e.clientX - mouseDownPos.x;
      const dy = e.clientY - mouseDownPos.y;
      const clickDistance = Math.hypot(dx, dy);

      if (clickDuration > 250 || clickDistance > 5) {
        return;
      }

      const rect = canvas.getBoundingClientRect();
      mouse.x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
      mouse.y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

      raycaster.setFromCamera(mouse, cam);
      const intersects = raycaster.intersectObjects(rackGroup.children, true);
      
      if (intersects.length > 0) {
        for (let i = 0; i < intersects.length; i++) {
          const obj = intersects[i].object;
          if (obj.userData && obj.userData.slot && obj.userData.rack) {
            openSlotPopup(obj.userData.slot, obj.userData.rack, null);
            break;
          }
        }
      }
    });

    function animate() {
      threeAnimId = requestAnimationFrame(animate);
      controls.update();
      renderer.render(scene, cam);
    }
    animate();
  }

  if (window.THREE) {
    _doInit();
  } else {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/three@0.160.0/build/three.min.js';
    s.onload = _doInit;
    s.onerror = () => showToast('Gagal memuat Three.js.', 'error');
    document.head.appendChild(s);
  }
}

function _makeOrbitControls(camera, domElement) {
  let isDragging = false;
  let prevMouse  = { x: 0, y: 0 };
  let spherical  = { theta: Math.PI / 4, phi: Math.PI / 3, radius: 12 };
  const target   = { x: 0, y: 1.0, z: 0 };

  function _update() {
    camera.position.x = target.x + spherical.radius * Math.sin(spherical.phi) * Math.sin(spherical.theta);
    camera.position.y = target.y + spherical.radius * Math.cos(spherical.phi);
    camera.position.z = target.z + spherical.radius * Math.sin(spherical.phi) * Math.cos(spherical.theta);
    camera.lookAt(target.x, target.y, target.z);
  }
  _update();

  domElement.addEventListener('mousedown', (e) => {
    if (e.button !== 0) return;
    isDragging = true;
    prevMouse  = { x: e.clientX, y: e.clientY };
    domElement.style.cursor = 'grabbing';
  });
  window.addEventListener('mouseup', () => {
    isDragging = false;
    domElement.style.cursor = 'grab';
  });
  window.addEventListener('mousemove', (e) => {
    if (!isDragging) return;
    const dx = e.clientX - prevMouse.x;
    const dy = e.clientY - prevMouse.y;
    prevMouse = { x: e.clientX, y: e.clientY };
    spherical.theta -= dx * 0.008;
    spherical.phi   -= dy * 0.008;
    spherical.phi    = Math.max(0.1, Math.min(Math.PI * 0.75, spherical.phi));
    _update();
  });
  domElement.addEventListener('wheel', (e) => {
    e.preventDefault();
    spherical.radius = Math.max(4, Math.min(28, spherical.radius + e.deltaY * 0.02));
    _update();
  }, { passive: false });

  let lastTouchDist = 0;
  domElement.addEventListener('touchstart', (e) => {
    if (e.touches.length === 1) {
      isDragging = true;
      prevMouse  = { x: e.touches[0].clientX, y: e.touches[0].clientY };
    } else if (e.touches.length === 2) {
      isDragging = false;
      lastTouchDist = Math.hypot(
        e.touches[0].clientX - e.touches[1].clientX,
        e.touches[0].clientY - e.touches[1].clientY
      );
    }
  });
  domElement.addEventListener('touchmove', (e) => {
    e.preventDefault();
    if (e.touches.length === 1 && isDragging) {
      const dx = e.touches[0].clientX - prevMouse.x;
      const dy = e.touches[0].clientY - prevMouse.y;
      prevMouse = { x: e.touches[0].clientX, y: e.touches[0].clientY };
      spherical.theta -= dx * 0.01;
      spherical.phi   -= dy * 0.01;
      spherical.phi    = Math.max(0.1, Math.min(Math.PI * 0.75, spherical.phi));
      _update();
    } else if (e.touches.length === 2) {
      const dist = Math.hypot(
        e.touches[0].clientX - e.touches[1].clientX,
        e.touches[0].clientY - e.touches[1].clientY
      );
      spherical.radius = Math.max(4, Math.min(28, spherical.radius - (dist - lastTouchDist) * 0.05));
      lastTouchDist = dist;
      _update();
    }
  }, { passive: false });
  domElement.addEventListener('touchend', () => { isDragging = false; });

  return {
    target,
    spherical,
    update: _update,
  };
}

initPage();

</script>
