<?php

namespace oleyur\autocompleteAjax;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\InputWidget;

class AutocompleteAjax extends InputWidget
{
    public $url = [];
    public $view_url = [];
    public $options = [];


    public $multiple = false;

    private $_baseUrl;
    private $_ajaxUrl;

    public function registerActiveAssets()
    {
        if ($this->_baseUrl === null) {
            $this->_baseUrl = ActiveAssets::register($this->getView())->baseUrl;
        }
        return $this->_baseUrl;
    }

    public function getUrl()
    {
        $this->_ajaxUrl = Url::toRoute($this->url);
        return $this->_ajaxUrl;
    }

    public function getViewUrl()
    {
        $this->_ajaxUrl = Url::toRoute($this->view_url);
        return $this->_ajaxUrl;
    }

    public function run()
    {
        $value = $this->model->{$this->attribute};
        $this->registerActiveAssets();
        $this->getView()->registerJs("
            var cache_{$this->getId()} = {};
            var cache_{$this->getId()}_1 = {};
            var cache_{$this->getId()}_2 = {};
            jQuery('#{$this->getId()}').autocomplete(
            {
                minLength: 1,
                source: function( request, response )
                {
                    var term = request.term;
                    if ( term in cache_{$this->getId()} ) {
                        response( cache_{$this->getId()} [term] );
                        return;
                    }
                    $.getJSON('{$this->getUrl()}', request, function( data, status, xhr ) {
                        cache_{$this->getId()} [term] = data;
                        response(data);
                    });
                },
                select: function(event, ui)
                {
                    $('#{$this->getId()}-hidden').val(ui.item.id);
                }
            });
        ");


        if ($value) {
            $this->getView()->registerJs("
                $(function(){
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: '{$this->getViewUrl()}',
                        data: {id: '$value'},
                        success: function(data) {

                            if (data.length == 0) {
                                $('#{$this->getId()}').attr('placeholder', 'User not found !!!');
                            } else {
                                var arr = [];
                                for (var i = 0; i<data.length; i++) {
                                    arr[i] = data[i].label;
                                    if (!(data[i].id in cache_{$this->getId()}_2)) {
                                        cache_{$this->getId()}_1[data[i].label] = data[i].id;
                                        cache_{$this->getId()}_2[data[i].id] = data[i].label;
                                    }
                                }
                                $('#{$this->getId()}').val(arr.join(', '));
                            }
                            $('.autocomplete-image-load').hide();
                        }
                    });
                });
            ");
        }

        $container = Html::tag('div',

            Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->getId() . '-hidden', 'class' => 'form-control'])
            . ($value ? Html::tag('div', "<img src='{$this->registerActiveAssets()}/images/load.gif'/>", ['class' => 'autocomplete-image-load']) : '')
            . Html::textInput('', '', array_merge($this->options, ['id' => $this->getId(), 'class' => 'form-control']))

            , [
                'style' => 'position: relative;',
            ]
        );

        return $container;
    }
}
