<?php

ob_start();
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['register'])) {
		
    $studentname = trim($_POST['sname']); 
    $class = trim($_POST['cname']);
    $rfid = trim($_POST['RFID']);

    $stmt = $conn->prepare("SELECT * FROM db.students WHERE id=? AND classname=?");
    $stmt->bind_param("ss", $rfid, $class);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $count = $result->num_rows;

    if ($count == 0) {
		
        $import = $conn->prepare("SELECT day, start, end FROM class WHERE classname=?");
		$import->bind_param("s", $class);
		$import->execute();
		$result = $import->get_result();
		$row = mysqli_fetch_assoc($result);
		$import->close();
		$present = "NO";
		
        $stmts = $conn->prepare("INSERT INTO students(studentname,id,classname,day,start,end,present) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $stmts->bind_param("sssssss", $studentname, $rfid, $class, $row['day'], $row['start'], $row['end'], $present);
        $stmts->execute();
        $stmts->close();
		
    } 
	else {
        $errTyp = "warning";
        $errMSG = "Student name is already used";
    }


}
?>

<!DOCTYPE html>
<html lang="en">

	<head>

		<title>Add New Student</title>

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
						<li class="breadcrumb-item active">Student</li>
					</ol>

					<div class="card mb-3">
						<div class="card-header">
							<i class="fas fa-table"></i>
							New Student
						</div>
						<div class="card-body">
							<div class="card-body">
								<form method="post" autocomplete="off">
									<p class="lead">Details
									</p>
									<div class="form-group">
										<div class="form-row form-lab">
											<div class="col-md-3 input-group">
												<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
												<input type="text" name="sname" class="form-control" placeholder="Student Name" required/>
											</div>
										</div>
									</div>

									<p class="lead">Class
									</p>
									<div class="form-group">
										<div class="form-row">
											<div class="col-md-3">
												<div class="form-lab">
													<div class="input-group">
														<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
														<select name="cname" class="form-control" required>
															<?php
																$result = mysqli_query($conn,'SELECT * FROM class');    
																while($row = mysqli_fetch_array($result))
																{      	
																	echo '<option value="' . $row['classname'] . '">' . $row['classname'] . "</option>";
																	echo "
																	"; 
																} 
															?>
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>

									<p class="lead">RFID Tag
									</p>
									<p> Place the RFID tag close to the reader and ensure other tags are clear from the reader
									</p>
									<div class="form-group">
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group">
													<div class="form-label-group" id="refresh">
														<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
														<input readonly type="text" value="<?php 
														$result=mysqli_query($conn,"SELECT id FROM reading WHERE studentname='read'"); 
														$row = mysqli_fetch_assoc($result);
														echo $row["id"];
														?>" name="RFID" class="form-control" id="rfid" required/>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group modal-footer">
											<button type="submit" class="btn btn-block btn-primary col-md-3" name="register" value="blank">Register</button>
										</div>
									</div>
								</form>
							</div>
						</div>
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
						<a href="logout.php?logout" class="btn btn-primaryglyphicon glyphicon-log-out">Logout</a>
						<button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
					</div>
				</div>
			</div>
		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
		<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
		<script src="vendor/datatables/jquery.dataTables.js"></script>
		<script src="vendor/datatables/dataTables.bootstrap4.js"></script>
		<script src="js/sb-admin.min.js"></script>
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script langauge="javascript">
			window.setInterval("refreshDiv()", 1000);
			function refreshDiv(){
			$('#refresh').load("new_student.php" + ' #refresh');        
			}
		</script>

	</body>

</html>
