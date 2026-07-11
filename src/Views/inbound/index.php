

<?php if (!empty($_GET['success'])): ?>
  <?php if (!empty($_GET['discrepancy'])): ?>
    <div class="alert alert-warning alert-w-border-warning">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      <div>Laporan Ketidaksesuaian Inbound berhasil dicatat. Referensi: <strong><?= htmlspecialchars($_GET['ref'] ?? '') ?></strong></div>
    </div>
  <?php else: ?>
    <div class="alert alert-success alert-mb-20">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>Barang masuk berhasil dicatat. Referensi: <strong><?= htmlspecialchars($_GET['ref'] ?? '') ?></strong></div>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php if (!empty($_GET['arrival_success'])): ?>
  <div class="alert alert-success alert-mb-20">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>Kedatangan barang berhasil dicatat! Status PO telah diperbarui menjadi telah tiba di gudang.</div>
  </div>
<?php endif; ?>

<?php if (!empty($_GET['complete_success'])): ?>
  <div class="alert alert-success alert-mb-20">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div>Penerimaan PO berhasil diselesaikan dan diarsipkan.</div>
  </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-mb-20">
  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
  <div><?= htmlspecialchars($error) ?></div>
</div>
<?php endif; ?>

<div class="grid-2">

  <div class="card">
    <div class="card-header card-header-inbound">
      <div class="card-title card-title-inbound">Proses Penerimaan Inbound</div>
    </div>
    <div class="card-body card-body-inbound">

      <div id="po-list-panel" class="panel-fade-in">
        <div class="po-list-title">Daftar PO Menunggu Penerimaan</div>

        <div class="po-tabs-container">
          <button type="button" class="tab-btn active" onclick="filterPOTab('all', this)">Semua</button>
          <button type="button" class="tab-btn" onclick="filterPOTab('pending', this)">Belum Tiba</button>
          <button type="button" class="tab-btn" onclick="filterPOTab('arrived', this)">Tiba di Gudang</button>
          <button type="button" class="tab-btn" onclick="filterPOTab('completed', this)">Selesai</button>
        </div>

        <?php if (empty($activePOs)): ?>
          <div class="po-empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="po-empty-icon icon-40 mr-auto ml-auto mb-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <div class="po-empty-title">Tidak Ada PO Aktif</div>
            <div class="po-empty-subtitle">Semua permintaan restock barang telah selesai diproses.</div>
          </div>
        <?php else: ?>
          <div class="po-cards-container" id="po-cards-container" style="max-height: none; overflow-y: visible; padding-top: 6px !important; padding-bottom: 6px !important;">
            <div class="po-empty-state" id="po-tab-empty-state" style="display: none; padding: 32px 16px; border: 1px dashed var(--border); border-radius: 10px; background: #ffffff;">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="po-empty-icon icon-40 mr-auto ml-auto mb-12" style="color: var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <div class="po-empty-title" style="font-size: 15px; font-weight: 700; color: var(--text-main); text-align: center;">Tidak Ada Purchase Order</div>
              <div class="po-empty-subtitle" id="po-tab-empty-text" style="font-size: 13px; color: var(--text-muted); text-align: center; margin-top: 4px;">Tidak ada PO untuk status filter ini.</div>
            </div>
            <?php foreach ($activePOs as $po): ?>
              <?php 
                $totalRequested = 0;
                $totalReceived = 0;
                $totalPrice = 0;
                foreach ($po['items'] as $item) {
                    $totalRequested += $item['requested_qty'];
                    $totalReceived += $item['received_qty'];
                    $totalPrice += $item['item_price'] * $item['requested_qty'];
                }
                $progressPercent = $totalRequested > 0 ? round(($totalReceived / $totalRequested) * 100) : 0;
              ?>
              <div class="po-card-wrapper" data-arrival-status="<?= $po['arrival_status'] ?>" data-po-status="<?= $po['items'][0]['status'] ?>">
                <div class="po-item-card d-flex flex-column gap-12 p-20 bg-white border-radius-10 mb-16 shadow-sm">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-8">
                      <span class="po-badge text-uppercase"><?= htmlspecialchars($po['po_number']) ?></span>
                      <?php if ($po['items'][0]['status'] === 'completed'): ?>
                        <span class="badge bg-success-soft text-success">Selesai</span>
                      <?php elseif ($po['arrival_status'] === 'arrived'): ?>
                        <span class="badge bg-success-soft text-success">Disetujui</span>
                      <?php else: ?>
                        <span class="badge bg-warning-soft text-warning">Menunggu pengiriman</span>
                      <?php endif; ?>
                    </div>
                    <span class="po-date d-flex align-items-center gap-4">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                      <?= date('d M Y, H:i', strtotime($po['created_at'])) ?>
                    </span>
                  </div>

                  <div>
                    <h3 class="po-item-title m-0" style="font-size: 15px; font-weight: 800; color: var(--text-main);"><?= htmlspecialchars($po['supplier_name']) ?></h3>
                    <div class="po-footer-info mt-4" style="font-size: 12px; color: var(--text-secondary);">
                      Pemohon: <span class="po-footer-requester"><?= htmlspecialchars($po['requester_name']) ?></span> · <?= count($po['items']) ?> jenis barang
                    </div>
                  </div>

                  <?php if ($po['items'][0]['status'] === 'completed'): ?>
                    <div class="po-card-status-banner arrived">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      Penerimaan Selesai — Semua barang telah dicocokkan & dimasukkan ke gudang
                    </div>
                  <?php elseif ($po['arrival_status'] === 'arrived'): ?>
                    <div class="po-card-status-banner arrived">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-4 0h4"/></svg>
                      Barang tiba di gudang — <?= date('d M Y, H:i', strtotime($po['arrival_date'])) ?> · Ekspedisi: <?= htmlspecialchars($po['expedition']) ?>
                    </div>
                  <?php else: ?>
                    <div class="po-card-status-banner pending">
                      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      Barang belum tiba — estimasi <?= date('d M Y', strtotime($po['created_at'] . ' + 1 day')) ?>
                    </div>
                  <?php endif; ?>

                  <div class="po-inactive-content">
                    <div class="po-btn-row">
                      <button type="button" class="btn btn-outline btn-sm d-flex align-items-center gap-4" onclick="showPODocument(<?= htmlspecialchars(json_encode($po)) ?>)">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Lihat dokumen PO
                      </button>
                      <?php if ($po['items'][0]['status'] !== 'completed'): ?>
                        <?php if ($po['arrival_status'] === 'arrived'): ?>
                          <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-4" onclick="activatePO(this)">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mulai Penerimaan
                          </button>
                        <?php else: ?>
                          <button type="button" class="btn btn-warning btn-sm d-flex align-items-center gap-4" onclick="openRecordArrival('<?= htmlspecialchars($po['po_number']) ?>')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1m-4 0h4"/></svg>
                            Catat kedatangan
                          </button>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </div>

                  <?php if ($po['items'][0]['status'] !== 'completed'): ?>
                    <div class="po-active-content">
                      <div class="po-items-list-box">
                        <?php foreach ($po['items'] as $item): ?>
                          <?php 
                            $isArrived = ($po['arrival_status'] === 'arrived');
                            $isIncomplete = ($item['received_qty'] < $item['requested_qty']);
                            $clickable = ($isArrived && $isIncomplete);
                          ?>
                          <div class="po-item-row <?= $clickable ? 'cursor-pointer' : '' ?>"
                               <?php if ($clickable): ?>
                               onclick="selectPOItem(<?= htmlspecialchars(json_encode($po)) ?>, <?= htmlspecialchars(json_encode($item)) ?>)"
                               <?php endif; ?>>
                            <div class="po-item-left-info">
                              <div class="po-item-icon-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                              </div>
                              <div>
                                <div class="po-item-row-title"><?= htmlspecialchars($item['item_name']) ?></div>
                                <div class="po-item-row-sku">SKU: <?= htmlspecialchars($item['item_sku']) ?> · <?= htmlspecialchars($item['category_name']) ?></div>
                              </div>
                            </div>
                            <div class="po-item-right-info">
                              <div class="po-item-row-qty">
                                <strong><?= $item['received_qty'] ?></strong> / <?= $item['requested_qty'] ?> <?= htmlspecialchars($item['item_unit']) ?>
                              </div>
                              <?php if ($item['received_qty'] >= $item['requested_qty']): ?>
                                <span class="badge bg-success-soft text-success">Lengkap</span>
                              <?php elseif ($item['received_qty'] > 0): ?>
                                <span class="badge bg-warning-soft text-warning">Kurang <?= $item['requested_qty'] - $item['received_qty'] ?></span>
                              <?php else: ?>
                                <span class="badge bg-secondary-soft text-muted">Belum tiba</span>
                              <?php endif; ?>
                              <?php if ($clickable): ?>
                                <button type="button" class="btn btn-primary btn-sm p-4 d-flex align-items-center justify-content-center" style="border-radius: 4px;" title="Scan/Terima Barang">
                                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9V6a3 3 0 013-3h3m6 0h3a3 3 0 013 3v3m0 6v3a3 3 0 01-3 3h-3M9 21H6a3 3 0 01-3-3v-3M7 12h10"/></svg>
                                </button>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>

                      <div class="po-progress-wrapper">
                        <div class="po-progress-bar">
                          <div class="po-progress-fill" style="width: <?= $progressPercent ?>%;"></div>
                        </div>
                        <div class="po-progress-text-row">
                          <span><?= $totalReceived ?> dari <?= $totalRequested ?> item diterima</span>
                          <span><?= $progressPercent ?>%</span>
                        </div>
                      </div>

                      <div class="po-btn-row">
                        <button type="button" class="btn btn-outline btn-sm d-flex align-items-center gap-4" onclick="showPODocument(<?= htmlspecialchars(json_encode($po)) ?>)">
                          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                          Lihat dokumen PO
                        </button>
                        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-4" onclick="completePO('<?= htmlspecialchars($po['po_number']) ?>')" <?= ($totalReceived === 0) ? 'disabled style="opacity: 0.6; cursor: not-allowed;" title="Lakukan scan/penerimaan barang terlebih dahulu"' : '' ?>>
                          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                          Selesaikan penerimaan
                        </button>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <button type="button" class="btn btn-outline btn-manual-inbound" onclick="startManualInbound()">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Terima Barang Tanpa PO (Manual / Adjustment)
        </button>
      </div>

      <div id="inbound-wizard-wrapper" style="display:none;" class="panel-fade-in">

        <div class="wizard-steps">
          <div class="w-step active" id="step-ind-1">
            <span class="step-num">1</span>
            <span class="step-label">Scan & Verifikasi</span>
          </div>
          <div class="w-step-line" id="step-line-1"></div>
          <div class="w-step" id="step-ind-2">
            <span class="step-num">2</span>
            <span class="step-label">Rekomendasi Slot</span>
          </div>
          <div class="w-step-line" id="step-line-2"></div>
          <div class="w-step" id="step-ind-3">
            <span class="step-num">3</span>
            <span class="step-label">Konfirmasi</span>
          </div>
        </div>

        <form method="POST" action="index.php?page=inbound" id="form-inbound">

        <input type="hidden" name="item_id" id="hidden-item-id" />
        <input type="hidden" name="slot_id" id="hidden-slot-id" />
        <input type="hidden" name="quantity" id="hidden-quantity" />
        <input type="hidden" name="po_number" id="hidden-po-number" />
        <input type="hidden" name="condition" id="hidden-condition" value="baik" />
        <input type="hidden" name="notes" id="hidden-notes" />
        <input type="hidden" name="is_discrepancy" id="hidden-is-discrepancy" value="0" />
        <input type="hidden" name="restock_request_id" id="hidden-restock-request-id" />

        <div class="wizard-step-panel" id="panel-step-1">

          <div id="scanner-wrapper">
            <div class="form-group">
              <label class="form-label form-label-bold">Pindai/Scan QR Code Barang Supplier</label>

              <div class="qr-method-tabs">
                <button type="button" class="tab-btn active" id="tab-camera" onclick="switchQRMethod('camera')">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="tab-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                  Kamera Live
                </button>
                <button type="button" class="tab-btn" id="tab-file" onclick="switchQRMethod('file')">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="tab-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                  Unggah Gambar QR
                </button>
              </div>

              <div id="method-camera-container" class="tab-content-fade">

                <div id="camera-insecure-warning" style="display:none;margin-bottom:12px;">
                  <div class="qr-camera-zone camera-warning-zone">
                    <svg fill="none" stroke="var(--danger)" viewBox="0 0 24 24" class="camera-warning-icon">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div class="zone-text camera-warning-title">Kamera Tidak Dapat Diakses (Koneksi Tidak Aman)</div>
                    <div class="zone-subtext camera-warning-desc">
                      Browser memblokir akses kamera pada koneksi non-localhost atau non-HTTPS. <br/>
                      Silakan gunakan domain secure/localhost, atau unggah foto menggunakan tab <strong>Unggah File</strong> di sebelah kanan.
                    </div>
                  </div>
                </div>

                <div id="camera-active-wrapper">
                  <div class="qr-camera-zone" id="qr-camera-placeholder" onclick="toggleScanner()">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                      <circle cx="12" cy="13" r="3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    <div class="zone-text">Klik untuk Mengaktifkan Kamera Pemindai</div>
                    <div class="zone-subtext">Pastikan izin kamera browser telah disetujui</div>
                  </div>

                  <div id="qr-reader" style="display:none;" class="qr-reader-inbound"></div>

                  <button type="button" class="btn btn-danger btn-stop-camera-inbound" id="btn-stop-camera" onclick="toggleScanner()" style="display:none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Matikan Kamera Pemindai
                  </button>
                </div>

                <input type="hidden" id="qr-input"/>
              </div>

              <div id="method-file-container" class="tab-content-fade" style="display:none;">
                <div class="qr-drag-drop-zone" id="qr-drop-zone">
                  <input type="file" id="qr-file-input" accept="image/*" style="display:none;"/>
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                  </svg>
                  <div class="zone-text">Seret & taruh gambar QR di sini</div>
                  <div class="zone-subtext">atau klik untuk memilih file dari komputer</div>
                </div>
              </div>
            </div>

            <div id="qr-reader-temp" style="display:none;"></div>

            <div class="form-group form-group-divider">
              <label class="form-label form-label-semibold">Pilih Barang Secara Manual (Tanpa Scan QR)</label>
              <select id="item-select" class="form-control" onchange="onItemDropdownChange(this)">
                <option value="">-- Pilih Barang dari Database --</option>
                <?php foreach ($items as $item): ?>
                <option value="<?= $item['id'] ?>" data-qr="<?= htmlspecialchars($item['qr_code']) ?>">
                  [<?= htmlspecialchars($item['sku']) ?>] <?= htmlspecialchars($item['name']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="button" class="btn btn-outline btn-back-po" id="btn-back-to-po" onclick="cancelPOInbound()">
              Kembali ke Daftar PO
            </button>
          </div>

          <div id="verification-panel-wrapper" style="display:none;">
            <div class="verify-header">
              <div class="verify-title">Langkah 1: Verifikasi Barang Terhadap PO</div>
              <button type="button" class="btn-change-item" onclick="resetStep1()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Ganti Barang / Scan Ulang
              </button>
            </div>

            <div class="verification-panel">
              <div class="verify-meta-label">Identitas Barang</div>
              <div class="verify-item-title" id="verify-item-name">Nama Barang</div>

              <div class="info-row">
                <span class="info-label">Kode SKU</span>
                <span class="verify-sku-val" id="verify-item-sku">SKU</span>
              </div>
              <div class="info-row">
                <span class="info-label">Kategori</span>
                <span class="info-val" id="verify-item-category">Kategori</span>
              </div>
              <div class="info-row">
                <span class="info-label">Satuan</span>
                <span class="info-val" id="verify-item-unit">Unit</span>
              </div>
              <div class="info-row border-none">
                <span class="info-label">Stok Tercatat di Sistem</span>
                <span class="info-val verify-stock-val" id="verify-item-stock">0</span>
              </div>
            </div>

            <div class="form-group" id="po-select-wrapper" style="display:none;">
              <label class="form-label fw-700">Pilih Purchase Order (PO) Aktif</label>
              <select id="select-po-request" class="form-control" onchange="onPoSelectChange(this)">

              </select>
              <p class="po-expected-desc" id="po-expected-label"></p>
            </div>

            <div class="form-group" id="po-manual-wrapper">
              <label class="form-label fw-700">Nomor Purchase Order (PO)</label>
              <input type="text" id="input-po-number" class="form-control" placeholder="Contoh: PO-2026-0034 (Opsional)" />
            </div>

            <div class="form-group">
              <label class="form-label fw-700">Jumlah Fisik yang Diterima <span id="verify-unit-label" class="text-muted fw-500"></span></label>
              <input type="number" id="input-quantity" class="form-control" min="1" placeholder="Masukkan jumlah fisik barang yang datang" oninput="checkQuantityCompliance()" />
              <div id="quantity-warning" style="display:none;" class="quantity-warning-box">

              </div>
            </div>

            <div class="form-group">
              <label class="form-label fw-700">Kondisi Barang yang Diterima</label>
              <div class="condition-selector">
                <label class="condition-card active-baik" id="cond-card-baik" onclick="selectCondition('baik')">
                  <input type="radio" name="temp_condition" value="baik" checked />
                  <span class="cond-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  </span>
                  <span class="cond-label">Baik</span>
                </label>
                <label class="condition-card" id="cond-card-sebagian" onclick="selectCondition('sebagian_rusak')">
                  <input type="radio" name="temp_condition" value="sebagian_rusak" />
                  <span class="cond-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                  </span>
                  <span class="cond-label">Sebagian Cacat</span>
                </label>
                <label class="condition-card" id="cond-card-rusak" onclick="selectCondition('rusak')">
                  <input type="radio" name="temp_condition" value="rusak" />
                  <span class="cond-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-22"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                  </span>
                  <span class="cond-label">Rusak</span>
                </label>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label fw-700">Catatan Tambahan</label>
              <textarea id="input-notes" class="form-control" rows="2" placeholder="Catatan supplier, supir, plat nomor, dll (opsional)"></textarea>
            </div>

            <div class="d-flex flex-column gap-10 mt-20">
              <button type="button" class="btn btn-success w-full justify-center p-12 fw-700" onclick="validateAndGoToStep2()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Sesuai, Lanjut ke Penempatan
              </button>

              <button type="button" class="btn btn-warning-outline" onclick="toggleDiscrepancyPanel()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Laporkan Ketidaksesuaian Barang
              </button>
            </div>

            <div class="discrepancy-panel" id="discrepancy-form">
              <div class="disc-title">Detail Laporan Ketidaksesuaian</div>
              <p class="disc-desc">Laporan ini akan langsung dicatat ke dalam database untuk di-review oleh Divisi Pembelian & Penjualan. Barang tidak akan dimasukkan ke dalam rak gudang.</p>

              <div class="form-group">
                <label class="form-label form-label-xs-bold">Kategori Ketidaksesuaian</label>
                <select id="discrepancy-category" class="form-control select-bg-white">
                  <option value="Jumlah Fisik Tidak Sesuai PO">Jumlah Fisik Tidak Sesuai PO</option>
                  <option value="Barang Rusak / Cacat Pabrik">Barang Cacat / Rusak Seluruhnya</option>
                  <option value="Barang Salah / Tidak Sesuai Dokumen">Barang Salah / Tidak Sesuai PO</option>
                  <option value="Lainnya">Lainnya</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label form-label-xs-bold">Deskripsi Masalah / Alasan Penolakan</label>
                <textarea id="discrepancy-details" class="form-control textarea-bg-white" rows="2" placeholder="Contoh: Barang basah kuyup karena hujan, ditolak supirnya atau diretur."></textarea>
              </div>

              <button type="button" class="btn btn-warning w-full justify-center p-12" onclick="submitDiscrepancyReport()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Kirim Laporan & Simpan Log
              </button>
            </div>

          </div>

        </div>

        <div class="wizard-step-panel" id="panel-step-2" style="display:none;">
          <div class="verify-title mb-6">Langkah 2: Rekomendasi Lokasi Rak</div>
          <p class="recom-desc">
            Sistem merekomendasikan lokasi penyimpanan optimal berdasarkan kategori barang <strong class="recom-category" id="recommendation-category-name">Kertas</strong> dan ketersediaan ruang slot gudang.
          </p>

          <div id="recommendation-loader" style="display:none;" class="recom-loader-box">
            <div class="recom-loader-spinner"></div>
            <div class="recom-loader-text">Mencari slot kosong terdekat...</div>
          </div>

          <div class="slot-recommendations" id="recommendation-container">

          </div>

          <div class="form-group form-group-divider">
            <label class="form-label fw-600">Gunakan Slot Alternatif Lain (Opsional)</label>
            <select id="slot-select-fallback" class="form-control" onchange="onFallbackSlotChange(this)">
              <option value="">-- Pilih dari Semua Slot Kosong --</option>
              <?php foreach ($freeSlots as $slot): ?>
              <option value="<?= $slot['id'] ?>" data-zone="<?= htmlspecialchars($slot['zone_name']) ?>" data-rack="<?= htmlspecialchars($slot['rack_code']) ?>" data-slot="<?= $slot['slot_number'] ?>">
                <?= htmlspecialchars($slot['zone_name']) ?> · <?= htmlspecialchars($slot['rack_code']) ?> · Slot <?= $slot['slot_number'] ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="wizard-nav-btns">
            <button type="button" class="btn btn-outline btn-nav-half" onclick="goToStep(1)">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
              Kembali
            </button>
            <button type="button" class="btn btn-primary btn-nav-main" onclick="validateAndGoToStep3()">
              Lanjutkan
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
          </div>
        </div>

        <div class="wizard-step-panel" id="panel-step-3" style="display:none;">
          <div class="verify-title mb-12">Langkah 3: Tinjau & Konfirmasi Inbound</div>

          <div class="verification-panel verify-panel-success">
            <div class="verify-meta-label-success">Ringkasan Data Inbound</div>

            <div class="info-row">
              <span class="info-label">Barang</span>
              <span class="info-val verify-item-title-success" id="summary-item-name">Nama Barang</span>
            </div>
            <div class="info-row">
              <span class="info-label">Nomor PO</span>
              <span class="info-val" id="summary-po-number">-</span>
            </div>
            <div class="info-row">
              <span class="info-label">Jumlah Masuk</span>
              <span class="info-val verify-qty-success" id="summary-quantity">0 unit</span>
            </div>
            <div class="info-row">
              <span class="info-label">Kondisi Fisik</span>
              <span class="info-val" id="summary-condition">Baik</span>
            </div>
            <div class="info-row border-none">
              <span class="info-label">Lokasi Penempatan Rak</span>
              <span class="info-val verify-slot-success" id="summary-slot">Zona · Rak · Slot</span>
            </div>
          </div>

          <div class="form-group putaway-confirm-box">
            <div class="putaway-confirm-header">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="putaway-confirm-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
              Konfirmasi Penempatan Rak (Put Away)
            </div>
            <p class="putaway-confirm-desc">
              Silakan pindai/scan QR Code pada rak target <strong class="text-primary" id="target-slot-label"></strong> untuk memverifikasi penempatan fisik barang.
            </p>

            <div id="rack-scan-status" class="putaway-status-badge">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
              Belum Terverifikasi
            </div>

            <div class="qr-method-tabs">
              <button type="button" class="tab-btn active" id="tab-rack-camera" onclick="switchRackScanMethod('camera')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="tab-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Kamera Live
              </button>
              <button type="button" class="tab-btn" id="tab-rack-file" onclick="switchRackScanMethod('file')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="tab-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Unggah Gambar QR
              </button>
            </div>

            <div id="rack-camera-container" class="tab-content-fade">
              <div class="qr-camera-zone qr-camera-zone-rack" id="rack-camera-placeholder" onclick="toggleRackScanner()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="qr-camera-icon-rack"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
                <div class="zone-text zone-text-rack">Aktifkan Kamera Scan Rak</div>
              </div>
              <div id="qr-reader-rack" style="display:none;" class="qr-reader-rack-box"></div>
              <button type="button" class="btn btn-danger btn-sm btn-stop-rack-camera-box" id="btn-stop-rack-camera" onclick="toggleRackScanner()" style="display:none;">Matikan Kamera</button>
            </div>

            <div id="rack-file-container" class="tab-content-fade" style="display:none;">
              <div class="qr-drag-drop-zone qr-drag-drop-zone-rack" id="rack-drop-zone">
                <input type="file" id="rack-file-input" accept="image/*" style="display:none;"/>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="qr-camera-icon-rack"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <div class="zone-text zone-text-rack">Pilih File Gambar QR Rak</div>
              </div>
            </div>
          </div>

          <div class="form-group summary-notes-box">
            <div class="summary-notes-title">Catatan:</div>
            <div class="summary-notes-content" id="summary-notes">-</div>
          </div>

          <div class="form-group" id="auto-complete-po-wrapper" style="margin-top: 16px; display: none;">
            <label class="d-flex align-items-center gap-8" style="font-size: 13.5px; color: var(--text-main); cursor: pointer; user-select: none;">
              <input type="checkbox" name="auto_complete_po" value="1" checked style="width: 18px; height: 18px; accent-color: var(--primary); margin: 0; cursor: pointer;"/>
              <span>Selesaikan penerimaan Purchase Order ini sekaligus</span>
            </label>
          </div>

          <div class="wizard-nav-btns">
            <button type="button" class="btn btn-outline btn-nav-half" onclick="goToStep(2)">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
              Kembali
            </button>
            <button type="submit" id="btn-submit-inbound" class="btn btn-success btn-submit-inbound-final" disabled>
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Simpan & Perbarui Stok
            </button>
          </div>
        </div>

      </form>
      </div>

    </div>
  </div>

  <div class="card">
    <div class="card-header card-header-inbound">
      <div class="card-title card-title-inbound">Riwayat Masuk Terakhir</div>
    </div>
    <div class="card-body today-inbound-card-body" id="today-inbound"></div>
  </div>
</div>

<div class="po-modal-overlay" id="po-document-modal">
  <div class="po-modal-content">
    <button type="button" class="po-modal-close" onclick="closePODocument()">&times;</button>
    <div class="po-doc-header">
      <div class="po-doc-title">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20 text-primary"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Dokumen PO <span id="doc-po-number" class="text-primary ml-4"></span>
      </div>
    </div>
    
    <div class="po-doc-grid">
      <div class="po-doc-info-block">
        <label>Supplier</label>
        <span id="doc-supplier-name"></span>
      </div>
      <div class="po-doc-info-block">
        <label>Tanggal PO</label>
        <span id="doc-po-date"></span>
      </div>
      <div class="po-doc-info-block">
        <label>Dibuat Oleh</label>
        <span id="doc-requester"></span>
      </div>
      <div class="po-doc-info-block">
        <label>Disetujui Oleh</label>
        <span id="doc-approver"></span>
      </div>
    </div>

    <table class="po-doc-table">
      <thead>
        <tr>
          <th>Nama Barang</th>
          <th style="text-align: right;">Qty</th>
          <th style="text-align: right;">Harga Satuan</th>
          <th style="text-align: right;">Subtotal</th>
        </tr>
      </thead>
      <tbody id="doc-items-body">
      </tbody>
      <tfoot>
        <tr class="po-total-row">
          <td colspan="3" style="text-align: right; font-weight: 700;">Subtotal</td>
          <td id="doc-subtotal" style="text-align: right; font-weight: 700;"></td>
        </tr>
        <tr>
          <td colspan="3" style="text-align: right; color: var(--text-muted);">PPN 11%</td>
          <td id="doc-tax" style="text-align: right; color: var(--text-muted);"></td>
        </tr>
        <tr style="border-top: 2px solid var(--border); font-size: 14px;">
          <td colspan="3" style="text-align: right; font-weight: 800; color: var(--primary);">Total</td>
          <td id="doc-total" style="text-align: right; font-weight: 800; color: var(--primary);"></td>
        </tr>
      </tfoot>
    </table>

    <div class="po-timeline">
      <div class="po-timeline-title">Riwayat PO</div>
      <div class="po-timeline-list">
        <div class="po-timeline-item active">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">PO dibuat</span>
            <span class="po-timeline-time" id="time-po-created"></span>
          </div>
        </div>
        <div class="po-timeline-item active">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">Disetujui manajer</span>
            <span class="po-timeline-time" id="time-po-approved"></span>
          </div>
        </div>
        <div class="po-timeline-item active">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">Dikonfirmasi supplier</span>
            <span class="po-timeline-time" id="time-po-confirmed"></span>
          </div>
        </div>
        <div class="po-timeline-item active">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">Barang dikirim</span>
            <span class="po-timeline-time" id="time-po-shipped"></span>
          </div>
        </div>
        <div class="po-timeline-item" id="timeline-arrived">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">Tiba di gudang</span>
            <span class="po-timeline-time" id="time-po-arrived">Menunggu</span>
          </div>
        </div>
        <div class="po-timeline-item" id="timeline-completed">
          <div class="po-timeline-node"></div>
          <div class="po-timeline-content">
            <span class="po-timeline-label">Penerimaan selesai</span>
            <span class="po-timeline-time" id="time-po-completed">Menunggu</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="po-modal-overlay" id="po-arrival-modal">
  <div class="po-modal-content" style="max-width: 450px;">
    <button type="button" class="po-modal-close" onclick="closeRecordArrival()">&times;</button>
    <h3 class="po-timeline-title" style="margin-bottom: 20px;">Catat Kedatangan Barang</h3>
    <form action="index.php?page=inbound" method="POST">
      <input type="hidden" name="action" value="record_arrival"/>
      <input type="hidden" name="po_number" id="arrival-po-number"/>
      
      <div class="form-group mb-16">
        <label class="form-label form-label-bold">Nama Ekspedisi / Kurir</label>
        <select name="expedition" class="form-control" required>
          <option value="JNE Cargo">JNE Cargo</option>
          <option value="Sicepat Cargo">Sicepat Cargo</option>
          <option value="J&T Cargo">J&T Cargo</option>
          <option value="Deliveree">Deliveree</option>
          <option value="Armada Supplier">Armada Supplier</option>
        </select>
      </div>
      
      <div class="form-group mb-20">
        <label class="form-label form-label-bold">Tanggal & Waktu Tiba</label>
        <input type="datetime-local" name="arrival_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required/>
      </div>

      <div class="d-flex gap-12">
        <button type="button" class="btn btn-outline flex-1" onclick="closeRecordArrival()">Batal</button>
        <button type="submit" class="btn btn-primary flex-1">Simpan Kedatangan</button>
      </div>
    </form>
  </div>
</div>

<form id="complete-po-form" action="index.php?page=inbound" method="POST" style="display:none;">
  <input type="hidden" name="action" value="complete_po"/>
  <input type="hidden" name="po_number" id="complete-po-number"/>
</form>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let scannerOpen = false;
let selectedItem = null;
let selectedSlot = null;
let currentCondition = 'baik';
let activeSelectedPO = null;

if (typeof playSuccessFeedback !== 'function') {
  window.playSuccessFeedback = function() { console.log("Success audio-haptic feedback mock"); };
}
if (typeof playErrorFeedback !== 'function') {
  window.playErrorFeedback = function() { console.log("Error audio-haptic feedback mock"); };
}

document.addEventListener('DOMContentLoaded', () => {

  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    document.getElementById('camera-insecure-warning').style.display = 'block';
    document.getElementById('camera-active-wrapper').style.display = 'none';
  }

  selectCondition('baik');
  filterPOTab('all');
});

