<?php
/**
 * Block base class
 * @author f0rud
 *
 */
class PHPoBlock 
{

}

/**
 * Po header
 * @author f0rud
 *
 */
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
	
	/**
	 * Export header to string
	 */
	public function __toString()
	{
		$result = '';
		//First export header comments
		foreach ($this->comments as $comment)
			if ($comment)
				$result .= "# " . $comment . PHP_EOL;
			else 
				$result .= '#' . PHP_EOL;
		
		//Then there is an empty msgid, msgstr block pair
		$result .= 'msgid ""' . PHP_EOL;
		$result .= 'msgstr ""' . PHP_EOL;
		
		//Now its time to export meta strings
		foreach($this->attributes as $attr => $value)
			$result .= '"' . $attr . ': ' . $value . '"' . PHP_EOL;
		
		return $result;
	}
	
}

/**
 * po Statement
 * @author f0rud
 *
 */
class PHPoStatement extends PHPoBlock
{
	/**
	 * Placements of current block in source files
	 * @var array 
	 */
	private $placements = array ();
	
	
	/**
	 * Flags of this block
	 * @var array
	 */
	private $flags = array();
	
	/**
	 * Translator comments
	 * @var array
	 */
	private $translatorComments = array();
	
	/**
	 * Extracted comments
	 * @var array
	 */
	private $extractedComments = array();
	
	/**
	 * @var array
	 */
	private $previousUntranslatedStrings = array();
	
	/**
	 * msgid 
	 * @var array
	 */
	private $msgId = array();
	
	/**
	 * msgstr
	 * @var unknown_type
	 */
	private $msgStr = array();

	/**
	 * message context
	 * @var string
	 */
	private $msgctxt;
	
	/**
	 * plural message if any
	 * @var string
	 */
	private $msgIdPlural ;
	
	/**
	 * Add a placements
	 * @param string $strPlace
	 */
	public function addPlacement($strPlace)
	{
		$this->placements[] = $strPlace;
	}
	
	/**
	 * Get placements
	 * @return array();
	 */
	public function getPlacements()
	{
		return $this->placements;
	}
	
	/**
	 * Add flags to current flag list
	 * @param string|array $flags
	 */
	public function addFlag($flags)
	{
		if (!is_array($flags))
			$flags = array ($flags);
		//TODO: validate flag type
		foreach ($flags as $flag)
			$this->flags[] = trim($flag);
		$this->flags = array_unique($this->flags);
	}
	
	/**
	 * Current block has this flag or not?
	 * @param string $flag
	 * @return boolean
	 */
	public function hasFlasg($flag)
	{
		return in_array($flag, $this->flags);
	}
	
	/**
	 * Get all flags
	 * 
	 * @return array
	 */
	public function getFlags()
	{
		return $this->flags;
	}
	
	/**
	 * Add a translator comment
	 * @param string $commnet
	 */
	public function addTranslatorComment($commnet)
	{
		$this->translatorComments[] = $commnet;
	}
	
	/**
	 * get all translator comments
	 * @return array
	 */
	public function getTranslatorComments()
	{
		return $this->translatorComments;
	}
	
	/**
	 * Add a extracted comment
	 * @param string $comment
	 */
	public function addExtractedComment($comment)
	{
		$this->extractedComments[] = $comment;
	}
	
	/**
	 * get all extracted comments
	 * @return array
	 */
	public function getExtractedComments()
	{
		return $this->extractedComments;
	}

	/**
	 * 
	 * @param string $comment
	 */
	public function addPreviousUntranslatedString($comment)
	{
		$this->previousUntranslatedStrings[] = $comment;
	}
	
	/**
	 * @return array
	 */
	public function getPreviousUntranslatedStrings()
	{
		return $this->previousUntranslatedStrings;
	}	
	
	/**
	 * Add a msg id line
	 * @param string $msg
	 */
	public function addMsgId($msg)
	{
		$this->msgId[] = $msg;
	}
	
	/**
	 * Get all msh id as an array
	 * @return array
	 */
	
	public function getMsgId()
	{
		return $this->msgId;
	}
	
	/**
	 * Get msg id as a single string
	 * @return string
	 */
	public function getMsgIdAsString()
	{
		return implode(' ', $this->msgId);
	}
	
	/**
	 * Add a msgstr
	 * @param string $msg
	 */
	public function addMsgStr($msg)
	{
		$this->msgStr[] = $msg;
	}
	
	/**
	 * Get all message str as an array
	 * @return array
	 */
	public function getMsgStr()
	{
		return $this->msgStr;
	}
	
	/**
	 * get message context 
	 * @return string 
	 */
	public function getMsgctxt()
	{
		return $this->msgctxt;
	}
	
