<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config.php");
if (isset($_GET['action'])) {
	if ($_GET['action'] == 'delete') {
		$stmt = $dbh->prepare("DELETE FROM records WHERE NUMID=:id");
		$stmt->bindValue(':id', $_GET['id'], PDO::PARAM_STR);
		$stmt->execute();
		header ("LOCATION: ?action=deletesuccess");
	}
	if ($_GET['action'] == 'deletesuccess') {
		$success = 'delete';
	}
}
if (isset($_POST['action'])) {
	if ($_POST['action'] == 'checkIn') {
		$stmt = $dbh->query('SELECT * FROM records ORDER BY NUMID desc LIMIT 1');
		//if ($stmt) {
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$new_id = $results[0]['NUMID'] + 1;
		//}
		//else {
		//	$new_id = 0;
		//}
		try {
			$stmt = $dbh->prepare("INSERT INTO records(NUMID,ID,Time,Type,Reason) VALUES(:field1,:field2,:field3,:field4, :field5)");
			$stmt->execute(array(':field1' => $new_id, ':field2' => $_POST['ID'], ':field3' => date('d/m/Y h:i A'), ':field4' => 'CheckIn', ':field5' => $_POST['reason']));
			$success = 'checkIn';
		} catch(PDOException $ex) {
			die("An error occured: ".$ex->getMessage() ." Did you import the <a href='database.sql'>.sql file</a> to the database?");
		}
	}
	if ($_POST['action'] == 'checkOut') {
		$stmt = $dbh->query('SELECT NUMID, MAX(NUMID) FROM records');
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$new_id = $results[0]['MAX(NUMID)'] + 1;
		try {
			$stmt = $dbh->prepare("INSERT INTO records(NUMID,ID,Time,Type) VALUES(:field1,:field2,:field3,:field4)");
			$stmt->execute(array(':field1' => $new_id, ':field2' => $_POST['ID'], ':field3' => date('d/m/Y h:i A'), ':field4' => 'CheckOut'));
			$success = 'checkOut';
		} catch(PDOException $ex) {
			die("An error occured: ".$ex->getMessage() ." Did you import the <a href='database.sql'>.sql file</a> to the database?");
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title><?php echo config::$SITE_NAME; ?></title>

	<!-- Bootsrap CSS -->
	<link href="css/bootstrap.min.css" rel="stylesheet">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">

	<!-- Custom CSS -->
	<link href="css/style.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
  </head>
  <body>
	<!-- Check in -->
	<div class="modal fade" id="checkIn" tabindex="-1" role="dialog" aria-labelledby="checkInLabel">
	  <div class="modal-dialog" role="document">
		<form class="form-horizontal" name="check_in" method="POST">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="checkInLabel">Check in</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label for="CheckIn_ID" class="col-sm-2 control-label">ID</label>
					<div class="col-sm-10">
					  <input type="text" class="form-control" id="CheckIn_ID" name="ID" placeholder="Scan ID..." />
					</div>
				  </div>
					<div class="form-group">
					<label for="CheckIn_Reason" class="col-sm-2 control-label">Reason</label>
					<div class="col-sm-10">
						<select class="form-control" name="reason">
						  <option name="free-period">Free Period</option>
						  <option name="pass">Pass</option>
						  <option name="other">Other</option>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label for="DateAndTime" class="col-sm-2 control-label">Date/Time</label>
					<div class="col-sm-10">
					  <input type="text" class="form-control" id="DateAndTime" value="Will automatically log..." readonly/>
					</div>
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" name="action" value="checkIn" class="btn btn-success"><i class="fa fa-check"></i> Check in</button>
			  </div>
			</div>
		</form>
	  </div>
	</div>

	<!-- Check out -->
	<div class="modal fade" id="checkOut" tabindex="-1" role="dialog" aria-labelledby="checkInLabel">
	  <div class="modal-dialog" role="document">
		<form class="form-horizontal" name="check_out" method="POST">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="checkOutLabel">Check out</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label for="CheckOut_ID" class="col-sm-2 control-label">ID</label>
					<div class="col-sm-10">
					  <input type="text" class="form-control" id="CheckOut_ID" name="ID" placeholder="Scan ID..." />
					</div>
				  </div>
				  <div class="form-group">
					<label for="DateAndTime" class="col-sm-2 control-label">Date/Time</label>
					<div class="col-sm-10">
					  <input type="text" class="form-control" id="DateAndTime" value="Will automatically log..." readonly/>
					</div>
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" name="action" value="checkOut" class="btn btn-success"><i class="fa fa-sign-out"></i> Check out</button>
			  </div>
			</div>
		</form>
	  </div>
	</div>

	<!-- Records -->
	<div class="modal fade" id="records" tabindex="-1" role="dialog" aria-labelledby="recordsLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="recordsLabel">Records</h4>
		  </div>
		  <div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-success">
							<div class="panel-body">
								<input type="text" class="form-control" id="task-table-filter" data-action="filter" data-filters="#task-table" value="<?php echo date('d/m/Y'); ?>" placeholder="Enter ID, date, or time to filter result" />
							</div>
							<table class="table table-hover" id="task-table">
								<thead>
									<tr>
										<th>ID</th>
										<th>Action</th>
										<th>Reason</th>
										<th>Date and Time</th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($dbh->query('SELECT * FROM records order by NUMID DESC') as $row) {
									?>
									<tr>
										<td><?php echo $row['ID']; ?></td>
										<td><?php echo $row['Type']; ?></td>
										<td><?php echo $row['Reason']; ?></td>
										<td><?php echo $row['Time']; ?> <a href="?action=delete&id=<?php echo $row['NUMID']; ?>"><i class="fa fa-trash"></i></a></td>
									</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type="reset" class="btn btn-default" data-dismiss="modal">Close</button>
			<button type="submit" name="action" value="checkOut" class="btn btn-success"><i class="fa fa-sign-out"></i> Check out</button>
		  </div>
		</div>
	  </div>
	</div>

	<div class="container">
		<div class="jumbotron main_jumbotron">
			<h1><?php echo config::$SITE_NAME; ?></h1>
			<hr />
			<?php
			if (isset($success)) {
				if ($success == 'checkIn') {
					echo "<p class='text-success'>Person was checked in. <a href='?action=delete&id=$new_id'>Undo</a></p>";
				}
				elseif ($success == 'checkOut') {
					echo "<p class='text-info'>Person was checked out. <a href='?action=delete&id=$new_id'>Undo</a></p>";
				}
				elseif ($success == 'delete') {
					echo "<p class='text-danger'>Record deleted.</p>";
				}
			}
			?>
			<div class="row">
				<div class="col-md-6">
					<center>
						<a class="btn btn-success" data-toggle="modal" data-target="#checkIn">
							<i class="fa fa-check fa-4x fa-border"></i>
							<h3>Check in</h3>
						</a>
					</center>
				</div>
				<div class="col-md-6">
						<a class="btn btn-primary" data-toggle="modal" data-target="#records">
							<i class="fa fa-list fa-4x fa-border"></i>
							<h3>Records</h3>
						</a>
				</div>
			</div>
			<hr /><br /><br />
			<p class="text-muted text-center">Made with <i class="fa fa-heart"></i> for Helix.</p>
		</div>
	</div>

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>

	<script>
	$(window).load(function(){
	    $('#checkIn').modal('show');
	});
	$('#checkIn').on('shown.bs.modal', function () {
		$('#CheckIn_ID').focus();
	})
	$('#checkOut').on('shown.bs.modal', function () {
		$('#CheckOut_ID').focus();
	})
		$('#records').on('shown.bs.modal', function () {
		$('#task-table-filter').focus();
	})
	</script>

	<script src="js/tablesorter.js"></script>
  </body>
</html>