function switchQRMethod(method) {
  stopQRScanner();
  scannerOpen = false;
  document.getElementById('qr-reader').style.display = 'none';
  document.getElementById('btn-stop-camera').style.display = 'none';
  document.getElementById('qr-camera-placeholder').style.display = 'flex';

  if (method === 'camera') {
    document.getElementById('tab-camera').classList.add('active');
    document.getElementById('tab-file').classList.remove('active');
    document.getElementById('method-camera-container').style.display = 'block';
    document.getElementById('method-file-container').style.display = 'none';
  } else {
    document.getElementById('tab-camera').classList.remove('active');
    document.getElementById('tab-file').classList.add('active');
    document.getElementById('method-camera-container').style.display = 'none';
    document.getElementById('method-file-container').style.display = 'block';
  }
}

function toggleScanner() {
  const reader = document.getElementById('qr-reader');
  const placeholder = document.getElementById('qr-camera-placeholder');
  const stopBtn = document.getElementById('btn-stop-camera');

  if (!scannerOpen) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showToast("Akses kamera ditolak oleh browser (insecure context).", "error");
      return;
    }

    placeholder.style.display = 'none';
    reader.style.display = 'block';
    stopBtn.style.display = 'inline-flex';

    startQRScanner('qr-reader', (code) => {
      document.getElementById('qr-input').value = code;
      lookupQR(code);

      reader.style.display = 'none';
      stopBtn.style.display = 'none';
      placeholder.style.display = 'flex';
      scannerOpen = false;
    }, (err) => {
      reader.style.display = 'none';
      stopBtn.style.display = 'none';
      placeholder.style.display = 'flex';
      scannerOpen = false;
    });
    scannerOpen = true;
  } else {
    stopQRScanner();
    reader.style.display = 'none';
    stopBtn.style.display = 'none';
    placeholder.style.display = 'flex';
    scannerOpen = false;
  }
}

