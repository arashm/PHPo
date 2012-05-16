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
			$this->flags[] = $flag;
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
	
	public function addTranslatorComment($commnet)
	{
		$this->translatorComments[] = $commnet;
	}
	
	public function getTranslatorComments()
	{
		return $this->translatorComments;
	}
	
	public function addExtractedComment($comment)
	{
		$this->extractedComments[] = $comment;
	}
	
	public function getExtractedComments()
	{
		return $this->extractedComments;
	}
	
	public function addPreviousUntranslatedString($comment)
	{
		$this->previousUntranslatedStrings[] = $comment;
	}
	
	public function getPreviousUntranslatedStrings()
	{
		return $this->previousUntranslatedStrings;
	}	
	
	
	public function addMsgId($msg)
	{
		$this->msgId[] = $msg;
	}
	
	public function getMsgId()
	{
		return $this->msgId;
	}
	
	public function getMsgIdAsString()
	{
		return implode(' ', $this->msgId);
	}
	
	public function addMsgStr($msg)
	{
		$this->msgStr[] = $msg;
	}
	
	public function getMsgStr()
	{
		return $this->msgStr;
	}
	
	public function getMsgStrAsString()
	{
		return implode(' ', $this->msgStr);
	}
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
	
	/**
	 * Parse 
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
				//Its time to check for translated strings
				if (substr($line, 0, 5) == "msgid")
				{
					//It must follow with a msgstr
					$line = preg_replace('/^[a-zA-Z]{5}\s?"(.*)"$/', '\\1', $line);
					$statement->addMsgId($line);
					$msgStr = false;
					while ($line = $this->getNextLine())
					{
						if (substr($line, 0, 6) == "msgstr")
						{
							$msgStr = true;
							$line = preg_replace('/^[a-zA-Z]{6}\s?"(.*)"$/', '\\1', $line);
						}
						
						if ($msgStr)
							$statement->addMsgStr($line);
						else
							$statement->addMsgId($line);
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
	 * Parse po file into an array
	 * 
	 */

	public function parsePO()
	{
		// Reset current result
		$this->statements = array();
		//First of all, parse header 
		$this->parseHeder();
		while ($this->parseABlock());
	}

	public function getHeader()
	{
		return $this->header;
	}
	
	public function getStatements()
	{
		return $this->statements;
	}

}
