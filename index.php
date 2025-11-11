<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Alex's Crashout Counter</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --crash-red: #b30000;
      --crash-red-dark: #800000;
    }

    body {
      background-color: #111;
      color: var(--crash-red);
      text-align: center;
      padding: 30px 10px;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
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

    .modal-content {
      background-color: #000;
      border: 2px solid var(--crash-red);
      color: var(--crash-red);
    }
    .modal-header, .modal-footer { border-color: var(--crash-red); }
    .modal-title { color: var(--crash-red); }
    .form-control {
      background-color: #111;
      color: var(--crash-red);
      border: 1px solid var(--crash-red);
    }
    .form-control::placeholder { color: #ff6666; }
    .btn-danger, .btn-secondary {
      background-color: var(--crash-red) !important;
      color: white !important;
      border: none !important;
    }
    .btn-danger:hover, .btn-secondary:hover {
      background-color: var(--crash-red-dark) !important;
    }
    hr { border: 1px solid var(--crash-red); }

    /* 🔐 Password Gate Overlay */
    #passwordOverlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 9999;
      background-color: rgba(0, 0, 0, 0.9);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      transition: opacity 1s ease;
      padding: 20px;
    }
    #passwordOverlay.fadeOut {
      opacity: 0;
      pointer-events: none;
    }

    /* Background image now centered and scaled */
    #passwordBackground {
      width: auto;
      height: 65vh; /* about 65% of screen height */
      max-width: 90vw;
      object-fit: contain;
      border: 2px solid var(--crash-red);
      border-radius: 10px;
      opacity: 0.7;
      margin-bottom: 20px;
    }

    /* Centered prompt box */
    #passwordPrompt {
      position: relative;
      background: rgba(0, 0, 0, 0.75);
      border: 2px solid var(--crash-red);
      border-radius: 10px;
      padding: 25px 30px;
      text-align: center;
      max-width: 400px;
      width: 90%;
      color: var(--crash-red);
    }

    #passwordPrompt h2 {
      color: var(--crash-red);
      margin-bottom: 15px;
    }

    #passwordInput {
      background-color: #111;
      color: var(--crash-red);
      border: 1px solid var(--crash-red);
      padding: 10px;
      width: 80%;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 10px;
    }

    #passwordSubmit {
      background-color: var(--crash-red);
      color: white;
      border: none;
      padding: 10px 25px;
      border-radius: 6px;
      font-weight: bold;
    }

    #passwordSubmit:hover {
      background-color: var(--crash-red-dark);
    }
  </style>
</head>
<body>
  <!-- 🔐 Password Overlay -->
  <div id="passwordOverlay">
    <img id="passwordBackground" src="1000039571.jpg" alt="Background Image">
    <div id="passwordPrompt">
      <h2>Mel Gibson's Famous Quote:</h2>
      <input type="password" id="passwordInput" placeholder="Enter password..." 
             onkeydown="if(event.key==='Enter'){checkPassword();}">
      <br>
      <button id="passwordSubmit" onclick="checkPassword()">Submit</button>
    </div>
  </div>

  <h1>🔥 Crashout Tracker 🔥</h1>

  <!-- Crashout Buttons -->
  <button class="btn-crash" onclick="addCrashout('sports')">Yelling About Sports</button>
  <button class="btn-crash" onclick="addCrashout('gaming')">Yelling About Gaming</button>
  <button class="btn-crash" onclick="addCrashout('minorities')">Yelling About Minorities</button>
  <button class="btn-crash" onclick="addCrashout('delivery')">Yelling About Delivery</button>
  <button class="btn-crash" onclick="addCrashout('music')">Yelling About Music</button>
  <button class="btn-crash" onclick="addCrashout('women')">Yelling About Women</button>
  <button class="btn-crash" onclick="openOtherModal()">Other Crashout</button>

  <!-- Totals Section -->
  <div class="totals mt-4">
    <h2>Crashout Totals</h2>
    <div id="totalsContainer">
      <p>Loading totals...</p>
    </div>
    <button class="btn btn-clear" onclick="clearAll()">Clear All Crashouts</button>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="otherModal" tabindex="-1" aria-labelledby="otherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="otherModalLabel">Add Other Crashout</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
    function checkPassword() {
      const input = document.getElementById('passwordInput').value.trim().toLowerCase();
      const overlay = document.getElementById('passwordOverlay');
      if (input === 'soon') {
        overlay.classList.add('fadeOut');
        setTimeout(() => overlay.style.display = 'none', 1000);
      } else {
        alert('Incorrect password. Try again.');
      }
    }

    const categories = ['sports', 'gaming', 'minorities', 'delivery', 'music', 'women', 'other'];

    async function addCrashout(category, reason = '') {
      try {
        const res = await fetch('./api.php?action=add', {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `category=${encodeURIComponent(category)}&reason=${encodeURIComponent(reason)}`
        });
        const data = await res.json();
        if (data.success) loadTotals();
        else alert('Failed to add crashout.');
      } catch { alert('Server error.'); }
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
      } catch {
        document.getElementById('totalsContainer').innerHTML = '<p>Error loading totals.</p>';
      }
    }

    async function clearAll() {
      if (!confirm('Clear all crashouts?')) return;
      try {
        const res = await fetch('./api.php?action=clear', { method: 'POST' });
        const data = await res.json();
        if (data.success) loadTotals();
        else alert('Failed to clear crashouts.');
      } catch { alert('Error clearing crashouts.'); }
    }

    function openOtherModal() {
      new bootstrap.Modal(document.getElementById('otherModal')).show();
    }

    function submitOther() {
      const reason = document.getElementById('otherReason').value.trim();
      if (!reason) { alert('Please enter a reason.'); return; }
      addCrashout('other', reason);
      bootstrap.Modal.getInstance(document.getElementById('otherModal')).hide();
      document.getElementById('otherReason').value = '';
    }

    document.addEventListener('DOMContentLoaded', loadTotals);
  </script>
</body>
</html>
