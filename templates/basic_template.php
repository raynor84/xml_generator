<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8"> 
        <link rel="stylesheet" type="text/css" href="./templates/css/style.css" />
        <link rel="stylesheet" type="text/css" href="./templates/css/formularstyle.css" />

        <!-- jquery ui -->
        <link type="text/css" href="css/jquery_ui/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
		<script type="text/javascript">
			/*$(function(){
				// Tabs
				$('#tabs').tabs();
			});*/
		</script>
        
    </head>
    <body>
        <h1><?php echo $this->_['blog_title']; ?></h1>
        <?php echo $this->_['blog_content']; ?>
        <hr />
        <?php echo $this->_['blog_footer']; ?>
    </body>
</html>