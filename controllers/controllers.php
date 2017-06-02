<?php
require 'controllers/sessions.php';
require 'lib/template.php';
require 'lib/funciones.php';

class User {
	function getUsers(){
		echo 'usuarios';
	}

	function loginVal(){
		$className = 'sessionsVal';
		$val=call_user_func(array($className, 'valLog'));
		if (isset($_POST)) {
			if ($val) {
				foreach ($val as $data) {
					if ($data[0]==$_POST['username'] && $data[1]==$_POST['password']) {
						$resp = array('msj'=>'Welcome '.$_POST['username'], 'respuesta'=>1, 'page'=>$data[2]);
						$saveSession=call_user_func(array($className, 'logIn'),$_POST['username'],$data[2]);
					}else{
						$resp = array('msj'=>'Login Failed', 'respuesta'=>0);
					}
				}
			}
		}
		echo json_encode($resp);
	}

	function loginOut(){
		$className = 'sessionsVal';
		call_user_func(array($className, 'logOut'));
		header('Location: /');
	}
}

class Index {
	function loginForm(){
		$className = 'sessionsVal';
		$val=call_user_func(array($className, 'is_logged'));
		if (!$val) { //if not logged show the login form 
			$template=new Template('static/login.html');
			$header=get_header();
			$footer=get_footer();
			$template->assign_data(array(
				'header' => $header,
				'footer' => $footer
			));
			echo $template->render();
		}else{ //function if is logged, redirect to the page that belongs
			header('Location: '.$val);
		}
	}
}

class Pages{
	function pageCall($id){
		if ($id>3 || $id<1) {
			include 'static/404.php';
		}else{
			$className = 'sessionsVal';
			$val=call_user_func(array($className, 'is_logged'));
			if (!$val) {
				header('Location: /');
			}else{
				if ('pag-'.$id==$_SESSION['page']) {
					echo 'Hello '.$_SESSION['username'];
					echo '<br /><a href="/logout">Exit</a>';
				}else{
					echo 'Permission Denied';
				}
			}
		}
	}
}