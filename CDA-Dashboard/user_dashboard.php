<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - CDA</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="sidebar" style="background-color: darkgreen;">
    <h2>CDA User</h2>
    <a href="user_dashboard.php">🏠 Dashboard</a>
    <a href="sectors.php">📍 Sectors</a>
    <a href="zones.php">🌍 Zones</a>
    <a href="map.php">🗺️ Markaz</a>
    <a href="logout.php" style="color:red;">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="header">
      <h1>Welcome <?php echo $_SESSION['username']; ?> 👋</h1>
    </div>
    <div class="content">
      <p>You have limited access. Explore the sectors, zones, and markaz of Islamabad.</p>
    </div>
  </div>
</body>
</html>
