<?php

/*
+-----------------------------------------------------------------+
|+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++|
|## Kirby Publisher+++++++++++++++++++++++++++++++++++++++++++++++|
|+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++|
|PHP class to publish Kirby articles automatically based on YAML +|
|directive.+++++++++++++++++++++++++++++++++++++++++++++++++++++++|
|+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++|
+-----------------------------------------------------------------+
*/


class kirby_publisher{

/*
+-----------------------------------------------------------------+
|Kirby Publisher Configuration Parameters:                        |
|                                                                 |
|$CONTENT_PATH ▶▶ The Kirby content directory (default =          |
|/var/www/content)                                                |
|                                                                 |
|$TOOLKIT_PATH ▷▷ The Kirby toolkit directory (default =          |
|/var/www/kirby/toolkit)                                          |
|                                                                 |
|$DIRECTORY_DIGITS ▶▶ Number of digits used in preceding directory|
|names (default = 4)                                              |
|                                                                 |
|$YAML_DIRECTIVE ▷▷ The YAML variable name to look for Publish    |
|status (default = published)                                     |
|                                                                 |
|$PUBLIHSED_VALUE ▶▶ The value used with the $YAML_DIRECTIVE to   |
|indicate the post should be published (default = Publish)        |
|e.g.                                                             |
|----                                                             |
|published: Publish                                               |
|----                                                             |
|                                                                 |
|$DRAFT_VALUE ▷▷ The value used with the $YAML_DIRECTIVE to       |
|indicate the post should be drafted (default = Draft)            |
|e.g.                                                             |
|----                                                             |
|published: Draft                                                 |
|----                                                             |
+-----------------------------------------------------------------+

*/

	private $CONTENT_PATH;
	private $TOOLKIT_PATH;
	private $DIRECTORY_DIGITS;
	private $YAML_DIRECTIVE;
	private $PUBLIHSED_VALUE;
	private $DRAFT_VALUE;

/*
+-----------------------------------------------------------------+
|-----------------------------------------------------------------|
|kirby_publisher()------------------------------------------------|
|-----------------------------------------------------------------|
|Class constructor------------------------------------------------|
|-----------------------------------------------------------------|
|arguments--------------------------------------------------------|
|(----------------------------------------------------------------|
|$content="/var/www/public_html/content"--------------------------|
|$toolkit="/var/www/public_html/kirby/toolkit"--------------------|
|$digits=4--------------------------------------------------------|
|$yaml="published"------------------------------------------------|
|$publish="Publish"-----------------------------------------------|
|$draft="Draft"---------------------------------------------------|
|)----------------------------------------------------------------|
|-----------------------------------------------------------------|
|returns (kirby_publisher)----------------------------------------|
|-----------------------------------------------------------------|
+-----------------------------------------------------------------+
*/
	function kirby_publisher($content="/var/www/public_html/content",$toolkit="/var/www/public_html/kirby/toolkit",$digits=4,$yaml="published",$publish="Publish",$draft="Draft"){
		$this->CONTENT_PATH=$content;
		$this->TOOLKIT_PATH=$toolkit;
		$this->DIRECTORY_DIGITS=$digits;
		$this->YAML_DIRECTIVE=$yaml;
		$this->PUBLIHSED_VALUE=$publish;
		$this->DRAFT_VALUE=$draft;
	}

/*
+-----------------------------------------------------------------+
|-----------------------------------------------------------------|
|get_last_id()----------------------------------------------------|
|-----------------------------------------------------------------|
|returns the next id to be used for the upcoming post.------------|
|-----------------------------------------------------------------|
|arguments (void)-------------------------------------------------|
|-----------------------------------------------------------------|
|returns (string)-------------------------------------------------|
|-----------------------------------------------------------------|
+-----------------------------------------------------------------+
*/
	function get_last_id(){
		$last_id=0;
		$directories = scandir($this->CONTENT_PATH,1);

		//Check published posts

		
		$regex_pattern = "/^(\d{".$this->DIRECTORY_DIGITS."})-/";
		$published_array = preg_grep($regex_pattern, $directories);
		
		$last_id = substr(array_values($published_array)[0],0,$this->DIRECTORY_DIGITS);

		//Check draft posts

		$draft_array = array_diff($directories, $published_array);

		foreach ($draft_array as $directory){

			if (file_exists($this->CONTENT_PATH."/".$directory."/".".file_id")){
				$id_file=fopen($this->CONTENT_PATH."/".$directory."/".".file_id","r");
				$directory_id=fgets($id_file);
				fclose($id_file);
				$last_id=($last_id>$directory_id?$last_id:$directory_id);
			}
		}

		$last_id++;
		return str_pad($last_id,$this->DIRECTORY_DIGITS,"0",STR_PAD_LEFT);
	}

/*
+-----------------------------------------------------------------+
|-----------------------------------------------------------------|
|is_published()---------------------------------------------------|
|-----------------------------------------------------------------|
|returns the publishing status true or false for specific article |
|directory.-------------------------------------------------------|
|-----------------------------------------------------------------|
|arguments (void)-------------------------------------------------|
|-----------------------------------------------------------------|
|returns (bool)---------------------------------------------------|
|-----------------------------------------------------------------|
+-----------------------------------------------------------------+
*/

