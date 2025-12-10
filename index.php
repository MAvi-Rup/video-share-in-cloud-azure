<?php
require_once __DIR__ . '/auth.php';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Home | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <header class="bg-white shadow">
    <div class="max-w-4xl mx-auto py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Cloud Video Share</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-gray-700">Home</a>
        <a href="my-videos.php" class="text-gray-700">My Videos</a>
        <a href="upload.php" class="text-gray-700">Upload Video</a>
        <?php if ($user): ?>
          <span class="text-gray-500">Hello, <?= htmlspecialchars($user['name']) ?></span>
          <a href="logout.php" class="text-gray-700">Logout</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <?php if ($user): ?>
      <h2 class="text-xl font-semibold mb-4">Welcome, <?= htmlspecialchars($user['name']) ?></h2>
    <?php else: ?>
      <h2 class="text-xl font-semibold mb-4">You are not logged in.</h2>
    <?php endif; ?>

    <ul id="videoList" class="space-y-4"></ul>
  </main>

  <script>
    const API_BASE_URL =
      "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";

    async function loadVideos() {
      try {
        const res = await fetch(`${API_BASE_URL}/videos`);
        if (!res.ok) {
          console.error("Failed to fetch videos", res.status);
          return;
        }
        const videos = await res.json();
        const videoList = document.getElementById("videoList");

        videos.forEach(video => {
          const li = document.createElement("li");
          li.className = "border p-4 rounded shadow";
          li.innerHTML = `
            <h3 class="text-lg font-semibold">${video.title}</h3>
            <p class="text-gray-500">${video.description ?? ""}</p>
            <a href="watch.php?id=${video.id}" class="text-blue-600">Watch</a>
          `;
          videoList.appendChild(li);
        });
      } catch (e) {
        console.error("Error loading videos", e);
      }
    }

    loadVideos();
  </script>
</body>
</html>
