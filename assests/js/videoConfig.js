// Backend API base URL for video-related actions
const VIDEO_API_BASE_URL =
    "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";

// Fetch videos from backend API
async function fetchVideos() {
    try {
        const response = await fetch(`${VIDEO_API_BASE_URL}/videos`);
        if (!response.ok) {
            console.error("Failed to fetch videos", response.status);
            return [];
        }
        return await response.json();
    } catch (err) {
        console.error("Error fetching videos:", err);
        return [];
    }
}

// Fetch a specific video by ID
async function fetchVideoById(videoId) {
    try {
        const response = await fetch(`${VIDEO_API_BASE_URL}/videos/${videoId}`);
        if (!response.ok) {
            console.error("Failed to fetch video by ID", response.status);
            return null;
        }
        return await response.json();
    } catch (err) {
        console.error("Error fetching video by ID:", err);
        return null;
    }
}

// Fetch videos uploaded by a specific user
async function fetchUserVideos(userId) {
    try {
        const response = await fetch(`${VIDEO_API_BASE_URL}/users/${userId}/videos`);
        if (!response.ok) {
            console.error("Failed to fetch user videos", response.status);
            return [];
        }
        return await response.json();
    } catch (err) {
        console.error("Error fetching user videos:", err);
        return [];
    }
}

// Upload a new video to the backend API
async function uploadVideo(formData) {
    try {
        const response = await fetch(`${VIDEO_API_BASE_URL}/videos`, {
            method: "POST",
            body: formData,
        });
        if (!response.ok) {
            console.error("Failed to upload video", response.status);
            return null;
        }
        return await response.json();
    } catch (err) {
        console.error("Error uploading video:", err);
        return null;
    }
}
