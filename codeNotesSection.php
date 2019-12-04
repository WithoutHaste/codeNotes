<?php
	//ini_set('display_errors', 1);

	include "codeNotesUtilities.php";
	
	$codeDirectory = "./".$section."/";
	$codeHandle = opendir($codeDirectory);
	$codeArray = array();
	while($fileName = readdir($codeHandle)) {
		if($fileName[0] == '.') continue;
		if(!StringEndsWith($fileName, 'txt')) continue;
		array_push($codeArray, $fileName);
	}
	closedir($codeHandle);
	usort($codeArray, "CompareFilenames");

	function CompareFilenames($a, $b) 
	{
		$aTemp = str_replace('_', '0', $a);
		$bTemp = str_replace('_', '0', $b);     
		return strcmp($aTemp,$bTemp);
	}

	function DisplaySidebarHeader($filename, $line, $anchorName)
	{
		global $section;
		global $sectionHeader;
		
		$line = substr($line,2);
		
		echo "<a class='sidebarHeader' href='#header",EncodeURI($anchorName),"'>";
		echo ucwords($line);
		echo "</a><br/>";
	}

	function DisplaySidebarSubHeader($line, $anchorName)
	{
		$line = substr($line,1);
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='#header",EncodeURI($anchorName),"'>";
		echo ucwords($line);
		echo "</a><br/>";
	}
	
	function DisplayHeader($line, $anchorName)
	{
		$line = substr($line,2);
		echo "<div class='header'><a class='offset' id='header",EncodeURI($anchorName),"'></a>".ucwords($line)."</div>";
	}
	
	function DisplaySubHeader($line, $anchorName)
	{
		echo "<div class='subheader'><a class='offset' id='header",EncodeURI($anchorName),"'></a>".ucwords(substr($line,1))."</div>";
	}
	
	function DisplayImage($line)
	{
		//Markdown format
		//![alt text](address)
		//![alt text](address =WIDTHx)
		//![alt text](address =WIDTHxHEIGHT)
		
		global $section;
		
		$openAltText = strpos($line, "[");
		$closeAltText = strpos($line, "]");
		$altText = substr($line, $openAltText + 1, $closeAltText - $openAltText - 1);
		
		$openDimensions = strpos($line, " =");
		$closeDimensions = strpos($line, ")");

		$openAddress = strpos($line, "(");
		$closeAddress = $closeDimensions;
		if($openDimensions)
		{
			$closeAddress = $openDimensions;
		}
		$address = substr($line, $openAddress + 1, $closeAddress - $openAddress - 1);
		$isFullAddress = (strlen($address) > 4 && substr($address, 0, 4) == "http");
		
		$width = -1;
		$height = -1;
		if($openDimensions)
		{
			$dimensions = substr($line, $openDimensions + 2, $closeDimensions - $openDimensions - 2);
			$closeWidth = strpos($dimensions, "x");
			$width = substr($dimensions, 0, $closeWidth);
			if($closeWidth + 1 < $closeDimensions)
			{
				$height = substr($dimensions, $closeWidth + 1);
			}
		}
		
		echo "<img src='";
		if(! $isFullAddress)
		{
			echo "$section/";
		}
		echo "$address' alt='$altText'";
		if($width > -1)
		{
			echo " width='$width'";
		}
		if($height > -1)
		{
			echo " height='$height'";
		}
		echo " /><br/>"; 
	}	
	
	function DisplayHyperlink($line)
	{
		//codenotes format
		//!![text](address)
		
		global $section;
		
		$openText = strpos($line, "[");
		$closeText = strpos($line, "]");
		$text = substr($line, $openText + 1, $closeText - $openText - 1);
		
		$openAddress = strpos($line, "(");
		$closeAddress = strpos($line, ")");
		$address = substr($line, $openAddress + 1, $closeAddress - $openAddress - 1);
		
		echo "<a class='external' target='_blank' href='$address'>[$text]</a><br/>";
	}	
?>

