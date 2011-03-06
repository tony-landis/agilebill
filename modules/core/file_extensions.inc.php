<?PHP

class file_extensions
{
	function file_extensions()
	{
		$this->arr = Array
		(
			Array ('name' => 'jpg',     'type' => 'image/jpeg',            			'disposition' => 'inline'),
			Array ('name' => 'jpeg',    'type' => 'image/jpeg',            			'disposition' => 'inline'),
			Array ('name' => 'jpe',    	'type' => 'image/jpeg',            			'disposition' => 'inline'),
			Array ('name' => 'gif',     'type' => 'image/gif',             			'disposition' => 'inline'),
			Array ('name' => 'bmp',     'type' => 'image/bmp',             			'disposition' => 'inline'),
			Array ('name' => 'tif',     'type' => 'image/tif',             			'disposition' => 'inline'),
			Array ('name' => 'png',     'type' => 'image/png',             			'disposition' => 'inline'),
			Array ('name' => 'wbmp',    'type' => 'image/vnd.wap.wbmp',             'disposition' => 'inline'),
			
			Array ('name' => 'pdf',     'type' => 'application/pdf',       			'disposition' => 'inline'),
			Array ('name' => 'exe',     'type' => 'application/octet-stream',		'disposition'=>  'attatchment'),
			Array ('name' => 'zip',     'type' => 'application/x-zip',     			'disposition' => 'attatchment'),
			Array ('name' => 'gzip',    'type' => 'application/gzip',      			'disposition' => 'attatchment'),
			Array ('name' => 'tgz',     'type' => 'application/tgz',       			'disposition' => 'attatchment'),
			Array ('name' => 'gz',      'type' => 'application/gz',        			'disposition' => 'attatchment'),
			Array ('name' => 'doc',     'type' => 'application/ms-word',   			'disposition' => 'inline'),
			Array ('name' => 'xls',     'type' => 'application/ms-excel',  			'disposition' => 'inline'),
			Array ('name' => 'csv',     'type' => 'application/ms-excel',  			'disposition' => 'inline'),
			Array ('name' => 'swf',   	'type' => 'application/x-shockwave-flash', 	'disposition' => 'inline'),
			
			Array ('name' => 'txt',     'type' => 'text/plain',            			'disposition' => 'inline'),
			Array ('name' => 'text',    'type' => 'text/plain',            			'disposition' => 'inline'),
			Array ('name' => 'rtf',     'type' => 'text/richtext',         			'disposition' => 'inline'),
			Array ('name' => 'xml',     'type' => 'text/xml',       				'disposition' => 'inline'),
			Array ('name' => 'css',     'type' => 'text/css',            			'disposition' => 'inline'),
			Array ('name' => 'js',      'type' => 'text/plain',            			'disposition' => 'inline'),
			Array ('name' => 'wml',     'type' => 'text/vnd.wap.wml',            	'disposition' => 'inline'),
			
			Array ('name' => 'avi',     'type' => 'video/avi',            			'disposition' => 'attatchment'),
			Array ('name' => 'mpg',     'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
			Array ('name' => 'mpeg',    'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
			Array ('name' => 'mpe',     'type' => 'video/mpeg',            			'disposition' => 'attatchment'),
			Array ('name' => 'wmv',   	'type' => 'video/x-ms-wmv', 				'disposition' => 'attatchment'),
			Array ('name' => 'asf',   	'type' => 'video/x-ms-asf', 				'disposition' => 'attatchment')
		);
	}
	
	function content_type($file)
	{
		for($i=0; $i<count($this->arr); $i++)		
			if(preg_match('/'.$this->arr[$i]['name'].'$/i', $file))
				return $this->arr[$i]['type']; 					
	}
	
	/**
	 * Set headers for a specific file extension 
	 */
	function set_headers_ext($ext,$name="Download",$size=0) {  
		foreach($this->arr as $types) {
			if($types['name'] == strtolower($ext)) {
				header("Content-type: {$types['type']}");
				header("Content-disposition: \"{$types['disposition']}\"; filename=\"$name.$ext\"; size=\"$size\""); 			 
				return $types['type'];
			}
		}  
	}
}       
?>