<?php require_once LIB_PATH .'/session.php'; ?>
<?php $pageTitle = 'Login'; 
include '_partials/head.php'; 

//$appName = $_SESSION['appName'] ?? "" ;

echo <<< _END
<body>
_END;

include '_partials/messages.php';


/*
api/auth/login_action.php
test_auth.php
*/


$school_id_escaped = htmlspecialchars($school_id);
$username_escaped = htmlspecialchars($username);

echo <<< _END
  <div class="form-container">
    <h2>Teacher Login</h2>
    <form id="loginForm" action="/auth/api/login.php" method="POST">
      <label>School ID:</label>
      <input type="text" name="school_id" id="school_id" value = "$school_id_escaped" required>

      <label>Username:</label>
      <input type="text" name="username" id="username" value = "$username_escaped" required>

      <label>Password:</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Login</button>
    </form>
    <p>New school? <a href="/auth/signup.php">Create one</a></p>
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      const school = document.getElementById("school_id").value.trim();
      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value;

      if (!school || !username || !password) {
        alert("All fields are required.");
        e.preventDefault();
        return;
      }

      if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        e.preventDefault();
        return;
      }
    });
  </script>
</body>
</html>
_END;
?>










