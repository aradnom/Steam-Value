<?php
include( "includes/form_tools.class.php" );
$regex = "#<div class=\"current\" id=\"singleFinalPrice\">(.*?)</div>#";

// Process form submission
if ( isset($_POST['process']) ) {
	// First get all variable names from form
	$post_keys = array_keys( $_POST );
	
	// Then put form contents into XML file for later use
	$fout = fopen( 'links.xml', 'w' ) or die( "Couldn't open file.  Like, at all. =(" );
	
	$xml = "<items>";

	foreach ( $post_keys as $row ) {
		if ( $row != "process" ) {
			$xml .= "
	<item>
		<name>" . $row . "</name>
		<item_link>" . $_POST["$row"] . "</item_link>
	</item>";			
		}
	}
	
	$xml .= "
</items>";

	fwrite( $fout, $xml );
	
	fclose( $fout );
	
	// That done, move on to actually processing items ///////////////////////////////////
	
	$titles = array(); // Holds product titles for display later
	
	foreach ( $_POST as $item ) {
		if ( stristr($item, "http") ) {
			
			$item = implode('', file($item) );
			preg_match( $regex, $item, $matches );
			preg_match( "#</span>(.*)<sup>#", $matches[0], $dollars );
			preg_match( "#<sup>(.*)</sup#", $matches[0], $cents );
			preg_match( "#<title>(.*)</title>#", $item, $title );
			$dollars = preg_replace( "/[^\d]/", "", $dollars );
			$cents = preg_replace( "/[^\d]/", "", $cents );
			array_push( $titles, str_ireplace("Newegg.com - ", "", $title[1]) );
			
			$total_dollars += $dollars[0];
			$total_cents += $cents[0];
			
			if ( $total_cents > 99 ) {
				$total_dollars++;
				$total_cents += -100;	
			}
		}		
	}	
}

// Load in existing links from file if they exist
if ( file_exists('links.xml') ) {
	$links = simplexml_load_file( 'links.xml' );
	foreach ( $links as $row ) {
		//echo $row->name;
		$_POST["$row->name"] = $row->item_link;	
		//echo $_POST['motherboard'];
	}
} else {
	echo "File does not exist.  Sorry. =(";	
}

// Make new form tools object for use below
$form_tools = new form_tools();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<style type="text/css">
body {
	font-family: Georgia, "Times New Roman", Times, serif;
}

input[type="submit"] {
	width: 300px;
	height: 100px;
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 20px;
	background-color: #F90;
	border: none;
	-moz-border-radius: 4px;
	-webkit-border-radius: 4px;
	color: #FFF;
	font-style: italic;
	font-weight: bold;
	cursor: pointer;
}

input[type="text"] {
	width: 400px;
}

h1 {
	font-size: 20px;
}

#wrapper {
	width: 850px;
}

.categories {
	width: 400px;
	text-align: center;
}

.titles {
	width: 400px;
	position: absolute;
	left: 430px;
	top: 70px;
	font-size: 12px;
}

.title {
	margin-bottom: 20px;
}

.price {
	font-size: 18px;
	width: 300px;
	height: 70px;
	background-color: #F90;
	text-align: center;
	vertical-align: middle;
	padding-top: 25px;
	color: #FFF;
	-moz-border-radius: 4px; 
	-webkit-border-radius: 4px;
}

.price span {
	font-size: 24px;
}

.price sup {
	font-size: 11px;
}

.numbers {
	font-weight: bold;
	display: inline-block;
}
</style>
</head>

<body>

<div id="wrapper" name="wrapper">

    <div class="categories">
        <h1>Components:</h1><br />
    
        <?php
        $fields = array(
            "Memory: ",
            "<br />",
            "text|memory|" . $_POST['memory'],
            "<br />",
            "Case: ",
            "<br />",
            "text|case|" . $_POST['case'],
            "<br />",
            "PSU: ",
            "<br />",
            "text|psu|" . $_POST['psu'],
            "<br />",
            "CPU: ",
            "<br />",
            "text|cpu|" . $_POST['cpu'],
            "<br />",
            "GPU: ",
            "<br />",
            "text|gpu|" . $_POST['gpu'],
            "<br />",
            "Sound card: ",
            "<br />",
            "text|sound|" . $_POST['sound'],
            "<br />",
            "HDD: ",
            "<br />",
            "text|hdd|" . $_POST['hdd'],
            "<br />",
            "Motherboard: ",
            "<br />",
            "text|motherboard|" . $_POST['motherboard'],
            "<br /><br />"
        );
		
		if ( isset($total_dollars) ) {
			array_push( $fields, "submit|process|" . "Price: $" . $total_dollars . "." . $total_cents );
		} else {
			array_push( $fields, "submit|process|Process Components" );
		}
		
        
        echo $form_tools->generate_custom_form( $fields, "components_form" );
        ?>
    </div>
    
    <?php if ( isset($titles) ) { ?>
    <div class="titles">
        <?php foreach ( $titles as $row ) { ?>
        <div class="title">
        	<?php echo $row; ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>

</div>

<br />

<?php if ( false ) { ?>
<!-- Disabled, check against $total_dollars to enable again -->
<div class="price">
	Price: 
    <div class="numbers">
    	<span>$</span>
		<?php echo $total_dollars; ?>
        <sup>.<?php echo $total_cents; ?></sup>
    </div>
</div>
<?php } ?>

</body>
</html>