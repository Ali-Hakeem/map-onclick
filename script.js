document.addEventListener("DOMContentLoaded", () => {
  const infoPanel = document.getElementById("infoPanel");
  const areas = document.querySelectorAll("svg path");
  const tableBody = document.getElementById("dataTableBody");
  const blokButtons = document.querySelectorAll("#blokFilter button");

  let areaData = {};
  let currentBlok = "1";

  /* =============================
     FUNGSI PUSAT
  ============================== */
  function activateArea(key) {
    const data = areaData[key];
    if (!data) {
      console.warn("Data tidak ditemukan untuk:", key);
      return;
    }

    // SVG aktif
    areas.forEach((a) => a.classList.remove("active"));
    const targetPath = document.querySelector(`path[data-area="${key}"]`);
    if (targetPath) targetPath.classList.add("active");

    // tabel aktif
    document
      .querySelectorAll("tr[data-id]")
      .forEach((tr) => tr.classList.remove("active"));
    const row = document.querySelector(`tr[data-id="${key}"]`);
    if (row) row.classList.add("active");

    // detail
    infoPanel.innerHTML = `
      <img src="${data.image}"
           alt="${data.title}"
           style="height:200px;max-width:auto;margin:10px 0;">
      <h4><span class="badge bg-danger">${data.title}</span></h4>
      <p class="fw-bold"><i class="fa-solid fa-map-location-dot me-2 text-orange"></i> ${data.alamat}</p>

      <div class="mt-2 d-flex flex-wrap gap-3">
        <!-- pemilik -->
        <div class="px-3 py-3 border rounded justify-content-between align-items-center w-100">
          <p class="mb-0 text-secondary">PEMILIK</p>
          <i class="fa-solid fa-person text-info"></i> <span class="fw-bold text-dark mx-1">${data.pemilik}</span>
        </div>
      </div>
      <div class="row g-3 mt-1">
        <!-- STATUS -->
        <div class="col-md-4 col-12">
          <div class="px-3 py-3 border rounded h-100">
            <p class="mb-0 text-secondary">KETERANGAN</p>
            <i class="fa-regular fa-newspaper text-warning"></i> <span class="fw-bold text-dark mx-1" style="font-size: 14px;">${data.status}</span>
          </div>
        </div>

        <!-- SPPT -->
        <div class="col-md-4 col-12">
          <div class="px-3 py-3 border rounded h-100">
            <p class="mb-0 text-secondary">NO. SPPT</p>
            <i class="fa-solid fa-file-invoice text-primary"></i> <span class="fw-bold text-dark mx-1" style="font-size: 14px;">${data.no_sppt}</span>
          </div>
        </div>

        <!-- KONDISI -->
        <div class="col-md-4 col-12">
          <div class="px-3 py-3 border rounded h-100">
            <p class="mb-0 text-secondary">FUNGSI</p>
            <i class="fa-solid fa-house-chimney-user text-success"></i> <span class="fw-bold text-dark mx-1" style="font-size: 14px;">${data.kondisi}</span>
          </div>
        </div>
      </div>
    `;
  }

  /* =============================
     LOAD JSON + ISI TABEL
  ============================== */
  fetch("data.json")
    .then((res) => res.json())
    .then((data) => {
      areaData = data;
      renderTable();
    })
    .catch((err) => console.error("Gagal load JSON:", err));

  function renderTable() {
    let rows = "";

    Object.keys(areaData).forEach((key) => {
      const item = areaData[key];

      rows += `
        <tr data-id="${key}" style="font-size:11px; ${
        item.blok == currentBlok ? "" : "display:none"
      }">
          <td><strong>${key}</strong></td>
          <td>${item.title}</td>
          <td>${item.pemilik}</td>
          <td>${item.status}</td>
          <td>${item.no_sppt}</td>
          <td>${item.kondisi}</td>
        </tr>
      `;
    });

    tableBody.innerHTML = rows;
  }

  /* =============================
     KLIK SVG
  ============================== */
  areas.forEach((area) => {
    area.addEventListener("click", () => {
      activateArea(area.dataset.area);
    });
  });

  /* =============================
     KLIK TABEL
  ============================== */
  document.addEventListener("click", (e) => {
    const row = e.target.closest("tr[data-id]");
    if (!row) return;

    activateArea(row.dataset.id);
  });

  /* =============================
     FILTER BLOK (TABEL SAJA)
  ============================== */
  blokButtons.forEach((btn) => {
    btn.addEventListener("click", () => {
      currentBlok = btn.dataset.blok;

      // tombol aktif
      blokButtons.forEach((b) => {
        b.classList.remove("btn-primary", "active");
        b.classList.add("btn-outline-primary");
      });

      btn.classList.remove("btn-outline-primary");
      btn.classList.add("btn-primary", "active");

      renderTable();
    });
  });

  /* =============================
     LABEL SVG
  ============================== */
  const svg = document.querySelector("svg");
  if (svg) {
    svg.querySelectorAll("path[data-area]").forEach((path) => {
      const bbox = path.getBBox();
      const text = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "text"
      );

      text.setAttribute("x", bbox.x + bbox.width / 2);
      text.setAttribute("y", bbox.y + bbox.height / 2);
      text.setAttribute("text-anchor", "middle");
      text.setAttribute("dominant-baseline", "middle");
      text.setAttribute("class", "map-label");
      text.textContent = path.dataset.label;

      svg.appendChild(text);
    });
  }
});
