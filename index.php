<?php
    session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=Cp1252">
<title>Link Shortener</title>
<link rel="stylesheet" href="css/global.css">
</head>
    <body>
    <div class="container">
    <h1 class="title"> Link Shortener </h1>
    <form action="shorten.php" method="post">
    	<input type="url" name="url" placeholder="Enter link here..." autocomplete="off">
    	<input type="submit" value="Shorten Link...">
    </form>
    </div>
    
 	<?php
    if(isset($_SESSION['feedback'])) {
        echo "<p>{$_SESSION['feedback']}</p>";
        unset($_SESSION['feedback']);
    }
	?>
    </body>
</html>