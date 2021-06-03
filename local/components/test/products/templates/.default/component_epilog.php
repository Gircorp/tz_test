<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="nav-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:main.pagenavigation",
                    "nav",
                    array(
                        "NAV_OBJECT" => $arResult["NAV_OBJECT"],
                        "SEF_MODE" => "N",
                        "COMPONENT_TEMPLATE" => ".default"
                    ),
                    false
                );?>
            </div>
        </div>
    </div>
</div>
