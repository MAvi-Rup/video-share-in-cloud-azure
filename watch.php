<!-- watch.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Watch Video | Cloud Video Share</title>
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
      </nav>
    </div>
  </header>

  <main class="max-w-3xl mx-auto py-8" id="watchContainer">
    <p>Loading...</p>
  </main>

  <script src="assets/js/config.js"></script>
  <script>
    function getQueryParam(name) {
      const params = new URLSearchParams(window.location.search);
      return params.get(name);
    }

    async function loadVideo() {
      const id = getQueryParam("id");
      if (!id) {
        document.getElementById("watchContainer").innerText = "No video ID provided.";
        return;
      }

      const res = await fetch(`${API_BASE_URL}/videos/${id}`);
      if (!res.ok) {
        document.getElementById("watchContainer").innerText = "Video not found.";
        return;
      }
      const v = await res.json();

      const container = document.getElementById("watchContainer");
      container.innerHTML = `
        <h2 class="text-xl font-semibold mb-4">${v.title}</h2>
        <video src="${v.blobUrl}" controls class="w-full max-h-[480px] mb-4"></video>
        <p class="text-gray-700 mb-2">${v.description || ""}</p>
        <p class="text-sm text-gray-500">
          Uploaded by: ${v.userId}<br/>
          Created at: ${new Date(v.createdAt).toLocaleString()}
        </p>
      `;
    }

    loadVideo();
  </script>
</body>
</html>