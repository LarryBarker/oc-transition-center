<?php namespace FireUnion\BlogFront\Traits;

use Backend\Models\User;
use FireUnion\BlogFront\Classes\NewPostMailable;
use Mail;
use RainLab\User\Models\UserGroup;
trait Mailer {

	/**
	 * Load a Group and necessary associated data
	 * @param  GroupId $group_id
	 * @return void
	 */
	public function getGroup($group_id) {
		return UserGroup::with(['users' => function ($q) {
			$q->select('name', 'surname', 'email', 'id');
		}])->
			where('id', $group_id)->first();
	}

	/**
	 * Load a Group Recipients data
	 * @param  GroupId $group_id
	 * @return void
	 */
	public function getGroupRecipients($group_id) {
		$recipients = [];
		if (!$group_users = $this->getGroup($group_id)->users) {
			return $recipients;
		}
		foreach ($group_users as $user) {
			$recipients[$user->id] = [
				'name' => $user->name . ' ' . $user->surname,
				'address' => $user->email,
			];
		}
		return $recipients;
	}

	/**
	 * Notify users about a new post
	 * @param  String $category
	 * @param  Post Object $post
	 * @param  URL $postUrl
	 * @return void
	 */
	public function notifyGroups($groups, $post, $postUrl) {
		$recipients = [];
		if ($groups) {
			foreach ($groups as $group_id) {
				$recipients = array_merge($recipients, $this->getGroupRecipients($group_id));
			}
			if (count($recipients)) {
				$this->sendNotifications($recipients, $post, $postUrl);
			}
		}
	}

	/**
	 * Sends notifications to user about a new post
	 * @param  String $category
	 * @param  Post Object $post
	 * @param  URL $postUrl
	 * @return void
	 */
	public function sendNotifications($recipients, $post, $postUrl) {
		$build = \System\Models\Parameter::get('system::core.build');
		$postUrl = $postUrl . '/' . $post->slug;
		foreach ($recipients as $user) {

			if ($build > 419) {
				Mail::to($user['address'])->queue(
					new NewPostMailable($post, $postUrl, $user)
				);
			} else {
				$data = [
					'post' => $post,
					'user' => $user,
					'postUrl' => $postUrl,
				];

				$address = $user['address'];

				Mail::queue('fireunion.blogfront::mail.new_post', $data, function ($message) use ($address) {
					$message->to($address);
				});
			}

		}

	}

}