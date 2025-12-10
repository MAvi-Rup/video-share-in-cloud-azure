<!-- my-videos.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Videos | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <header class="bg-white shadow">
    <div class="max-w-4xl mx-auto py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">Cloud Video Share</h1>
      <nav class="space-x-4">
        <a href="index.php" class="text-gray-700">Home</a>
        <a href="my-videos.php" class="text-blue-600">My Videos</a>
        <a href="upload.php" class="text-gray-700">Upload Video</a>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">My Videos</h2>
    <p class="mb-2 text-sm text-gray-600" id="userLabel"></p>
    <ul id="myVideoList" class="space-y-4"></ul>
  </main>

  <script src="assets/js/config.js"></script>
  <script>
    async function loadMyVideos() {
      const userId = getCurrentUserId();
      document.getElementById("userLabel").innerText = "Showing videos for user: " + userId;

      const res = await fetch(`${API_BASE_URL}/users/${encodeURIComponent(userId)}/videos`);
      const videos = await res.json();
      const list = document.getElementById("myVideoList");
      list.innerHTML = "";

      videos.forEach(v => {
        const li = document.createElement("li");
        li.className = "bg-white shadow rounded p-4 flex justify-between items-center";
        li.innerHTML = `
          <div>
            <h3 class="font-semibold">${v.title}</h3>
            <video src="${v.blobUrl}" controls class="mt-2 w-48 h-32"></video>
            <p class="text-sm text-gray-600 mt-1">${v.description || ""}</p>
          </div>
          <button data-id="${v.id}" class="bg-red-600 text-white px-3 py-1 rounded delete-btn">
            Delete
          </button>
        `;
        list.appendChild(li);
      });

      document.querySelectorAll(".delete-btn").forEach(btn => {
        btn.addEventListener("click", async () => {
          const id = btn.dataset.id;
          await fetch(`${API_BASE_URL}/videos/${id}`, { method: "DELETE" });
          loadMyVideos();
        });
      });
    }

    loadMyVideos();
  </script>
</body>
</html>