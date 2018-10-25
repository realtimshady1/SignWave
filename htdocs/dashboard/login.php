<?php
ob_start();
session_start();
require_once 'dbconnect.php';

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['btn-login'])) {
    $email = $_POST['email'];
    $upass = $_POST['pass'];

    $password = hash('sha256', $upass);
	
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email= ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);

    $count = $res->num_rows;
    if ($count == 1 && $row['password'] == $password) {
        $_SESSION['user'] = $row['id'];
        header("Location: index.php");
    } elseif ($count == 1) {
        $errMSG = "Wrong password";
    } else $errMSG = "User does not exist";
}
?>

<!DOCTYPE html>
<html lang="en">

	<head>

		<title>Login</title>

		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content="" />
		<meta name="author" content="Timothy Guo" />

		<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css"/>
		<link rel="stylesheet" href="assets/css/style.css" type="text/css"/>

	</head>

	<body>

		<div class="container">
			<div id="login-form">
				<form method="post" autocomplete="on">
					<div class="col-md-12">
						<div class="form-group">
							<h2 class="">Login</h2>
						</div>

						<div class="form-group">
							<hr/>
						</div>

						<?php
                if (isset($errMSG)) {
                    ?>
						<div class="form-group">
							<div class="alert alert-danger">
								<span class="glyphicon glyphicon-info-sign"></span> <?php echo $errMSG; ?>
							</div>
						</div>
						<?php
                }
                ?>
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
								<input type="email" name="email" class="form-control" placeholder="Email" required/>
							</div>
						</div>

						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
								<input type="password" name="pass" class="form-control" placeholder="Password" required/>
							</div>
						</div>

						<div class="form-group">
							<hr/>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-block btn-primary" name="btn-login">Login</button>
						</div>

						<div class="form-group">
							<hr/>
						</div>

						<div class="form-group">
							<a href="register.php" type="button" class="btn btn-block btn-danger"
									name="btn-login">Register</a>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

	</body>

</html>
