    
<div class="card border-0  py-2 my-2">

    <? if (is_object($this->Get("ARTICLE_OBJECT")->GetImage(0))) { ?>
        <a title="<?= $this->Get("TITLE") ?>" href="<?= $this->Get("URL") ?>">
            <?= $this->Get("ARTICLE_OBJECT")->GetImage(0)->GetHtml("_l",$this->Get("TITLE"),"card-img-top"); ?>
        </a>
    <? } else { 
        // try to grab an image from article body text
        $html = $this->Get("ARTICLE_OBJECT")->GetDescFull();
        $arrImgUrl = array();
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $arrImgUrl );
        if (count($arrImgUrl[1]) >= 1)
        { ?>
    	<a title="<?= $this->Get("TITLE") ?>" href="<?= $this->Get("URL") ?>">
    		<img class='img-responsive img-rounded' src='<?= $arrImgUrl[1][0] ?>' alt='<?= $this->Get("TITLE") ?>' border='0' />
    	</a><?php 
        }
    }?>

    <div class="card-body col-4 card-img-overlay">
    	<div class="box box2">
            <h2 class="card-title"><a class="blue" style="font-weight: bold;" href="<?= $this->Get("URL"); ?>" title="<?= $this->Get("TITLE"); ?>"><?= $this->Get("TITLE"); ?></a></h2>
            <p class="card-text"><?= $this->Get("DESC_SHORT_160"); ?></p>
    		<!-- <p class="card-text"><small class="text-muted"><?= $this->Get("PUBLISHED_DATE"); ?></small></p> -->
		</div>        
    </div>
</div>

