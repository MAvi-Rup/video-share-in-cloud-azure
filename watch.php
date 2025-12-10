<?php
  session_start();
  if (!isset($_SESSION['user'])) {
    header("Location: /.auth/login");
    exit;
  }
  $user = $_SESSION['user'];
  
  // Get video ID from the URL
  $videoId = $_GET['id'] ?? null;
  if (!$videoId) {
    echo "Video ID is required";
    exit;
  }

  // Fetch video details from the backend
  $video = null;
  try {
    $videoUrl = "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api/videos/$videoId";
    $response = file_get_contents($videoUrl);
    if ($response) {
      $video = json_decode($response, true);
    }
  } catch (Exception $e) {
    echo "Error fetching video details: " . $e->getMessage();
    exit;
  }

  if (!$video) {
    echo "Video not found";
    exit;
  }
?>
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
        <a href="javascript:void(0)" onclick="logout()" class="text-gray-700">Logout</a>
      </nav>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8">
    <h2 class="text-xl font-semibold mb-4"><?php echo $video['title']; ?></h2>
    <div class="bg-white shadow rounded p-4 mb-4">
      <video controls class="w-full">
        <source src="<?php echo $video['blobUrl']; ?>" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </div>
    <p class="text-sm text-gray-500"><?php echo $video['description']; ?></p>
    <p class="mt-2">Views: <?php echo $video['views']; ?></p>
  </main>
</body>
</html>
