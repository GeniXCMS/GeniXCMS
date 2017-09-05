<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?=Site::meta('backend');?>
    <!-- LOAD CSS -->
    <link href="<?=Site::$url;?>assets/css/bootstrap.min.css" rel="stylesheet">
<!--    <link href="--><?//=Site::$url;?><!--assets/css/bootstrap-theme.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/grideditor.css" rel="stylesheet">

    <!-- Custom CSS -->
<!--    <link href="--><?//=Site::$url;?><!--assets/css/sb-admin-2.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/dashboard.css" rel="stylesheet">
<!--    <link href="--><?//=Site::$url;?><!--assets/css/genixfont.css" rel="stylesheet">-->
    <link href="<?=Site::$url;?>assets/css/flag-icon.min.css" rel="stylesheet">
    <link href="<?=Site::$url;?>assets/css/jquery.tagsinput.min.css" rel="stylesheet">



    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/iCheck/square/blue.css">
    <!-- Morris chart -->
<!--    <link rel="stylesheet" href="--><?//=Site::$url;?><!--assets/plugins/morris/morris.css">-->
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
<!--    <link rel="stylesheet" href="--><?//=Site::$url;?><!--assets/plugins/datepicker/datepicker3.css">-->
    <!-- Daterange picker -->
<!--    <link rel="stylesheet" href="--><?//=Site::$url;?><!--assets/plugins/daterangepicker/daterangepicker.css">-->
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- bootstrap slider -->
    <link rel="stylesheet" href="<?=Site::$url;?>assets/plugins/bootstrap-slider/slider.css">

    <!-- LOAD Javascript -->
    <script src="<?=Site::$url;?>assets/js/jquery.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/bootstrap.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/moment-locales.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.tagsinput.min.js"></script>

    <!-- jQuery UI 1.11.4 -->
<!--    <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>-->
    <script src="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.structure.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <link rel="stylesheet" href="<?=Site::$url;?>assets/js/jquery-ui/jquery-ui.theme.min.css" type="text/css" media="screen" title="no title" charset="utf-8">
    <!-- Metis Menu Plugin JavaScript -->
<!--    <script src="--><?//=Site::$url;?><!--assets/js/plugins/metisMenu/metisMenu.min.js"></script>-->

    <!-- Custom Theme JavaScript -->
    <!--    <script src="--><?//=Site::$url;?><!--assets/js/sb-admin-2.js"></script>-->

    <!-- MetisMenu CSS -->
    <!--    <link href="--><?//=Site::$url;?><!--assets/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">-->

    <link href="<?=Site::$url;?>assets/css/summernote.css" rel="stylesheet">
    <script src="<?=Site::$url;?>assets/js/summernote.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-ext-genixcms.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-image-attributes.js"></script>
    <script src="<?=Site::$url;?>assets/js/plugins/summernote-floats-bs.min.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.grideditor.js"></script>
    <script src="<?=Site::$url;?>assets/js/jquery.grideditor.summernote.js"></script>
    <script src="<?=Site::$url;?>assets/js/genixcms.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition  login-page">
