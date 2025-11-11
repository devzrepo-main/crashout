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
      color: #ff0000;
      font-family: 'Courier New', monospace;
      text-align: center;
      padding-top: 50px;
    }
    h1 {
      margin-bottom: 40px;
      color: #ff0000;
    }
    .btn {
      margin: 10px;
      font-weight: bold;
      border-radius: 12px;
    }
    .counter {
      font-size: 1.3rem;
      margin-top: 8px;
      color: #ff0000;
    }
    .totals-title {
      color: #ff0000;
      font-size: 1.8rem;
      margin-top: 50px;
      margin-bottom: 20px;
      text-transform: uppercase;
      font-weight: bold;
    }
    .modal-content {
      background-color: #111;
      color: #ff0000;
      border: 1px solid #ff0000;
    }
    .form-control {
      background-color: #111;
      color: #ff0000;
      border: 1px solid #ff0000;
    }
    .form-control:focus {
      box-shadow: 0 0 5px #ff0000;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔥 Crashout Counter 🔥</h1>

    <div class="d-grid gap-2 col-6 mx-auto">
      <button class="btn btn-danger" onclick="addCrashout('sports')">Yelling about Sports</button>
      <button class="btn btn-danger" onclick="addCrashout('gaming')">Yelling about a Video Game</button>
      <button class="btn btn-danger" onclick="addCrashout('minorities')">Yelling about Minorities</button>
      <button class="btn btn-danger" onclick="addCrashout('delivery')">Yelling about Doordash Delivery</button>
      <button class="btn btn-danger" onclick="addCrashout('technology')">Yelling about Technology</button>
      <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#otherModal">Add Other</button>
    </div>

    <div id="stats" class="mt-5">
      <div class="totals-title">Crashout Totals</div>
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

  <!-- 🟥 Other Reason Modal -->
  <div class="modal fade" id="otherModal" tabindex="-1" aria-labelledby="otherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="otherModalLabel">Add Other Crashout Reason</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="otherReasonInput" class="form-control" placeholder="Enter other reason...">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" onclick="submitOther()">Add Other</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  // 🔄 Fetch and update counter totals
  function loadCounters() {
    fetch('api.php?action=stats')
      .then(res => res.json())
      .then(data => {
        document.getElementById('sports-count').textContent = data.sports || 0;
        document.getElementById('video-count').textContent = data.gaming || 0;
        document.getElementById('minorities-count').textContent = data.minorities || 0;
        document.getElementById('doordash-count').textContent = data.delivery || 0;
        document.getElementById('technology-count').textContent = data.technology || 0;
        document.getElementById('other-count').textContent = data.other || 0;
      })
      .catch(err => console.error('Error loading counters:', err));
  }

  // 💥 Add a new crashout entry
  function addCrashout(category, reason = '') {
    fetch('api.php?action=add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ category, reason })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        loadCounters();
      } else {
        alert('Failed to add event.');
      }
    })
    .catch(err => console.error('Error adding event:', err));
  }

  // 💥 Handle the Other reason modal submission
  function submitOther() {
    const reason = document.getElementById('otherReasonInput').value.trim();
    if (reason === '') {
      alert('Please enter a reason.');
      return;
    }

    // Add the event
    addCrashout('other', reason);

    // Reset input and close modal
    document.getElementById('otherReasonInput').value = '';
    const modal = bootstrap.Modal.getInstance(document.getElementById('otherModal'));
    modal.hide();
  }

  // Load totals when page opens
  document.addEventListener('DOMContentLoaded', loadCounters);
  </script>
</body>
</html>
