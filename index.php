<?php
include( "includes/form_tools.class.php" );
define( "MAX_PAGES", 169 );
define( "STEP", 10 );

$form = new form_tools();

$link = "http://store.steampowered.com/search/?sort_by=&sort_order=ASC&page=";
$regex_strike = "#<strike\b[^>]*>(.*?)</strike>#";
$regex_price = "/(&#36;)(\d{1,3})\.(\d{2})/";

if ( isset($_POST['next']) ) {
	$i = $_POST['count'];
	$limit = $i + STEP;
	if ( $limit > MAX_PAGES )
		$limit = MAX_PAGES;
	$sum = $_POST['sum'];
	$priced_items = $_POST['priced_items'];
} else {
	$i = 1;
	$limit = 0;
	$sum = 0;
	$priced_items = 0;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Steam Price-check</title>

<style type="text/css">
body {
	font-family: Georgia, "Times New Roman", Times, serif;
}

#currentsum {
	font-size: 30px;
	color: #999;
	padding: 20px;
	background-color: #EEE;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	position: absolute;
	top: 5px;
	width: 500px;
}

#calculator {
	margin-top: 100px;
}

.price {
	font-size: 44px;
	color: #F90;
	display: inline-block;
}
</style>

</head>

<body>

<div id="calculator">
<?php
for ( $i; $i < $limit; $i++ ) {
	if ( $i < MAX_PAGES ) {
		$matches = array();
		$url = $link . $i;
		
		$item = implode('', file($url));
		
		$item = preg_replace( $regex_strike, "", $item );
		preg_match_all( $regex_price, $item, $matches );	
		
		$matches[0] = str_ireplace( "&#36;", "", $matches[0] );	
		
		foreach ( $matches[0] as $row )
			$sum += $row;
			
		$priced_items += count($matches[0]);
			
		unset($matches);
		
		echo "Page " . $i . " sum: " . $sum . "<br />";
	}
}

echo "<br />" . "Pages: " . $i;
echo "<br />" . "Items with price: " . $priced_items;

if ( $priced_items > 0 )
	echo "<br />" . "Average price: " . $sum / $priced_items;

$fields = array(
	"hidden|count|".$i,
	"hidden|sum|".$sum,
	"hidden|priced_items|".$priced_items,
	"submit|next|Next " . STEP
);
?>
</div>

<div id="next_button">
<?php
echo $form->generate_custom_form( $fields, "next_form" );
?>
</div>

<div id="currentsum">
	<?php
	echo "Current Sum: " . "<div class='price'>" . $sum . "</div>";
	?>
</div>
</body>
</html>