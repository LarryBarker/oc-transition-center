<?php namespace FireUnion\BlogFront\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use RainLab\Blog\Models\Category as BlogCategory;

/**
 * Authors Back-end Controller
 */
class Authors extends Controller {
	public $categoriesOptions = [];
	public $implement = [
		'Backend.Behaviors.FormController',
		'Backend.Behaviors.ListController',
	];

	public $formConfig = 'config_form.yaml';
	public $listConfig = 'config_list.yaml';

	public function __construct() {
		parent::__construct();

		BackendMenu::setContext('RainLab.Blog', 'blog', 'authors');
	}

	/**
	 * Sets the "currenttime" attribute
	 *
	 * @return integer
	 */
	public function getCatNames($categories) {
		$this->categoriesOptions = $this->categoriesOptions ?: BlogCategory::orderBy('name')->lists('name', 'id');
		$categoriesNames = array_flip($this->categoriesOptions);
		$cat_names = array_flip(array_intersect($categoriesNames, $categories));
		return implode(', ', $cat_names);
	}
}
