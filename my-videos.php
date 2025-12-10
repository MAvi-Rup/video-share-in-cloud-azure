<?php
session_start();
include("auth.php");

$user = $_SESSION['user'] ?? null;
$userId = $user['userId'] ?? null;

if (!$userId) {
    echo "Please login to see your videos.";
    exit;
}
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
            <nav class="space-x-4">
                <a href="index.php" class="text-gray-700">Home</a>
                <a href="my-videos.php" class="text-blue-600">My Videos</a>
                <a href="upload.php" class="text-gray-700">Upload Video</a>
                <?php if ($user): ?>
                    <span class="text-gray-500">Hello, <?= htmlspecialchars($user['name']) ?></span>
                    <a href="logout.php" class="text-gray-700">Logout</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="max-w-4xl mx-auto py-8">
        <h2 class="text-xl font-semibold mb-4">Your Videos</h2>

        <div id="videoList"></div>

        <p id="status" class="text-sm text-gray-600"></p>
    </main>

    <script>
        const API_BASE_URL = "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";
        const CURRENT_USER_ID = <?= json_encode($userId); ?>;
        const videoListEl = document.getElementById("videoList");
        const statusEl = document.getElementById("status");

        async function fetchUserVideos() {
            try {
                const res = await fetch(`${API_BASE_URL}/users/${CURRENT_USER_ID}/videos`);
                if (!res.ok) {
                    statusEl.textContent = "Failed to load your videos.";
                    console.error("Error fetching user videos:", res.status);
                    return;
                }

                const videos = await res.json();
                if (videos.length === 0) {
                    videoListEl.innerHTML = "<p>No videos uploaded yet.</p>";
                    return;
                }

                const videoItems = videos.map(video => `
                    <div class="bg-white p-4 rounded shadow mb-4">
                        <h3 class="text-lg font-semibold">${video.title}</h3>
                        <p class="text-sm text-gray-600">${video.description}</p>
                        <p class="text-xs text-gray-400">Uploaded: ${new Date(video.createdAt).toLocaleString()}</p>
                        <a href="watch.php?id=${video.id}" class="text-blue-600">Watch</a>
                    </div>
                `);

                videoListEl.innerHTML = videoItems.join("");
            } catch (err) {
                statusEl.textContent = "Error loading your videos.";
                console.error("Error:", err);
            }
        }

        fetchUserVideos();
    </script>
</body>
</html>
