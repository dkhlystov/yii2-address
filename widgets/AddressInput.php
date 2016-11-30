<?php

namespace dkhlystov\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use dkhlystov\widgets\assets\AddressInputGoogleAsset;
use dkhlystov\widgets\assets\AddressInputYandexAsset;

/**
 * Address input widget
 */
class AddressInput extends InputWidget
{

	/**
	 * Map types
	 */
	const GOOGLE = 0;
	const YANDEX = 1;

	/**
	 * @var string latitude attribute name
	 */
	public $latitudeAttribute;

	/**
	 * @var string longitude attribute name
	 */
	public $longitudeAttribute;

	/**
	 * @var array HTML options for address input tag
	 */
	public $addressOptions = ['class' => 'form-control'];

	/**
	 * @var array HTML options for map tag
	 */
	public $mapOptions = [];

	/**
	 * @var integer map height
	 */
	public $height = 300;

	/**
	 * @var integer map type
	 */
	public $type = 1;

	/**
	 * @var string key for Google maps
	 */
	public $key;

	/**
	 * @var string label for "search address" button
	 */
	public $searchLabel = 'Find on map';

	/**
	 * @var string label for "remove marker" button
	 */
	public $removeLabel = 'Remove marker';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();

		if ($this->latitudeAttribute === null)
			throw new InvalidConfigException('Property "latitudeAttribute" must be set.');
			
		if ($this->longitudeAttribute === null)
			throw new InvalidConfigException('Property "longitudeAttribute" must be set.');

		if (!in_array($this->type, [self::GOOGLE, self::YANDEX]))
			throw new InvalidConfigException('Unknown map type.');

		if ($this->type == self::GOOGLE && $this->key === null)
			throw new InvalidConfigException('Property "key" must be set if "type" is GOOGLE.');

		$this->registerClientScript();
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$input = $this->renderInput();
		$map = $this->renderMap();

		echo Html::tag('div', $input . $map, $this->options);
	}

	/**
	 * Client script registration
	 * @return void
	 */
	private function registerClientScript()
	{
		if ($this->type == self::GOOGLE) {
			AddressInputGoogleAsset::register($this->view);
			$this->view->registerJsFile("https://maps.googleapis.com/maps/api/js?key={$this->key}&callback=initAddressInputMap", [
				'depends' => [AddressInputGoogleAsset::className()],
			]);
		} else {
			AddressInputYandexAsset::register($this->view);
			$lang = str_replace('-', '_', Yii::$app->language);
			$this->view->registerJsFile("https://api-maps.yandex.ru/2.1/?load=package.full&lang={$lang}", [
				'position' => \yii\web\View::POS_HEAD,
			]);
		}
	}

	/**
	 * Address input rendering
	 * @return string
	 */
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

	/**
	 * Map rendering
	 * @return string
	 */
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

		$options = ['class' => 'address-map-container'];
		if ($this->key !== null)
			$options['data-key'] = $this->key;

		return Html::tag('div', $latitude . $longitude . $map, $options);
	}

}
