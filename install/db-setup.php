<!DOCTYPE html>
<html lang="en">

<head>
	<?php require_once 'templates/metahead.php'; ?>
</head>

<body class="installer">

  <div class="container">

  <div class="page-header">
    <h1>Album install wizard</h1>
    <p class="lead">Database setup.</p>
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