<?php

// location: vendor/kartik-v/yii2-grid/src/GridViewTrait.php

protected function registerAssets()
    {
        $view = $this->getView();
        $script = '';
        if ($this->bootstrap) {
            GridViewAsset::register($view);
        }
        Dialog::widget($this->krajeeDialogSettings);
        $gridId = $this->options['id'];
        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            if (!isset($this->_module->downloadAction)) {
                $action = ["/../bengkulu/portalpintar/{$this->moduleId}/export/download"];// KHUSUS UNTUK WEBAPPS
            } else {
                $action = (array)$this->_module->downloadAction;
            }
            $gridOpts = Json::encode(
                [
                    'gridId' => $gridId,
                    'action' => Url::to($action),
                    'module' => $this->moduleId,
                    'encoding' => ArrayHelper::getValue($this->export, 'encoding', 'utf-8'),
                    'bom' => (int)ArrayHelper::getValue($this->export, 'bom', 1),
                    'target' => ArrayHelper::getValue($this->export, 'target', self::TARGET_BLANK),
                    'messages' => $this->export['messages'],
                    'exportConversions' => $this->exportConversions,
                    'skipExportElements' => $this->export['skipExportElements'],
                    'showConfirmAlert' => ArrayHelper::getValue($this->export, 'showConfirmAlert', true),
                ]
            );
            $gridOptsVar = 'kvGridExp_'.hash('crc32', $gridOpts);
            $view->registerJs("var {$gridOptsVar}={$gridOpts};");
            foreach ($this->exportConfig as $format => $setting) {
                $id = "jQuery('#{$gridId} .export-{$format}')";
                $genOpts = Json::encode(
                    [
                        'filename' => $setting['filename'],
                        'showHeader' => $setting['showHeader'],
                        'showPageSummary' => $setting['showPageSummary'],
                        'showFooter' => $setting['showFooter'],
                    ]
                );
                $genOptsVar = 'kvGridExp_'.hash('crc32', $genOpts);
                $view->registerJs("var {$genOptsVar}={$genOpts};");
                $expOpts = Json::encode(
                    [
                        'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
                        'gridOpts' => new JsExpression($gridOptsVar),
                        'genOpts' => new JsExpression($genOptsVar),
                        'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                        'config' => ArrayHelper::getValue($setting, 'config', []),
                    ]
                );
                $expOptsVar = 'kvGridExp_'.hash('crc32', $expOpts);
                $view->registerJs("var {$expOptsVar}={$expOpts};");
                $script .= "{$id}.gridexport({$expOptsVar});";
            }
        }
        $contId = '#'.$this->containerOptions['id'];
        $container = "jQuery('{$contId}')";
        if ($this->resizableColumns) {
            $rcDefaults = [];
            if ($this->persistResize) {
                GridResizeStoreAsset::register($view);
            } else {
                $rcDefaults = ['store' => null];
            }
            $rcOptions = Json::encode(array_replace_recursive($rcDefaults, $this->resizableColumnsOptions));
            GridResizeColumnsAsset::register($view);
            $script .= "{$container}.resizableColumns('destroy').resizableColumns({$rcOptions});";
        }
        $edited = $this->initEditedRow();
        if (!empty($edited)) {
            GridEditedRowAsset::register($view);
            $opts = Json::encode($edited);
            $script .= "kvGridEditedRow({$opts});";
        }
        $psVar = 'ps_'.Inflector::slug($this->containerOptions['id'], '_');
        if (!empty($this->perfectScrollbar)) {
            GridPerfectScrollbarAsset::register($view);
            $script .= "var {$psVar} = new PerfectScrollbar('{$contId}', ".
                Json::encode($this->perfectScrollbarOptions).');';
        }
        $this->genToggleDataScript();
        $script .= $this->_toggleScript;
        $this->_gridClientFunc = 'kvGridInit_'.hash('crc32', $script);
        $this->options['data-krajee-grid'] = $this->_gridClientFunc;
        $this->options['data-krajee-ps'] = $psVar;
        $view->registerJs("var {$this->_gridClientFunc}=function(){\n{$script}\n};\n{$this->_gridClientFunc}();");
    }