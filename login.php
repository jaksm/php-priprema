<?php
include_once 'includes/konekcija.php';
if(isset($_POST['login'])) {
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  $errors = [];

  if(empty($email)) {
    $errors[] = 'Email je prazan';
  }

  if(empty($pass)) {
    $errors[] = 'Password je prazan';
  }

  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email nije validan';
  }

  if(count($errors) === 0) {
    $stmt = $pdo->prepare('SELECT * FROM users u INNER JOIN roles r ON u.role_id = r.id WHERE u.email = :email LIMIT 1;');

    if($stmt->execute([
      ':email' => $email
    ])) {
      if($stmt->rowCount() !== 1) {
         $errors[] = "$email ne postoji";
      } else {
        $res = $stmt->fetch();
        if(password_verify($pass, $res->password)) {
          $_SESSION['isLoggedIn'] = true;
          $_SESSION['user'] = [
            'id' => $res->id,
            'ime' => $res->ime,
            'prezime' => $res->prezime,
            'email' => $res->email,
            'role' => $res->role
          ];
          header('Location: index.php');
        }
        else {
          $errors[] = "password nije ispravan";
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <form action="" method="post">
    <input type="email" name="email">
    <input type="password" name="pass">
    <input type="submit" name="login" value="Login">
  </form>
  <ul id="errors">
  <?php
   if($errors && count($errors)) {
    $html = '';
    foreach($errors as $error) {
      $html .= '<li>' .$error . '</li>';
    }
    echo $html;
  }
  ?>
</ul>
  <script>
    const form = document.querySelector('form');
    const errorsUl = document.getElementById('errors');

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const emailReg = /^[a-zA-Z0-9\.]+@([a-zA-Z]+\.)+[a-z]+$/;

      const errors = [];
      if(this.email.value === '') {
        errros.push('Email je prazan');
      }
      if(!emailReg.test(this.email.value)) {
        errros.push('Email nije validan');
      }

      if(this.password.value === '') {
        errros.push('Password je prazan');
      }

      if(errors.length) {
        errorsUl.innerHTML = errors.map(err => `<li>${err}</li>`).join('');
        return;
      }
      this.send();
    })

  </script>
</body>
</html>
