<?php
ob_start();
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['set'])) {

    $margin = trim($_POST['timeMargin']); 
    $timezone = trim($_POST['timezone']);
	
	$stmts = $conn->prepare("UPDATE reading SET id='?' WHERE studentname='timezone'");
	$stmts->bind_param("s", $timezone);
	$stmts->execute();
	$stmts->close();
	
	$stmt = $conn->prepare("UPDATE reading SET id='?' WHERE studentname='margin'");
	$stmt->bind_param("s", $margin);
	$stmt->execute();
	$stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

	<head>

		<title>Settings</title>

		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="description" content="" />
		<meta name="author" content="Timothy Guo" />

		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"/>
		<link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet"/>
		<link href="css/sb-admin.css" rel="stylesheet"/>

	</head>

	<body id="page-top">
		<nav class="navbar navbar-expand navbar-dark bg-dark static-top">
			<a class="navbar-brand mr-1" href="index.php">SignWave Management</a>
			<button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
				<i class="fas fa-bars"></i>
			</button>
			<form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
			</form>

			<ul class="navbar-nav ml-auto ml-md-0">
				<li class="nav-item dropdown no-arrow">
					<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-user-circle fa-fw"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
						<a class="dropdown-item" href="settings.php">Settings</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
					</div>
				</li>
			</ul>
		</nav>


		<div id="wrapper">
			<ul class="sidebar navbar-nav">
				<li class="nav-item active">
					<a class="nav-link" href="index.php">
						<i class="fas fa-fw fa-tachometer-alt"></i>
						<span>Dashboard</span>
					</a>
				</li>

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fw fa-folder"></i>
						<span>Classes</span>
					</a>
					<div class="dropdown-menu" aria-labelledby="pagesDropdown">
						<h6 class="dropdown-header">Class Names</h6>
						<?php
	$result = mysqli_query($conn,'SELECT * FROM class');    
    while($row = mysqli_fetch_array($result))
    {      
        echo '<a class="dropdown-item" href="students.php">' . $row['classname'] . "</a>";
        echo "
		"; 
    } 
?>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="new_class.php">+ Create New Class</a>
					</div>
				</li>

				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fw fa-table"></i>
						<span>Students</span>
					</a>

					<div class="dropdown-menu" aria-labelledby="pagesDropdown">
						<h6 class="dropdown-header">Students</h6>
						<a class="dropdown-item" href="students.php">
							View All Students</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="new_student.php">+ Add New Students</a>
					</div>
				</li>
			</ul>

			<div id="content-wrapper">
				<div class="container-fluid">
					<ol class="breadcrumb">
						<li class="breadcrumb-item">
							<a href="#">Dashboard</a>
						</li>
						<li class="breadcrumb-item active">Class</li>
					</ol>

					<div class="card mb-3">
						<div class="card-header">
							<i class="fas fa-folder"></i>
							New Class
						</div>
						<div class="card-body">
							<div class="card-body">
								<form method="post" autocomplete="on">
									<p class="lead">Time sign-on margin
									</p><p>The window of that will allow students register as attended (default: 10mins)
									</p>
									<div class="form-group">
										<div class="form-row">
											<div class="col-md-3">
												<div class="form-lab">
													<div class="input-group">
														<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
														<input readonly type="text" value="10" name="timeMargin" class="form-control" id="margin" required/>
													</div>
													<div class="input-group">
														<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
														<input type="range" class="form-control" min="0" max="60" value="10" step="1" required onchange="updateTextInput(this.value);" />
													</div>
												</div>
											</div>
										</div>
									</div>

									<p class="lead">Timezone
									</p>
									<div class="form-group">
										<div class="form-row">
											<div class="col-md-3">
												<div class="form-lab">
													<div class="input-group">
														<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
														<select name="timezone" class="form-control" required>
															<option value="Australia/Sydney">Australia/Sydney</option>
															<option value="Australia/Adelaide">Australia/Adelaide</option>
															<option value="Australia/Brisbane">Australia/Brisbane</option>
															<option value="Australia/Perth">Australia/Perth</option>
															<option value="Australia/Melbourne">Australia/Melbourne</option>
															<option value="Australia/Darwin">Australia/Darwin</option>
															<option value="Australia/Hobart">Australia/Hobart</option>
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group modal-footer">
										<button type="submit" class="btn btn-block btn-primary col-md-3" name="set">Confirm Settings</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<div class="card-footer small text-muted">Updated
				</div>
			</div>


			<footer class="sticky-footer">
				<div class="container my-auto">
					<div class="copyright text-center my-auto">
						<span>SignWave 2018</span>
					</div>
				</div>
			</footer>
		</div>
	</div>


	<a class="scroll-to-top rounded" href="#page-top">
		<i class="fas fa-angle-up"></i>
	</a>


	<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
					<button class="close" type="button" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
					<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
				</div>
			</div>
		</div>
	</div>

	<script src="vendor/jquery/jquery.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
	<script src="vendor/datatables/jquery.dataTables.js"></script>
	<script src="vendor/datatables/dataTables.bootstrap4.js"></script>
	<script src="js/sb-admin.min.js"></script>
	<script src="js/demo/datatables-demo.js"></script>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/tos.js"></script>
	<script>
		function updateTextInput(val) {
		document.getElementById('margin').value=val; 
		}
	</script>

</body>
</html>
