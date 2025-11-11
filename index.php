<?php
// Simple front page; uses Bootstrap from CDN
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Crashout Counter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  />
  <link rel="stylesheet" href="style.css" />
</head>
<body class="bg-dark text-light">
  <div class="container py-4">
    <h1 class="display-5 mb-3 text-center">😤 Crashout Counter 😤</h1>
    <p class="text-center text-secondary mb-4">
      Track those classic blowups: Sports, Gaming, Delivery, Minorities... log a custom reason.
    </p>

    <!-- Totals -->
    <div class="row g-3 mb-4" id="totalsRow">
      <div class="col-6 col-md-3">
        <div class="card metric">
          <div class="card-body text-center">
            <div class="metric-label">Sports</div>
            <div class="metric-value" id="count-sports">0</div>
            <button class="btn btn-sm btn-outline-light mt-2 w-100" data-cat="sports">Sports Crashout</button>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card metric">
          <div class="card-body text-center">
            <div class="metric-label">Gaming</div>
            <div class="metric-value" id="count-gaming">0</div>
            <button class="btn btn-sm btn-outline-light mt-2 w-100" data-cat="gaming">Gaming Crashout</button>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card metric">
          <div class="card-body text-center">
            <div class="metric-label">Delivery</div>
            <div class="metric-value" id="count-delivery">0</div>
            <button class="btn btn-sm btn-outline-light mt-2 w-100" data-cat="delivery">Delivery Crashout</button>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card metric">
          <div class="card-body text-center">
            <div class="metric-label">Minorities</div>
            <div class="metric-value" id="count-minorities">0</div>
            <button class="btn btn-sm btn-outline-light mt-2 w-100" data-cat="minorities">Minorities Crashout</button>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card metric">
          <div class="card-body text-center">
            <div class="metric-label">Other</div>
            <div class="metric-value" id="count-other">0</div>
            <button class="btn btn-sm btn-outline-light mt-2 w-100" data-cat="other">Other Crashout</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Other reason input -->
    <div class="card mb-4">
      <div class="card-body">
        <label for="otherReason" class="form-label">Other reason (optional)</label>
        <div class="input-group">
          <input id="otherReason" class="form-control" maxlength="255" placeholder="e.g., Neighbor complaining about noise etc..." />
          <button id="addOtherBtn" class="btn btn-primary">Add Other</button>
        </div>
        <div id="otherHelp" class="form-text text-secondary mt-1">If left blank, “Other” will still be logged.</div>
      </div>
    </div>

    <!-- Recent log -->
    <div class="card">
      <div class="card-header">Recent Crashouts</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-dark table-striped table-hover mb-0">
            <thead>
              <tr>
                <th style="width: 120px;">When</th>
                <th style="width: 120px;">Category</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody id="logTbody">
              <tr><td colspan="3" class="text-center text-secondary py-3">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <p class="text-center mt-4 text-secondary small">
      Crashout Shitstorm V 1.0 Build Number: Fuck You.
    </p>
  </div>

  <script>
    const btns = document.querySelectorAll('button[data-cat]');
    const otherBtn = document.getElementById('addOtherBtn');
    const otherInput = document.getElementById('otherReason');

    async function api(path = '', opts = {}) {
      const res = await fetch('api.php' + path, {
        headers: { 'Accept': 'application/json' },
        ...opts
      });
      if (!res.ok) throw new Error('Network error');
      return res.json();
    }

    function renderCounts(counts) {
      const map = {
        sports: document.getElementById('count-sports'),
        gaming: document.getElementById('count-gaming'),
        delivery: document.getElementById('count-delivery'),
        minorities: document.getElementByID('count-minorities'),
        other: document.getElementById('count-other'),
      };
      for (const k in map) map[k].textContent = counts[k] ?? 0;
    }

    function escapeHtml(s) {
      return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    function renderLog(events) {
      const tbody = document.getElementById('logTbody');
      if (!events.length) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-secondary py-3">No events yet.</td></tr>';
        return;
      }
      tbody.innerHTML = events.map(ev => `
        <tr>
          <td>${escapeHtml(ev.when)}</td>
          <td><span class="badge text-bg-secondary">${escapeHtml(ev.category)}</span></td>
          <td>${ev.detail ? escapeHtml(ev.detail) : '<span class="text-secondary">—</span>'}</td>
        </tr>
      `).join('');
    }

    async function refresh() {
      try {
        const data = await api('?action=stats');
        renderCounts(data.counts);
        renderLog(data.recent);
      } catch (e) {
        console.error(e);
      }
    }

    async function add(cat, detail='') {
      try {
        await api('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ category: cat, detail })
        });
        otherInput.value = '';
        await refresh();
      } catch (e) {
        alert('Failed to add event.');
      }
    }

    btns.forEach(b => b.addEventListener('click', () => add(b.dataset.cat)));
    otherBtn.addEventListener('click', () => add('other', otherInput.value.trim()));

    refresh();
    // Refresh every 20s so multiple viewers stay in sync
    setInterval(refresh, 20000);
  </script>
</body>
</html>