	/**
	 * Set message context 
	 * @param string $ctxt
	 */
	public function setMsgctxt($ctxt)
	{
		$this->msgctxt = $ctxt;
	}
	
	/**
	 * Add plural message
	 * @param string $msg
	 */
	public function setMsgPlural($msg)
	{
		$this->msgIdPlural= $msg;
	}
	
	/**
	 * Get all plural message 
	 * @return string
	 */
	public function getMsgPlural()
	{
		return $this->msgIdPlural;
	}
	

	/**
	 * Get messages as a single string
	 * @return string
	 */
	public function getMsgStrAsString()
	{
		return implode(' ', $this->msgStr);
	}

	/**
	 * Return true if there is a translation and false if not. fuzzy set as not
	 * @return boolean
	 **/
	function getIsTranslated()
	{
		if ($this->hasFlasg('fuzzy'))
			return false;
		elseif (count($this->msgStr) == 1 && $this->msgStr[0])
			return true;
		elseif (count($this->msgStr) > 1 && $this->msgStr[1])
			return true; 
		return false;
	}
	
	/**
	 * Convert this object to string
	 * @return string
	 */
	public function __toString()
	{
		//First an empty line, means start of block
		$result = PHP_EOL;
		
		//Translator comments
		foreach ($this->translatorComments as $comment)
			$result .= '# ' . $comment . PHP_EOL;
		
		//Extracted comments
		foreach ($this->extractedComments as $comment)
			$result .= '#.' . $comment . PHP_EOL;
		
		//Refrences
		foreach ($this->placements as $comment)
			$result .= '#:' . $comment . PHP_EOL;
		
		//Flags 
		if (count($this->flags) > 0)
			$result .= '#,' . implode(',', $this->flags) . PHP_EOL;
		
		//Previus untranslated strings
		foreach ($this->previousUntranslatedStrings as $comment)
			$result .= '#|' . $comment . PHP_EOL;
		
		//Is there a ctxt?
		if ($this->msgctxt)
			$result .= 'msgctxt "' . $this->msgctxt . '"' . PHP_EOL;
		
		//OK now its time for msgid's
		$i = 0;
		foreach ($this->msgId as $str)
		{
			if ($i == 0)
				$result .= 'msgid "' . $str . '"' . PHP_EOL;
			else
				$result .= '"' . $str . '"' . PHP_EOL;
			$i++;
		}
		
		//Is there any plural form?
		$hasPlural = false;
		if ($this->msgIdPlural)
		{
			$hasPlural = true;
			$result .= 'msgid_plural "' . $this->msgIdPlural . '"' . PHP_EOL;
		}
		
		//OK now its time for msgstr's
		$i = 0;
		foreach ($this->msgStr as $str)
		{
			if ($hasPlural)
				$result .= 'msgstr[' . $i . '] "' . $str . '"' . PHP_EOL;
			elseif ($i == 0)
				$result .= 'msgstr "' . $str . '"' . PHP_EOL;
			else
				$result .= '"' . $str . '"' . PHP_EOL;
			$i++;
		}	

		return $result;
	}
}

