<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peta Wilayah</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- custom css -->
  <link rel="stylesheet" href="style.css">
</head>

<body style="padding-top: 100px;">

  <nav class="navbar fixed-top navbar-light bg-light" style="height: 100px;">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i width="60" height="54" class="fa-solid fa-map-location-dot me-2 text-orange d-inline-block align-text-top"></i>
        Peta Wilayah Kota
      </a>
    </div>
  </nav>

  <!-- KONTEN -->
  <div class="container-fluid py-3 px-3 px-lg-5 px-xl-6">
    <div class="row g-3">

      <!-- MAP -->
      <div class="col-lg-8">
        <div class="map-wrapper border rounded maps-mobile">

          <!-- STAGE (yang di-zoom & pan) -->
          <div class="map-stage">
            <div id="mapZoom">

              <!-- PNG BASE MAP -->
              <img src="assets/peta.png" class="map-base">

              <!-- SVG (tetap, tidak diubah isinya) -->
              <?php include "maps.svg"; ?>

            </div>
          </div>

          <div class="map-controls">
            <button onclick="zoomIn()">+</button>
            <button onclick="zoomOut()">−</button>
            <button onclick="resetZoom()">⟳</button>
          </div>

        </div>
      </div>

      <!-- DETAIL + TABEL (TAB) -->
      <div class="col-lg-4">

        <!-- TAB HEADER -->
        <ul class="nav nav-tabs mb-2" id="infoTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active"
                    id="detail-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#detailTab"
                    type="button"
                    role="tab">
            <i class="fa-solid fa-circle-info me-1"></i>
              Detail
            </button>
          </li>

          <li class="nav-item" role="presentation">
            <button class="nav-link"
                    id="table-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#tableTab"
                    type="button"
                    role="tab">
              <i class="fa-solid fa-table me-1"></i>
              Daftar
            </button>
          </li>
        </ul>

        <!-- TAB CONTENT -->
        <div class="tab-content">
          <!-- TAB DETAIL -->
          <div class="tab-pane fade show active" id="detailTab" role="tabpanel">
            <div class="details-container">
              <div class="info-panel" id="infoPanel">
                <h4>Silakan Pilih Wilayah</h4>
                <p>Klik area pada peta</p>
              </div>
            </div>
          </div>

          <!-- TAB TABEL -->
          <div class="tab-pane fade" id="tableTab" role="tabpanel">
            
          <div class="mb-2 d-flex gap-2" id="blokFilter">
            <button class="btn btn-sm btn-primary active" data-blok="1">Blok A</button>
            <button class="btn btn-sm btn-outline-primary" data-blok="2">Blok B</button>
            <button class="btn btn-sm btn-outline-primary" data-blok="3">Blok C</button>
          </div>
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-sm info-table">
                <thead class="table-dark">
                  <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Pemilik</th>
                    <th>Status</th>
                    <th>No SPPT</th>
                    <th>Kondisi</th>
                  </tr>
                </thead>
                <tbody id="dataTableBody">
                  <!-- Data diisi lewat JS -->
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script src="script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  const map = document.getElementById("mapZoom");

  let scale = 1;
  let translateX = 0;
  let translateY = 0;

  let isDragging = false;
  let startX = 0;
  let startY = 0;

  // ======================
  // DESKTOP (MOUSE)
  // ======================
  map.addEventListener("wheel", (e) => {
    e.preventDefault();

    const zoomSpeed = 0.1;
    scale += e.deltaY < 0 ? zoomSpeed : -zoomSpeed;
    scale = Math.min(Math.max(1, scale), 4);

    applyTransform();
  });

  map.addEventListener("mousedown", (e) => {
    isDragging = true;
    startX = e.clientX - translateX;
    startY = e.clientY - translateY;
  });

  window.addEventListener("mouseup", () => {
    isDragging = false;
  });

  window.addEventListener("mousemove", (e) => {
    if (!isDragging) return;

    translateX = e.clientX - startX;
    translateY = e.clientY - startY;
    applyTransform();
  });

  // ======================
  // MOBILE (TOUCH)
  // ======================
  let lastTouchDistance = 0;

  map.addEventListener("touchstart", (e) => {
    if (e.touches.length === 1) {
      // PAN (1 jari)
      isDragging = true;
      startX = e.touches[0].clientX - translateX;
      startY = e.touches[0].clientY - translateY;
    }

    if (e.touches.length === 2) {
      // PINCH ZOOM (2 jari)
      lastTouchDistance = getTouchDistance(e.touches);
    }
  });

  map.addEventListener("touchmove", (e) => {
    e.preventDefault();

    if (e.touches.length === 1 && isDragging) {
      // PAN
      translateX = e.touches[0].clientX - startX;
      translateY = e.touches[0].clientY - startY;
      applyTransform();
    }

    if (e.touches.length === 2) {
      // PINCH ZOOM
      const currentDistance = getTouchDistance(e.touches);
      const zoomFactor = (currentDistance - lastTouchDistance) * 0.005;

      scale += zoomFactor;
      scale = Math.min(Math.max(1, scale), 4);

      lastTouchDistance = currentDistance;
      applyTransform();
    }
  });

  map.addEventListener("touchend", () => {
    isDragging = false;
  });

  // ======================
  // HELPER
  // ======================
  function getTouchDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx * dx + dy * dy);
  }

  function applyTransform() {
    map.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
  }

  function zoomIn() {
  scale = Math.min(scale + 0.2, 4);
  applyTransform();
  }

  function zoomOut() {
    scale = Math.max(scale - 0.2, 1);
    applyTransform();
  }

  function resetZoom() {
    scale = 1;
    translateX = 0;
    translateY = 0;
    applyTransform();
  }
</script>


</body>
</html>
