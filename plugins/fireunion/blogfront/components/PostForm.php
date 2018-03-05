<?php namespace FireUnion\BlogFront\Components;

use Cms\Classes\ComponentBase;
use Redirect;

class PostForm extends ComponentBase {
	use \FireUnion\BlogFront\Traits\Loaders;
	use \FireUnion\BlogFront\Traits\Mailer;
	public $listPage;
	public $postPage;

	public function componentDetails() {
		return [
			'name' => 'fireunion.blogfront::lang.form_comp.name',
			'description' => 'fireunion.blogfront::lang.form_comp.description',
		];
	}

	public function defineProperties() {
		return $this->propertiesFor('form');
	}

	public function init() {
		$this->initFor('form');
	}

	public function onRun() {
		$this->runFor('form');
	}

	/**
	 * Ajax handler to save an event from form
	 * triggers onRun to show list after delete
	 * @return array for a flash like error message if there is a problem with form validation
	 */
	public function onSave() {

		if (!$this->save()) {
			return null;
		}

		// Redirect to the intended page after successful update
		$redirectUrl = $this->pageUrl($this->property('listPage'));
		return Redirect::to($redirectUrl);
	}
}
