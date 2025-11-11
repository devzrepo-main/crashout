<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crashout Counter</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #0a0a0a;
      color: #00ff00;
      font-family: 'Courier New', monospace;
      text-align: center;
      padding-top: 50px;
    }
    .btn {
      margin: 10px;
      font-weight: bold;
      border-radius: 12px;
    }
    h1 {
      margin-bottom: 40px;
      color: #0f0;
    }
    .counter {
      font-size: 1.3rem;
      margin-top: 8px;
    }
    input {
      background-color: #111;
      color: #0f0;
      border: 1px solid #0f0;
    }
    .form-control:focus {
      background-color: #111;
      color: #0f0;
      border-color: #00ff00;
      box-shadow: 0 0 5px #00ff00;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔥 Crashout Counter 🔥</h1>

    <div class="d-grid gap-2 col-6 mx-auto">
      <button class="btn btn-danger" onclick="addCrashout('Sports')">Yelling about Sports</button>
      <button class="btn btn-warning" onclick="addCrashout('Video Game')">Yelling about a Video Game</button>
      <button class="btn btn-secondary" onclick="addCrashout('Minorities')">Yelling about Minorities</button>
      <button class="btn btn-info" onclick="addCrashout('Doordash Delivery')">Yelling about Doordash Delivery</button>
      <button class="btn btn-primary" onclick="addCrashout('Technology')">Yelling about Technology</button>

      <div class="input-group mt-3">
        <input type="text" id="otherReason" class="form-control" placeholder="Other reason...">
        <button class="btn btn-success" onclick="addCrashout('Other', document.getElementById('otherReason').value)">Add Other</button>
      </div>
    </div>

    <div id="stats" class="mt-5">
      <h3>Crashout Totals</h3>
      <div class="row justify-content-center">
        <div class="col-md-3 counter">Sports: <span id="sports-count">0</span></div>
        <div class="col-md-3 counter">Video Games: <span id="video-count">0</span></div>
        <div class="col-md-3 counter">Minorities: <span id="minorities-count">0</span></div>
        <div class="col-md-3 counter">Doordash: <span id="doordash-count">0</span></div>
        <div class="col-md-3 counter">Technology: <span id="technology-count">0</span></div>
        <div class="col-md-3 counter">Other: <span id="other-count">0</span></div>
      </div>
    </div>
  </div>

  <script>
  // 🔄 Fetch and update counter totals
  function loadCounters() {
    fetch('api.php?action=stats')
      .then(res => res.json())
      .then(data => {
        document.getElementById('sports-count').textContent = data.Sports || 0;
        document.getElementById('video-count').textContent = data["Video Game"] || 0;
        document.getElementById('minorities-count').textContent = data.Minorities || 0;
        document.getElementById('doordash-count').textContent = data["Doordash Delivery"] || 0;
        document.getElementById('technology-count').textContent = data.Technology || 0;
        document.getElementById('other-count').textContent = data.Other || 0;
      })
      .catch(err => console.error('Error loading counters:', err));
  }

  // 💥 Add a new crashout entry
  function addCrashout(category, reason = '') {
    if (category === 'Other' && reason.trim() === '') {
      alert('Please enter a reason for Other.');
      return;
    }

    fetch('api.php?action=add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ category, reason })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        console.log(`Added ${category}:`, reason);
        document.getElementById('otherReason').value = '';
        loadCounters(); // Refresh totals instantly
      } else {
        alert('Failed to add event.');
      }
    })
    .catch(err => console.error('Error adding event:', err));
  }

  // Load totals when page opens
  document.addEventListener('DOMContentLoaded', loadCounters);
  </script>
</body>
</html>
