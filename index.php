<?php
  session_start();

  // Retrieve user data from session
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
            <a href="logout.php" class="text-gray-700">Logout</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <?php if ($user): ?>
      <h2 class="text-xl font-semibold mb-4">Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
    <?php else: ?>
      <h2 class="text-xl font-semibold mb-4">Please log in to view videos.</h2>
    <?php endif; ?>

    <ul id="videoList" class="space-y-4"></ul>
  </main>

  <script src="assets/js/videoConfig.js"></script>
  <script>
    // Fetch and display all videos from backend
    async function fetchVideos() {
      const response = await fetch("https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api/videos");
      const videos = await response.json();
      const videoList = document.getElementById("videoList");

      videos.forEach(video => {
        const videoItem = document.createElement("li");
        videoItem.classList.add("border", "p-4", "rounded", "shadow", "flex", "items-center");
        videoItem.innerHTML = `
          <h3 class="text-lg font-semibold">${video.title}</h3>
          <p class="text-gray-500">${video.description}</p>
          <a href="watch.php?id=${video.id}" class="text-blue-600">Watch</a>
        `;
        videoList.appendChild(videoItem);
      });
    }

    fetchVideos();
  </script>
</body>
</html>
