<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>
<div class="goods">
	<div class="container">
		<div class="row mb-2">
			<div class="col-md-6">
				<h2><?=Loc::getMessage('STANDARD_ELEMENTS_LIST_TEMPLATE_TITLE');?></h2>
				<br>
				<a href="/generate_goods.php">Перегенерировать товары</a>
				<br>
				<br>
			</div>
			<div class="col-md-6">
				<div class="d-flex justify-content-start justify-content-md-end">
					<div class="dropdown">
						<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
							Сортировка
						</a>
						<ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
							<li><a class="dropdown-item<?=((($_GET["sort"] == "ACTIVE_FROM" && $_GET["order"] == "DESC") || !isset($_GET["sort"]) || empty($_GET["sort"])) ? ' active' : '')?>" href="?sort=ACTIVE_FROM&order=DESC&clear_cache=Y">Сначала новые</a></li>
							<li><a class="dropdown-item<?=(($_GET["sort"] == "ACTIVE_FROM" && $_GET["order"] == "ASC") ? ' active' : '')?>" href="?sort=ACTIVE_FROM&order=ASC&clear_cache=Y">Сначала старые</a></li>
							<li><a class="dropdown-item<?=(($_GET["sort"] == "REVIEWS" && $_GET["order"] == "DESC") ? ' active' : '')?>" href="?sort=REVIEWS&order=DESC&clear_cache=Y">Больше отзывов</a></li>
							<li><a class="dropdown-item<?=(($_GET["sort"] == "REVIEWS" && $_GET["order"] == "ASC") ? ' active' : '')?>" href="?sort=REVIEWS&order=ASC&clear_cache=Y">Меньше отзывов</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
        <? if(count($arResult['ITEMS'])){ ?>
			<div class="row">
                <? foreach ($arResult['ITEMS'] as $item){ ?>
					<div class="col-md-6">
						<div class="card product-card">
							<div class="card-body">
								<h5 class="card-title mb-6"><?=$item['NAME'];?> (id: <?=$item['ID']?>)</h5>
                                <? if($item['DESCRIPTION']){ ?>
									<p class="card-text"><?=$item['DESCRIPTION'];?></p>
                                <? } ?>
                                <? if($item['REVIEWS_COUNT'] > 0){ ?>
									<p>
										<a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample_<?=$item['ID'];?>" role="button" aria-expanded="false" aria-controls="collapseExample_<?=$item['ID'];?>">
											Смотреть отзывы (<?=$item['REVIEWS_COUNT']?>)
										</a>
									</p>
									<div class="collapse" id="collapseExample_<?=$item['ID'];?>">
										<div class="card card-body">
                                            <? foreach ($item['REVIEWS'] as $review){?>
												<b>
                                                    <?
                                                    if($review['NAME'] || $review['SURNAME']){
                                                        echo $review['NAME'].' '.$review['SURNAME'];
                                                    }else{
                                                        echo 'Аноним';
                                                    }
                                                    ?>
												</b>
												<p><?=$review['TEXT']?></p>
												<hr>
                                            <? } ?>
										</div>
									</div>
                                <? } ?>
							</div>
						</div>
					</div>
                <? } ?>
			</div>
        <? }else{ ?>
			<div class="row">
				<div class="col-md-12">
					<h3>Каталог скоро пополнится</h3>
				</div>
			</div>
        <? } ?>
	</div>
</div>