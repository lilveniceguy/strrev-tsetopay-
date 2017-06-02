<?php 


function get_header(){
	ob_start();
	include 'static/header.html';
	return ob_get_clean();
}

function get_footer(){
	ob_start();
	include 'static/footer.html';
	return ob_get_clean();
}


?>