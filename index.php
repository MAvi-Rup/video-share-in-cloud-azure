<!-- index.php -->
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
        <a href="index.php" class="text-blue-600">Home</a>
        <a href="my-videos.php" class="text-gray-700">My Videos</a>
        <a href="upload.php" class="text-gray-700">Upload Video</a>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4">Latest Videos</h2>
    <ul id="homeVideoList" class="space-y-4"></ul>
  </main>

  <script src="assets/js/config.js"></script>
  <script>
    async function loadHomeVideos() {
      const res = await fetch(`${API_BASE_URL}/videos`);
      const videos = await res.json();
      const list = document.getElementById("homeVideoList");
      list.innerHTML = "";

      videos.forEach(v => {
        const li = document.createElement("li");
        li.className = "bg-white shadow rounded p-4 flex gap-4";
        li.innerHTML = `
          <video src="${v.blobUrl}" controls class="w-48 h-32"></video>
          <div>
            <h3 class="font-semibold">${v.title}</h3>
            <p class="text-sm text-gray-600">${v.description || ""}</p>
            <p class="text-xs text-gray-500 mt-1">
              Uploaded by: ${v.userId}<br/>
              Created: ${new Date(v.createdAt).toLocaleString()}
            </p>
            <a href="watch.php?id=${v.id}" class="inline-block mt-2 text-blue-600 underline">
              Watch full page
            </a>
          </div>
        `;
        list.appendChild(li);
      });
    }

    loadHomeVideos();
  </script>
</body>
</html>