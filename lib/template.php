<?php
// Error message's assignament
define('TEMPLATE_REQ_STRING_TYPE','El parametro ingresado debe ser del tipo String');
define('TEMPLATE_REQ_ARRAY_TYPE','El parametro ingresado debe ser del tipo Array');
define('TEMPLATE_OPEN_FAIL','Error al abrir el template seleccionado');
define('TEMPLATE_ATTR_ERROR','El atributo %s no se encuentra registrado');
define('TEMPLATE_NODATA_ERROR','El template no contiene datos');
/**
 * Class to manage html templates
 * PHP versions 4 and 5
 * 
 * LICENSE: This source file is subject to version 2.0 of the Atribucion-Licenciar Igual
 * available through the Creative Commons Chile at the following
 * URI: http://creativecommons.org/licenses/by-sa/2.0/cl/
 * 
 * @author		RiuugA Hidek1 [ryoga@netprodigy.cl]
 * @version		SVN: 2.8rc
 * @copyright		2009-2010 Netprodigy.cl
 * 
 */
class Template{
	/**
	 * Location of template
	 * @var String $file_location
	 * @access Private
	 */
	private $file_location;
	
	/**
	 * File contents
	 * @var String $template
	 * @access Private
	 */
	private $template;
	
	/**
	 * Php variable to template
	 * @var Array $data
	 * @access Private
	 */
	private $data = array();
	
	/**
	 * List of Attributes and Events HTML allowed
	 * @var Array $attributes
	 * @access Private
	 */
	private $attributes = array('id', 'class', 'style', 'title', 'lang', 'dir',
				    'method', 'abbr', 'value', 'name', 'src', 'type',
				    'target', 'tabindex', 'selected', 'readonly', 'border',
				    'maxlength', 'href', 'enctype', 'disabled', 'action',
				    'checked', 'alt', 'onclick', 'ondblclick', 'onmousedown',
				    'onmouseup', 'onmouseover', 'onmousemove', 'onmouseout',
				    'onkeypress', 'onkeydown', 'onkeyup', 'onload', 'onunload',
				    'onfocus', 'onblur', 'onsubmit', 'onreset', 'onselect', 'onchange');
	
	/**
	 * If no parameters will make a template empty
	 * to add data on empty template use $this->html("", "data");
	 * Get file location, including the extention
	 * The file can be of any extension, provided that is readable.
	 * @access Public
	 *
	 * @param String[optional] $file_location
	 */
	public function __construct($file_location = null){
		if($file_location){
			if(is_string($file_location)){
				if(file_exists($file_location)){
					$this->file_location = $file_location;
					$this->template = file_get_contents($this->file_location);
				}else{
					$this->error(TEMPLATE_OPEN_FAIL);
					return False;
				}
			}else{
				$this->error(TEMPLATE_REQ_STRING_TYPE);
				return False;
			}
		}else{
			$this->template = "";
		}
	}
	
	/**
	 * Associative array with the name of each variable
	 * Only allow the following characters a-z A-Z 0-9 _ -
	 * @access Public
	 *
	 * @example		$template->assign_data(array("variable1"=>$variable1));
	 * @example		$template->assign_data(array("variable1"=>$variable1,"variable2"=>$variable2));
	 * @param Array $data
	 * @return Boolean
	 */
	public function assign_data($data){
		if(is_array($data)){
			$this->data = $data;
			$this->template = str_replace("'", "\'", $this->template);
			if(!empty($this->data)){
				$this->template = preg_replace('/\{([a-z0-9\-_]+)\}/is', "'.$$1.'", $this->template);
				reset($data);
				foreach($this->data as $key => $value) $$key = $value;
				eval("\$this->template = '$this->template';");
				reset($this->data);
				foreach($this->data as $key => $value) unset($$key);
			}
			$this->template = str_replace("\'", "'", $this->template );
			return True;
		}else{
			$this->error(TEMPLATE_REQ_ARRAY_TYPE);
		}
	}
	