const dropZone = document.getElementById('qr-drop-zone');
const fileInput = document.getElementById('qr-file-input');

if (dropZone) {
  dropZone.addEventListener('click', () => fileInput.click());

  dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
  });

  ['dragleave', 'dragend'].forEach(type => {
    dropZone.addEventListener(type, () => dropZone.classList.remove('dragover'));
  });

  dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    if (e.dataTransfer.files.length > 0) {
      scanUploadedFile(e.dataTransfer.files[0]);
    }
  });
}

if (fileInput) {
  fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
      scanUploadedFile(e.target.files[0]);
    }
  });
}

function scanUploadedFile(file) {
  if (!file.type.startsWith('image/')) {
    showToast("File harus berupa gambar!", "error");
    return;
  }

  const html5Qr = new Html5Qrcode("qr-reader-temp");
  showToast("Membaca gambar QR...", "info");

  html5Qr.scanFile(file, true)
    .then(decodedText => {
      document.getElementById('qr-input').value = decodedText;
      lookupQR(decodedText);
      showToast("QR Code berhasil dibaca!", "success");
      html5Qr.clear();
    })
    .catch(err => {
      console.warn(err);
      showToast("Gagal membaca QR. Pastikan gambar jelas & berisi QR Code.", "error");
      playErrorFeedback();
      html5Qr.clear();
    });
}

