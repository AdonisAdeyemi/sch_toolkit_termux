<?php 
 // script : signup.php
 
// use Core\Config\Env ; 
    
    require_once LIB_PATH . '/session.php'; ?>
<?php $pageTitle = 'Signup'; 
include '_partials/head.php'; 

//$appName = $_SESSION['appName'] ?? "" ; 
//$appUrl = Env::get("APP_URL");

?>

<?php
echo <<< _END
<body>
_END;

include '_partials/messages.php';

/*
action="/api/auth/signup_action2.php" method="POST"

action="/api/signup.php" method="POST"
*/

echo <<< _END
  <div class="form-container">
    <h2>Create Your School</h2>

    <!-- signup form -->
    <form id="signupForm" > 
      <label>School Name:</label>
      <input type="text" name="school_name" id="school_name" required>

<label>School Address:</label>
      <input type="text" name="address" id="address" required>


      <label>Creator Full Name:</label>
      <input type="text" name="creator_name" id="creator_name" required>

      <label>Email:</label>
      <input type="email" name="email" id="email" required>

      <label>Username:</label>
      <input type="text" name="username" id="username" required>

      <label>Password:</label>
      <input type="password" name="password" id="password" required>
      
  
      <label>Confirm Password:</label>   
      <input type="password" id="confirm_password" name="confirm_password" required>
        
        <br><br>
        <div id="passwordError" style="color:red; align:center; margin:auto; width:100%;"></div>

      <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="/auth/login.php">Login</a></p>
  </div>
  
  
_END;

echo <<< JS
  <script>

  /* wait until the DOM is fully loaded */
  document.addEventListener("DOMContentLoaded", function() {
  

    /* async signup handler */
    document.getElementById("signupForm").addEventListener("submit", async function(e) {
    
    
      /* stop default submit */
      
     e.preventDefault(); 


      /* collect form data */
       const school = document.getElementById("school_name").value.trim();
       const address = document.getElementById("address").value.trim();
       
      const creator = document.getElementById("creator_name").value.trim();
      const email = document.getElementById("email").value.trim();
      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirm_password").value;
  const errorBox = document.getElementById("passwordError");

      
  console.log({ school, address, creator, email, username, password,confirmPassword });    
      
   
      /* validate inputs */
      if (!school || !address || !creator || !email || !username || !password || !confirmPassword) {
       
      errorBox.innerText = "All fields are required...";
        
        return;
        
      }

      /* email check */
      if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      errorBox.innerText = "Please enter a valid email address.";
      return;
      }

      /* password length */
      if (password.length < 6) { 
    errorBox.innerText = "Password must be at least 6 characters long.";
        return;
      }
      
  if(password !== confirmPassword){
    errorBox.innerText = "Passwords do not match.";
    return;
  }
      

      try {
        /* prepare request data */
        const formData = { 
          school_name: school,
          address, 
          creator_name: creator, 
           email, 
           username, 
           password
        };
        
        console.log("formDataaa", formData);

        /* send to API */
        const response = await fetch(`/auth/api/signup.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });
        
        
        /* get response */
       /*
        const result = await response.json();
        */
        
console.log(await response.clone().text());
const result = await response.json(); 
       
       

        /* handle result */
        if (result.status === "success") {
          alert(result.message || "Signup successful!");
     
  /* retrieve creator info */
 let { school_id , username } = result.signup_info ;

school_id = encodeURIComponent(school_id) ;
username = encodeURIComponent(username);
            
/* build the URL safely */
const url = `/auth/login.php?school_id=\${school_id}&username=\${username}`;

window.location.href = url;

        } else {
          errorBox.innerText = result.error || "Signup failed. Please try again." ;
       
        }

      } catch (error) {
        /* handle error */
        console.error("Signup error:", error);
        errorBox.innerText = "Signup error: Please try again.";
      }
    });
     
 })
     
     
  </script>
JS;

echo <<< _END
</body>
</html>
_END;
?>








