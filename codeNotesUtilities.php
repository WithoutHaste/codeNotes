<?php
	function StringEndsWith($string, $suffix)
	{
		$length = strlen($suffix);
		if ($length == 0) 
		{
			return true;
		}
		return (substr($string, -$length) === $suffix);
	}

	function FormatHeader($header)
	{
		$header = RemoveFileExtension($header);
		$header = InsertSpaceBetweenLettersAndNumbers($header);
		$header = InsertSpaceBeforeCapitals($header);
		$header = ucwords($header);
		return $header;
	}
	
	function RemoveFileExtension($text)
	{
		return preg_replace("/(\.[a-z]+)/i","", $text);
	}
	
	function InsertSpaceBetweenLettersAndNumbers($text)
	{
		return preg_replace("/([a-z]+)([0-9]+)/i","$1 $2", $text);
	}
	
	function InsertSpaceBeforeCapitals($text)
	{
		return preg_replace("/([a-z]+)([A-Z]+)/","$1 $2", $text);
	}

	function EncodeURI($text)
	{
		$text = str_replace("#", "%23", $text);
		$text = str_replace(".", "%2E", $text);
		$text = str_replace("(", "%28", $text);
		$text = str_replace(")", "%29", $text);
		$text = str_replace(" ", "%20", $text);
		return $text;
	}
	
	function HTMLEncodeSpecialCharacters($line)
	{
		$line = str_replace("<", "&lt;", $line);
		$line = str_replace(">", "&gt;", $line);
		return $line;
	}
	
	function IsTitle($line)
	{
		return (strlen($line) > 2 && substr($line, 0, 3) == "###");
	}

	function IsHeader($line)
	{
		return (!IsTitle($line) && strlen($line) > 1 && substr($line, 0, 2) == "##"); //Markdown header format
	}

	function IsSubHeader($line)
	{
		return (!IsTitle($line) && !IsHeader($line) && strlen($line) > 0 && substr($line, 0, 1) == "#"); //Markdown subheader format
	}
	
	function IsImage($line)
	{
		return (strlen($line) > 2 && substr($line, 0, 2) == "!["); //Markdown image format ![alt text](address)
	}
	
	function IsHyperlink($line)
	{
		return (strlen($line) > 3 && substr($line, 0, 3) == "!!["); //codenotes hyperlink format !![text](address) must be on its own line
	}
	
	function EndsWith($text, $end)
	{
		if(strlen($text) < strlen($end)) return false;
		return (substr($text, strlen($text) - strlen($end)) == $end);
	}
	
	$insidePreTag = false;
	$insideNomnomlTag = false;
	$insidePinkerTag = false;
	function DisplayLine($line, $syntaxLanguage)
	{
		global $insidePreTag;
		global $insideNomnomlTag;
		global $insidePinkerTag;

		if(IsCodeTag($line))
		{
			$insidePreTag = (!$insidePreTag);
			echo PrepCodeTag($line, $syntaxLanguage);
			return;
		}

		if(IsNomnomlTag($line))
		{
			$insideNomnomlTag = (!$insideNomnomlTag);
			echo PrepNomnomlTag($line);
			return;
		}
		if($insideNomnomlTag)
		{
			echo $line."\n";
			return;
		}
		
		if(IsPinkerTag($line))
		{
			$insidePinkerTag = (!$insidePinkerTag);
			echo PrepPinkerTag($line);
			return;
		}
		if($insidePinkerTag)
		{
			echo $line."\n";
			return;
		}
		
		$line = HTMLEncodeSpecialCharacters($line);
		$line = CheckForInlineComments($line);
		$line = CheckForMultilineComments($line);
		$line = CheckForTabs($line);
		echo $line;
		if(!$insidePreTag)
		{
			echo "<br/>";
		}
	}
	
	function CheckForInlineComments($line)
	{
		$commentsStart = strpos($line, "--");
		if($commentsStart != FALSE)
		{
			$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
			$line = $line."</span>";
		}
		$commentsStart = strpos($line, "//");
		if($commentsStart != FALSE)
		{
			$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
			$line = $line."</span>";
		}
		return $line;
	}
	
	function CheckForMultilineComments($line)
	{
		$commentsStart = strpos($line, "/*");
		$commentsEnd = strrpos($line, "*/");
		if($commentsStart == FALSE && $commentsEnd == FALSE)
		{
			return $line;
		}
		if($commentsStart == FALSE)
		{
			$commentsStart = 0;
		}
		if($commentsEnd == FALSE)
		{
			$commentsEnd = strlen($line)-2;
		}	
		$line = substr_replace($line, "</span>", $commentsEnd+2, 0);
		$line = substr_replace($line, "<span class='comments'>", $commentsStart, 0);
		return $line;
	}
	
	function CheckForTabs($line)
	{
		return preg_replace("/\t/","&nbsp;&nbsp;&nbsp;&nbsp;", $line);
	}
	
	function IsCodeTag($text)
	{
		return (IsOpenCodeTag($text) || IsCloseCodeTag($text));
	}
	
	function IsOpenCodeTag($text)
	{
		return (preg_match("/^(\t|\s)*<code.*>\s*$/", $text) == 1);
	}
	
	function IsCloseCodeTag($text)
	{
		return (preg_match("/^(\t|\s)*<\/code.*>\s*$/", $text) == 1);
	}
	
	function IsNomnomlTag($text)
	{
		return (preg_match("/^(\t|\s)*<\/?nomnoml.*>\s*$/", $text) == 1);
	}
	
	function IsPinkerTag($text)
	{
		return (preg_match("/^(\t|\s)*<\/?pinker.*>\s*$/", $text) == 1);
	}
	
	function PrepCodeTag($line, $syntaxLanguage)
	{
		if(IsOpenCodeTag($line))
		{
			$line = preg_replace("/(<code.*>)/","<pre>$1", $line);
			if(preg_match("/lang=\".*\"/", $line) == 1)
			{
				//convert <code lang="markup"> to <code class="language-markup">
				$line = preg_replace("/(.*)<code lang=\"(.*?)\">/","$1<code class=\"language-$2\">", $line);
			}
			else
			{
				$line = preg_replace("/(.*)<code.*>/","$1<code class=\"language-$syntaxLanguage\">", $line);
			}
		}
		else if(IsCloseCodeTag($line))
		{
			$line = preg_replace("/(<\/code>)/","$1</pre>", $line);
		}
		
		return $line;
	}
	
	$nomnomlCount = 0;
	function PrepNomnomlTag($line)
	{
		global $nomnomlCount;
		$line = preg_replace("/(<nomnoml.*>)/","<div class='nomnoml'><canvas id='target-canvas-".$nomnomlCount."'></canvas></div><script>var source = `", $line);
		$line = preg_replace("/(<\/nomnoml>)/","`;nomnoml.draw(document.getElementById('target-canvas-".$nomnomlCount."'), source);</script>", $line);
		if(strpos($line, 'nomnoml.draw') !== false)
		{
			$nomnomlCount++;
		}
		return $line;
	}
	
	$pinkerCount = 0;
	function PrepPinkerTag($line)
	{
		global $pinkerCount;
		$line = preg_replace("/(<pinker.*>)/","<div class='pinker'><canvas id='pinker-canvas-".$pinkerCount."'></canvas></div><script>var source = `", $line);
		$line = preg_replace("/(<\/pinker>)/","`;pinker.draw(document.getElementById('pinker-canvas-".$pinkerCount."'), source);</script>", $line);
		if(strpos($line, 'pinker.draw') !== false)
		{
			$pinkerCount++;
		}
		return $line;
	}
?>