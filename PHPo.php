<?php

class PHPoBlock 
{

}


class PHPoHeader extends PHPoBlock
{
	
	/**
	 * Header comments before header content
	 * @var array
	 */
	private $comments = array();
	
	/**
	 * po file header attribute
	 * @var array
	 */
	private $attributes = array();
	
	/**
	 * Push a comment
	 * @param string $comment
	 */
	public function addComment($comment)
	{
		$this->comments[] = $comment;
	}
	
	/**
	 * Get all comments
	 * @return array
	 */
	public function getComments()
	{
		return $this->comments;
	}
	
	/**
	 * Add an attribute from po header
	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function addAttribute($name, $value)
	{
		$this->attributes[$name] = trim($value);
	}
	
	/**
	 * Get a header attribute
	 * @param string $name
	 * @param string $default
	 * 
	 * @return string
	 */
	
	public function getAttribute($name, $default = null)
	{
		if (isset($this->attributes[$name]))
			return $this->attributes[$name];
		else
			return $default;
	}
	
	/**
	 * Get all attributes
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}
	
}

class PHPoStatement extends PHPoBlock
{
	
}

class PHPo {


	
	/**
	 * File address
	 * @var string
	 */
	private $fileName;
	
	/**
	 * Array contain file lines
	 * @var array
	 */
	private $poLines;
	
	
	/**
	 * Header of this po file
	 * @var PHPoHeader
	 */
	private $header;
	/**
	 * Array of statements
	 * @var array
	 */
	private $statements = array();
	private $flags = array();
	private $strId = array();


	public function __construct($file = false) 
	{
		if ($file)
		{
			$this->loadFile($file);
			$this->parsePO();	
		}
	}
	
	public function loadFile($file)
	{
		if (!is_file($file) && is_readable($file))
			throw new Exception("Error: The file you have passed doesn't exist.");
		elseif (substr($file, strrpos($file, ".")) != '.po')
			throw new Exception("Error: The passed file should be a \'.po\' file.");		
		$this->fileName = $file;
		$this->poLines = file($this->fileName);		
	}
	
	private function getNextLine()
	{
		$next = array_shift($this->poLines);
		//Trim do the trick :D
		return trim($next);
	}
	
	private function parseHeder()
	{
		$this->header = new PHPoHeader();
		while ($line = $this->getNextLine())
		{
			if (substr($line, 0, 2) == '# ')
			{
				$line = substr($line, 2);
				$this->header->addComment($line);
			}
			elseif ($line{0} == '"' )
			{
				$line = substr( $line, 1 , strlen($line) - 2);
				//Its normal to have an \n in the end, remove that since not usefull here
				$line = str_replace('\n', '', $line);
				$attr = explode(':', $line);
				if (count($attr) == 2)
					$this->header->addAttribute($attr[0], $attr[1]);
				//else just ignore it!
			}
		}
	}
	
	private function parseABlock()
	{
		while ($line = $this->getNextLine())
		{
			
		}
	}
	
	/**
	 * Parse po file into an array
	 * 
	 * @return array
	 */

	public function parsePO()
	{
		// Reset current result
		$this->statements = array();
		//First of all, parse header 
		$this->parseHeder();
		
		var_dump($this->header);
		return;
		// $i is the count of blank lines. We use it as the ID of strings too.
		$i = 0;
		foreach ($this->poLines as $line) {
			// Until we don't reach a blank line, we're still in header.
			if ($i == 0) {
				switch ($value) {
					// If it start with "# " or "#" It's a header comment.
					case substr($value, 0, 2)  == '# ':
						$this->header[] = $value;
						break;
					// If the line starts with a " befor the first empty line, It's our flags.
					case substr($value, 0, 1)  == '"':
						// if ($i == 0) {
							// We want to know the flags and put them in key => value order
							$ex = explode(':', $value, 2);
							// Need to get rid of those qutations
							$this->flags[str_replace('"', '', $ex[0])] = str_replace('"', '', $ex[1]);
						// }
						break;
					// Just for finding the first blank line
					case strlen($value) == 1:
						$i++;
						break;
					}
			// We have reached to the first blank line, so we've passed the Headers land
			// and now we're in Translations territory.
			} else {
				switch ($value) {
					// If it starts with a "#:" it's the string place
					case substr($value, 0, 2) == "#:":
						$this->strId[$i]['strPlace'] = substr($value, 3, -1);
						break;
					// If it starts with a "#," it's a flag (Usually fuzzy)
					case substr($value, 0, 2) == "#,":
						$this->strId[$i]['strFlag'] = substr($value, 3, -1);
						break;
					// If it starts with a "#." it's a translation comment
					case substr($value, 0, 2) == "#.":
						$this->strId[$i]['strComment'] = substr($value, 3, -1);
						break;
					// If it starts with a "msgid" it's the orginal string
					case substr($value, 0, 5) == "msgid":
						// $this->strId[$i]['msgId'] = str_replace('"', '', $value);
						$this->strId[$i]['msgId'] = preg_replace('/^[a-zA-Z]{5}\s?"(.+)"/', '\\1', $value);
						break;
					// If it starts with a "msgstr" it's the translated string
					case substr($value, 0, 6) == "msgstr":
						$this->strId[$i]['msgStr'] = preg_replace('/^[a-zA-Z]{6}\s?"(.+)"/', '\\1', $value);
						break;
					default:
						$i++;
				}
			}
		}
		print_r($this->strId);

	}

	private function instantiate()
	{
		
	}


}
