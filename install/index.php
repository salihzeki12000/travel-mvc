<?php
// JosephAlai Installer v1.0
function checkPHPVersion() {
    $version = phpversion();
    if($version >= 5.3) {
        return '<span class="label label-success">'.$version.'</span>';
    } else {
        return '<span class="label label-danger">'.$version.'</span>';
    }
}
function checkMySQLi() {
    if(function_exists('mysqli_connect')) {
      return '<span class="label label-success">Yes</span>';
  } else {
      return '<span class="label label-danger">No</span>';
  }
}
function checkModRewrite() {
    if(in_array('mod_rewrite', apache_get_modules())) {
        return '<span class="label label-success">Yes</span>';
    } else {
        return '<span class="label label-danger">No</span>';
    }
}
function checkShortTag() {
    if(ini_get('short_open_tag')) {
        return '<span class="label label-success">Yes</span>';
    } else {
        return '<span class="label label-danger">No</span>';
    }
}
function checkHtaccess() {
    if(file_exists('../.htaccess')) {
        return '<span class="label label-success">Yes</span>';
    } else {
        return '<span class="label label-danger">No</span>';
    }
}
function checkIsWritable() {
    if(is_writable('../core')) {
        return '<span class="label label-success">Yes</span>';
    } else {
        return '<span class="label label-danger">No</span>';
    }
}
if(isset($_POST['install'])) {
    $config_path = '../core/config.php';
    $website_name = $_POST['website_name'];
    $website_domain = $_POST['website_domain'];
    $mysql_db_host = $_POST['mysql_host'];
    $mysql_db_user = $_POST['mysql_db_user'];
    $mysql_db_pass= $_POST['mysql_db_pass'];
    $mysql_db_name = $_POST['mysql_db_name'];
    $filename = 'db.sql';
    $config_contents = "<?php
    \$domain = '".$website_domain."';

    // Database Configuration
    \$_db['host'] = '".$mysql_db_host."';
    \$_db['user'] = '".$mysql_db_user."';
    \$_db['pass'] = '".$mysql_db_pass."';
    \$_db['name'] = '".$mysql_db_name."';

    \$site_name = '".$website_name."';
    \$meta['keywords'] = '';
    \$meta['description'] = '';

    // Facebook Login Configuration
    \$fb_app_id = ''; 
    \$fb_secret_key = ''; 

    // Misc Configuration
    \$minimum_age = '16'; 

    // Units of Measurement
    \$unit['height'] = 'cm';
    \$unit['weight'] = 'kg';
    
    \$db = new mysqli(\$_db['host'], \$_db['user'], \$_db['pass'], \$_db['name']) or die('MySQL Error');

    error_reporting(0);
    ";
    fopen($config_path, 'w+');
    file_put_contents($config_path, $config_contents);

    // Connect to MySQL server
    mysqli_connect($mysql_db_host, $mysql_db_user, $mysql_db_pass, $mysql_db_name) or die('Error connecting to MySQL server: ' . mysqli_error());
    // Select database
//    mysqli_select_db($mysql_db_name) or die('Error selecting MySQL database: ' . mysqli_error());

    // Temporary variable, used to store current query
    $templine = '';
    // Read in entire file
    $lines = file($filename);
    // Loop through each line
    foreach ($lines as $line)
    {
        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '')
            continue;

        // Add this line to the current segment
        $templine .= $line;
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';')
        {
        // Perform the query
            mysqli_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error() . '<br /><br />');
        // Reset temp variable to empty
            $templine = '';
        }
    }
    fopen('install.lock', 'w+');
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<head> <meta name="robots" content="noindex">
	<meta charset="utf-8">
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'>
    <meta name="viewport" content="width=device-width" />
    <title> JosephAlai Installer </title>
    <!-- Plugins -->
    <link href="../assets/bootstrap3/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/css/jquery.gritter.css" rel="stylesheet">
    <link href="../assets/css/plugins.css" rel="stylesheet">
    <link href="../assets/css/jquery.mCustomScrollbar.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
    <!-- Main CSS -->
    <link href="../assets/css/theme.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="installer.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="col-lg-12 well">
            <div class="col-lg-7 pull-left">
                <h2> JosephAlai Installer </h2>
                <div class="col-lg-8 pull-left">
                    <form action="" method="post">
                        <div class="form-group">
                            <label> Website name </label>
                            <input type="text" name="website_name" placeholder="e.g MyDatingSite" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> Website domain </label>
                            <input type="text" name="website_domain" placeholder="e. http://yourwebsite.com" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> MySQL database host </label>
                            <input type="text" name="mysql_db_host" placeholder="e.g localhost" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> MySQL database user </label>
                            <input type="text" name="mysql_db_user" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> MySQL database password </label>
                            <input type="text" name="mysql_db_pass" class="form-control">
                        </div>
                        <div class="form-group">
                            <label> MySQL database name </label>
                            <input type="text" name="mysql_db_name" class="form-control">
                        </div>
                        <button type="submit" name="install" class="btn btn-success btn-lg btn-block"> Install Script </button>
                    </form>
                </div>
                <div class="col-lg-3 pull-right">
                    <h3> Requirements </h3>
                    <p class="rq"> PHP Version: <?=checkPHPVersion()?> </p>
                    <p class="rq"> MySQLi extension: <?=checkMySQLi()?> </p>
                    <p class="rq"> mod_rewrite module: <?=checkModRewrite()?> </p>
                    <p class="rq"> short_open_tag: <?=checkShortTag()?> </p>
                    <p class="rq"> .htaccess: <?=checkHtaccess()?> </p>
                    <p class="rq"> CHMOD 755 /core/ folder: <?=checkIsWritable()?> </p>
                    <br>

                </div>
            </div>
        </div>
    </div>
</body>
</html>