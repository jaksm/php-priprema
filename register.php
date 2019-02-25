<?php
  include_once 'includes/konekcija.php';

  if(isset($_POST["submit"])) {
    $ime = $_POST["ime"];
    $prezime = $_POST["prezime"];
    $email= $_POST["email"];
    $grad = $_POST["grad"];
    $password = $_POST["password"];

    $errors = [];

    if (empty($ime)) {
      $errors[] = "Ime je prazno";
    }
    if (empty($prezime)) {
      $errors[] = "Prezime je prazno";
    }
    if (empty($email)) {
      $errors[] = "Email je prazan";
    }
    if (empty($grad)) {
      $errors[] = "Grad je prazan";
    }
    if (empty($password)) {
      $errors[] = "Password je prazan";
    }

    $reg = [
      "ime" => "/^[A-Z][a-z]{2,30}$/",
      "prezime" => "/^[A-Z][a-z]{2,30}$/",
      "email" => "/^\w+@([a-zA-Z0-9]+\.)[a-z]+$/",
      "grad" => "/^[A-Z][a-z]{2,30}$/",
      "password" => "/^\w+$/",
    ];

    foreach ($reg as $polje => $test) {
      if(!preg_match($test, $_POST[$polje])) {
        $errors[] = "$polje nije validno";
      }
    }

    if(count($errors) === 0) {
      // select user role
      $role_stmt = $pdo->prepare("SELECT id FROM roles WHERE name = 'user';");
      if (!$role_stmt->execute()) {
        die();
      }
      $roleId = $role_stmt->fetch()->id;
      // check if email unique
      $email_unique_stmt = $pdo->prepare("SELECT email FROM user WHERE email = :email LIMIT 1;");
      if(!$email_unique_stmt->execute([
        ":email" => $email
      ])) {
        die();
      }
      if($email_unique_stmt->rowCount() === 1) {
        $errors[] = "Email je zauzet";
      } else {
        // finally, add the madafaka to db
        $add_user_stmt = $pdo->prepare("INSERT INTO users (ime, prezime, email, grad, password, roleId) VALUES (:ime, :prezime, :email, :grad, :password, :roleId);");
        if($add_user_stmt->execute([
          ":ime" => $ime,
          ":prezime" => $prezime,
          ":email" => $email,
          ":grad" => $grad,
          ":password" => password_hash($password, PASSWORD_BCRYPT),
          ":roleId" => $roleId
        ])) {
          header("Location: login.php");
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
    <input type="text" name="ime">
    <input type="text" name="prezime">
    <input type="email" name="email">
    <input type="text" name="grad">
    <input type="password" name="password">
    <input type="submit" value="Register" name="submit">
  </form>

  <ul id="errors">
  <?php
    if ($errors && count($errors) > 0) {
      foreach ($errors as $error) {
        echo "<li>$error</li>";
      }
    }
  ?>
  </ul>

  <script>
    const form = document.querySelector("form")
    const errorsUl = document.querySelector("#errors")

    form.addEventListener("submit", function(e) {
      e.preventDefault()

      const reg = [
        /^[A-Z][a-z]{2,30}$/,
        /^[A-Z][a-z]{2,30}$/,
        /^\w+@([a-zA-Z0-9]+\.)[a-z]+$/,
        /^[A-Z][a-z]{2,30}$/,
        /^\w+$/,
      ]

      const values = [
        this.ime.value,
        this.prezime.value,
        this.email.value,
        this.grad.value,
        this.password.value
      ]

      const errors = []

      reg.forEach((r, i) => {
        const val = values[i]
        if (!r.test(val) || val === "") {
          errors.push(`${val} nije ispravan`);
        }
      });

      if(errors.length) {
        errorsUl.innerHTML = errors.map(err => `<li>${err}</li>`).join("");
        return;
      }

      this.send()
    })

  </script>
</body>
</html>
