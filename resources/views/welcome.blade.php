<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>CheeseCake</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&amp;display=swap"
    rel="stylesheet" />
  <style>
    body {
      font-family: "Montserrat", sans-serif;
    }
  </style>
  @vite(['resources/js/app.js'])
</head>

<body>
  <div id="app"></div>
</body>

</html>