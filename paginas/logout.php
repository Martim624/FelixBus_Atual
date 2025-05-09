<html>
<head>
	<style>
		body {

			background-position: top center;
		}
	</style>

<body>
</body>
</head>

</html>

<?php
session_start();
session_destroy();
header("refresh:0;url = index.php");
?>