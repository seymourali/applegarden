<?php

/* @var $this yii\web\View */
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
$this->title = 'My Yii Application'; ?>
<div class="site-index">
    <?php
        function get_image($size) {
            $size = (float)$size;
            $size = 1-$size;
            $svg = '';
	        if ($size>0.01 && $size<=0.25) {
		       $svg = '25.svg';
	        } else if ($size>0.25 && $size<=0.5) {
		        $svg =  '50.svg';
	        } else if ($size>0.5 && $size<=0.75) {
		        $svg = '75.svg';
	        } else if ($size>0.75 && $size<1) {
		        $svg = '99.svg';
	        } else {$svg = '0.svg';}
	        return $svg;
        }
        
        $form = ActiveForm::begin(); ?>
        <div class="body-content">
            <div class="row">
                <div style="position: absolute;z-index: 1;">
                    <button type="button" title="Generate apples" class="btn btn-lg btn-default generate">Watering
                        <img src="<?=Yii::$app->homeUrl. '/img/plant.png';?>" alt="" />
                    </button>
                </div>
                <div style="display: flex;justify-content: center;position: relative;">
                    <?= Html::img('@web/img/apple-tree.png');?>
                    <img src="<?=Yii::$app->homeUrl. '/img/sun.png';?>" alt="" style="position: absolute;right: 100px;"/>
                    <div class="tree" style="width: 260px;height: 260px;top: 85px;display: flex;justify-content: center;position: absolute;transform: rotate(45deg);">
	                    <?php foreach ($apples_on_tree as $apple) { ?>
                            <div title="Click to pluck from the tree">
                                <svg class="apple onTree" data-id="<?=$apple['id']?>" style="fill:<?=$apple['color'];?>">
                                    <use xlink:href="<?= Yii::$app->homeUrl . '/svg/' . get_image($apple['size']); ?>#Capa_1"></use>
                                </svg>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="ground">
                        <div class="row" style="width: 100%;margin-left: 3px;">
                            <?php foreach ($apples_fell_chunk as $k => $apples_fell) {
                                $target_box = '';
                                if ($k===0) {$target_box='bottom';}
                                if ($k===1) {$target_box='middle';}
                                if ($k===2) {$target_box='upper';} ?>
                                <div data-position="<?=$target_box;?>">
                                    <?php foreach ($apples_fell as $apple) { ?>
                                        <div class="apple-container">
                                            <?php if (time() - strtotime($apple['fall_datetime']) > 5*3600) { ?>
                                                <div title="Rotten">
                                                    <svg class="apple" style="fill:<?=$apple['color'];?>">
                                                        <use xlink:href="<?= Yii::$app->homeUrl . '/svg/' . get_image($apple['size']); ?>#Capa_1"></use>
                                                    </svg>
                                                </div>
                                            <?php } else { ?>
                                                <div class="callout top-right">
                                                    <div style="display: flex;justify-content: space-between;">
                                                        <input type="number" class="form-control percent" max="100" style="width: 70px;" value="<?= (100 - round($apple['size'] * 100)) ?>"/>
                                                        <span style="padding-top: 5px;">%</span>
                                                        <button type="button" class="btn btn-default eat"
                                                                data-id="<?= $apple['id'] ?>"><i class="fa fa-cutlery" aria-hidden="true" style="height: 34px;"></i> Eat
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="fell" title="Click to eat">
                                                    <svg class="apple"  style="fill:<?=$apple['color'];?>">
                                                        <use xlink:href="<?= Yii::$app->homeUrl . '/svg/' . get_image($apple['size']); ?>#Capa_1"></use>
                                                    </svg>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div style="display: flex;justify-content: center;width: 100%;">
                            <img src="<?= Yii::$app->homeUrl . '/img/grass.png';?>" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<?$form->end(); ?>

    <script>
        
        let homeUrl = '<?=Yii::$app->homeUrl;?>';
        function getSvg(size) {
            if (!size) {return false;}
            size = 1-size;
            let svg = '';
            if (size>0.01 && size<=0.25) {
                svg = homeUrl + '/svg/25.svg';
            } else if (size>0.25 && size<=0.5) {
                svg = homeUrl + '/svg/50.svg';
            } else if (size>0.5 && size<=0.75) {
                svg = homeUrl + '/svg/75.svg';
            } else if (size>0.75 && size<1) {
                svg = homeUrl + '/svg/99.svg';
            } else {
                svg = homeUrl + '/svg/0.svg';
            }
            return svg;
        }
        
        $(document).ready(function () {
            
            $( '.onTree' ).each(function( index ) {
                $(this).css({
                    left : Math.random() * ($('.tree').width() - $(this).width()),
                    top : Math.random() * ($('.tree').height() - $(this).height())
                });
            });

            $('body').on('click', '.onTree', function () {
                let apple = $(this);
                let appleId = apple.data('id');
                if (!appleId) {return false;}
                let imgPath = homeUrl + '/img/apple.png';
                let url = homeUrl + "/site/fall-apple-to-ground/?id=" + appleId;
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            let appleFell = response.data.apple;
                            let targetBox = '',
                                fellCount = parseInt(response.data.fell_count);
                            if (fellCount<=15) {
                                targetBox = 'bottom';
                            } else if (fellCount<=30) {
                                targetBox = 'middle';
                            } else if (fellCount<=45) {
                                targetBox = 'upper';
                            }
                            apple.remove();
                            $(".ground .row div[data-position='" + targetBox +"']").append(
                            '<div class="apple-container">' +
                                '<div class="callout top-right">' +
                                    '<div style="display: flex;justify-content: space-between;">' +
                                        '<input type="number" class="form-control percent" max="100" style="width: 70px;" />' +
                                        '<span style="padding-top: 5px;">%</span>' +
                                        '<button type="button" class="btn btn-default eat" data-id="'+appleFell.id+'"> Eat</button>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="fell" title="Click to eat">' +
                                    '<svg class="apple" style="fill: '+appleFell.color+'"><use xlink:href="'+getSvg(appleFell.size)+'#Capa_1"></use></svg>' +
                                '</div>' +
                            '<div>');
                        }
                    },
                    error: function () {console.log("error");}
                })
            });
            
            $('body').on('click', '.fell', function () {
                let apple = $(this);
                let percentBox = apple.parent().find('.callout');
                $('.callout').hide();
                percentBox.show();
            });
            
            $('body').on('click', '.eat', function () {
                let apple = $(this);
                let appleContainer = apple.closest('.apple-container');
                let appleId = apple.data('id');
                let calloutDiv = apple.parent().parent();
                let percent = apple.parent().find('.percent');
                let percentValue = percent.val();
                if (!appleId || !percentValue) {return false;}
                if (parseFloat(percentValue)<=0 || parseFloat(percentValue)>100) {return false;}
                let url = homeUrl + "/site/eat-apple";
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        id: appleId,
                        percent: percentValue
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            if (response.data) {
                                let size = parseFloat(response.data.size);
                                let svg = getSvg(size);
                                appleContainer.find('.fell').remove();
                                calloutDiv.after('<div title="Click to eat" class="fell"><svg class="apple" style="fill: '+response.data.color+'"><use xlink:href="'+svg+'#Capa_1"></use></svg></div>');
                            } else {
                                appleContainer.remove();
                            }
                        }
                    },
                    error:function () {console.log('error');}
                });
            });
            
            $('.generate').on('click', function () {
                let url = homeUrl + "/site/generate";
                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            let generated = response.data;
                            if (generated) {
                                $.each(generated, function(k, v) {
                                    $('.tree').append('<div title="Click to pluck from the tree"><svg class="apple onTree" data-id="'+v.id+'" style="fill:'+v.color+'"><use xlink:href="'+getSvg(v.size)+'#Capa_1"></use></svg></div>');
                                    let elem = $(".onTree[data-id='" + v.id +"']");
                                    elem.css({
                                        left : Math.random() * ($('.tree').width() - elem.width()),
                                        top : Math.random() * ($('.tree').height() - elem.height())
                                    });
                                });
                            }
                        }
                    },
                    error: function() {console.log('error');}
                })
            });
        });
    </script>
</div>