function onItemDropdownChange(selectEl) {
  const selectedOpt = selectEl.options[selectEl.selectedIndex];
  if (selectedOpt && selectedOpt.value) {
    const qrCode = selectedOpt.dataset.qr;
    lookupQR(qrCode);
  }
}

function lookupQR(code) {
  if (!code) return;

  let qrCodeStr = code;
  let demoQty = '';
  let demoPO = '';

  try {
    const parsed = JSON.parse(code);
    if (parsed && parsed.code) {
      qrCodeStr = parsed.code;
      demoQty = parsed.qty || '';
      demoPO = parsed.po || '';
    }
  } catch (e) {

  }

  if (activeSelectedPO) {
    if (qrCodeStr !== activeSelectedPO.item_qr) {
      showToast(`Kesesuaian Gagal! Barang tidak cocok dengan dokumen PO. Diharapkan: [${activeSelectedPO.item_sku}] ${activeSelectedPO.item_name}.`, "error");
      playErrorFeedback();
      return;
    }
  }

  fetch(BASE_URL + '/api/qr.php?code=' + encodeURIComponent(qrCodeStr))
    .then(r => r.json())
    .then(data => {
      if (data.error) {
        showToast(data.error, 'error');
        playErrorFeedback();
        return;
      }

      selectedItem = data;
      playSuccessFeedback();

      document.getElementById('hidden-item-id').value = data.id;
      document.getElementById('verify-item-name').textContent = data.name;
      document.getElementById('verify-item-sku').textContent = data.sku;
      document.getElementById('verify-item-category').textContent = data.category_name;
      document.getElementById('verify-item-unit').textContent = data.unit;
      document.getElementById('verify-item-stock').textContent = data.total_stock + ' ' + data.unit;
      document.getElementById('verify-unit-label').textContent = '(' + data.unit + ')';

      if (activeSelectedPO) {

        document.getElementById('hidden-restock-request-id').value = activeSelectedPO.id;
        document.getElementById('input-po-number').value = 'PO-RESTOCK-' + activeSelectedPO.id;
        document.getElementById('input-quantity').value = activeSelectedPO.requested_qty;
      } else {

        document.getElementById('hidden-restock-request-id').value = '';
        document.getElementById('input-po-number').value = demoPO;
        document.getElementById('input-quantity').value = demoQty;

        fetchActivePOs(data.id);
      }

      document.getElementById('quantity-warning').style.display = 'none';

      document.getElementById('scanner-wrapper').style.display = 'none';
      document.getElementById('verification-panel-wrapper').style.display = 'block';

      setTimeout(() => {
        if (!activeSelectedPO && demoQty) {
          document.getElementById('input-notes').focus();
        } else {
          document.getElementById('input-quantity').focus();
        }
      }, 100);

    }).catch(() => {
      showToast('Gagal memindai barang', 'error');
      playErrorFeedback();
    });
}

