<?php

class PHPo {

	private $path;
	private $poLines;
	private $header = array();
	private $flags = array();
	private $strId = array();


	public function __construct($path) {
		if (!is_file($path)) {
			throw new Exception("Error: The file you have passed doesn't exist.");
		} elseif (substr($path, strrpos($path, ".")) != '.po') {
			throw new Exception("Error: The passed file should be a \'.po\' file.");
		} else {
			$this->poLines = file($path);
			// $file = file_get_contents($path);
			$this->parsePO();
		}
	}

	/*
	* TODO
	* Getter and setters
	*/

	public function getPath()
	{
	    return $this->path;
	}
	
	public function setPath($path)
	{
	    $this->path = $path;
	    return $this;
	}

	/*
	* Functions
	*/

	private function parsePO()
	{
		// $i is the count of blank lines. We use it as the ID of strings too.
		$i = 0;
		foreach ($this->poLines as $lines => $value) {
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