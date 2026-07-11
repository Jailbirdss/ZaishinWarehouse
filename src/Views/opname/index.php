<?php

?>

<?php if (!empty($success)): ?>
  <div class="alert alert-success mb-20">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><?= htmlspecialchars($success) ?></div>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger mb-20">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div><?= htmlspecialchars($error) ?></div>
  </div>
<?php endif; ?>

<?php if ($activeOpname): ?>
  <div class="opname-card">
    <div class="opname-header-row">
      <div>
        <div class="opname-active-title">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
          Sesi Opname Aktif: <span class="font-monospace"><?= htmlspecialchars($activeOpname['opname_no']) ?></span>
        </div>
        <p class="fs-12-5 text-muted mt-4">
          Diinisiasi oleh <strong><?= htmlspecialchars($activeOpname['creator_name']) ?></strong> pada <?= date('d M Y H:i', strtotime($activeOpname['created_at'])) ?>.
        </p>
      </div>
      <div class="d-flex gap-10">
        <?php if ($role !== 'manajemen'): ?>
        <a href="index.php?page=opname&action=scan" class="btn btn-primary p-10-18">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
          Mulai Pindai Fisik (Petugas)
        </a>
        <?php endif; ?>

        <?php if ($role === 'admin_gudang'): ?>
          <form id="cancel-opname-form" method="POST" action="index.php?page=opname&action=cancel">
            <input type="hidden" name="opname_id" value="<?= $activeOpname['id'] ?>" />
            <button type="button" onclick="confirmCancelOpname()" class="btn btn-cancel-session">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              Batalkan Sesi
            </button>
          </form>
        <?php endif; ?>

        <?php if (in_array($role, ['admin_gudang', 'manajemen'])): ?>
          <form id="finalize-opname-form" method="POST" action="index.php?page=opname&action=finalize">
            <input type="hidden" name="opname_id" value="<?= $activeOpname['id'] ?>" />
            <button type="button" onclick="confirmFinalizeOpname()" class="btn btn-success p-10-18">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Finalisasi & Update Stok
            </button>
          </form>
        <?php endif; ?>
      </div>
    </div>

    <div class="card mt-20 border-radius-12">
      <div class="card-header card-header-opname-progress">
        <div class="card-title card-title-fs-14">Daftar Progress Audit Fisik & Perbandingan Selisih</div>
      </div>
      <div class="card-body card-body-opname-progress">
        <div class="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Lokasi Rak/Slot</th>
                <th>Kode SKU</th>
                <th>Nama Barang</th>
                <th class="text-right">Stok Sistem</th>
                <th class="text-right">Hitung Fisik</th>
                <th class="text-right">Selisih (*Discrepancy*)</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($details)): ?>
                <tr>
                  <td colspan="7" class="text-center p-24 text-muted">Belum ada data slot yang diaudit. Petugas wajib melakukan scanning di lapangan terlebih dahulu.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($details as $det):
                  $disc = $det['discrepancy'];
                  $rowClass = '';
                  $discText = '-';

                  if ($det['status'] === 'verified') {
                    if ($disc > 0) {
                      $rowClass = 'has-warning';
                      $discText = '<span class="text-warning font-700">+' . $disc . ' ' . $det['unit'] . ' (Lebih)</span>';
                    } elseif ($disc < 0) {
                      $rowClass = 'has-error';
                      $discText = '<span class="text-danger font-700">' . $disc . ' ' . $det['unit'] . ' (Kurang)</span>';
                    } else {
                      $discText = '<span class="text-success font-700">Cocok</span>';
                    }
                  }
                ?>
                  <tr class="<?= $rowClass ?>">
                    <td class="font-700 text-main"><?= htmlspecialchars($det['zone_name']) ?> · <span class="font-monospace"><?= htmlspecialchars($det['rack_code']) ?> · Slot <?= $det['slot_number'] ?></span></td>
                    <td class="font-monospace"><?= htmlspecialchars($det['sku']) ?></td>
                    <td><?= htmlspecialchars($det['item_name']) ?></td>
                    <td class="text-right font-600"><?= $det['system_quantity'] ?> <?= htmlspecialchars($det['unit']) ?></td>
                    <td class="text-right font-700 text-primary-dark"><?= $det['physical_quantity'] !== null ? $det['physical_quantity'] . ' ' . htmlspecialchars($det['unit']) : '-' ?></td>
                    <td class="text-right"><?= $discText ?></td>
                    <td class="text-center">
                      <?php if ($det['status'] === 'verified'): ?>
                        <span class="status-badge verified">Selesai</span>
                      <?php else: ?>
                        <span class="status-badge pending">Pending</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php else: ?>

  <div class="initiate-zone">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="empty-state-icon mb-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    <div class="no-session-title">Tidak Ada Sesi Stock Opname Aktif</div>
    <p class="fs-12-5 text-muted max-w-500px mx-auto mb-16">
      Untuk melakukan verifikasi stok fisik dan deteksi selisih barang, inisiasi sesi stock opname baru terlebih dahulu.
    </p>
    <?php if ($role === 'admin_gudang'): ?>
      <button class="btn btn-primary" onclick="openModal('initiate-modal')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Inisiasi Sesi Opname Baru
      </button>
    <?php else: ?>
      <span class="badge badge-muted">Menunggu Inisiasi oleh Admin Gudang</span>
    <?php endif; ?>

  </div>
