<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if ( ! function_exists('css_url'))
{
	function css_url($nom)
	{
		return base_url() . 'assets/css/' . $nom . '.css';
	}
}

if ( ! function_exists('js_url'))
{
	function js_url($nom)
	{
		return base_url() . 'assets/js/' . $nom . '.js';
	}
}

if ( ! function_exists('js_php_url'))
{
	function js_php_url($nom)
	{
		return base_url() . 'assets/js/' . $nom;
	}
}

if ( ! function_exists('img_url'))
{
	function img_url($nom)
	{
		return base_url() . 'assets/images/' . $nom;
	}
}

if ( ! function_exists('img'))
{
	function img($nom, $alt = '')
	{
		return '<img src="' . img_url($nom) . '" alt="' . $alt . '" />';
	}
}

if ( ! function_exists('form_dropdown')){
	function form_dropdown($id,$name,$styles,$default_content){
		$output = "<div id='filtre$id' style='float:left;margin:2px;'>
		<label for='$id'>$default_content</label><br />
		<select id='$id' name='$name' class='$styles'  data-placeholder='$default_content'>";
		$output.="<option value=''>$default_content</option></select></div>";
		return $output;
	}
}

if ( ! function_exists('form_dropdown2')){
	function form_dropdown2($id,$name,$styles,$default_content,$titre){
		$output = "<div id='filtre$id' style='float:left;margin:2px;'>
		<label for='$id'>$titre</label><br />
		<select id='$id' name='$name' class='$styles' data-placeholder='$default_content'>";
		foreach ($default_content as $cle => $valeur)
			$output.="<option value='$cle'>$valeur</option>";
		$output.=" </select>
		</div>";
		return $output;
	}
}

if ( ! function_exists('form_dropdown3')){
	function form_dropdown3($id,$name,$styles,$default_content){
		$output="";

		if(is_array($default_content)){
			$output = "<div id='filtre$id' style='float:left;margin:2px;' data-placeholder='$default_content'>
			<label for='$id'>{$default_content['title']}</label><br />
			<select id='$id' name='$name' class='$styles'>";

			foreach ($default_content as $option)
				if ($option!=$default_content['title'])
				$output.="<option value=''>$option</option>";
		}
		else $output.="<option value=''>".$default_content."</option>";
		$output.=" </select></div>";
		return $output;
	}
}
