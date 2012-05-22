<?php
/*
 * PHPoEditor
 * 
 * Copyright 2012 Unknown <f0rud@elbit>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
	require "../PHPo/PHPo.php";
	
	if (isset($_POST["submit"]))
	{
		$tmp = new PHPo();
		$header = new PHPoHeader();
		foreach (explode(PHP_EOL, $_POST['headercoment']) as $comment)
			$header->addComment($comment);
		foreach ($_POST['attribs'] as $key => $val)
			$header->addAttribute($val, $_POST['vals'][$key]);
		$tmp->setHeader($header);
		
		foreach ($_POST['flags'] as $key => $dummy){
			$statement = new PHPoStatement();
			foreach (explode(',',$_POST['flags'][$key]) as $flag)
				$statement->addFlag($flag);
				
			foreach (explode(PHP_EOL,$_POST['placements'][$key]) as $place)
				$statement->addPlacement($place);
			
			foreach (explode(PHP_EOL,$_POST['comments'][$key]) as $place)
				$statement->addTranslatorComment($place);
			
			foreach (explode(PHP_EOL,$_POST['extracted'][$key]) as $place)
				$statement->addExtractedComment($place);
				
			foreach (explode(PHP_EOL,$_POST['previous'][$key]) as $place)
				$statement->addPreviousUntranslatedString($place);		

			$array = explode(PHP_EOL, $_POST['msgid'][$key]);
			if (count($array) > 1)
				$statement->addMsgId("");
			foreach ($array as $msgid)
				$statement->addMsgId($msgid);				
			
			
			$array = explode(PHP_EOL, $_POST['msgstr'][$key]);
			if (count($array) > 1)
				$statement->addMsgStr("");
			foreach ($array as $msgstr)
				$statement->addMsgStr($msgstr);				
					
			$tmp->addStatement($statement);			
		}
		
		$str = $tmp->__toString();
		file_put_contents('./out.po',$str);
	}
	
	
	$phpo = new PHPo('./test.po');
	$header = $phpo->getHeader();
	if (!$header) 
		$header = new PHPoHeader();
	$headerComments = implode(PHP_EOL, $header->getComments());
?>
<!DOCTYPE html >
<html lang="en">
<head>
	<title>PHPoEditor</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		body {
			padding-top : 50px;
		}
	</style>
</head>
	
<body>
	<!-- Navbar
    ================================================== -->
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="./index.html">PHPoEditor</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="">
                <a href="./index.html">Index</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="container">
		
		<div class="row-fluid">
			<div class="span12">
				
			</div>
		</div>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-horizontal">
			<fieldset>
			<legend>Header</legend>
				<div class="control-group">
					<label class="control-label" for="headercoment">Header comment</label>
					<div class="controls">
						<textarea class="input-xxlarge" id="headercoment" name="headercoment"><?php echo $headerComments?></textarea>
						<p class="help-block">Header comments, not so important</p>
					</div>
				</div>
<?php
	$i = 0;
	foreach ($header->getAttributes() as $attr => $value):
		$i++;
?>
	<div class="control-group">
		<label class="control-label" for="vals[<?php echo $i;?>]"><?php echo $attr; ?></label>
		<div class="controls">
			<input type="hidden" name="attribs[<?php echo $i;?>]" value="<?php echo $attr;?>" />
			<input class="input-xxlarge" id="vals[<?php echo $i;?>]" name="vals[<?php echo $i;?>]" value="<?php echo $value; ?>" />
		</div>
	</div>
<?php
	endforeach;
?>
			</fieldset>

<?php
	$i = 0;
	foreach ($phpo->getStatements() as $statement):
		$i++;
		//First flags 
		$flags = $flagsStr = '';
		foreach ($statement->getFlags() as $flag)
		{
			$flags .= '<span class="label label-info">' . $flag . '</span> ';
		}
		$flagsStr = implode(',',$statement->getFlags());
		$placements = implode(PHP_EOL, $statement->getPlacements());
		$comments = implode(PHP_EOL, $statement->getTranslatorComments());
		$extracted = implode(PHP_EOL, $statement->getExtractedComments());
		$previous = implode(PHP_EOL, $statement->getPreviousUntranslatedStrings());
		
		$multiLine = count($statement->getMsgId()) > 1;
		$msgId = $statement->getMsgId();
		$msgStr = $statement->getMsgStr();
		if ($multiLine)
		{
			unset($msgId[0]);
			if (isset($msgStr[0])) unset($msgStr[0]);
		}
		$msgId = implode(PHP_EOL, $msgId);
		$msgStr = implode(PHP_EOL, $msgStr);
?>
	<fieldset>
		<legend>Statement <?php echo $i ?></legend>
		
		<div class="control-group">
			<label class="control-label" for="flags[<?php echo $i;?>]"><?php echo 'Flags ' . $i; ?></label>
			<div class="controls">
			<input class="input-xxlarge" id="flags[<?php echo $i;?>]" name="flags[<?php echo $i;?>]" value="<?php echo $flagsStr; ?>" />
			<?php echo $flags;?>
		</div>
		<div class="control-group">
			<label class="control-label" for="placements[<?php echo $i;?>]"><?php echo 'Placements comment ' . $i; ?></label>
			<div class="controls">
			<textarea class="input-xxlarge" id="placements[<?php echo $i;?>]" name="placements[<?php echo $i;?>]" ><?php echo $placements; ?></textarea>
		</div>		
		<div class="control-group">
			<label class="control-label" for="comments[<?php echo $i;?>]"><?php echo 'Translator comment ' . $i; ?></label>
			<div class="controls">
			<textarea class="input-xxlarge" id="comments[<?php echo $i;?>]" name="comments[<?php echo $i;?>]"><?php echo $comments; ?></textarea>
		</div>		
		<div class="control-group">
			<label class="control-label" for="extracted[<?php echo $i;?>]"><?php echo 'Extracted comment ' . $i; ?></label>
			<div class="controls">
			<textarea class="input-xxlarge" id="extracted[<?php echo $i;?>]" name="extracted[<?php echo $i;?>]"><?php echo $extracted; ?></textarea>
		</div>	
		<div class="control-group">
			<label class="control-label" for="previous[<?php echo $i;?>]"><?php echo 'Previous ' . $i; ?></label>
			<div class="controls">
			<textarea class="input-xxlarge" id="previous[<?php echo $i;?>]" name="previous[<?php echo $i;?>]"><?php echo $previous; ?></textarea>
		</div>
		<div class="control-group">
			<label class="control-label" for="msgid[<?php echo $i;?>]"><?php echo 'Msg id ' . $i; ?></label>
			<div class="controls">
			<?php if ($multiLine) :?>
			<textarea class="input-xxlarge" id="msgid[<?php echo $i;?>]" name="msgid[<?php echo $i;?>]"><?php echo $msgId; ?></textarea>
			<?php else : ?>
			<input class="input-xxlarge" id="msgid[<?php echo $i;?>]" value="<?php echo $msgId; ?>" name="msgid[<?php echo $i;?>]"/>
			<?php endif;?>
		</div>		
		<div class="control-group">
			<label class="control-label" for="msgstr[<?php echo $i;?>]"><?php echo 'Msg str ' . $i; ?></label>
			<div class="controls">
			<?php if ($multiLine) :?>
			<textarea class="input-xxlarge" id="msgstr[<?php echo $i;?>]" name="msgstr[<?php echo $i;?>]"><?php echo $msgStr; ?></textarea>
			<?php else : ?>
			<input class="input-xxlarge" id="msgstr[<?php echo $i;?>]" value="<?php echo $msgStr; ?>" name="msgstr[<?php echo $i;?>]"/>
			<?php endif;?>
		</div>			
	</div>
	</fieldset>
<?php
	endforeach;
?>				
			<div class="form-actions">
				<button class="btn btn-primary" type="submit" name="submit">Save changes</button>
				<button class="btn">Cancel</button>
			</div>
		</form>
	</div>
</body>

</html>
