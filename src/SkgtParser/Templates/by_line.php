<?php 
    $this->layout('layout', array('title' => 'Търсене по линия')) ;
?>

        <div class="row">
            <div class="twelve column" style="margin-top: 1%; text-align: center">
                <input type="button" id="toggle_form" value="Покажи/Скрий формата" /><br />
            </div>
        </div>
    
    <form method="GET" action="/">
        <input type="hidden" name="lc" value="" />
        <div id="form_div" style="display:<?=empty($times)?'block':'none'?>">
<?php
    foreach($selectsData as $name => $options):
?>
    <div class="row">
        <div class="one-half column labels">
            <label for="<?=$paramsShort[$name]?>"><?=$paramsNames[$name]?>:</label>
        </div>
        <div class="one-half column selects">
        
        <select name="<?=$paramsShort[$name]?>" id="<?=$paramsShort[$name]?>">
<?php
        foreach($options as $k=>$v):
            $selected = '';
            if ($k===$params[$name]):
                $selected = "selected='selected'";
            endif;
?>
            <option value="<?=$k?>" <?=$selected?>><?=$v?></option>
<?php
        endforeach;
?>
        </select></div>
    </div>
<?php
    endforeach;
?>
            </div>

        

        <div class="row">
            <div class="twelve column" style="text-align: center">
                <?php if(!empty($times)): ?>
                Изчислено в <?=$times[0]['time_calculated']?><br />
                следващ транспорт след:<br />
                <?php foreach ($times as $time): ?>
                <?=$time['minutes']?> мин. ( <?=$time['time']?> ) <br />
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

<?php if(!empty($params[SkgtParser\Parser::STOPS_SELECT])): ?>
    <div class="row">
        <div class="twelve column" style="text-align: center">
            <?php
            $stop_name = $selectsData[SkgtParser\Parser::STOPS_SELECT][$params[SkgtParser\Parser::STOPS_SELECT]];
            $stopCode = substr($stop_name, 1, 4)+0;
            ?>
            <a href="?bystop=&amp;sc=<?=$stopCode?>">Друг транспорт от спирката</a>
        </div>
    </div>
<?php endif; ?>
    <div class="row">
        <div class="twelve column" style="text-align: center">
            <a href="?bystop">Търсене по спирка</a>
        </div>
    </div>
    