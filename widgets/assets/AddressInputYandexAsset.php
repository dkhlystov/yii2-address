<?php

namespace dkhlystov\widgets\assets;

use yii\web\AssetBundle;

class AddressInputYandexAsset extends AssetBundle
{

	public $js = [
		'address-input-yandex.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		$this->sourcePath = __DIR__ . '/address-input';
	}

}
