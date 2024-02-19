<div class="col-4 py-2 my-2">

    	<div class="my-3">
        <? if (is_object($this->Get("ARTICLE_OBJECT")->GetImage(0))) { ?>
            <a title="<?= $this->Get("TITLE") ?>" href="<?= $this->Get("URL") ?>">
                <?= $this->Get("ARTICLE_OBJECT")->GetImage(0)->GetHtml("_mf",$this->Get("TITLE"),""); ?>
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
        </div>

    	<div class="col-8">
            <h3 class=""><a class="" href="<?= $this->Get("URL"); ?>" title="<?= $this->Get("TITLE"); ?>"><?= $this->Get("TITLE"); ?></a></h3>
            <p class=""><?= $this->Get("DESC_SHORT_160"); ?></p>
    		<p class=""><small class="text-muted"><?= $this->Get("PUBLISHED_DATE"); ?></small></p>
    	</div>

</div>