<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crashout Counter</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #111;
      color: #fff;
      text-align: center;
      padding-top: 40px;
    }
    h1, h2 {
      color: #ff3333;
    }
    .btn-crash {
      background-color: #ff3333;
      color: white;
      border: none;
      margin: 8px;
      padding: 12px 20px;
      font-size: 1.2em;
      border-radius: 8px;
    }
    .btn-crash:hover {
      background-color: #cc0000;
    }
    .totals {
      margin-top: 40px;
      padding: 20px;
      border: 2px solid #ff3333;
      border-radius: 10px;
      display: inline-block;
    }
    #totalCount {
      font-size: 1.5em;
      color: #ff3333;
    }
    .btn-clear {
      background-color: #444;
      color: #fff;
      margin-top: 10px;
    }
    .btn-clear:hover {
      background-color: #666;
    }
  </style>
</head>
<body>
  <h1>🔥 Crashout Tracker 🔥</h1>

  <!-- 🔹 Crashout Category Buttons -->
  <div id="buttons">
    <button class="btn-crash" onclick="addCrashout('sports')">Yelling About Sports</button>
    <button class="btn-crash" onclick="addCrashout('gaming')">Yelling About Video Games</button>
    <button class="btn-crash" onclick="addCrashout('minorities')">Yelling About Minorities</button>
    <button class="btn-crash" onclick="addCrashout('delivery')">Yelling About DoorDash</button>

    <!-- 🆕 Moved Music ABOVE Other -->
    <button class="btn-crash" onclick="addCrashout('music')">Yelling About Music</button>

    <hr style="border-color:#ff3333; width:60%; margin:20px auto;">

    <!-- 🔹 "Other" Section Trigger -->
    <button class="btn-crash" onclick="openOtherModal()">Other Crashout</button>
  </div>

  <!-- 🔹 Totals Display -->
  <div class="totals mt-4">
    <h2>Crashout Totals</h2>
    <p id="totalCount">Loading...</p>
    <button class="btn btn-clear" onclick="clearAll()">Clear All Crashouts</button>
  </div>

  <!-- 🔹 Modal for "Other" -->
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
        let total = 0;
        for (const count of Object.values(data)) {
          total += parseInt(count);
        }
        document.getElementById('totalCount').innerText = `Total Crashouts: ${total}`;
      } catch (err) {
        console.error('Failed to load totals:', err);
        document.getElementById('totalCount').innerText = 'Error loading totals.';
      }
    }

    async function clearAll() {
      if (!confirm('Clear all crashouts?')) return;
      try {
        const res = await fetch('./api.php?action=clear', {method: 'POST'});
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

    // Initialize totals on load
    document.addEventListener('DOMContentLoaded', loadTotals);
  </script>
</body>
</html>
