<?php namespace FireUnion\BlogFront\Components;

use Cms\Classes\ComponentBase;

class PostList extends ComponentBase {
	use \FireUnion\BlogFront\Traits\Loaders;
	public $formPage;

	public function componentDetails() {
		return [
			'name' => 'fireunion.blogfront::lang.list_comp.name',
			'description' => 'fireunion.blogfront::lang.list_comp.description',
		];
	}

	public function defineProperties() {
		return $this->propertiesFor('list');
	}

	public function init() {
		$this->initFor('list');
	}

	public function onRun() {
		$this->runFor('list');
	}
}