function resetStep1() {
  selectedItem = null;
  document.getElementById('hidden-item-id').value = '';
  document.getElementById('hidden-restock-request-id').value = '';
  document.getElementById('item-select').value = '';
  document.getElementById('qr-input').value = '';
  document.getElementById('quantity-warning').style.display = 'none';

  document.getElementById('scanner-wrapper').style.display = 'block';
  document.getElementById('verification-panel-wrapper').style.display = 'none';
  document.getElementById('discrepancy-form').style.display = 'none';
}

function selectPOItem(po, item) {
  activeSelectedPO = {
    id: item.id,
    item_qr: item.item_qr,
    item_sku: item.item_sku,
    item_name: item.item_name,
    requested_qty: item.requested_qty - item.received_qty,
    po_number: po.po_number
  };

  document.getElementById('po-list-panel').style.display = 'none';
  document.getElementById('auto-complete-po-wrapper').style.display = 'block';
  const wrapper = document.getElementById('inbound-wizard-wrapper');
  wrapper.style.display = 'block';
  wrapper.classList.remove('panel-fade-in');
  void wrapper.offsetWidth;
  wrapper.classList.add('panel-fade-in');

  selectedItem = {
    id: item.item_id,
    sku: item.item_sku,
    name: item.item_name,
    unit: item.item_unit,
    qr_code: item.item_qr
  };

  document.getElementById('hidden-item-id').value = item.item_id;
  document.getElementById('hidden-restock-request-id').value = item.id;
  document.getElementById('hidden-po-number').value = po.po_number;
  document.getElementById('hidden-quantity').value = item.requested_qty - item.received_qty;

  document.getElementById('verify-item-name').textContent = item.item_name;
  document.getElementById('verify-item-sku').textContent = item.item_sku;
  document.getElementById('verify-item-category').textContent = 'Restock PO';
  document.getElementById('verify-item-unit').textContent = item.item_unit;
  document.getElementById('verify-item-stock').textContent = 'Memuat...';
  document.getElementById('verify-unit-label').textContent = '(' + item.item_unit + ')';

  fetch(BASE_URL + '/api/qr.php?code=' + encodeURIComponent(item.item_qr))
    .then(r => r.json())
    .then(data => {
      document.getElementById('verify-item-stock').textContent = data.total_stock + ' ' + item.item_unit;
      selectedItem.category_id = data.category_id;
      selectedItem.category_name = data.category_name;
      document.getElementById('verify-item-category').textContent = data.category_name;
    });

  document.getElementById('input-po-number').value = po.po_number;
  document.getElementById('input-quantity').value = item.requested_qty - item.received_qty;
  document.getElementById('quantity-warning').style.display = 'none';

  document.getElementById('po-select-wrapper').style.display = 'none';
  document.getElementById('po-manual-wrapper').style.display = 'block';
  document.getElementById('input-po-number').setAttribute('readonly', 'true');

  document.getElementById('btn-back-to-po').style.display = 'none';

  const label = document.querySelector('#scanner-wrapper label.form-label');
  if (label) {
    label.innerHTML = `Pindai/Scan QR Code Barang Supplier untuk Verifikasi Kesesuaian PO:<br/><strong style="color:var(--primary); font-size:14px;">[${item.item_sku}] ${item.item_name}</strong>`;
  }

  document.getElementById('scanner-wrapper').style.display = 'block';
  document.getElementById('verification-panel-wrapper').style.display = 'none';

  const changeItemBtn = document.querySelector('.btn-change-item');
  if (changeItemBtn) {
    changeItemBtn.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Batalkan Penerimaan PO / Kembali
    `;
    changeItemBtn.setAttribute('onclick', 'cancelPOInbound()');
  }

  goToStep(1);
}

function cancelPOInbound() {
  activeSelectedPO = null;
  selectedItem = null;

  document.getElementById('inbound-wizard-wrapper').style.display = 'none';
  const poListPanel = document.getElementById('po-list-panel');
  poListPanel.style.display = 'block';
  poListPanel.classList.remove('panel-fade-in');
  void poListPanel.offsetWidth;
  poListPanel.classList.add('panel-fade-in');

  document.getElementById('hidden-item-id').value = '';
  document.getElementById('hidden-restock-request-id').value = '';
  document.getElementById('hidden-po-number').value = '';
  document.getElementById('hidden-quantity').value = '';
  document.getElementById('input-po-number').removeAttribute('readonly');

  const label = document.querySelector('#scanner-wrapper label.form-label');
  if (label) {
    label.textContent = `Pindai/Scan QR Code Barang Supplier`;
  }

  resetStep1();
}

function startManualInbound() {
  activeSelectedPO = null;
  selectedItem = null;

  document.getElementById('po-list-panel').style.display = 'none';
  document.getElementById('auto-complete-po-wrapper').style.display = 'none';
  const wrapper = document.getElementById('inbound-wizard-wrapper');
  wrapper.style.display = 'block';
  wrapper.classList.remove('panel-fade-in');
  void wrapper.offsetWidth;
  wrapper.classList.add('panel-fade-in');

  document.getElementById('hidden-item-id').value = '';
  document.getElementById('hidden-restock-request-id').value = '';
  document.getElementById('hidden-po-number').value = '';
  document.getElementById('hidden-quantity').value = '';
  document.getElementById('input-po-number').removeAttribute('readonly');

  document.getElementById('btn-back-to-po').style.display = 'block';

  const label = document.querySelector('#scanner-wrapper label.form-label');
  if (label) {
    label.textContent = `Pindai/Scan QR Code Barang Supplier`;
  }

  const changeItemBtn = document.querySelector('.btn-change-item');
  if (changeItemBtn) {
    changeItemBtn.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Ganti Barang / Scan Ulang
    `;
    changeItemBtn.setAttribute('onclick', 'resetStep1()');
  }

  resetStep1();
}

