// assets/js/config.js
const API_BASE_URL =
    "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";

function getCurrentUserId() {
    let userId = localStorage.getItem("userId");
    if (!userId) {
        userId = prompt("Enter a username (for My Videos):") || "guest";
        localStorage.setItem("userId", userId);
    }
    return userId;
}