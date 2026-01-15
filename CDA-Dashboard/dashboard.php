<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="dashboard.css" />
  <title>Capital Development Authority</title>
  <style>
    /* Optional styling for typewriter */
    .typewriter {
      font-size: 28px;
      font-weight: bold;
      border-right: 2px solid black; /* blinking cursor */
      white-space: nowrap;
      overflow: hidden;
      animation: blink 0.7s infinite;
      color: white; /* make welcome text white */
      text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.6); /* attractive shadow */
      text-align: center;
    }

    @keyframes blink {
      50% { border-color: transparent; }
    }
  </style>
</head>
<body>
  <div class="sidebar" style="background-color: darkgreen;">
    <!-- Logo -->
    <div class="logo">
      <img src="CDALOGO.png" alt="CDA Logo">
    </div>

    <h2>Dashboard</h2>
    <a href="dashboard.php">Home</a>
    <a href="sectors.php">Sectors</a>
    <a href="zones.php">Zones</a>
    <a href="map.php">Markaz</a>
    <a href="plots.php">Plots</a>
  </div>

  <div class="main">
  <div class="navbar" style="background-color: darkgreen; padding: 15px; text-align:center;">
    <h1 id="typewriter" class="typewriter"></h1>
  </div>

  <div class="cards">
    <div class="card">
      <h3>Sectors</h3>
      <h4>30 Sectors</h4>
      <a href="sectors.php" class="btn">Explore</a>
    </div>

    <div class="card">
      <h3>Zones</h3>
      <h4>5 Zones</h4>
      <a href="zones.php" class="btn">Explore</a>
    </div>

    <div class="card">
      <h3>Markaz</h3>
      <h4>16 Markaz</h4>
      <a href="map.php" class="btn">Explore</a>
    </div>

    <div class="card">
      <h3>CDA Housing Schemes</h3>
      <h4>40 Housing Schemes</h4>
      <a href="Housing.php" class="btn">Explore</a>
    </div>
  </div>
</div>


  <script>
    const text = "Welcome to Capital Devel ment Authority";
    let i = 0;
    const speed = 200; 

    function typeWriter() {
      if (i < text.length) {
        document.getElementById("typewriter").textContent += text.charAt(i);
        i++;
        setTimeout(typeWriter, speed);
      }
    }

    window.onload = typeWriter;
  </script>
</body>
</html>