	/**
	 * If dont send id information, will replace all template!
	 * If just first parameter defined.
	 * looking into a template and return string with the information
	 * of the first html tag found with the respective id
	 * but if you define the second parameter
	 * the information is modified, not returned.
	 * 
	 * @access Public
	 *
	 * @example		$div = $template->html("div_id");
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->html("div_id","other_data");
	 * 			output: <div id="div_id">other_data</div>
	 * 			
	 * @param String[optional] $id
	 * @param String[optional] $data
	 * @return Mixed
	 */
	public function html($id = null, $data = null){
		if($id){
			if(is_string($id)){
				$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
				if($data){
					if(is_string($data)){
						$replace = '<$1 $2>'.$data.'</$1>';
						$this->template = preg_replace($search, $replace, $this->template);
						return True;
					}else{
						$this->error(TEMPLATE_REQ_STRING_TYPE);
					}
				}else{
					preg_match($search, $this->template, $matches);
					return ($matches) ? $matches[0] : False;
				}
			}else{
				$this->error(TEMPLATE_REQ_STRING_TYPE);
			}
		}else{
			$this->template = ($data) ? $data : "";
		}
	}
	
	/**
	 * Looking into a template and replace html tag with $data parameter
	 * 
	 * @access Public
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->replaceWith("div_id","<span>content</span>");
	 * 			output: <span>content</span>
	 * 
	 * @param String $id
	 * @param String $data
	 * @return Boolean
	 */
	public function replaceWith($id, $data){
		if(is_string($id) && is_string($data)){
			$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
			$this->template = preg_replace($search, $data, $this->template);
			return True;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
		}
	}
	
	/**
	 * Looking into a template for a tag with id="$id"
	 * and insert $data after tag.
	 * 
	 * @access Public
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->after("div_id","<span>content</span>");
	 * 			output: <div id="div_id">text</div><span>content</span>
	 * 			
	 * @param String $id
	 * @param String $data
	 * @return Boolean
	 */
	public function after($id, $data){
		if(is_string($id) && is_string($data)){
			$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
			$this->template = preg_replace($search, "$0".$data, $this->template);
			return True;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
		}
	}
	
	/**
	 * Looking into a template for a tag with id="$id"
	 * and insert $data before tag.
	 * 
	 * @access Public
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->before("div_id","<span>content</span>");
	 * 			output: <span>content</span><div id="div_id">text</div>
	 * 			
	 * @param String $id
	 * @param String $data
	 * @return Boolean
	 */
	public function before($id, $data){
		if(is_string($id) && is_string($data)){
			$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
			$this->template = preg_replace($search, $data."$0", $this->template);
			return True;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
		}
	}
	
	/**
	 * Looking into a template for a tag with id="$id"
	 * and append $data
	 * 
	 * @access Public
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->append("div_id","<span>content</span>");
	 * 			output: <div id="div_id">text<span>content</span></div>
	 * 			
	 * @param String $id
	 * @param String $data
	 * @return Boolean
	 */
	public function append($id, $data){
		if(is_string($id) && is_string($data)){
			$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
			$this->template = preg_replace($search, '<$1 $2>$5'.$data.'</$1>', $this->template);
			return True;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
		}
	}
	
	/**
	 * Looking into a template for a tag with id="$id"
	 * and prepend $data
	 * 
	 * @access Public
	 *
	 * @example		template: <div id="div_id">text</div>
	 * 			$template->prepend("div_id","<span>content</span>");
	 * 			output: <div id="div_id"><span>content</span>text</div>
	 * 			
	 * @param String $id
	 * @param String $data
	 * @return Boolean
	 */
	public function prepend($id, $data){
		if(is_string($id) && is_string($data)){
			$search = '/\<([a-z0-9]+)(?:[^\>]*)(id=(?:(\'|"))(?:'.$id.')(?:(\'|"))(?:[^\>]*))\>\s*(.*)\s*\<\/\1\>/i';
			$this->template = preg_replace($search, "<$1 $2>".$data.'$5</$1>', $this->template);
			return True;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
		}
	}
	
