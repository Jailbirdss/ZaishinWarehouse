<?php if (!empty($_GET['saved'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Data user berhasil disimpan.
</div>
<?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  User berhasil dihapus.
</div>
<?php endif; ?>


<div class="flex-between mb-20 mt-10 justify-end">
  <button class="btn btn-primary" onclick="openModal('modal-user')">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Tambah User
  </button>
</div>

<?php
$totalUsers = count($users);
$activeUsers = 0;
$inactiveUsers = 0;
foreach ($users as $u) {
    if ((int)$u['is_active'] === 1) {
        $activeUsers++;
    } else {
        $inactiveUsers++;
    }
}
?>

<div class="kpi-grid mb-24 kpi-grid-3">
  <div class="kpi-card primary kpi-card-row">
    <div class="kpi-icon kpi-icon-items primary">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $totalUsers ?></div>
      <div class="kpi-label kpi-label-sm">Total User Terdaftar</div>
    </div>
  </div>
  <div class="kpi-card success kpi-card-row">
    <div class="kpi-icon kpi-icon-items success">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $activeUsers ?></div>
      <div class="kpi-label kpi-label-sm">User Aktif</div>
    </div>
  </div>
  <div class="kpi-card danger kpi-card-row">
    <div class="kpi-icon kpi-icon-items danger">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
    </div>
    <div>
      <div class="kpi-value kpi-value-sm"><?= $inactiveUsers ?></div>
      <div class="kpi-label kpi-label-sm">User Nonaktif</div>
    </div>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr><th>User</th><th>Username</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <?php
          $roleColors = [
            'admin_gudang'     => '#1e40af',
            'petugas_gudang'   => '#16a34a',
            'divisi_penjualan' => '#7c3aed',
            'divisi_pembelian' => '#d97706',
            'manajemen'        => '#0891b2',
          ];
          $roleNames = [];
          foreach ($roles as $r) {
              $roleNames[$r['role_key']] = $r['display_name'];
          }
          $color = $roleColors[$u['role']] ?? '#64748b';
        ?>
        <tr>
          <td>
            <div class="d-flex align-center gap-10">
              <div style="width:36px;height:36px;border-radius:10px;background:<?= $color ?>22;color:<?= $color ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">
                <?= htmlspecialchars($u['avatar'] ?? 'U') ?>
              </div>
              <div>
                <div class="font-600-main"><?= htmlspecialchars($u['name']) ?></div>
                <div class="fs-11 text-muted"><?= date('d M Y', strtotime($u['created_at'])) ?></div>
              </div>
            </div>
          </td>
          <td><code class="username-code"><?= htmlspecialchars($u['username']) ?></code></td>
          <td><span class="badge" style="background:<?= $color ?>22;color:<?= $color ?>"><?= $roleNames[$u['role']] ?? $u['role'] ?></span></td>
          <td>
            <?= $u['is_active']
              ? '<span class="badge badge-success">Aktif</span>'
              : '<span class="badge badge-muted">Nonaktif</span>' ?>
          </td>
          <td>
            <div class="d-flex gap-6">
              <button class="btn btn-secondary btn-sm user-btn-action user-btn-edit" onclick='editUser(<?= json_encode($u) ?>)'>Edit</button>
              <?php if ($u['id'] != $_SESSION['user_id']): ?>
              <a href="index.php?page=users&toggle=1&id=<?= $u['id'] ?>"
                class="btn btn-sm user-btn-action <?= $u['is_active'] ? 'user-btn-toggle-inactive' : 'user-btn-toggle-active' ?>"
                onclick="confirmToggle(event, this.href, '<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>', <?= $u['is_active'] ? 'true' : 'false' ?>)">
                <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
              </a>
              <?php if (hasPermission('users.delete')): ?>
              <a href="index.php?page=users&action=delete&id=<?= $u['id'] ?>"
                class="btn btn-sm user-btn-action user-btn-delete"
                onclick="confirmDelete(event, this.href, '<?= htmlspecialchars($u['name'], ENT_QUOTES) ?>')">Hapus</a>
              <?php endif; ?>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="modal-user">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title" id="modal-user-title">Tambah User</div>
    </div>
    <form method="POST" action="index.php?page=users" id="form-user">
      <div class="modal-body">
        <input type="hidden" name="id" id="user-id"/>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Nama Lengkap *</label>
            <input name="name" id="u-name" class="form-control" placeholder="Nama lengkap" required/>
          </div>
          <div class="form-group">
            <label class="form-label">Username *</label>
            <input name="username" id="u-username" class="form-control" placeholder="username" required/>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Role *</label>
          <select name="role" id="u-role" class="form-control" required>
            <?php foreach ($roles as $r): ?>
              <option value="<?= htmlspecialchars($r['role_key']) ?>"><?= htmlspecialchars($r['display_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Password <span id="pw-hint" class="pw-hint-text">(kosongkan untuk tidak mengubah)</span></label>
          <input type="password" name="password" id="u-pw" class="form-control" placeholder="Password baru"/>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-user');resetUserForm()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan User</button>
      </div>
    </form>
  </div>
</div>

<script>
function editUser(u) {
  document.getElementById('modal-user-title').textContent = 'Edit User';
  document.getElementById('user-id').value    = u.id;
  document.getElementById('u-name').value     = u.name;
  document.getElementById('u-username').value = u.username;
  document.getElementById('u-role').value     = u.role;
  document.getElementById('pw-hint').textContent = '(kosongkan untuk tidak mengubah)';
  openModal('modal-user');
}
function resetUserForm() {
  document.getElementById('form-user').reset();
  document.getElementById('user-id').value = '';
  document.getElementById('modal-user-title').textContent = 'Tambah User';
  document.getElementById('pw-hint').textContent = '';
}
async function confirmToggle(event, url, name, isActive) {
  event.preventDefault();
  const actionText = isActive ? 'menonaktifkan' : 'mengaktifkan';
  const actionTitle = isActive ? 'Nonaktifkan User' : 'Aktifkan User';
  const confirmBtnText = isActive ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan';
  const isDanger = isActive;

  const confirmed = await showConfirm(
    `Apakah Anda yakin ingin ${actionText} user "${name}"?`,
    actionTitle,
    confirmBtnText,
    isDanger
  );
  if (confirmed) {
    window.location.href = url;
  }
}
async function confirmDelete(event, url, name) {
  event.preventDefault();
  const confirmed = await showConfirm(
    `Apakah Anda yakin ingin menghapus permanen user "${name}"? Tindakan ini tidak dapat dibatalkan.`,
    'Hapus User',
    'Ya, Hapus',
    true
  );
  if (confirmed) {
    window.location.href = url;
  }
}
</script>
