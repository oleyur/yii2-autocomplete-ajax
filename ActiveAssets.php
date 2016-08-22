<?php

namespace oleyur\autocompleteAjax;

use \yii\web\AssetBundle;

/**
 * @author KeyGen <keygenqt@gmail.com>
 */
class ActiveAssets extends AssetBundle
{
	public $sourcePath = '@oleyur/autocompleteAjax/assets';

	// public $js = [
	// 	'js/jquery-ui-1.9.2.custom.min.js',
	// ];

	public $depends = [
		'yii\web\JqueryAsset'
	];

	public $css = [
		'css/jquery-ui-1.9.2.custom.min.css',
		'css/yii2-autocomplete-ajax.css',
	];
}