	/**
	 * Make a new table and insert data
	 * @access Public
	 *
	 * @example		$template->table();
	 *			output: <table></table>
	 *
	 * @example		$template->table(array("border"=>1));
	 * 			output: <table border="1"></table>
	 *
	 * @example		$template->table(array("border"=>1), 1, 2);
	 * 			output: <table border="1"><tr><td></td><td></td></tr></table>
	 * 			
	 * @example		$data[1][1] = "data";
	 * 			$template->table(array("border"=>1), 1, 2, $data);
	 * 			output: <table border="1"><tr><td>dato</td><td></td></tr></table>
	 * 			
	 * @param Array[optional] $attributes Attributes
	 * @param Integer[optional] $rows Rows
	 * @param Integer[optional] $cols Columns
	 * @param Array[optional] $content[][] Multidimensional
	 * @return String <table>
	 */
	public function table($attributes = array(), $rows = 0, $cols = 0, $content = array()){
		$table = "<table"; $table_content = "";
		if(!empty($attributes)){
			if(is_array($attributes)){
				foreach($attributes as $key => $value){
					$key = strtolower($key);
					if(in_array($key, $this->attributes)){
						$table .= ' '.$key.'="'.$value.'"';
					}else{
						$this->error(sprintf(TEMPLATE_ATTR_ERROR, '<b>'.$key.'</b>'));
					}
				}
			}else{
				$this->error(TEMPLATE_REQ_ARRAY_TYPE);
			}
		}
		if(!empty($content)){
			if(!is_array($content)) $this->error(TEMPLATE_REQ_ARRAY_TYPE);
		}
		if(is_int($rows) && is_int($cols)){
			if($cols > 0 && $rows > 0){
				for($i = 1; $i <= $rows; ++$i){
					$table_content .= "<tr>";
					for($o = 1; $o <= $cols; ++$o){
						$table_content .= (!empty($content[$i][$o])) ? "<td>".$content[$i][$o]."</td>" : "<td>&nbsp;</td>";
					}
					$table_content .= "</tr>";
				}
			}
		}
		$table .= ">".$table_content."</table>";
		return $table;
	}
	
	/**
	 * Make a new div with attributes and content
	 * @access Public
	 *
	 * @example		$template->div(array("id"=>"div_id", "class"=>"div_class"), "div content");
	 * 			output: <div id="div_id" class="div_class">div content</div>
	 * 			
	 * @example		$template->div("", "div content");
	 * 			output: <div>div content</div>
	 * 			
	 * @example		$template->div();
	 * 			output: <div></div>
	 * 			
	 * @param Array[optional] $attributes
	 * @param String[optional] $content
	 * @return String <div>
	 */
	public function div($attributes = array(), $content = null){
		$div = "<div";
		if(!empty($attributes)){
			if(is_array($attributes)){
				foreach($attributes as $key => $value){
					$key = strtolower($key);
					if(in_array($key, $this->attributes)){
						$div .= ' '.$key.'="'.$value.'"';
					}else{
						$this->error(sprintf(TEMPLATE_ATTR_ERROR, '<b>'.$key.'</b>'));
					}
				}
			}else{
				$this->error(TEMPLATE_REQ_ARRAY_TYPE);
			}
		}
		if($content){
			if(is_string($content) || is_integer($content)){
				$content = $content;
			}else{
				$content = "";
			}
		}
		$div .= ">".$content."</div>";
		return $div;
	}
	
