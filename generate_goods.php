<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

use Bitrix\Main\Loader;
?>
<div class="goods">
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-12">
                <a href="/?clear_cache=Y">Назад в каталог</a>
                <br>
                <br>
                <?
                // работа с товарами
                if(CModule::IncludeModule("iblock")) {
                    // удаляем товары
                    $res = CIBlockElement::GetList([], ['IBLOCK_ID' => 1], false, []);
                    while ($r = $res->GetNext()) {
                        $id = $r['ID'];
                        CIBlockElement::Delete($id); // удаляем элемент
                    }

                    // добавляем товары
                    $goods_ids = array();
                    $goods_count = 20;
                    while (++$i <= $goods_count) {
                        $el = new CIBlockElement;

                        $arLoadProductArray = Array(
                            "MODIFIED_BY" => $USER->GetID(),
                            "IBLOCK_SECTION_ID" => false,
                            "IBLOCK_ID" => 1,
                            "NAME" => "Товар " . $i,
                            "ACTIVE" => "Y",
                            "DATE_ACTIVE_FROM" => date('d.m.Y h:i:s', (time() - $i * 3600)),
                            "PREVIEW_TEXT" => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.",
                        );

                        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
                            echo "<br>Товар с ID " . $PRODUCT_ID . "добавлен";
                            $goods_ids[] = $PRODUCT_ID;
                        }else {
                            echo "<br>Error: " . $el->LAST_ERROR;
                        }
                    }
                }

                // работа с отзывами
                Loader::includeModule("highloadblock");

                use Bitrix\Highloadblock as HL;
                use Bitrix\Main\Entity;
                $hlbl = 1;
                $hlblock = HL\HighloadBlockTable::getById($hlbl)->fetch();

                $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                $entity_data_class = $entity->getDataClass();

                //получаем отзывы
                $rsData = $entity_data_class::getList();
                while($arData = $rsData->Fetch()){
                    //удаляем отзыв
                    $entity_data_class::Delete($arData['ID']);
                }

                //добавляем отзывы к товарам
                foreach ($goods_ids as $goods_id){
                    $reviews_count = rand(2, 10); //количество отзывов

                    $i = 0;
                    while (++$i <= $reviews_count) {
                        // Массив полей для добавления
                        $data = array(
                            "UF_ACTIVE" => '1',
                            "UF_NAME" => 'Имя '.$i,
                            "UF_SURNAME" => 'Фамилия '.$i,
                            "UF_TEXT" => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                            "UF_PRODUCT" => $goods_id,
                        );

                        $result = $entity_data_class::add($data);
                        if ($result->isSuccess()) {
                            echo "<br>Добавлен отзыв к товару ".$goods_id;
                        } else {
                            echo '<br>Ошибка: ' . implode(', ', $otvet->getErrors());
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>
