<?php
/*
 *
 */

include "objects/Field_Definitions.php";


$template = new OxAdminTemplate ();

$template->getHTMLHeader ();
$template->getHTMLAdminBar();
$template->getContent();

$template->getHTMLFooter ();

/**
 *
 * Da wir kein SMARTY einsetzen, bauen wir unser HTML selber
 * @author Dennis Heidtmann
 *
 */
class OxAdminTemplate {

	function getHTMLHeader() {
		?>
		<html>
		<head>

		<?php
		$this->getHTMLJSLibs ();
		$this->getHTMLcss();
		?>
			<script>
		$(document).ready(function(){
			$('.panels .panelHead').click(function() {
				$(this).next().toggleClass("hide");
				return false;
			});
		});
		</script>
		</head>
		<body>
		<!-- bof page -->
		<div id="page">
		<?php
	}

	private function getHTMLcss() {
		?>
	<!-- oxid admin basis -->
	<link rel="stylesheet" type="text/css" media="screen" href="css/ox_reset.css">
	<link rel="stylesheet" type="text/css" media="screen" href="css/ox_style.css">

	<!-- jquery ui Theme-->
	<link rel="stylesheet" type="text/css" media="screen" href="css/smoothness/jquery-ui-1.8.18.custom.css">

	<!-- Fluid CSS -->
	<link rel="stylesheet" type="text/css" media="screen" href="css/fluid_grid.css">
	<?php
	}

	private function getHTMLJSLibs() {
	?>
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>

	<!-- DataTable Grid TEST -->
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>

	<!-- unsere jquery functions -->
	<script type="text/javascript" src="js/ox_jquery.js"></script>
	<?php
	}

	function getHTMLAdminBar() {
		?>
		<div id="adminbar" class="ui-menu" >
			<div class="left">
				<ul>
					<li>Ich bin Punkt 1</li>
					<li>Ich bin Punkt 2</li>
				</ul>
			</div>
			<div class="right">
				<ul class="ui-widget ui-helper-clearfix">
				<li class="ui-state-default ui-corner-all">
					<span class="ui-icon ui-icon-gear"></span>
				</li>

				</ul>
			</div>
		</div>
		<?php
	}


	function getHTMLFooter() {
		?>
	<!-- eof page -->
	</div>
	</body>
	</html>
	<?php
	}

	function getContent()
	{
		$url = "json/article.json";
		$obj = json_decode(file_get_contents($url));
		?>
		<form>
		<?php
		Field_Definitions::renderSite($obj);
		?>
		 </form>
		<?php
	}

}

?>

