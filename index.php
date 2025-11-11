<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crashout Counter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --crash-red: #b30000; /* single source of truth for red color */
      --crash-red-dark: #800000;
    }

    body {
      background-color: #111;
      color: var(--crash-red);
      text-align: center;
      padding: 30px 10px;
      font-family: Arial, sans-serif;
    }

    h1, h2, p, label {
      color: var(--crash-red);
      font-weight: bold;
    }

    .btn-crash {
      background-color: var(--crash-red);
      color: white;
      border: none;
      width: 90%;
      max-width: 400px;
      margin: 10px auto;
      display: block;
      padding: 15px;
      font-size: 1.2em;
      border-radius: 8px;
      transition: background-color 0.2s ease;
    }

    .btn-crash:hover,
    .btn-danger:hover,
    .btn-clear:hover {
      background-color: var(--crash-red-dark);
    }

    .totals {
      margin-top: 40px;
      padding: 20px;
      border: 2px solid var(--crash-red);
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      margin-left: auto;
      margin-right: auto;
      background-color: #1a1a1a;
    }

    .btn-clear {
      background-color: var(--crash-red);
      color: white;
      width: 90%;
      max-width: 400px;
      margin: 20px auto 0;
      display: block;
      padding: 12px;
      border-radius: 8px;
      border: none;
      font-weight: bold;
    }

    .btn-danger {
      background-color: var(--crash-red) !important;
      border: none !important;
      color: white !important;
    }

    hr {
      border: 1px solid var(--crash-red);
    }
  </style>
</head>
<body>
  <h1>🔥 Crashout Tracker 🔥</h1>

  <!-- Crashout Buttons -->
  <button class="btn-crash" onclick="addCrashout('sports')">Yelling About Sports</button>
  <button class="btn-crash" onclick="addCrashout('gaming')">Yelling About Video Games</button>
  <button class="btn-crash" onclick="addCrashout('minorities')">Yelling About Minorities</button>
  <button class="btn-crash" onclick="addCrashout('delivery')">Yelling About DoorDash</button>
  <button class="btn-crash" onclick="addCrashout('music')">Yelling About Music</button>
  <button class="btn-crash" onclick="openOtherModal()">Other Crashout</button>

  <!-- Totals -->
  <div class="totals mt-4">
    <h2>Crashout Totals</h2>
    <div id="totalsContainer">
      <p>Loading totals...</p>
    </div>
    <button class="btn btn-clear" onclick="clearAll()">Clear All Crashouts</button>
  </div>

  <!-- Modal for Other -->
  <div class="modal fade" id="otherModal" tabindex="-1" aria-labelledby="otherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-dark">
        <div class="modal-header">
          <h5 class="modal-title" id="otherModalLabel">Add Other Crashout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="otherReason" class="form-control" placeholder="Enter reason..." />
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-danger" onclick="submitOther()">Add Other</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const categories = ['sports', 'gaming', 'minorities', 'delivery', 'music', 'other'];

    async function addCrashout(category, reason = '') {
      try {
        const res = await fetch('./api.php?action=add', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `category=${encodeURIComponent(category)}&reason=${encodeURIComponent(reason)}`
        });
        const data = await res.json();
        if (data.success) {
          loadTotals();
        } else {
          console.error('Add failed', data);
          alert('Failed to add crashout.');
        }
      } catch (err) {
        console.error('Error:', err);
        alert('Server error.');
      }
    }

    async function loadTotals() {
      try {
        const res = await fetch('./api.php?action=stats');
        const data = await res.json();
        const totalsDiv = document.getElementById('totalsContainer');
        totalsDiv.innerHTML = '';

        let grandTotal = 0;
        for (const cat of categories) {
          const count = parseInt(data[cat] || 0);
          grandTotal += count;
          const label = cat.charAt(0).toUpperCase() + cat.slice(1);
          const p = document.createElement('p');
          p.innerHTML = `${label}: <strong>${count}</strong>`;
          totalsDiv.appendChild(p);
        }

        const totalP = document.createElement('p');
        totalP.innerHTML = `<hr>Total: <strong>${grandTotal}</strong>`;
        totalsDiv.appendChild(totalP);
      } catch (err) {
        console.error('Failed to load totals:', err);
        document.getElementById('totalsContainer').innerHTML = '<p>Error loading totals.</p>';
      }
    }

    async function clearAll() {
      if (!confirm('Clear all crashouts?')) return;
      try {
        const res = await fetch('./api.php?action=clear', { method: 'POST' });
        const data = await res.json();
        if (data.success) {
          loadTotals();
        } else {
          alert('Failed to clear crashouts.');
        }
      } catch (err) {
        console.error(err);
        alert('Error clearing crashouts.');
      }
    }

    function openOtherModal() {
      const modal = new bootstrap.Modal(document.getElementById('otherModal'));
      modal.show();
    }

    function submitOther() {
      const reason = document.getElementById('otherReason').value.trim();
      if (reason === '') {
        alert('Please enter a reason.');
        return;
      }
      addCrashout('other', reason);
      const modal = bootstrap.Modal.getInstance(document.getElementById('otherModal'));
      modal.hide();
      document.getElementById('otherReason').value = '';
    }

    document.addEventListener('DOMContentLoaded', loadTotals);
  </script>
</body>
</html>
