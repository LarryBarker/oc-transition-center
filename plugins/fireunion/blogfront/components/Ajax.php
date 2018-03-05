<?php namespace FireUnion\BlogFront\Components;

use Cms\Classes\ComponentBase;

class Ajax extends ComponentBase {
	use \FireUnion\BlogFront\Traits\Loaders;
	use \FireUnion\BlogFront\Traits\Mailer;

	public $postPage;

	public function componentDetails() {
		return [
			'name' => 'fireunion.blogfront::lang.ajax_comp.name',
			'description' => 'fireunion.blogfront::lang.ajax_comp.description',
		];
	}

	public function defineProperties() {
		return $this->propertiesFor('ajax');
	}

	public function init() {
		$this->isAjax = true;
		$this->initFor('ajax');
	}

	public function onRun() {
		$this->runFor('ajax');
	}

	/**
	 * Ajax handler to generates form and populates values if they exist yet
	 * @return null if event object does not exist
	 */
	protected function onPostForm() {
		if (!$this->post = $this->loadPost()) {
			return null;
		}
	}

	/**
	 * Ajax handler to save an post from form
	 * triggers onRun to show list after delete
	 * @return array for a flash like error message if there is a problem with form validation
	 */
	public function onSave() {
		if (!$this->save()) {
			return null;
		}
		$this->onRun();
	}
}
