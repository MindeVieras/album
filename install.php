<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Album | Installer</title>    

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="application-name" content="Album Installer" />
	<meta property="title" content="Installer" />
	<meta name="description" content="installer"  />

	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">

	<link rel="stylesheet" type="text/css" href="/assets/css/install.min.css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" type="text/javascript"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>

	<script src="/installer/assets/js/install.min.js" type="text/javascript"></script>
</head>

<body class="installer">

    <div class="container">

      <div class="page-header">
        <h1>Album install wizard</h1>
        <p class="lead">Install album DB and files.</p>
      </div>
      
        <h3>Server information</h3>
	<ul class="list-group">
	  <li class="list-group-item list-group-item-{% if page.phpVersion[1] == 'ok' %}success{% else %}danger{% endif %}">
	  	PHP Version
	  	<span class="pull-right">My version: {{page.phpVersion[0]}}, required: atleast 5.6.28</span>
	  </li>
	</ul>
	<hr>

  <h3>Libraries</h3>
	<ul class="list-group">
	  <li class="list-group-item list-group-item-{% if page.phpVersion[1] == 'ok' %}success{% else %}danger{% endif %}">
	  	PHP Version
	  	<span class="pull-right">GD Image library: {{page.phpVersion[0]}}, required: atleast 5.6.28</span>
	  </li>
	</ul>
  <hr>

  <a href="/install" class="btn btn-info pull-left">Go Back</a>
  <a href="/install/db-setup" class="btn btn-success pull-right">Next</a>

    </div>
  

</body>



</html>

<?php

require_once 'installer/start.php';
//phpinfo();

?>