	function is_published($directory){
		$regex_pattern = "/^(\d{".$this->DIRECTORY_DIGITS."})-/";
		return preg_match($regex_pattern, $directory);
	}
	
/*
+-------------------------------------------------------------------------+
|-------------------------------------------------------------------------|
|publish_toggle()---------------------------------------------------------|
|-------------------------------------------------------------------------|
|Changes the publish status for article directory based on ---------------|
|$published_status.-------------------------------------------------------|
|-------------------------------------------------------------------------|
|arguments (--------------------------------------------------------------|
|$directory //directory name inside content directory---------------------|
|$published_status // 0 ▶▶ currently draft || 1 ▶▶ currently published----|
|)------------------------------------------------------------------------|
|-------------------------------------------------------------------------|
|returns (void)-----------------------------------------------------------|
|-------------------------------------------------------------------------|
+-------------------------------------------------------------------------+
*/

	function publish_toggle($directory, $published_status){
	
		if(!$published_status){
			
			if (file_exists($this->CONTENT_PATH."/".$directory."/".".file_id")){
				$id_file=fopen($this->CONTENT_PATH."/".$directory."/".".file_id","r");
				$directory_id=fgets($id_file);
				
				fclose($id_file);
				rename($this->CONTENT_PATH."/".$directory,$this->CONTENT_PATH."/".$directory_id."-".$directory);
				unlink($this->CONTENT_PATH."/".$directory_id."-".$directory."/".".file_id");
			}else{
				$directory_id=$this->get_last_id();
				rename($this->CONTENT_PATH."/".$directory,$this->CONTENT_PATH."/".$directory_id."-".$directory);
			}
		}else{
			$directory_id=substr($directory, 0,$this->DIRECTORY_DIGITS);
			$article_name=substr($directory,$this->DIRECTORY_DIGITS + 1);
			$id_file=fopen($this->CONTENT_PATH."/".$directory."/".".file_id", "w");
			fwrite($id_file, $directory_id);
			fclose($id_file);
			rename($this->CONTENT_PATH."/".$directory,$this->CONTENT_PATH."/".$article_name);
		}
	}
/*
+-----------------------------------------------------------------+
|-----------------------------------------------------------------|
|update_published_status()----------------------------------------|
|-----------------------------------------------------------------|
|Scans content directory and changes the published status for each|
|article based on the YAML variable in each blogarticle.txt file.-|
|-----------------------------------------------------------------|
|arguments (void)-------------------------------------------------|
|-----------------------------------------------------------------|
|returns (void)---------------------------------------------------|
|-----------------------------------------------------------------|
+-----------------------------------------------------------------+

*/
	function update_published_status(){
		require_once($this->TOOLKIT_PATH."/lib/yaml.php");
		require_once($this->TOOLKIT_PATH."/vendors/yaml/yaml.php");
		$directories = scandir($this->CONTENT_PATH);
		foreach ($directories as $directory){
			$published_status = $this->is_published($directory);
			if (file_exists($this->CONTENT_PATH."/".$directory."/"."blogarticle.txt")){
				$yaml = Yaml::read($this->CONTENT_PATH."/".$directory."/"."blogarticle.txt");
				if(array_key_exists($this->YAML_DIRECTIVE,$yaml) && $yaml[$this->YAML_DIRECTIVE]===$this->PUBLIHSED_VALUE && $published_status==0){
					$this->publish_toggle($directory,0);
				}elseif (array_key_exists($this->YAML_DIRECTIVE,$yaml) && $yaml[$this->YAML_DIRECTIVE]==$this->DRAFT_VALUE && $published_status==1) {
					$this->publish_toggle($directory,1);
				}
			}
		}
	}
	
}