<?php endif; ?>

<div class="card border-radius-12">
  <div class="card-header p-18-20-0">
    <div class="card-title card-title-fs-16">Riwayat Sesi Opname Selesai</div>
  </div>
  <div class="card-body p-10-0-0">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>No. Opname</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Diinisiasi Oleh</th>
            <th class="text-right">Total Item Diaudit</th>
            <th class="text-right">Item Ada Selisih</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($history)): ?>
            <tr>
              <td colspan="7" class="text-center p-24 text-muted">Belum ada riwayat stock opname yang diselesaikan.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($history as $h): ?>
              <tr>
                <td class="font-700 font-monospace text-main"><?= htmlspecialchars($h['opname_no']) ?></td>
                <td><?= date('d M Y H:i', strtotime($h['created_at'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($h['completed_at'])) ?></td>
                <td><?= htmlspecialchars($h['creator_name']) ?></td>
                <td class="text-right font-600"><?= $h['total_items'] ?> item</td>
                <td style="text-align:right; font-weight:700; color:<?= $h['discrepancy_items'] > 0 ? 'var(--danger)' : 'var(--success)' ?>;">
                  <?= $h['discrepancy_items'] ?> item
                </td>
                <td class="text-center">
                  <span class="status-badge verified badge-completed-opname">Selesai & Diterapkan</span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="initiate-modal">
  <div class="modal-box max-w-480px">
    <form method="POST" action="index.php?page=opname&action=initiate">
      <div class="modal-header">
        <div class="modal-title">Inisiasi Stock Opname Baru</div>
      </div>
      <div class="modal-body p-16-20">

        <div class="form-group">
          <label class="form-label font-700">Tipe Lingkup Audit (Target)</label>
          <div class="d-flex gap-16 mt-6">
            <label class="target-type-label">
              <input type="radio" name="target_type" value="zone" checked onchange="toggleInitiateTarget('zone')" />
              Per Zona Gudang
            </label>
            <label class="target-type-label">
              <input type="radio" name="target_type" value="rack" onchange="toggleInitiateTarget('rack')" />
              Per Baris Rak
            </label>
          </div>
        </div>

        <div class="form-group target-form-group" id="group-zone">
          <label class="form-label font-700">Pilih Zona Gudang Target</label>
          <select name="zone_id" class="form-control">
            <option value="">-- Pilih Zona --</option>
            <?php foreach ($zones as $zone): ?>
              <option value="<?= $zone['zone_id'] ?>"><?= htmlspecialchars($zone['zone_name']) ?> (<?= $zone['loaded_slots'] ?>/<?= $zone['total_slots'] ?> Terisi)</option>
            <?php endforeach; ?>
          </select>
          <p class="fs-11-5 text-muted mt-4">Seluruh slot dan barang aktif pada zona terpilih akan di-lock dalam list audit opname.</p>
        </div>

        <div class="form-group target-form-group d-none" id="group-rack">
          <label class="form-label font-700">Pilih Rak Target</label>
          <select name="rack_id" class="form-control">
            <option value="">-- Pilih Baris Rak --</option>
            <?php foreach ($racks as $rack): ?>
              <option value="<?= $rack['id'] ?>"><?= htmlspecialchars($rack['zone_name']) ?> · Rak <?= htmlspecialchars($rack['rack_code']) ?></option>
            <?php endforeach; ?>
          </select>
          <p class="fs-11-5 text-muted mt-4">Hanya 8 slot pada baris rak terpilih yang akan dikunci untuk audit.</p>
        </div>

      </div>
      <div class="modal-footer p-12-20">
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('initiate-modal')">Batal</button>
        <button type="submit" class="btn btn-primary btn-sm">Mulai Sesi Opname</button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleInitiateTarget(type) {
  if (type === 'zone') {
    document.getElementById('group-zone').classList.remove('d-none');
    document.getElementById('group-rack').classList.add('d-none');
  } else {
    document.getElementById('group-zone').classList.add('d-none');
    document.getElementById('group-rack').classList.remove('d-none');
  }
}

async function confirmFinalizeOpname() {
  const confirmed = await showConfirm(
    'Apakah Anda yakin ingin memfinalisasi hasil opname ini? Stok akan diperbarui permanen sesuai hasil audit fisik.',
    'Konfirmasi Finalisasi Opname',
    'Ya, Finalisasi',
    false
  );
  if (confirmed) {
    document.getElementById('finalize-opname-form').submit();
  }
}

async function confirmCancelOpname() {
  const confirmed = await showConfirm(
    'Apakah Anda yakin ingin membatalkan sesi stock opname aktif ini? Semua progress perhitungan fisik yang belum difinalisasi akan dihapus permanen.',
    'Konfirmasi Pembatalan Opname',
    'Ya, Batalkan Sesi',
    true
  );
  if (confirmed) {
    document.getElementById('cancel-opname-form').submit();
  }
}
</script>
