<?php
require_once __DIR__ . '/auth.php'; // Azure auth → $_SESSION['user']

$user    = $_SESSION['user'] ?? null;
$videoId = $_GET['id'] ?? null;

if (!$videoId) {
    echo "No video selected.";
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
  <!-- Header / Navigation -->
  <header class="bg-white/80 backdrop-blur shadow-sm">
    <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center gap-2">
        <span class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">CV</span>
        <h1 class="text-xl font-semibold tracking-tight">Cloud Video Share</h1>
      </div>
      <nav class="flex items-center gap-4 text-sm">
        <a href="index.php" class="text-gray-700 hover:text-blue-600 transition">Home</a>
        <a href="my-videos.php" class="text-gray-700 hover:text-blue-600 transition">My Videos</a>
        <a href="upload.php" class="text-gray-700 hover:text-blue-600 transition">Upload</a>

        <?php if ($user): ?>
          <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700 border border-slate-200">
            <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-[10px] font-semibold">
              <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?>
            </span>
            <span><?= htmlspecialchars($user['name'] ?? 'User'); ?></span>
          </span>
        <?php else: ?>
          <span class="text-xs text-slate-500">Not signed in</span>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main class="max-w-5xl mx-auto px-4 py-8 space-y-8">
    <!-- Video card -->
    <section class="bg-white shadow-sm rounded-xl overflow-hidden border border-slate-100">
      <div class="bg-black aspect-video">
        <video id="videoPlayer" controls class="w-full h-full rounded-t-xl">
          <source id="videoSource" src="" type="video/mp4" />
          Your browser does not support the video tag.
        </video>
      </div>

      <div class="p-5 space-y-3">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
          <div>
            <h2 id="videoTitle" class="text-lg sm:text-xl font-semibold text-slate-900">
              Loading video...
            </h2>
            <p id="videoDescription" class="mt-1 text-sm text-slate-600"></p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
          <span id="videoMeta" class="inline-flex items-center gap-1"></span>
        </div>

        <p id="status" class="text-sm text-amber-600 mt-2"></p>
      </div>
    </section>

    <!-- Comments section -->
    <section class="bg-white shadow-sm rounded-xl border border-slate-100 p-5 space-y-5">
      <div class="flex items-center justify-between gap-2">
        <div>
          <h3 class="text-base font-semibold text-slate-900">Comments</h3>
          <p class="text-xs text-slate-500">
            Share your thoughts about this video. Only you can delete your own comments.
          </p>
        </div>
        <span id="commentsCount" class="text-xs text-slate-500"></span>
      </div>

      <!-- New comment form -->
      <form id="commentForm" class="space-y-3">
        <textarea
          id="commentText"
          rows="3"
          class="w-full text-sm rounded-lg border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
          placeholder="Write a comment..."
        ></textarea>
        <div class="flex items-center justify-between gap-3">
          <p id="commentUserInfo" class="text-xs text-slate-500"></p>
          <button
            type="submit"
            class="inline-flex items-center gap-2 rounded-full bg-blue-600 text-xs font-medium text-white px-4 py-2 hover:bg-blue-700 active:bg-blue-800 transition shadow-sm"
          >
            Post Comment
          </button>
        </div>
        <p id="commentStatus" class="text-xs text-amber-600"></p>
      </form>

      <!-- Comments list -->
      <div id="commentsList" class="space-y-3">
        <!-- Comments will be rendered here -->
      </div>
    </section>
  </main>

  <script>
    // PHP → JS
    const SESSION_USER = <?= json_encode($user); ?>;
    const videoId      = <?= json_encode($videoId); ?>;

    // Backend base URL
    const API_BASE_URL =
      "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";

    // Video elements
    const titleEl        = document.getElementById("videoTitle");
    const descEl         = document.getElementById("videoDescription");
    const metaEl         = document.getElementById("videoMeta");
    const statusEl       = document.getElementById("status");
    const videoSourceEl  = document.getElementById("videoSource");
    const videoPlayerEl  = document.getElementById("videoPlayer");

    // Comment elements
    const commentsListEl    = document.getElementById("commentsList");
    const commentsCountEl   = document.getElementById("commentsCount");
    const commentFormEl     = document.getElementById("commentForm");
    const commentTextEl     = document.getElementById("commentText");
    const commentStatusEl   = document.getElementById("commentStatus");
    const commentUserInfoEl = document.getElementById("commentUserInfo");

    // ---- User helpers ----
    function getStoredUserIdOrNull() {
      if (SESSION_USER && SESSION_USER.userId) {
        return SESSION_USER.userId;
      }
      return localStorage.getItem("userId") || null;
    }

    function ensureUserIdentity() {
      // Called when user actually tries to post/delete
      let userId = getStoredUserIdOrNull();
      if (!userId) {
        const entered = prompt("Enter a name to comment as:");
        if (!entered) {
          return null;
        }
        userId = entered.trim();
        if (!userId) {
          return null;
        }
        localStorage.setItem("userId", userId);
      }
      return userId;
    }

    function getCurrentUserName() {
      if (SESSION_USER && SESSION_USER.name) {
        return SESSION_USER.name;
      }
      const stored = getStoredUserIdOrNull();
      return stored || "Guest";
    }

    function updateCommentUserInfo() {
      const name = getCurrentUserName();
      const storedId = getStoredUserIdOrNull();
      if (storedId || (SESSION_USER && SESSION_USER.userId)) {
        commentUserInfoEl.textContent = `Commenting as ${name}`;
      } else {
        commentUserInfoEl.textContent =
          "You are not signed in. You will be asked for a name when you post your first comment.";
      }
    }

    // ---- Small helper to avoid XSS when rendering text ----
    function escapeHtml(str) {
      return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    // ---- Load video details ----
    async function loadVideo() {
      statusEl.textContent = "Loading video details...";

      try {
        const res = await fetch(
          `${API_BASE_URL}/videos/${encodeURIComponent(videoId)}`
        );
        if (!res.ok) {
          statusEl.textContent = "Failed to load video.";
          titleEl.textContent = "Video not found.";
          console.error("Error fetching video:", res.status);
          return;
        }

        const video = await res.json();

        titleEl.textContent = video.title || "Untitled video";
        descEl.textContent = video.description || "";
        metaEl.textContent =
          `Views: ${video.views ?? 0} · Uploaded: ${
            video.createdAt
              ? new Date(video.createdAt).toLocaleString()
              : "N/A"
          }`;

        if (video.blobUrl) {
          videoSourceEl.src = video.blobUrl;
          videoPlayerEl.load();
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

    // ---- Load and render comments ----
    async function loadComments() {
      commentStatusEl.textContent = "Loading comments...";
      commentsListEl.innerHTML = "";

      try {
        const res = await fetch(
          `${API_BASE_URL}/videos/${encodeURIComponent(videoId)}/comments`
        );
        if (!res.ok) {
          throw new Error("Failed to load comments");
        }

        const comments = await res.json();
        const count = comments.length;

        commentsCountEl.textContent = count
          ? `${count} comment${count > 1 ? "s" : ""}`
          : "No comments yet";
        if (!count) {
          commentStatusEl.textContent = "Be the first to comment.";
          return;
        }

        commentStatusEl.textContent = "";
        const currentUserId = getStoredUserIdOrNull();

        comments.forEach((c) => {
          const wrapper = document.createElement("div");
          wrapper.className =
            "border border-slate-100 rounded-lg px-3 py-2 bg-slate-50/70 hover:bg-slate-50 transition";

          const createdAt = c.createdAt
            ? new Date(c.createdAt).toLocaleString()
            : "";

          wrapper.innerHTML = `
            <div class="flex items-start justify-between gap-2">
              <div>
                <p class="text-sm font-medium text-slate-900">${escapeHtml(
                  c.userName || c.userId || "User"
                )}</p>
                <p class="text-[11px] text-slate-400">${escapeHtml(
                  createdAt
                )}</p>
              </div>
              <div class="flex items-center gap-2" data-actions></div>
            </div>
            <p class="mt-2 text-sm text-slate-800 leading-snug">${escapeHtml(
              c.text || ""
            )}</p>
          `;

          // Delete button only for the owner
          const actionsEl = wrapper.querySelector("[data-actions]");
          if (currentUserId && c.userId === currentUserId) {
            const delBtn = document.createElement("button");
            delBtn.type = "button";
            delBtn.textContent = "Delete";
            delBtn.className =
              "text-[11px] text-red-500 hover:text-red-600 font-medium";
            delBtn.addEventListener("click", () => handleDeleteComment(c));
            actionsEl.appendChild(delBtn);
          }

          commentsListEl.appendChild(wrapper);
        });
      } catch (err) {
        console.error("Error loading comments:", err);
        commentStatusEl.textContent = "Could not load comments.";
      }
    }

    // ---- Create a new comment ----
    commentFormEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const text = commentTextEl.value.trim();
      if (!text) {
        return;
      }

      const userId = ensureUserIdentity();
      if (!userId) {
        return; // user cancelled
      }
      const userName = getCurrentUserName();

      updateCommentUserInfo();

      const payload = {
        userId,
        userName,
        text,
      };

      commentStatusEl.textContent = "Posting comment...";

      try {
        commentFormEl.classList.add("opacity-60", "pointer-events-none");

        const res = await fetch(
          `${API_BASE_URL}/videos/${encodeURIComponent(videoId)}/comments`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
          }
        );

        if (!res.ok) {
          throw new Error("Failed to post comment");
        }

        commentTextEl.value = "";
        await loadComments();
        commentStatusEl.textContent = "";
      } catch (err) {
        console.error("Error posting comment:", err);
        commentStatusEl.textContent = "Could not post comment.";
      } finally {
        commentFormEl.classList.remove("opacity-60", "pointer-events-none");
      }
    });

    // ---- Delete a comment (only own comment) ----
    async function handleDeleteComment(comment) {
      if (!confirm("Delete this comment?")) return;

      const userId = ensureUserIdentity();
      if (!userId) return;

      commentStatusEl.textContent = "Deleting comment...";

      try {
        const res = await fetch(
          `${API_BASE_URL}/videos/${encodeURIComponent(
            videoId
          )}/comments/${encodeURIComponent(comment.id)}`,
          {
            method: "DELETE",
            headers: {
              "x-user-id": userId,
            },
          }
        );

        if (!res.ok) {
          throw new Error("Failed to delete comment");
        }

        await loadComments();
        commentStatusEl.textContent = "";
      } catch (err) {
        console.error("Error deleting comment:", err);
        commentStatusEl.textContent = "Could not delete comment.";
      }
    }

    // ---- Initial load ----
    updateCommentUserInfo();
    loadVideo();
    loadComments();
  </script>
</body>
</html>
