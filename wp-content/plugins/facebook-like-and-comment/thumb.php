<?
isset($_GET['img']) or die('NO IMAGE');
include "thumbnail.class.php";
$thumb = new T10Thumbnail;
$size=100;
if(isset($_GET['size']))$size=$_GET['size'];
$thumb->setMaxWidth($size);
$thumb->getThumbnail($_GET['img']);	//generate thumbnail image
?>
