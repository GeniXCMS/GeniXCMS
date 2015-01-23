    
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
    <script src="<?=GX_URL;?>/assets/js/bootstrap.min.js"></script>
    <script src="<?=GX_URL;?>/assets/js/holder.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?=GX_URL;?>/assets/js/ie10-viewport-bug-workaround.js"></script>
    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">

    <!-- include codemirror (codemirror.css, codemirror.js, xml.js, formatting.js)-->
  <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.min.css" />
  <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/blackboard.min.css">
  <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/theme/monokai.min.css">
  <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/codemirror.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/codemirror/3.20.0/mode/xml/xml.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/codemirror/2.36.0/formatting.min.js"></script>

    <link href="<?=GX_URL;?>/assets/css/summernote.css" rel="stylesheet">
    <script src="<?=GX_URL;?>/assets/js/summernote.min.js"></script>
    <script>
    $(document).ready(function() {
      $('.content').summernote({
          height: 300,
      });

       $(".alert").alert();
    });


</script>
  </body>
</html>