function fetchActivePOs(itemId) {
  const selectPo = document.getElementById('select-po-request');
  const wrapper = document.getElementById('po-select-wrapper');
  const expectedLabel = document.getElementById('po-expected-label');

  selectPo.innerHTML = '<option value="">-- Pilih PO (Opsional) --</option>';
  wrapper.style.display = 'none';
  expectedLabel.textContent = '';

  fetch(BASE_URL + '/api/restock.php?item_id=' + itemId)
    .then(r => r.json())
    .then(data => {
      if (data && data.length > 0) {
        data.forEach(po => {
          const opt = document.createElement('option');
          opt.value = po.id;
          opt.dataset.qty = po.requested_qty;
          opt.dataset.po_num = 'PO-RESTOCK-' + po.id;
          opt.textContent = `PO #${po.id} (Minta: ${po.requested_qty} ${selectedItem.unit} - oleh ${po.requester_name})`;
          selectPo.appendChild(opt);
        });
        wrapper.style.display = 'block';
      }
    })
    .catch(err => {
      console.error('Gagal mengambil data PO:', err);
    });
}

function onPoSelectChange(selectEl) {
  const opt = selectEl.options[selectEl.selectedIndex];
  const inputPoNumber = document.getElementById('input-po-number');
  const inputQuantity = document.getElementById('input-quantity');
  const hiddenRestockRequestId = document.getElementById('hidden-restock-request-id');
  const expectedLabel = document.getElementById('po-expected-label');

  if (opt && opt.value) {
    hiddenRestockRequestId.value = opt.value;
    inputPoNumber.value = opt.dataset.po_num;
    inputQuantity.value = opt.dataset.qty;
    expectedLabel.textContent = `Jumlah yang diminta di PO: ${opt.dataset.qty} ${selectedItem.unit}`;
  } else {
    hiddenRestockRequestId.value = '';
    inputPoNumber.value = '';
    inputQuantity.value = '';
    expectedLabel.textContent = '';
  }

  checkQuantityCompliance();
}

function checkQuantityCompliance() {
  const selectPo = document.getElementById('select-po-request');
  const warningDiv = document.getElementById('quantity-warning');
  const inputQty = parseInt(document.getElementById('input-quantity').value) || 0;

  if (!selectPo || selectPo.selectedIndex <= 0) {
    warningDiv.style.display = 'none';
    return;
  }

  const opt = selectPo.options[selectPo.selectedIndex];
  const expectedQty = parseInt(opt.dataset.qty) || 0;

  if (inputQty !== expectedQty) {
    let warningMsg = '';
    if (inputQty < expectedQty) {
      warningMsg = `<strong>Perhatian:</strong> Kuantitas masuk (${inputQty}) <strong>kurang</strong> dari kuantitas yang diminta di PO (${expectedQty}). Silakan periksa kembali fisik barang, atau gunakan opsi "Laporkan Ketidaksesuaian Barang" di bawah jika terdapat retur/selisih barang bermasalah.`;
    } else {
      warningMsg = `<strong>Perhatian:</strong> Kuantitas masuk (${inputQty}) <strong>melebihi</strong> kuantitas yang diminta di PO (${expectedQty}). Harap verifikasi kesesuaian fisik barang dengan dokumen pengiriman dari supplier.`;
    }
    warningDiv.innerHTML = warningMsg;
    warningDiv.style.display = 'block';
  } else {
    warningDiv.style.display = 'none';
  }
}

function selectCondition(cond) {
  currentCondition = cond;
  document.getElementById('hidden-condition').value = cond;

  const cards = {
    'baik': document.getElementById('cond-card-baik'),
    'sebagian_rusak': document.getElementById('cond-card-sebagian'),
    'rusak': document.getElementById('cond-card-rusak')
  };

  for (const [key, card] of Object.entries(cards)) {
    if (!card) continue;

    card.classList.remove('active-baik', 'active-sebagian', 'active-rusak');

    const radio = card.querySelector('input[type="radio"]');
    if (key === cond) {
      radio.checked = true;
      card.classList.add('active-' + (key === 'sebagian_rusak' ? 'sebagian' : key));
    } else {
      radio.checked = false;
    }
  }
}

function toggleDiscrepancyPanel() {
  const panel = document.getElementById('discrepancy-form');
  if (panel.style.display === 'none' || !panel.style.display) {
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  } else {
    panel.style.display = 'none';
  }
}

function submitDiscrepancyReport() {
  const qty = parseInt(document.getElementById('input-quantity').value) || 0;
  const poNum = document.getElementById('input-po-number').value.trim();
  const discCat = document.getElementById('discrepancy-category').value;
  const discDetail = document.getElementById('discrepancy-details').value.trim();

  if (!discDetail) {
    showToast("Silakan tulis deskripsi masalah ketidaksesuaian barang!", "error");
    return;
  }

  document.getElementById('hidden-is-discrepancy').value = "1";
  document.getElementById('hidden-quantity').value = qty;
  document.getElementById('hidden-po-number').value = poNum;
  document.getElementById('hidden-condition').value = currentCondition === 'baik' ? 'rusak' : currentCondition;
  document.getElementById('hidden-notes').value = `[KETIDAKSESUAIAN: ${discCat}] ${discDetail}`;

  showToast("Mengirim laporan ketidaksesuaian...", "info");
  document.getElementById('form-inbound').submit();
}

function goToStep(step) {

  document.querySelectorAll('.wizard-step-panel').forEach(p => p.style.display = 'none');

  const activePanel = document.getElementById('panel-step-' + step);
  activePanel.style.display = 'block';
  activePanel.classList.remove('panel-fade-in');
  void activePanel.offsetWidth;
  activePanel.classList.add('panel-fade-in');

  for (let i = 1; i <= 3; i++) {
    const ind = document.getElementById('step-ind-' + i);
    const line = document.getElementById('step-line-' + i);

    ind.classList.remove('active', 'completed');
    if (line) line.classList.remove('active');

    if (i < step) {
      ind.classList.add('completed');
      if (line) line.classList.add('active');
    } else if (i === step) {
      ind.classList.add('active');
    }
  }
}

function validateAndGoToStep2() {
  const qty = parseInt(document.getElementById('input-quantity').value);
  const poNum = document.getElementById('input-po-number').value.trim();
  const notes = document.getElementById('input-notes').value.trim();

  if (!qty || qty <= 0) {
    showToast("Jumlah fisik yang diterima harus diisi dan lebih dari 0!", "error");
    document.getElementById('input-quantity').focus();
    return;
  }

  document.getElementById('hidden-quantity').value = qty;
  document.getElementById('hidden-po-number').value = poNum;
  document.getElementById('hidden-notes').value = notes;
  document.getElementById('hidden-is-discrepancy').value = "0";

  fetchRecommendedSlots(selectedItem.category_id);
}