<html>
<head>
	<title>Without Haste: <?php echo $sectionHeader; ?> Notes</title>
	<meta name="description" content="Programming Notes: <?php echo $sectionHeader; ?>"/>
	<script src="../javascript/jquery-1.8.2.min.js"></script>
	
	<!-- for syntax highlighting -->
	<link href="../javascript/syntaxPrism/prism.css" rel="stylesheet" />
	<script src="../javascript/syntaxPrism/prism.js"></script>
	
	<!-- for diagrams -->
	<!-- https://github.com/skanaar/nomnoml and http://www.nomnoml.com/ -->
	<!-- biggest limitation of tool, as of June 2019, is that you can arrow across scopes -->
	<!-- according to https://github.com/skanaar/nomnoml/issues/6 and https://github.com/skanaar/nomnoml/issues/69 -->
	<!-- this is because of a limitation in the underlying graphing library and pull requests are welcome -->
	<?php
		if($syntaxLanguage == "nomnoml")
		{
			echo "<script src='../javascript/nomnoml/underscore.min.js'></script>\n";
//			echo "<script src='../javascript/lodash/lodash.js'></script>\n";
			echo "<script src='../javascript/dagre/dagre.min.js'></script>\n";
			echo "<script src='../javascript/nomnoml/skanaar.canvas.js'></script>\n";
			echo "<script src='../javascript/nomnoml/skanaar.svg.js'></script>\n";
			echo "<script src='../javascript/nomnoml/skanaar.util.js'></script>\n";
			echo "<script src='../javascript/nomnoml/skanaar.vector.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.jison-parser.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.parser.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.visuals.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.layouter.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.renderer.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.js'></script>\n";
			echo "<script src='../javascript/nomnoml/nomnoml.jison.js'></script>\n";
			echo "<link href='../javascript/nomnoml/nomnoml.css' rel='stylesheet' />";
		}
	?>
	
	<!-- for diagrams -->
	<?php
		if($syntaxLanguage == "pinker")
		{
			echo "<script src='../javascript/pinker/Pinker.js'></script>\n";
		}
	?>
	
	<link rel="stylesheet" type="text/css" href="codeNotesSection.css" />
	<script src="codeNotesSection.js"></script>
</head>
<body>
	<div class='fixedHeader'>
		<div id="pageHeader">
			<a href='http://www.withouthaste.com' class='plain'>Without Haste</a><br/>
			<title><?php echo $sectionHeader; ?> Notes</title>
		</div>
	</div>
	<div class='mainContent'>
		<div class='sidebar'>
		<?php
			$headerCount = 1;
			for($i=0; $i<count($codeArray); $i++)
			{
				$file = fopen($section."/".$codeArray[$i],"r");
				while(! feof($file))
				{
					$line = fgets($file);
					if(IsHeader($line))
					{
						DisplaySidebarHeader($codeArray[$i], $line, $headerCount);
						$headerCount++;
					}
					else if(IsSubHeader($line))
					{
						DisplaySidebarSubHeader($line, $headerCount);
						$headerCount++;
					}
				}
				fclose($file);
			}
		?>
		</div>
		<div class='fullText'>
	<?php
		$headerCount = 1;
		$inHeaderDiv = false;
		$inSubHeaderDiv = false;
		for($i=0; $i<count($codeArray); $i++)
		{
			$file = fopen($section."/".$codeArray[$i],"r");
			while(! feof($file))
			{
				$line = fgets($file);
				if(IsTitle($line))
					continue;
				if(IsHeader($line))
				{
					if($inSubHeaderDiv)
					{
						echo "</div>\n";
						$inSubHeaderDiv = false;
					}
					if($inHeaderDiv)
					{
						echo "</div>\n";
						$inHeaderDiv = false;
					}
					DisplayHeader($line, $headerCount);
					echo "<div class='inset'>\n";
					$inHeaderDiv = true;
					$headerCount++;
					continue;
				}
				if(IsSubHeader($line))
				{
					if($inSubHeaderDiv)
					{
						echo "</div>\n";
						$inSubHeaderDiv = false;
					}
					DisplaySubHeader($line, $headerCount);
					echo "<div class='inset'>\n";
					$inSubHeaderDiv = true;
					$headerCount++;
					continue;
				}
				if(IsImage($line))
				{
					DisplayImage($line);
					continue;
				}
				if(IsHyperlink($line))
				{
					DisplayHyperlink($line);
					continue;
				}
				DisplayLine($line, $syntaxLanguage);
			}
			fclose($file);
			if($inSubHeaderDiv)
			{
				echo "</div>\n";
				$inSubHeaderDiv = false;
			}
			if($inHeaderDiv)
			{
				echo "</div>\n";
				$inHeaderDiv = false;
			}
		}
	?>
		</div>
	</div>
</body>
</html>
