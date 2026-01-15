<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - CDA</title>
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <div class="sidebar" style="background-color: darkgreen;">
    <h2>CDA Admin</h2>
    <a href="admin_dashboard.php">🏠 Dashboard</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="sectors.php">📍 Sectors</a>
    <a href="zones.php">🌍 Zones</a>
    <a href="map.php">🗺️ Markaz</a>
    <a href="logout.php" style="color:red;">🚪 Logout</a>
  </div>

  <div class="main">
    <div class="header">
      <h1>Welcome Admin <?php echo $_SESSION['username']; ?> 🎉</h1>
    </div>
    <div class="content">
      <p>You can manage users, zones, sectors, and markaz from here.</p>
    </div>
  </div>
</body>
</html>
