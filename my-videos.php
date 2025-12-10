<?php
// my-videos.php
require_once __DIR__ . '/auth.php';

// Get user from session (set in auth.php if Azure auth headers exist)
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Videos | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <!-- Header / Navigation -->
  <header class="bg-white shadow">
    <div class="max-w-4xl mx-auto py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Cloud Video Share</h1>
      <nav class="space-x-4 flex items-center">
        <a href="index.php" class="text-gray-700">Home</a>
        <a href="my-videos.php" class="text-blue-600 font-semibold">My Videos</a>
        <a href="upload.php" class="text-gray-700">Upload Video</a>
        <?php if ($user): ?>
          <span class="text-gray-500">Hello, <?= htmlspecialchars($user['name']) ?></span>
          <a href="logout.php" class="text-gray-700 ml-2">Logout</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <?php if (!$user): ?>
      <p class="text-red-600 text-lg">You must be logged in to see your videos.</p>
    <?php else: ?>
      <h2 class="text-xl font-semibold mb-2">
        Your Videos
      </h2>
      <p class="text-sm text-gray-500 mb-4">
        Showing videos uploaded by <?= htmlspecialchars($user['name']) ?>.
      </p>

      <ul id="videoList" class="space-y-4"></ul>

      <p id="status" class="text-sm text-gray-500 mt-4"></p>
    <?php endif; ?>
  </main>

<?php if ($user): ?>
<script>
  const API_BASE_URL =
    "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";
  const CURRENT_USER_ID = <?= json_encode($user['userId']); ?>;

  const statusEl = document.getElementById("status");
  const videoListEl = document.getElementById("videoList");

  async function loadMyVideos() {
    statusEl.textContent = "Loading your videos...";

    try {
      const res = await fetch(`${API_BASE_URL}/users/${encodeURIComponent(CURRENT_USER_ID)}/videos`);
      if (!res.ok) {
        statusEl.textContent = "Failed to load your videos.";
        console.error("Error fetching user videos:", res.status);
        return;
      }

      const videos = await res.json();
      videoListEl.innerHTML = "";

      if (!videos.length) {
        statusEl.textContent = "You have not uploaded any videos yet.";
        return;
      }

      statusEl.textContent = "";

      videos.forEach(video => {
        const li = document.createElement("li");
        li.className = "border rounded shadow bg-white p-4 flex flex-col gap-2";

        li.innerHTML = `
          <div class="flex justify-between items-start gap-4">
            <div>
              <h3 class="text-lg font-semibold">${video.title}</h3>
              <p class="text-gray-500 text-sm">
                ${video.description ? video.description : ""}
              </p>
              <p class="text-xs text-gray-400 mt-1">
                Uploaded: ${video.createdAt ? new Date(video.createdAt).toLocaleString() : "N/A"}
              </p>
              <p class="text-xs text-gray-400">
                Views: ${video.views ?? 0}
              </p>
            </div>
            <div class="flex flex-col gap-2">
              <a href="watch.php?id=${video.id}"
                 class="text-sm bg-blue-600 text-white px-3 py-1 rounded text-center">
                 Watch
              </a>
              <button
                class="text-sm bg-red-600 text-white px-3 py-1 rounded delete-btn"
                data-id="${video.id}">
                Delete
              </button>
            </div>
          </div>
        `;

        videoListEl.appendChild(li);
      });
    } catch (err) {
      console.error("Error loading user videos:", err);
      statusEl.textContent = "Error loading your videos.";
    }
  }

  // Event delegation for delete buttons
  videoListEl.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("delete-btn")) return;

    const id = e.target.getAttribute("data-id");
    if (!id) return;

    const confirmDelete = confirm("Are you sure you want to delete this video?");
    if (!confirmDelete) return;

    try {
      const res = await fetch(`${API_BASE_URL}/videos/${encodeURIComponent(id)}`, {
        method: "DELETE"
      });

      if (!res.ok) {
        alert("Failed to delete video.");
        console.error("Delete failed", res.status);
        return;
      }

      const data = await res.json();
      console.log("Deleted:", data);
      // Reload list
      await loadMyVideos();
    } catch (err) {
      console.error("Error deleting video:", err);
      alert("Error deleting video.");
    }
  });

  loadMyVideos();
</script>
<?php endif; ?>
</body>
</html>
