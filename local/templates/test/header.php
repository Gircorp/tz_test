<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Page\Asset;
?>
<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow" />
	<?$APPLICATION->ShowHead();?>
	<title><?$APPLICATION->ShowTitle();?></title>
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
	<?
	Asset::getInstance()->addString("<link href=\"//cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css\" rel=\"stylesheet\" integrity=\"sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x\" crossorigin=\"anonymous\">");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/assets/css/styles.css");
	?>
</head>
<body>
	<div id="panel">
		<?$APPLICATION->ShowPanel();?>
	</div>
	<div class="header">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1>Тестовое задание</h1>
				</div>
			</div>
		</div>
	</div>

