<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
  }
  $user = $_SESSION['user'];
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
        <a href="javascript:void(0)" onclick="logout()" class="text-gray-700">Logout</a>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Welcome, <?php echo $user['username']; ?></h2>
    <ul id="videoList" class="space-y-4"></ul>
  </main>

  <script src="assets/js/config.js"></script>
  <script src="assets/js/videoConfig.js"></script>
  <script>
    async function fetchVideos() {
      const videos = await fetchVideos();
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
