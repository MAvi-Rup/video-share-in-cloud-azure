<?php
session_start();
include("auth.php"); // Handle authentication via Azure

$user = $_SESSION['user'] ?? null;
$videoId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Watch Video | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <!-- Header / Navigation -->
  <header class="bg-white shadow">
    <div class="max-w-4xl mx-auto py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Cloud Video Share</h1>
      <nav class="space-x-4 flex items-center">
        <a href="index.php" class="text-gray-700">Home</a>
        <a href="my-videos.php" class="text-gray-700">My Videos</a>
        <a href="upload.php" class="text-gray-700">Upload Video</a>
        <?php if ($user): ?>
          <span class="text-gray-500">Hello, <?= htmlspecialchars($user['name']) ?></span>
          <a href="logout.php" class="text-gray-700 ml-2">Logout</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <?php if (!$videoId): ?>
      <p class="text-red-600 text-lg">No video selected.</p>
    <?php else: ?>
      <h2 id="videoTitle" class="text-xl font-semibold mb-4">Loading video...</h2>

      <div class="bg-white shadow rounded p-4 mb-4">
        <video id="videoPlayer" controls class="w-full max-h-[480px] bg-black rounded">
          <source id="videoSource" src="" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>

      <p id="videoDescription" class="text-sm text-gray-600 mb-2"></p>
      <p id="videoMeta" class="text-xs text-gray-400"></p>

      <p id="status" class="text-sm text-gray-500 mt-4"></p>
    <?php endif; ?>
  </main>

<?php if ($videoId): ?>
<script>
  const API_BASE_URL =
    "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";
  const VIDEO_ID = <?= json_encode($videoId); ?>;

  const titleEl = document.getElementById("videoTitle");
  const descEl = document.getElementById("videoDescription");
  const metaEl = document.getElementById("videoMeta");
  const statusEl = document.getElementById("status");
  const videoSourceEl = document.getElementById("videoSource");
  const videoPlayerEl = document.getElementById("videoPlayer");

  async function loadVideo() {
    statusEl.textContent = "Loading video details...";

    try {
      const res = await fetch(`${API_BASE_URL}/videos/${encodeURIComponent(VIDEO_ID)}`);
      if (!res.ok) {
        statusEl.textContent = "Failed to load video.";
        console.error("Error fetching video:", res.status);
        titleEl.textContent = "Video not found.";
        return;
      }

      const video = await res.json();
      console.log("Video:", video);

      titleEl.textContent = video.title || "Untitled video";
      descEl.textContent = video.description || "";
      metaEl.textContent =
        `Views: ${video.views ?? 0} · Uploaded: ${
          video.createdAt ? new Date(video.createdAt).toLocaleString() : "N/A"
        }`;

      if (video.blobUrl) {
        videoSourceEl.src = video.blobUrl; // Use the correct video URL
        videoPlayerEl.load(); // Reload the video player
      } else {
        statusEl.textContent = "No video file URL found.";
      }

      statusEl.textContent = "";
    } catch (err) {
      console.error("Error loading video:", err);
      statusEl.textContent = "Error loading video.";
      titleEl.textContent = "Video error.";
    }
  }

  loadVideo();
</script>
<?php endif; ?>
</body>
</html>
