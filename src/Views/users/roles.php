<?php if (!empty($_GET['saved'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Peran baru berhasil ditambahkan.
</div>
<?php endif; ?>
<?php if (!empty($_GET['deleted'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Peran berhasil dihapus.
</div>
<?php endif; ?>
<?php if (!empty($_GET['updated'])): ?>
<div class="alert alert-success mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  Konfigurasi hak akses berhasil diperbarui.
</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
  <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php
$roleColors = [
    'admin_gudang'     => '#1e40af',
    'petugas_gudang'   => '#16a34a',
    'divisi_penjualan' => '#7c3aed',
    'divisi_pembelian' => '#d97706',
    'manajemen'        => '#0891b2',
];
?>

<div class="grid-2-col gap-24">
  
  <!-- Left Side: Roles Cards List -->
  <div class="card">
    <div class="card-header p-header pb-14">
      <div class="card-title fs-16 fw-800">Daftar Peran (Roles)</div>
    </div>
    <div class="card-body p-24">
      <div class="role-cards-list" id="role-cards-container">
        <?php foreach ($rolesList as $r): 
          $isBuiltIn = in_array($r['role_key'], $builtInRoles, true);
          $color = $roleColors[$r['role_key']] ?? '#64748b';
          
          // Generate Initials
          $words = explode(' ', $r['display_name']);
          $initials = '';
          foreach ($words as $w) {
              $initials .= strtoupper(substr($w, 0, 1));
          }
          $initials = substr($initials, 0, 2);
        ?>
          <div class="role-item-card" id="role-card-<?= htmlspecialchars($r['role_key']) ?>" data-role="<?= htmlspecialchars($r['role_key']) ?>" style="border-left: 4px solid <?= $color ?>;">
            <div class="role-card-main">
              <div class="role-avatar" style="background: <?= $color ?>18; color: <?= $color ?>;">
                <?= htmlspecialchars($initials) ?>
              </div>
              <div class="role-info">
                <div class="role-name text-main fw-700"><?= htmlspecialchars($r['display_name']) ?></div>
                <div class="flex-row gap-6 align-center mt-2">
                  <code class="role-code font-monospace fs-10.5"><?= htmlspecialchars($r['role_key']) ?></code>
                  <?php if ($isBuiltIn): ?>
                    <span class="role-badge badge-builtin">Bawaan</span>
                  <?php else: ?>
                    <span class="role-badge badge-custom">Kustom</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            
            <div class="role-card-stats">
              <span class="role-user-count">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> 
                <?= $r['user_count'] ?> staf
              </span>
            </div>
            
            <div class="role-card-actions">
              <button type="button" class="btn-role-action edit-trigger" data-key="<?= htmlspecialchars($r['role_key']) ?>" data-name="<?= htmlspecialchars($r['display_name']) ?>">
                Kelola Izin
              </button>
              
              <?php if (!$isBuiltIn): ?>
                <button type="button" class="btn-role-delete-icon btn-delete-role" data-key="<?= htmlspecialchars($r['role_key']) ?>" data-name="<?= htmlspecialchars($r['display_name']) ?>" title="Hapus Peran">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              <?php else: ?>
                <button type="button" class="btn-role-delete-icon btn-delete-builtin" data-key="<?= htmlspecialchars($r['role_key']) ?>" data-name="<?= htmlspecialchars($r['display_name']) ?>" style="opacity: 0.45; cursor: pointer; border-color: #fecaca; color: #f87171;" title="Peran bawaan sistem tidak dapat dihapus">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <!-- Client-side Pagination controls -->
      <div class="pagination-wrapper flex-between align-center mt-20 pt-16 border-top-solid" id="role-pagination">
        <button type="button" class="btn btn-secondary py-6 px-12 fs-12" id="btn-prev-role-page">Sebelumnya</button>
        <span class="fs-12 text-muted fw-700" id="role-page-indicator">Halaman 1 dari 1</span>
        <button type="button" class="btn btn-secondary py-6 px-12 fs-12" id="btn-next-role-page">Berikutnya</button>
      </div>
    </div>
  </div>

  <!-- Right Side: Add Role Card -->
  <div class="card">
    <div class="card-header p-header pb-14">
      <div class="card-title fs-16 fw-800">Buat Peran Baru (Kustom)</div>
    </div>
    <form method="POST" action="index.php?page=roles" id="form-add-role">
      <input type="hidden" name="action" value="add_role"/>
      <div class="card-body p-24">
        <div class="form-group">
          <label class="form-label">Nama Tampilan Peran *</label>
          <input name="display_name" class="form-control" placeholder="Contoh: Staf Magang" required />
        </div>
        <div class="form-group">
          <label class="form-label">Kode Peran (Unique Key) *</label>
          <input name="role_key" class="form-control" placeholder="Contoh: staf_magang" required />
          <div class="fs-11 text-muted mt-4">Hanya boleh berisi huruf kecil, angka, dan garis bawah (_).</div>
        </div>
      </div>
      <div class="card-footer bg-f8fafc border-top-solid text-right">
        <button type="submit" class="btn btn-primary">Tambah Peran Baru</button>
      </div>
    </form>
  </div>

</div>

<!-- Modal Permissions Overlay -->
<div class="modal-overlay" id="modal-permissions">
  <div class="modal-box" style="max-width: 860px; width: 90%; border-radius: 12px; overflow: hidden; padding: 0;">
    
    <div class="modal-header bg-primary p-20-24 flex-between align-center">
      <div class="modal-title fs-16 fw-800 text-white m-0" id="modal-role-title">
        Izin Akses Peran
      </div>
      <button type="button" class="btn-close-modal-x" onclick="closePermissionsModal()" style="background:transparent; border:none; color:white; font-size:24px; cursor:pointer; line-height: 1; padding: 0 4px;">&times;</button>
    </div>
    
    <form method="POST" action="index.php?page=roles" id="form-edit-permissions" class="m-0 flex-column">
      <input type="hidden" name="action" value="edit_permissions"/>
      <input type="hidden" name="role_key" id="modal-role-key" value=""/>
      
      <div class="modal-body p-24 flex-grow-1" style="max-height: 65vh; overflow-y: auto;">
        <div class="fs-12 text-muted mb-20 border-bottom-solid pb-12">
          Centang kotak di bawah ini untuk mengizinkan peran melakukan aksi atau mengakses menu tersebut:
        </div>
        
        <!-- Horizontal Tabs Navigation with Scroll Buttons -->
        <div class="tabs-wrapper mb-24">
          <button type="button" class="btn-tab-scroll scroll-left" id="btn-scroll-left" title="Geser Kiri">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
          </button>
          
          <div class="tabs-nav-scroll" id="tabs-nav-container">
            <?php 
            $firstTab = true;
            $tabIdx = 0;
            foreach ($allPermissions as $categoryName => $perms): 
            ?>
              <button type="button" class="tab-nav-btn <?= $firstTab ? 'active' : '' ?>" data-target="tab-cat-<?= $tabIdx ?>">
                <?= htmlspecialchars($categoryName) ?>
              </button>
            <?php 
              $firstTab = false;
              $tabIdx++;
            endforeach; 
            ?>
          </div>
          
          <button type="button" class="btn-tab-scroll scroll-right" id="btn-scroll-right" title="Geser Kanan">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>
        
        <!-- Tab Contents -->
        <div class="tabs-content-wrapper">
          <?php 
          $firstTab = true;
          $tabIdx = 0;
          foreach ($allPermissions as $categoryName => $perms): 
          ?>
            <div class="tab-content-pane <?= $firstTab ? '' : 'd-none' ?>" id="tab-cat-<?= $tabIdx ?>">
              <div class="fs-13 fw-800 text-main mb-14 flex-row align-center gap-6">
                <span class="bullet bg-primary"></span>
                <?= htmlspecialchars($categoryName) ?>
              </div>
              <div class="grid-2-col gap-12">
                <?php foreach ($perms as $pKey => $pDesc): ?>
                  <label class="checkbox-container" for="p-<?= htmlspecialchars($pKey) ?>">
                    <input type="checkbox" name="permissions[]" value="<?= htmlspecialchars($pKey) ?>" id="p-<?= htmlspecialchars($pKey) ?>" class="real-checkbox" style="opacity: 0; position: absolute;" />
                    <span class="custom-checkbox"></span>
                    <span class="fs-12-5 text-secondary">
                      <strong class="text-main block fs-12-5 mb-2"><?= htmlspecialchars($pKey) ?></strong>
                      <?= htmlspecialchars($pDesc) ?>
                    </span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php 
            $firstTab = false;
            $tabIdx++;
          endforeach; 
          ?>
        </div>
      </div>
      
      <div class="modal-footer bg-f8fafc border-top-solid flex-between align-center p-20-24">
        <button type="button" class="btn btn-secondary" onclick="closePermissionsModal()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan Hak Akses</button>
      </div>
    </form>
  </div>
</div>

<script>
const rolePermissions = <?= json_encode($permsMapping) ?>;
const allPermissionsMap = {
    <?php 
    $flatPerms = [];
    foreach ($allPermissions as $cat => $perms) {
        foreach ($perms as $key => $desc) {
            $flatPerms[$key] = $desc;
        }
    }
    foreach ($flatPerms as $key => $desc) {
        echo json_encode($key) . ': ' . json_encode($desc) . ",\n";
    }
    ?>
};
</script>
