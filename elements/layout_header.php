	<title><?php echo $fstat_title; ?></title>
	<meta name="author" content="Fabian Neffgen">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="./style.css">
	<script type="text/javascript">
		//I will not support a IE fix!!!!
		function showhide(classmember) {
			elements = document.getElementsByClassName(classmember);
			var newstyle;
			if(elements[0].style.display == "none"){
				newstyle = "table-row";
			}else{
				newstyle = "none";
			}
			
			for (var i = 0; i < elements.length; i++) {
				elements[i].style.display = newstyle;
			}
		}
	</script>
