<?php
namespace app\controllers;
use kartik\grid\DataColumn;
use yii\helpers\Html;
class LinkColumnLinkapp extends DataColumn
{
    public $linkAttribute = 'link';
    public $linkOptions = [];
    protected function renderDataCellContent($model, $key, $index)
    {
        $value = $this->getDataCellValue($model, $key, $index);
        if ($value === null) {
            return $this->grid->emptyCell;
        }
        $link = $model->{$this->linkAttribute};
        $options = array_merge([
            'class' => 'link-click',
            'data-link-id' => $model->id_linkapp,
            'data-pjax' => '0',
        ], $this->linkOptions);
        return Html::a($link, $value, $options);
    }
}
