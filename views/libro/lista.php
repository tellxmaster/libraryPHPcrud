<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\LinkPager;
?>

<div class="page-header">
<h1 class="mt-3 mb-3">Catalogo de libros <small>Listado</small></h1>
</div>


<div class="row">
    <?php foreach($libros as $libro): ?>
        
        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <a href="<?= Url::toRoute(['libro/view', 'id' => $libro->id]); ?>" class="thumbnail">
                <?= Html::img($libro->imagen, ['height'=>'450px']); ?>
                <?= Html::tag('h3', Html::encode("{$libro->titulo}"), ['class' => 'label label-default pb-4 pt-3']) ?>
            </a>
        </div>
        
    <?php endforeach; ?>
</div>

<?= LinkPager::widget(['pagination'=>$paginacion]) ?>