/**
 * PHPo class to parse and use po file
 * @author f0rud
 *
 */
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

	/**
	 * Constructor
	 * @param string $file po file address
	 */
	public function __construct($file = false) 
	{
		if ($file)
		{
			$this->loadFile($file);
			$this->parsePO();	
		}
	}
	
	/**
	 * Load a file
	 * @param string $file
	 * @throws Exception
	 */
	public function loadFile($file)
	{
		if (!is_file($file) && is_readable($file))
			throw new Exception("Error: The file you have passed doesn't exist.");
		elseif (substr($file, strrpos($file, ".")) != '.po')
			throw new Exception("Error: The passed file should be a \'.po\' file.");		
		$this->fileName = $file;
		$this->poLines = file($this->fileName);		
	}
	
	/**
	 * Get next line from queue
	 * @return string
	 */
	private function getNextLine()
	{
		if (count($this->poLines) == 0)
			return false;
		$next = array_shift($this->poLines);
		//Trim do the trick :D
		return trim($next);
	}
	
	/**
	 * Parse header part
	 */
	private function parseHeader()
	{
		$this->header = new PHPoHeader();
		while ($line = $this->getNextLine())
		{
			if ($line{0} == '#')
			{
				$line = substr($line, 1);
				$this->header->addComment(trim ($line));
			}
			elseif ($line{0} == '"' )
			{
				$line = substr( $line, 1 , strlen($line) - 2);
				//Its normal to have an \n in the end, remove that since not usefull here
				//$line = str_replace('\n', '', $line);
				$attr = explode(':', $line, 2);
				if (count($attr) == 2)
					$this->header->addAttribute($attr[0], $attr[1]);
				//else just ignore it!
			}
		}
	}
	
	/**
	 * Parse a statment block
	 * return false on last block
	 * @return boolean 
	 */
	private function parseABlock()
	{
		$statement = new PHPoStatement();
		while ($line = $this->getNextLine())
		{
			$two = substr($line, 0, 2);
			if ($two == "#:")
			{
				$line = substr($line, 2);
				$statement->addPlacement($line);
			}
			elseif ($two == "#,")
			{
				$line = substr($line, 2);
				$flags = explode(',', $line);
				$statement->addFlag($flags);
			}
			elseif ($two == '#.')
			{
				$line = substr($line, 2);
				$statement->addExtractedComment($line);
			}	
			elseif ($two == '#|')
			{
				$line = substr($line, 2);
				$statement->addPreviousUntranslatedString($line);
			}
			elseif ($two{0} == '#')
			{
				$line = substr($line, 1);
				$statement->addTranslatorComment(trim($line));
			}
			else {
				//First check for msgctxt 
				if (substr($line, 0, 7) == 'msgctxt')
				{
					$line = preg_replace('/^[a-zA-Z]{7}\s?"(.*)"$/', '\\1', $line);
					$statement->setMsgctxt($line);
					//Ok just fetch next line.
					$line = $this->getNextLine();
				}
				//Its time to check for translated strings
				if (substr($line, 0, 5) == 'msgid')
				{
					//It must follow with a msgstr
					$line = preg_replace('/^[a-zA-Z]{5}\s?"(.*)"$/', '\\1', $line);
					$statement->addMsgId($line);
					$msgMode = 0 ; //Msg id mode
					$hasPlural = false;
					while ($line = $this->getNextLine())
					{
						//check for msgid_plural
						if (substr($line, 0, 12) == 'msgid_plural')
						{
							//TODO: There is a question. is there a msgid_plural with multiple line? 
							$hasPlural = true;
							$msgMode = 1;//Plural mode
							$line = preg_replace('/^[a-zA-Z_]{12}\s?(.*)/', '\\1', $line);
						}
						if (substr($line, 0, 6) == "msgstr")
						{
							$msgMode = 2;
							if ($hasPlural)
							{
								$line = preg_replace('/^[a-zA-Z]{6}\[[0-9]+\]\s?(.*)/', '\\1', $line);
							}
							else 
							{
								$line = preg_replace('/^[a-zA-Z]{6}\s?(.*)/', '\\1', $line);
							}
						}
						
						$line = preg_replace('/^"(.*)"$/', '\\1', $line);

						if ($msgMode == 0)
							$statement->addMsgId($line);
						elseif ($msgMode == 1)
							$statement->setMsgPlural($line);
						elseif ($msgMode == 2)
							$statement->addMsgStr($line);
					}
					//Exit this while means the block is ended, so exit the block
					break;
				}
			}		
		}
		
		$this->statements[] = $statement;
		return $line !== false;
	}
	
	/**
	 * Parse po file  
	 */
	public function parsePO()
	{
		// Reset current result
		$this->statements = array();
		//First of all, parse header 
		$this->parseHeader();
		while ($this->parseABlock());
	}
	
	/**
	 * Get header 
	 * @return PHPoHeader
	 */
	public function getHeader()
	{
		return $this->header;
	}
	
	/**
	 * Get statements
	 * @return array of PHPoStatement
	 */
	public function getStatements()
	{
		return $this->statements;
	}

	/**
	 * get translated strings
	 * @return array of PHPoStatement
	 **/
	public function getTranslatedStr()
	{
		$translatedStr = array();
		foreach ($this->statements as $statement => $value) 
		{
			if ($value->getIsTranslated()) 
			{
				$translatedStr[] = $value;
			}
		}
		return $translatedStr;
	}

	/**
	 * get untranslated strings
	 * @return array of PHPoStatement
	 **/
	function getUntranslatedStr()
	{
		$unTranslatedStr = array();
		foreach ($this->statements as $statement => $value) 
		{
			if (!$value->getIsTranslated()) 
			{
				$unTranslatedStr[] = $value;
			}
		}
		return $unTranslatedStr;
	}

	/**
	 * Get translation percentage
	 * @return int
	 **/
	function getTranslationPercentage()
	{
		return round(count($this->getTranslatedStr()) / count($this->statements) * 100);
	}
	
	public function __toString()
	{
		$result = $this->header->__toString();
		foreach ($this->statements as $statement)
			$result .= $statement->__toString();
		
		return $result;
	}

}
