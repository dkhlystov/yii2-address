<?php

namespace dkhlystov\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use dkhlystov\widgets\assets\AddressInputGoogleAsset;
use dkhlystov\widgets\assets\AddressInputYandexAsset;

class AddressInput extends InputWidget
{

	const GOOGLE = 0;

	const YANDEX = 1;

	public $latitudeAttribute;

	public $longitudeAttribute;

	public $addressOptions = ['class' => 'form-control'];

	public $mapOptions = [];

	public $height = 300;

	public $type = 1;

	public $key;

	public $searchLabel = 'Find on map';

	public $removeLabel = 'Remove placemark';

	public function init()
	{
		parent::init();

		if ($this->latitudeAttribute === null)
			throw new InvalidConfigException('Property "latitudeAttribute" must be set.');
			
		if ($this->longitudeAttribute === null)
			throw new InvalidConfigException('Property "longitudeAttribute" must be set.');

		if ($this->type == self::GOOGLE && $this->key === null)
			throw new InvalidConfigException('Property "key" must be set if "type" is GOOGLE.');

		$this->registerClientScript();
	}

	public function run()
	{
		$input = $this->renderInput();
		$map = $this->renderMap();

		echo Html::tag('div', $input . $map, $this->options);
	}

	private function registerClientScript()
	{
		if ($this->type == self::GOOGLE) {
			AddressInputGoogleAsset::register($this->view);

		} else {
			AddressInputYandexAsset::register($this->view);
			$lang = str_replace('-', '_', Yii::$app->language);
			$this->view->registerJsFile("https://api-maps.yandex.ru/2.1/?load=package.full&lang={$lang}", ['position' => \yii\web\View::POS_HEAD]);
		}
		//добавить инициализацию
	}

	private function renderInput()
	{
		$input = Html::activeTextInput($this->model, $this->attribute, $this->addressOptions);

		$search = Html::button('<span class="glyphicon glyphicon-map-marker"></span>', [
			'class' => 'btn btn-default address-map-search',
			'title' => $this->searchLabel,
		]);
		$remove = Html::button('<span class="glyphicon glyphicon-remove"></span>', [
			'class' => 'btn btn-default address-map-remove',
			'title' => $this->removeLabel,
		]);
		$group = Html::tag('span', $search . $remove, ['class' => 'input-group-btn']);

		return Html::tag('div', $input . $group, ['class' => 'input-group']);
	}

	private function renderMap()
	{
		$latitude = Html::activeHiddenInput($this->model, $this->latitudeAttribute, ['class' => 'address-map-latitude']);
		$longitude = Html::activeHiddenInput($this->model, $this->longitudeAttribute, ['class' => 'address-map-longitude']);
		$options = $this->mapOptions;
		Html::addCssStyle($options, [
			'height' => $this->height . 'px',
		]);
		Html::addCssClass($options, 'address-map');
		$map = Html::tag('div', '', $options);

		return Html::tag('div', $latitude . $longitude . $map, ['class' => 'address-map-container']);
	}

}
