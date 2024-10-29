<?php
namespace app\controllers;
use kartik\grid\DataColumn;
use yii\helpers\Html;
class LinkColumnLinkmat extends DataColumn
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
        // Extract the string between "https://" and the first slash ("/")
        $startPos = strpos($value, 'https://') + strlen('https://');
        $endPos = strpos($value, '/', $startPos);
        $extractedString = "_".substr($value, $startPos, $endPos - $startPos)."_";
        $options = array_merge([
            'class' => 'link-click',
            'data-link-id' => $model->id_linkmat,
            'data-pjax' => '0',
        ], $this->linkOptions);
        return Html::a($extractedString, $value, $options);
    }
}