function fetchRecommendedSlots(categoryId) {
  document.getElementById('recommendation-category-name').textContent = selectedItem.category_name;
  const loader = document.getElementById('recommendation-loader');
  const container = document.getElementById('recommendation-container');

  loader.style.display = 'block';
  container.innerHTML = '';

  fetch(BASE_URL + '/api/recommend-slots.php?category_id=' + categoryId)
    .then(r => r.json())
    .then(slots => {
      loader.style.display = 'none';

      if (!slots || slots.length === 0) {
        container.innerHTML = `
          <div class="slot-empty-state">
            Tidak ada slot kosong yang tersedia di gudang saat ini.
          </div>
        `;
        return;
      }

      selectedSlot = slots[0];
      document.getElementById('hidden-slot-id').value = slots[0].id;
      document.getElementById('slot-select-fallback').value = '';

      slots.forEach((slot, index) => {
        const isRec = slot.is_recommended == 1;
        const cardClass = index === 0 ? 'slot-card selected' : 'slot-card';
        const badgeHtml = isRec
          ? `<span class="slot-badge rec">
              <svg fill="currentColor" viewBox="0 0 20 20" class="icon-12"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
              Rekomendasi
             </span>`
          : `<span class="slot-badge">Alternatif</span>`;

        container.innerHTML += `
          <div class="${cardClass}" id="slot-card-${slot.id}" onclick="selectRecommendedSlot(${JSON.stringify(slot).replace(/"/g, '&quot;')})">
            <div class="slot-card-left">
              <div class="slot-card-icon">${slot.rack_code.substring(0, 2)}</div>
              <div>
                <div class="slot-card-title">${slot.rack_code} · Slot ${slot.slot_number}</div>
                <div class="slot-card-subtitle">${slot.zone_name}</div>
              </div>
            </div>
            <div>
              ${badgeHtml}
            </div>
          </div>
        `;
      });

      goToStep(2);
    })
    .catch(() => {
      loader.style.display = 'none';
      showToast("Gagal memuat rekomendasi slot", "error");
    });
}

function selectRecommendedSlot(slot) {
  selectedSlot = slot;
  document.getElementById('hidden-slot-id').value = slot.id;
  document.getElementById('slot-select-fallback').value = '';

  document.querySelectorAll('.slot-card').forEach(card => card.classList.remove('selected'));
  const activeCard = document.getElementById('slot-card-' + slot.id);
  if (activeCard) {
    activeCard.classList.add('selected');
  }
}

function onFallbackSlotChange(selectEl) {
  const opt = selectEl.options[selectEl.selectedIndex];
  if (opt && opt.value) {
    selectedSlot = {
      id: parseInt(opt.value),
      zone_name: opt.dataset.zone,
      rack_code: opt.dataset.rack,
      slot_number: parseInt(opt.dataset.slot)
    };
    document.getElementById('hidden-slot-id').value = opt.value;

    document.querySelectorAll('.slot-card').forEach(card => card.classList.remove('selected'));
  }
}

function validateAndGoToStep3() {
  const slotId = document.getElementById('hidden-slot-id').value;
  if (!slotId) {
    showToast("Silakan pilih slot penempatan rak terlebih dahulu!", "error");
    return;
  }

  document.getElementById('summary-item-name').textContent = selectedItem.name + ' [' + selectedItem.sku + ']';
  document.getElementById('summary-po-number').textContent = document.getElementById('hidden-po-number').value || 'Tidak Ada PO';
  document.getElementById('summary-quantity').textContent = document.getElementById('hidden-quantity').value + ' ' + selectedItem.unit;

  const condText = {
    'baik': 'Baik',
    'sebagian_rusak': 'Sebagian Cacat',
    'rusak': 'Rusak'
  }[currentCondition];

  const condBadge = document.getElementById('summary-condition');
  condBadge.textContent = condText;
  condBadge.className = 'badge-condition';

  if (currentCondition === 'baik') {
    condBadge.classList.add('badge-cond-baik');
  } else if (currentCondition === 'sebagian_rusak') {
    condBadge.classList.add('badge-cond-sebagian');
  } else {
    condBadge.classList.add('badge-cond-rusak');
  }

  document.getElementById('summary-slot').textContent = `${selectedSlot.zone_name} · ${selectedSlot.rack_code} · Slot ${selectedSlot.slot_number}`;
  document.getElementById('summary-notes').textContent = document.getElementById('hidden-notes').value || 'Tidak ada catatan tambahan';

  document.getElementById('btn-submit-inbound').setAttribute('disabled', 'true');
  document.getElementById('rack-scan-status').innerHTML = `
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    Belum Terverifikasi
  `;
  document.getElementById('target-slot-label').textContent = `${selectedSlot.rack_code}-S${selectedSlot.slot_number}`;

  goToStep(3);
}

let rackScannerOpen = false;

function switchRackScanMethod(method) {
  stopQRScanner();
  rackScannerOpen = false;
  document.getElementById('qr-reader-rack').style.display = 'none';
  document.getElementById('btn-stop-rack-camera').style.display = 'none';
  document.getElementById('rack-camera-placeholder').style.display = 'flex';

  if (method === 'camera') {
    document.getElementById('tab-rack-camera').classList.add('active');
    document.getElementById('tab-rack-file').classList.remove('active');
    document.getElementById('rack-camera-container').style.display = 'block';
    document.getElementById('rack-file-container').style.display = 'none';
  } else {
    document.getElementById('tab-rack-camera').classList.remove('active');
    document.getElementById('tab-rack-file').classList.add('active');
    document.getElementById('rack-camera-container').style.display = 'none';
    document.getElementById('rack-file-container').style.display = 'block';
  }
}

function toggleRackScanner() {
  const reader = document.getElementById('qr-reader-rack');
  const placeholder = document.getElementById('rack-camera-placeholder');
  const stopBtn = document.getElementById('btn-stop-rack-camera');

  if (!rackScannerOpen) {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      showToast("Akses kamera ditolak oleh browser (insecure context).", "error");
      return;
    }

    placeholder.style.display = 'none';
    reader.style.display = 'block';
    stopBtn.style.display = 'inline-flex';

    startQRScanner('qr-reader-rack', (code) => {
      verifyRackQR(code);

      reader.style.display = 'none';
      stopBtn.style.display = 'none';
      placeholder.style.display = 'flex';
      rackScannerOpen = false;
    }, (err) => {
      reader.style.display = 'none';
      stopBtn.style.display = 'none';
      placeholder.style.display = 'flex';
      rackScannerOpen = false;
    });
    rackScannerOpen = true;
  } else {
    stopQRScanner();
    reader.style.display = 'none';
    stopBtn.style.display = 'none';
    placeholder.style.display = 'flex';
    rackScannerOpen = false;
  }
}

