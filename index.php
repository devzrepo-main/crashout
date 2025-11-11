<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Crashout Counter</title>
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
      border: 2px solid var(--cra
