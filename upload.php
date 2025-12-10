<?php
  session_start();
  
  // Check if the user is authenticated
  $user = $_SESSION['user'] ?? null;
  if (!$user) {
    echo "You must be logged in to upload videos.";
    exit;
  }
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
        <a href="logout.php" class="text-gray-700">Logout</a>
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

      <button class="bg-blue-600 text-white px-4 py-2 rounded self-start">
        Upload
      </button>

      <p id="status" class="text-sm mt-2 text-gray-600"></p>
    </form>
  </main>

  <script src="assets/js/videoConfig.js"></script>
  <script>
    document.getElementById("uploadForm").addEventListener("submit", async e => {
      e.preventDefault();
      const status = document.getElementById("status");
      status.innerText = "Uploading...";

      const title = document.getElementById("title").value;
      const description = document.getElementById("description").value;
      const file = document.getElementById("file").files[0];
      const userId = <?php echo json_encode($user['userId']); ?>;

      if (!userId) {
        status.innerText = "You must be logged in to upload.";
        return;
      }

      const formData = new FormData();
      formData.append("title", title);
      formData.append("description", description);
      formData.append("userId", userId);
      formData.append("file", file);

      const result = await uploadVideo(formData);

      if (result) {
        status.innerText = "Upload successful!";
        e.target.reset();
      } else {
        status.innerText = "Error: Upload failed.";
      }
    });
  </script>
</body>
</html>
