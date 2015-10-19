<?php
$this->layout('layout', array('title' => 'Търсене по спирка'));
?>

<div class="row">
    <div class="twelve column" style="margin-top: 1%; text-align: center">
        <input type="button" id="toggle_form" value="Покажи/Скрий формата" /><br />
    </div>
</div>



<form method="GET" action="/">
    <input type="hidden" name="bystop" id="bystop" value="" />
    <input type="hidden" name="lc" value="" />
    
    <div id="form_div" style="display:<?= empty($times) ? 'block' : 'none' ?>">


        <div class="row">
            <div class="one-half column labels">
                <label for="<?= $paramsShort[SkgtParser\Parser::STOP_CODE_INPUT] ?>"><?= $paramsNames[SkgtParser\Parser::STOP_CODE_INPUT] ?>:</label>
            </div>
            <div class="one-half column selects">
                <input name="<?= $paramsShort[SkgtParser\Parser::STOP_CODE_INPUT] ?>" id="<?= $paramsShort[SkgtParser\Parser::STOP_CODE_INPUT] ?>" value="<?=$params[SkgtParser\Parser::STOP_CODE_INPUT]?>" />
            </div>
        </div>

        <div class="row">
            <div class="one-half column labels">
                <label for="<?= $paramsShort[SkgtParser\Parser::LINE_SELECT] ?>"><?= $paramsNames[SkgtParser\Parser::LINE_SELECT] ?>:</label>
            </div>
            <div class="one-half column selects">

                <select name="<?= $paramsShort[SkgtParser\Parser::LINE_SELECT] ?>" id="<?= $paramsShort[SkgtParser\Parser::LINE_SELECT] ?>">
                    <?php 
                    foreach ($selectsData[SkgtParser\Parser::LINE_SELECT] as $k => $v):
                        $selected = '';
                        if ($k === $params[SkgtParser\Parser::LINE_SELECT]):
                            $selected = "selected='selected'";
                        endif;
                        ?>
                        <option value="<?= $k ?>" <?= $selected ?>><?= $v ?></option>
                        <?php
                    endforeach;
                    ?>
                </select></div>
        </div>
    </div>

    <div class="row">
        <div class="twelve column" style="text-align: center">
            <?php if (!empty($times)): ?>
                Изчислено в <?= $times[0]['time_calculated'] ?><br />
                следващ транспорт след:<br />
                <?php foreach ($times as $time): ?>
                    <?= $time['minutes'] ?> мин. ( <?= $time['time'] ?> ) <br />
                <?php endforeach; ?>
            <?php endif; ?>
            <br />
        </div>
    </div>
    
<?php
    if(!empty($captchaImg)):
?>
    <div class="row">
        <div class="one-half column labels">
            &nbsp;
        </div>
        <div class="one-half column selects">
            <img src="data:image/png;base64,<?=  base64_encode($captchaImg)?>" alt="Captcha" />
        </div>
    </div>
    <div class="row">
        <div class="one-half column labels">
            <label for="<?=$paramsShort[SkgtParser\Parser::CAPTCHA_INPUT]?>"><?=$paramsNames[SkgtParser\Parser::CAPTCHA_INPUT]?>:</label>
        </div>
        <div class="one-half column selects">
            <input name="<?= $paramsShort[SkgtParser\Parser::CAPTCHA_INPUT] ?>" id="<?= $paramsShort[SkgtParser\Parser::CAPTCHA_INPUT] ?>" />
        </div>
    </div>
<?php
    endif;
?>
        

    <div class="row">
        <div class="twelve column" style="text-align: center">
            <input type="submit" value="Обнови" />
        </div>
    </div>

</form>

<div class="row">
    <div class="twelve column" style="text-align: center">
        <a href="/">Търсене по линия</a>
    </div>
</div>