	/**
	 * Make a new span with attributes and content
	 * @access Public
	 *
	 * @example		$template->span(array("id"=>"span_id", "class"=>"span_class"), "span content");
	 * 			output: <span id="span_id" class="span_class">span content</span>
	 * 			
	 * @example		$template->span("", "span content");
	 * 			output: <span>span content</span>
	 * 			
	 * @example		$template->span();
	 * 			output: <span></span>
	 * 			
	 * @param Array[optional] $attributes
	 * @param String[optional] $content
	 * @return String <span>
	 */
	public function span($attributes = array(), $content = null){
		$span = "<span";
		if(!empty($attributes)){
			if(is_array($attributes)){
				foreach($attributes as $key => $value){
					$key = strtolower($key);
					if(in_array($key, $this->attributes)){
						$span .= ' '.$key.'="'.$value.'"';
					}else{
						$this->error(sprintf(TEMPLATE_ATTR_ERROR, '<b>'.$key.'</b>'));
					}
				}
			}else{
				$this->error(TEMPLATE_REQ_ARRAY_TYPE);
			}
		}
		if($content){
			if(is_string($content) || is_integer($content)){
				$content = $content;
			}else{
				$content = "";
			}
		}
		$span .= ">".$content."</span>";
		return $span;
	}
	
	/**
	 * Make a new form with attributes and content
	 * @access Public
	 *
	 * @example		$template->form(array("method"=>"post", "action"=>"index.php"), $inputs);
	 * 			output: <form method="post" action="index.php"><inputs></form>
	 * 			
	 * @example		$template->form("", $inputs);
	 * 			output: <form><inputs></form>
	 * 			
	 * @example		$template->span();
	 * 			output: <form></form>
	 * 			
	 * @param Array[optional] $attributes
	 * @param String[optional] $content
	 * @return String <form>
	 */
	public function form($attributes = array(), $content = null){
		$form = "<form";
		if(!empty($attributes)){
			if(is_array($attributes)){
				foreach($attributes as $key => $value){
					$key = strtolower($key);
					if(in_array($key, $this->attributes)){
						$form .= ' '.$key.'="'.$value.'"';
					}else{
						$this->error(sprintf(TEMPLATE_ATTR_ERROR, '<b>'.$key.'</b>'));
					}
				}
			}else{
				$this->error(TEMPLATE_REQ_ARRAY_TYPE);
			}
		}
		if($content){
			if(is_string($content) || is_integer($content)){
				$content = $content;
			}else{
				$content = "";
			}
		}
		$form .= ">".$content."</form>";
		return $form;
	}
	
	/**
	 * Make a new input with attributes
	 * @access Public
	 * 			
	 * @example		$template->input(array("type"=>"text", "name"=>"input_name"));
	 * 			output: <input type="text" name="input_name" />
	 * 
	 * @param Array[optional] $attributes
	 * @return String <input />
	 */
	public function input($attributes = array()){
		$input = "<input";
		if(!empty($attributes)){
			if(is_array($attributes)){
				foreach($attributes as $key => $value){
					$key = strtolower($key);
					if(in_array($key, $this->attributes)){
						$input .= ' '.$key.'="'.$value.'"';
					}else{
						$this->error(sprintf(TEMPLATE_ATTR_ERROR, '<b>'.$key.'</b>'));
					}
				}
			}else{
				$this->error(TEMPLATE_REQ_ARRAY_TYPE);
			}
		}
		$input .= " />";
		return $input;
	}

	/**
	 * Getting values previously assigned
	 * @access Public
	 *
	 * @return String HTML
	 */
	public function render(){
		if($this->template){
			return $this->template;
		}else{
			$this->error(TEMPLATE_NODATA_ERROR);
		}
	}
	
	/**
	 * Shows up a box with an error text message
	 * @access Private
	 * 
	 * @param String $string_error
	 * @return Warning Echo
	 */
	private function error($string_error){
		if(is_string($string_error)){
			$message = "<div style='color:#666;font-size:12px;border:1px dashed #999;margin:20px;padding:3px;'>";
			$message .= "<div style='color:red;font-size:13px;'><b>Warning!</b></div>";
			$message .= "<div>$string_error</div>";
			$message .= "</div>";
			echo $message;
			return False;
		}else{
			$this->error(TEMPLATE_REQ_STRING_TYPE);
			return False;
		}
	}
}
?>