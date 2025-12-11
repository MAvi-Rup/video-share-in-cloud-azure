<?php
require_once __DIR__ . '/auth.php';
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Upload Video | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <header class="bg-white shadow">
    <div class="max-w-4xl mx-auto py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Cloud Video Share</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-gray-700">Home</a>
        <a href="my-videos.php" class="text-gray-700">My Videos</a>
        <a href="upload.php" class="text-blue-600">Upload Video</a>
       
      </nav>
    </div>
  </header>

  <main class="max-w-2xl mx-auto py-8">
    <?php if (!$user): ?>
      <p class="text-red-600">You must be logged in to upload videos.</p>
    <?php else: ?>
      <h2 class="text-xl font-semibold mb-4">Upload a New Video</h2>

      <form id="uploadForm" class="bg-white shadow rounded p-4 flex flex-col gap-3">
        <label class="text-sm font-medium">Title</label>
        <input id="title" class="border rounded px-3 py-2" required />

        <label class="text-sm font-medium">Description</label>
        <textarea id="description" class="border rounded px-3 py-2"></textarea>

        <label class="text-sm font-medium">Video File</label>
        <input id="file" type="file" accept="video/*" class="border rounded px-3 py-2" required />

        <button class="bg-blue-600 text-white px-4 py-2 rounded self-start">
          Upload
        </button>

        <p id="status" class="text-sm mt-2 text-gray-600"></p>
      </form>
    <?php endif; ?>
  </main>

  <?php if ($user): ?>
  <script>
    const API_BASE_URL =
      "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";
    const CURRENT_USER_ID = <?= json_encode($user['userId']); ?>;

    document.getElementById("uploadForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const statusEl = document.getElementById("status");
      statusEl.textContent = "Uploading...";

      const title = document.getElementById("title").value;
      const description = document.getElementById("description").value;
      const file = document.getElementById("file").files[0];

      if (!file) {
        statusEl.textContent = "Please choose a video file.";
        return;
      }

      const formData = new FormData();
      formData.append("title", title);
      formData.append("description", description);
      formData.append("userId", CURRENT_USER_ID);
      formData.append("file", file);

      try {
        const res = await fetch(`${API_BASE_URL}/videos`, {
          method: "POST",
          body: formData,
        });

        if (!res.ok) {
          const errText = await res.text();
          console.error("Upload failed", res.status, errText);
          statusEl.textContent = "Upload failed.";
          return;
        }

        const data = await res.json();
        console.log("Uploaded:", data);
        statusEl.textContent = "Upload successful!";
        e.target.reset();
      } catch (err) {
        console.error("Error uploading video", err);
        statusEl.textContent = "Upload error.";
      }
    });
  </script>
  <?php endif; ?>
</body>
</html>
