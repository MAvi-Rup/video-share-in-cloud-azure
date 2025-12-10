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
    <h2 class="text-xl font-semibold mb-4">Upload a New Video</h2>

    <form id="uploadForm" class="bg-white shadow rounded p-4 flex flex-col gap-3">
      <label class="text-sm font-medium">Title</label>
      <input id="title" class="border rounded px-3 py-2" required />

      <label class="text-sm font-medium">Description</label>
      <textarea id="description" class="border rounded px-3 py-2"></textarea>

      <label class="text-sm font-medium">Video File</label>
      <input id="file" type="file" accept="video/*" class="border rounded px-3 py-2" required />

      <button class="bg-blue-600 text-white px-4 py-2 rounded self-start mt-2">
        Upload
      </button>

      <p id="status" class="text-sm text-gray-600 mt-2"></p>
    </form>
  </main>

  <script src="assets/js/config.js"></script>
  <script>
    let currentUserId = null;

    // Initialise auth info when page loads
    (async () => {
      await initAuthUser();
      currentUserId = await getCurrentUserId();
    })();

    document.getElementById("uploadForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const status = document.getElementById("status");
      status.innerText = "";

      if (!currentUserId) {
        status.innerText = "You are not logged in via Azure.";
        return;
      }

      const title = document.getElementById("title").value.trim();
      const description = document.getElementById("description").value.trim();
      const fileInput = document.getElementById("file");
      const file = fileInput.files[0];

      if (!file) {
        status.innerText = "Please choose a video file.";
        return;
      }

      status.innerText = "Uploading...";

      const formData = new FormData();
      formData.append("title", title);
      formData.append("description", description);
      formData.append("userId", currentUserId);
      formData.append("file", file);

      try {
        const res = await fetch(`${API_BASE_URL}/videos`, {
          method: "POST",
          body: formData,
        });

        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err.error || `Upload failed (${res.status})`);
        }

        status.innerText = "Upload successful!";
        // Clear the form
        e.target.reset();
      } catch (err) {
        console.error("Upload error:", err);
        status.innerText = "Error: " + err.message;
      }
    });
  </script>
</body>
</html>
