<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Cloud Video Share</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow rounded p-8 w-full max-w-md">
    <h1 class="text-2xl font-bold mb-4 text-center">Cloud Video Share</h1>
    <h2 class="text-lg font-semibold mb-4 text-center">Login / Sign up</h2>

    <form>
      <a href="https://video-backend-azure-h9cgcgcsckf8aqgf.germanywestcentral-01.azurewebsites.net/auth/github" 
         class="bg-blue-600 text-white px-4 py-2 rounded self-center mt-4 text-center">
        Login with GitHub
      </a>
    </form>
  </div>

  <script>
    window.onload = () => {
      const user = localStorage.getItem("user");
      if (user) {
        window.location.href = "index.php";  // Redirect to homepage if already logged in
      }
    };
  </script>
</body>
</html>
