<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle("Главная");
?>
<?
if (isset($_GET["sort"]) && isset($_GET["order"]) && ($_GET["sort"] == "ACTIVE_FROM" || $_GET["sort"] == "REVIEWS")){
    $arParams["ELEMENT_SORT_FIELD"] = $_GET["sort"];
    $arParams["ELEMENT_SORT_ORDER"] = $_GET["order"];
}
$APPLICATION->IncludeComponent(
	"test:products", 
	".default", 
	array(
		"CACHE_TIME" => "600",
		"CACHE_TYPE" => "A",
		"COUNT" => "4",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "Y",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"IBLOCK_CODE" => "goods",
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE" => "content",
		"SHOW_NAV" => "Y",
		"SORT_DIRECTION1" => $arParams["ELEMENT_SORT_ORDER"],
		"SORT_DIRECTION2" => "DESC",
		"SORT_FIELD1" => $arParams["ELEMENT_SORT_FIELD"],
		"SORT_FIELD2" => "ACTIVE_FROM",
		"COMPONENT_TEMPLATE" => ".default",
		"REVIEWS_HBLOCK_ID" => "1"
	),
	false
);
?><?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>