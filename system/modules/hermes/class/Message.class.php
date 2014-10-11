<?php
/**
 * Message
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 27.04.13
*/

class Message {
	//ATTRIBUTES
	private $id = 0;
	private $thread = 0;
	private $rPlayerWriter = 0;
	private $rPlayerReader = 0;
	private $dSending = '';
	private $content = '';
	private $readed = 0;
	private $writerStatement = 1;
	private $readerStatement = 1;
	
	private $writerName = '';
	private $writerColor = 0;
	private $writerAvatar = '';
	private $readerName = '';
	private $readerColor = 0;
	private $readerAvatar = '';
	
	//GETTERS
	public function getId()							{ return $this->id; }
	public function getThread()						{ return $this->thread; }
	public function getRPlayerWriter()				{ return $this->rPlayerWriter; }
	public function getRPlayerReader()				{ return $this->rPlayerReader; }
	public function getDSending()					{ return $this->dSending; }
	public function getContent()					{ return $this->content; }
	public function getReaded()						{ return $this->readed; }
	public function getWriterStatement()			{ return $this->writerStatement; }
	public function getReaderStatement()			{ return $this->readerStatement; }
	
	// GETTER DE JOINTURES
	public function getWriterName()					{
		if ($this->getRPlayerWriter() == 0) {
			return 'Jean-Mi, administrateur système';
		} else {
			return $this->writerName;
		}
	}

	public function getWriterColor()				{ return $this->writerColor; }
	public function getWriterAvatar()				{
		if ($this->getRPlayerWriter() == 0) {
			return '059-1';
		} else {
			return $this->writerAvatar;
		}
	}

	public function getReaderName()					{ return $this->readerName; }
	public function getReaderColor()				{ return $this->readerColor; }
	public function getReaderAvatar()				{ return $this->readerAvatar; }

	//SETTERS
	public function setId($var = 0)					{ $this->id = $var; }
	public function setThread($var = 0)				{ $this->thread = $var; }
	public function setRPlayerWriter($var = 0)		{ $this->rPlayerWriter = $var; }
	public function setRPlayerReader($var = 0)		{ $this->rPlayerReader = $var; }
	public function setDSending($var = '')			{ $this->dSending = $var; }
	public function setContent($var = '')			{ $this->content = $var; }
	public function setReaded($var = 0)				{ $this->readed = $var; }
	public function setWriterStatement($var = 0)	{ $this->writerStatement = $var; }
	public function setReaderStatement($var = 0)	{ $this->readerStatement = $var; }

	// GETTER DE JOINTURES
	public function setWriterName($var = '')		{ $this->writerName = $var; }
	public function setWriterColor($var = 0)		{ $this->writerColor = $var; }
	public function setWriterAvatar($var = '')		{ $this->writerAvatar = $var; }
	public function setReaderName($var = '')		{ $this->readerName = $var; }
	public function setReaderColor($var = 0)		{ $this->readerColor = $var; }
	public function setReaderAvatar($var = '')		{ $this->readerAvatar = $var; }

	public function getRealId($ctrid) {
		if ($this->getRPlayerWriter() == 0 OR $this->getRPlayerReader() == 0) {
			return 0;
		} elseif ($this->getRPlayerWriter() == $ctrid) {
			return $this->getRPlayerReader();
		} else {
			return $this->getRPlayerWriter();
		}
	}

	public function getRealColor($ctrid) {
		if ($this->getRPlayerWriter() == 0 OR $this->getRPlayerReader() == 0) {
			return 0;
		} elseif ($this->getRPlayerWriter() == $ctrid) {
			return $this->getReaderColor();
		} else {
			return $this->getWriterColor();
		}
	}

	public function getRealName($ctrid) {
		if ($this->getRPlayerWriter() == 0 OR $this->getRPlayerReader() == 0) {
			return 'Jean-Mi';
		} elseif ($this->getRPlayerWriter() == $ctrid) {
			return $this->getReaderName();
		} else {
			return $this->getWriterName();
		}
	}

	public function getRealAvatar($ctrid) {
		if ($this->getRPlayerWriter() == 0 OR $this->getRPlayerReader() == 0) {
			return '059-1';
		} elseif ($this->getRPlayerWriter() == $ctrid) {
			return $this->getReaderAvatar();
		} else {
			return $this->getWriterAvatar();
		}
	}
}
?>