function verifyRackQR(scannedCode) {
  const targetCode = `SLOT-${selectedSlot.rack_code}-S${selectedSlot.slot_number}`;
  const statusEl = document.getElementById('rack-scan-status');
  const submitBtn = document.getElementById('btn-submit-inbound');

  if (scannedCode.trim() === targetCode) {
    statusEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16 text-success"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
      <span class="text-success">Lokasi Terverifikasi (Cocok)</span>
    `;
    submitBtn.removeAttribute('disabled');
    showToast("Rak terverifikasi! Silakan simpan untuk memperbarui stok.", "success");
    playSuccessFeedback();
  } else {
    statusEl.innerHTML = `
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-16 text-danger"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      <span class="text-danger">Lokasi Tidak Cocok (Terdeteksi: ${escHtml(scannedCode)})</span>
    `;
    submitBtn.setAttribute('disabled', 'true');
    showToast(`Scan Gagal! Lokasi rak tidak sesuai target. Target: ${targetCode}, Terbaca: ${scannedCode}`, "error");
    playErrorFeedback();
  }
}

function escHtml(str) {
  return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

document.addEventListener('DOMContentLoaded', () => {
  const rackDropZone = document.getElementById('rack-drop-zone');
  const rackFileInput = document.getElementById('rack-file-input');

  if (rackDropZone) {
    rackDropZone.addEventListener('click', () => rackFileInput.click());

    rackDropZone.addEventListener('dragover', (e) => {
      e.preventDefault();
      rackDropZone.classList.add('dragover');
    });

    ['dragleave', 'dragend'].forEach(type => {
      rackDropZone.addEventListener(type, () => rackDropZone.classList.remove('dragover'));
    });

    rackDropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      rackDropZone.classList.remove('dragover');
      if (e.dataTransfer.files.length > 0) {
        scanUploadedRackFile(e.dataTransfer.files[0]);
      }
    });
  }

  if (rackFileInput) {
    rackFileInput.addEventListener('change', (e) => {
      if (e.target.files.length > 0) {
        scanUploadedRackFile(e.target.files[0]);
      }
    });
  }
});

function scanUploadedRackFile(file) {
  if (!file.type.startsWith('image/')) {
    showToast("File harus berupa gambar!", "error");
    return;
  }

  const html5Qr = new Html5Qrcode("qr-reader-temp");
  showToast("Membaca gambar QR Rak...", "info");

  html5Qr.scanFile(file, true)
    .then(decodedText => {
      verifyRackQR(decodedText);
      showToast("QR Code Rak berhasil dibaca!", "success");
      html5Qr.clear();
    })
    .catch(err => {
      console.warn(err);
      showToast("Gagal membaca QR Rak. Pastikan gambar jelas & berisi QR Code.", "error");
      playErrorFeedback();
      html5Qr.clear();
    });
}

fetch(BASE_URL + '/api/dashboard.php?type=recent')
  .then(r => r.json())
  .then(txs => {
    const el = document.getElementById('today-inbound');
    const today = txs.filter(t => t.type === 'inbound');
    if (!today.length) {
      el.innerHTML = '<div class="tx-history-empty">Belum ada transaksi masuk</div>';
      return;
    }
    today.forEach(tx => {

      let condText = 'Baik';
      let condClass = 'badge-cond-baik';

      if (tx.condition === 'sebagian_rusak') {
        condText = 'Sebagian Cacat';
        condClass = 'badge-cond-sebagian';
      } else if (tx.condition === 'rusak') {
        condText = 'Rusak';
        condClass = 'badge-cond-rusak';
      }

      const poText = tx.po_number ? `<span class="badge-po-num">PO: ${tx.po_number}</span>` : '';
      const rackText = tx.rack_code
        ? `<span class="fw-600 text-primary">${tx.rack_code}</span>`
        : `<span class="fw-600 text-danger fst-italic">Tidak Ditempatkan (Retur)</span>`;

      const qtyText = tx.rack_code
        ? `<div class="tx-qty-plus">+${tx.quantity} ${tx.unit}</div>`
        : `<div class="tx-qty-minus">Retur ${tx.quantity} ${tx.unit}</div>`;

      el.innerHTML += `
      <div class="today-tx-item recent-tx-item">
        <div class="today-tx-icon-wrap">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="icon-20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
        </div>
        <div class="flex-1-min-w-0">
          <div class="tx-item-name">${tx.item_name}</div>
          <div class="tx-meta-row">
            <span class="badge-ref-no">${tx.reference_no}</span>
            <span>·</span>
            ${rackText}
            <span>·</span>
            ${poText}
            <span class="badge-condition ${condClass}">${condText}</span>
          </div>
        </div>
        ${qtyText}
      </div>`;
    });
  });

function showPODocument(po) {
  document.getElementById('doc-po-number').textContent = po.po_number;
  document.getElementById('doc-supplier-name').textContent = po.supplier_name;
  
  const createdDate = new Date(po.created_at);
  const formatDate = (d) => {
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
  };
  const formatTime = (d) => {
    return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
  };
  
  document.getElementById('doc-po-date').textContent = formatDate(createdDate);
  
  document.getElementById('doc-requester').textContent = po.requester_name;
  document.getElementById('doc-approver').textContent = po.approver_name;
  
  const tbody = document.getElementById('doc-items-body');
  tbody.innerHTML = '';
  let subtotal = 0;
  
  po.items.forEach(item => {
    const itemSub = item.item_price * item.requested_qty;
    subtotal += itemSub;
    
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <strong>${item.item_name}</strong><br/>
        <span style="font-size:11px;color:var(--text-muted);font-family:monospace;">${item.item_sku}</span>
      </td>
      <td style="text-align: right;">${item.requested_qty} ${item.item_unit}</td>
      <td style="text-align: right;">Rp ${item.item_price.toLocaleString('id-ID')}</td>
      <td style="text-align: right;">Rp ${itemSub.toLocaleString('id-ID')}</td>
    `;
    tbody.appendChild(tr);
  });
  
  const tax = Math.round(subtotal * 0.11);
  const total = subtotal + tax;
  
  document.getElementById('doc-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
  document.getElementById('doc-tax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
  document.getElementById('doc-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
  
  document.getElementById('time-po-created').textContent = formatDate(createdDate) + ', ' + formatTime(createdDate);
  
  const approvedTime = new Date(createdDate.getTime() + 30 * 60 * 1000);
  document.getElementById('time-po-approved').textContent = formatDate(approvedTime) + ', ' + formatTime(approvedTime);
  
  const confirmedTime = new Date(createdDate.getTime() + 45 * 60 * 1000);
  document.getElementById('time-po-confirmed').textContent = formatDate(confirmedTime) + ', ' + formatTime(confirmedTime);
  
  const shippedTime = new Date(createdDate.getTime() + 2 * 60 * 60 * 1000);
  document.getElementById('time-po-shipped').textContent = formatDate(shippedTime) + ', ' + formatTime(shippedTime);
  
  const tlArrived = document.getElementById('timeline-arrived');
  if (po.arrival_status === 'arrived') {
    tlArrived.classList.add('active');
    const arrivedDate = new Date(po.arrival_date);
    document.getElementById('time-po-arrived').textContent = formatDate(arrivedDate) + ', ' + formatTime(arrivedDate);
  } else {
    tlArrived.classList.remove('active');
    document.getElementById('time-po-arrived').textContent = 'Menunggu';
  }
  
  const tlCompleted = document.getElementById('timeline-completed');
  if (po.items && po.items.length > 0 && po.items[0].status === 'completed') {
    tlCompleted.classList.add('active');
    const completedDate = new Date(po.updated_at || po.created_at);
    document.getElementById('time-po-completed').textContent = formatDate(completedDate) + ', ' + formatTime(completedDate);
  } else {
    tlCompleted.classList.remove('active');
    document.getElementById('time-po-completed').textContent = 'Menunggu';
  }
  
  document.getElementById('po-document-modal').classList.add('active');
}

function closePODocument() {
  document.getElementById('po-document-modal').classList.remove('active');
}

function openRecordArrival(poNumber) {
  document.getElementById('arrival-po-number').value = poNumber;
  document.getElementById('po-arrival-modal').classList.add('active');
}

function closeRecordArrival() {
  document.getElementById('po-arrival-modal').classList.remove('active');
}

function completePO(poNumber) {
  if (confirm('Apakah Anda yakin ingin menyelesaikan penerimaan PO ' + poNumber + ' dan memperbarui statusnya menjadi selesai?')) {
    document.getElementById('complete-po-number').value = poNumber;
    document.getElementById('complete-po-form').submit();
  }
}

function activatePO(btn) {
  const card = btn.closest('.po-item-card');
  if (card) {
    card.classList.add('po-active');
  }
}

function filterPOTab(status, btn) {
  document.querySelectorAll('.po-tabs-container .tab-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');
  
  const cards = document.querySelectorAll('.po-card-wrapper');
  let visibleCount = 0;
  cards.forEach(card => {
    const arrival = card.dataset.arrivalStatus;
    const poStatus = card.dataset.poStatus;
    
    let show = false;
    if (status === 'all') {
      show = (poStatus !== 'completed');
    } else if (status === 'pending') {
      show = (arrival === 'pending' && poStatus !== 'completed');
    } else if (status === 'arrived') {
      show = (arrival === 'arrived' && poStatus !== 'completed');
    } else if (status === 'completed') {
      show = (poStatus === 'completed');
    }
    
    if (show) {
      card.style.display = 'block';
      card.style.animation = 'none';
      void card.offsetWidth;
      card.style.animation = 'poFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards';
      visibleCount++;
    } else {
      card.style.display = 'none';
    }
  });

  const emptyState = document.getElementById('po-tab-empty-state');
  if (emptyState) {
    if (visibleCount === 0) {
      const emptyText = document.getElementById('po-tab-empty-text');
      if (emptyText) {
        if (status === 'all') {
          emptyText.textContent = 'Tidak ada Purchase Order aktif saat ini.';
        } else if (status === 'pending') {
          emptyText.textContent = 'Tidak ada Purchase Order yang sedang dalam pengiriman/belum tiba.';
        } else if (status === 'arrived') {
          emptyText.textContent = 'Tidak ada Purchase Order yang telah tiba di gudang.';
        } else if (status === 'completed') {
          emptyText.textContent = 'Belum ada Purchase Order yang selesai diproses.';
        }
      }
      emptyState.classList.add('po-empty-active');
      emptyState.style.animation = 'none';
      void emptyState.offsetWidth;
      emptyState.style.animation = 'poFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards';
    } else {
      emptyState.classList.remove('po-empty-active');
    }
  }
}
</script>
