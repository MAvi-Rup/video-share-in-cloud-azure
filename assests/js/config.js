// Backend API base URL for user authentication
const API_BASE_URL =
    "https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/api";

// Fetch current authenticated user from Azure Easy Auth
async function fetchAuthUser() {
    try {
        const res = await fetch("/.auth/me", { credentials: "include" });
        if (!res.ok) {
            console.error("Failed to call /.auth/me", res.status);
            return null;
        }
        const data = await res.json();
        if (!Array.isArray(data) || data.length === 0) return null;
        const principal = data[0];
        return {
            userId: principal.user_id || principal.userDetails,
            name: principal.userDetails,
            provider: principal.provider_name,
        };
    } catch (err) {
        console.error("Error reading auth user:", err);
        return null;
    }
}

// Cache user on each page so we do not call /.auth/me repeatedly
let CURRENT_USER = null;

// Initialize user information
async function initAuthUser() {
    CURRENT_USER = await fetchAuthUser();
    if (!CURRENT_USER) {
        alert("You are not authenticated.");
    } else {
        console.log("Logged in as:", CURRENT_USER);
    }
}

// Helper used by upload / my-videos pages
async function getCurrentUserId() {
    if (!CURRENT_USER) {
        CURRENT_USER = await fetchAuthUser();
    }
    return CURRENT_USER ? CURRENT_USER.userId : null;
}

// Helper function to get the current logged-in user's data
function getCurrentUser() {
    return CURRENT_USER;
}

// Redirect to login if not authenticated
function checkAuthentication() {
    if (!CURRENT_USER) {
        window.location.href = "login.php";
    }
}
