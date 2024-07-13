<?php
session_start();
include "connection.php";

$login_error = $signup_error = $signup_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
      $row = mysqli_fetch_assoc($res);
      $password = $row['password'];
      $decrypt = password_verify($pass, $password);

      if ($decrypt) {
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        header("location: dashboard.php");
        exit();
      } else {
        $login_error = "Wrong Password";
      }
    } else {
      $login_error = "Wrong Email or Password";
    }
  } elseif (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpass'];

    $check = "SELECT * FROM users WHERE email='$email'";
    $res = mysqli_query($conn, $check);

    if (mysqli_num_rows($res) > 0) {
      $signup_error = "This email is already used, try another one!";
    } else {
      if ($pass === $cpass) {
        $passwd = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$passwd')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
          $signup_success = "You are registered successfully!";
        } else {
          $signup_error = "Signup failed, please try again.";
        }
      } else {
        $signup_error = "Password does not match.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign up / Login Form</title>
    <link rel="stylesheet" href="css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="main">
        <input type="checkbox" id="chk" aria-hidden="true">

        <div class="signup">
            <form action="combined.php" method="post">
                <label for="chk" aria-hidden="true">Sign up</label>
                <input type="text" name="username" placeholder="User name" required="">
                <input type="email" name="email" placeholder="Email" required="">
                <input type="password" name="password" placeholder="Password" required="">
                <input type="password" name="cpass" placeholder="Confirm Password" required="">
                <button type="submit" name="signup">Sign up</button>
            </form>
        </div>

        <div class="login">
            <form action="combined.php" method="post">
                <label for="chk" aria-hidden="true">Login</label>
                <input type="email" name="email" placeholder="Email" required="">
                <input type="password" name="password" placeholder="Password" required="">
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>

    <script>
        const toggle = document.querySelector(".toggle"),
            input = document.querySelector(".password");
        if (toggle) {
            toggle.addEventListener("click", () => {
                if (input.type === "password") {
                    input.type = "text";
                    toggle.classList.replace("fa-eye-slash", "fa-eye");
                } else {
                    input.type = "password";
                }
            });
        }

        <?php if (!empty($login_error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $login_error; ?>',
                toast: true,
                position: 'top-right',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (!empty($signup_error)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $signup_error; ?>',
                toast: true,
                position: 'top-right',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        <?php endif; ?>

        <?php if (!empty($signup_success)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?php echo $signup_success; ?>',
                toast: true,
                position: 'top-right',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>
</body>

</html>
