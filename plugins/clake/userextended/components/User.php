<?php namespace Clake\Userextended\Components;

use Clake\UserExtended\Classes\CommentManager;
use Clake\UserExtended\Classes\FriendsManager;
use Clake\UserExtended\Classes\UserGroupManager;
use Clake\UserExtended\Classes\UserManager;
use Clake\UserExtended\Classes\UserRoleManager;
use Clake\UserExtended\Classes\UserUtil;
use Clake\Userextended\Models\Settings;
use Clake\UserExtended\Plugin;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\Paginator;
use Response;
use File;
use Lang;
use RainLab\Builder\Classes\ComponentHelper;;
use SystemException;
use Auth;
use RainLab\User\Models\User as RainLabUser;
use Backend\Widgets\Lists;
use Backend\Controllers\ListController;
use Illuminate\Support\Collection;

/**
 * User Extended by Shawn Clake
 * Class User
 * User Extended is licensed under the MIT license.
 *
 * @author Shawn Clake <shawn.clake@gmail.com>
 * @link https://github.com/ShawnClake/UserExtended
 *
 * @license https://github.com/ShawnClake/UserExtended/blob/master/LICENSE MIT
 * @package Clake\Userextended\Components
 */
class User extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'User lists, displaying a user, user search, user profiles.'
        ];
    }

    public function defineProperties()
    {
        return [
            'type' => [
                'title'       => 'Type',
                'type'        => 'dropdown',
                'default'     => 'random',
                'placeholder' => 'Select type',
            ],
            'maxItems' => [
                'title'             => 'Max items',
                'description'       => 'Max items to show in a list. 0=unlimited',
                'default'           => 5,
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'The Max Items property can contain only numeric symbols'
            ],
            'paramCode' => [
                'title'       => 'User ID URL parameter',
                'description' => 'Specifies a user ID to generate a list for. blank=logged in user',
                'type'        => 'string',
                'default'     => 'user'
            ],
            'profilePage' => [
                'title'       => 'Profile Page',
                'description' => 'The page to redirect to for user profiles.',
                'type'        => 'dropdown',
                'default'     => 'user/profile'
            ],
			'template' => [
				'title' 		=> 'Template',
				'description' 	=> 'The template to use for profile pages',
				'type'			=> 'dropdown',
				'default'		=> 'social'
			]
        ];
    }

    /**
     * Used for properties dropdown menu
     * @return mixed
     */
    public function getProfilePageOptions()
    {
        return [''=>'- none -'] + Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Used for properties dropdown menu
     * @return array
     */
    public function getTypeOptions()
    {
        return [
            'sortList'  => 'Display sortable User List',
            'all'    => 'Display user list',
            'single'  => 'Display a User',
            'search'  => 'User Search',
            'profile' => 'User Profile',
        ];
    }
	
	public function getTemplateOptions(){
		return [
			'social' 		=> 'Social', 
			'business' 		=> 'Business',
			'portfolio' 	=> 'Portfolio',
			'team' 			=> 'Team', 
			'custom' 		=> 'Custom'
        ];
	}

    /**
     * Returns a list of user groups to the page in a variable called 'groups'
     */
    public function onRun()
    {
        Plugin::injectAssets($this);
        $this->page['groups'] = UserGroupManager::currentUser()->allGroups()->getUsersGroups();
        //$this->addCss('/modules/system/assets/ui/storm.css');
        $this->addJs('assets/js/lists.js');
        $this->addJs('assets/js/list.min.js');

        // Build a back-end form with the context of ‘frontend’
        $formController = new \Clake\UserExtended\Controllers\Users();
        $formController->index('frontend');
        // Append the formController to the page
        $this->page['users'] = $formController;

        foreach($formController->getAssetPaths() as $type => $assets)
            foreach($assets as $asset)
                $this->{'add'.ucfirst($type)}($asset);
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function listUsers()
    {
        return UserManager::getActiveUsers();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function paginateUsers()
    {
        $users = RainLabUser::where('status', '!=', 'unavailable')->paginate(10);

        return $users;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function randomUsers()
    {
        return UserManager::getRandomUserSet($this->property('maxItems'));
    }

    /**
     * Returns the list/component type
     */
    public function type()
    {
        return $this->property('type');
    }

    /**
     * AJAX call for when someone wants to send a friend request
     */
    public function onRequest()
    {
        $friend = new Friends();
        $friend->onRequest();
    }

    /**
     * Returns a user by ID specified in the component, or the logged in user if one is not specified
     * @return mixed
     */
    public function singleUser()
    {
        $code = $this->property('paramCode');

        if($code != '')
            $user = UserUtil::getUser($this->param($code));
        else
            $user = UserUtil::getLoggedInUser();

        return UserUtil::convertToUserExtendedUser($user);
    }

    /**
     * Searches for users based on a page field called 'phrase'
     * @return array
     */
    public function onSearch()
    {
        $phrase = post('phrase');

        $results = UserUtil::searchUsers($phrase);

        foreach($results as $key=>$value)
        {
            $results[$key] = UserUtil::convertToRainlabUser($value);
        }

        return $this->renderResults($results);
    }

    /**
     * Renders a search results partial.
     * @param $results
     * @return array
     */
    private function renderResults($results)
    {
        $content = $this->renderPartial('ueuser::cards.htm', ['results' => $results]);
        return ['#partialUsers' => $content];
    }


    /**
     * Provides user information to the page
     * @return mixed
     */
    public function user()
    {
        $userid = $this->property('paramCode');

        if(!isset($userid) || empty($userid))
            $userid = UserUtil::getUsersIdElseLoggedInUsersId();

        return UserUtil::convertToUserExtendedUser(UserUtil::getUser($userid));
    }

    /**
     * Returns the users avatar model
     * TODO: This should return the default avatar if the user doesn't have an avatar
     */
    public function userAvatar()
    {
        $userid = $this->property('paramCode');

        if(!$user = UserUtil::getRainlabUser($userid))
            return;

        return $user->avatar;
    }

    /*
     * Retrieve additional user documents
     * 
     */
    public function userDocuments() {

        $userid = $this->property('paramCode');

         if(!$user = UserUtil::getRainlabUser($userid))
            return;

        return $user->documents;
    }

    /**
     * Returns whether or not the user is our friend and thus
     * whether or not the page should be partially restricted
     * @return bool
     */
    public function locked()
    {
		$userid = $this->property('paramCode');
		
        if(!UserUtil::getLoggedInUser())
            return null;
        return (FriendsManager::isFriend($userid)) || (UserUtil::getLoggedInUser()->id == $userid);
    }

    /**
     * AJAX call for when someone wants to send a friend request
     */
    public function onFriendUser()
    {
        $userid = post('id');

        if(empty($userid))
            $userid = $this->property('paramCode');

        FriendsManager::sendFriendRequest($userid);
    }

    /**
     * Returns a collection of comments for a users profile
     * @return mixed
     */
    public function comments()
    {
        $userid = $this->user()->id;

        $user = UserUtil::getUser($userid);

        if(empty($user))
            return [];

        return $user->comments()->orderby('updated_at', 'desc')->get();
    }

    /**
     * AJAX handler for when someone leaves a comment on a profile
     * @return array
     */
    public function onComment()
    {
        $userid = $this->user()->id;
        $content = post('comment');

        CommentManager::createComment($userid, $content);

        return $this->renderComments($this->comments());
    }

    /**
     * AJAX handler for when deleting a comment
     * @return array
     */
    public function onDeleteComment()
    {
        $commentid = post('commentid');

        CommentManager::deleteComment($commentid);

        return $this->renderComments($this->comments());
    }

    /**
     * Used to dynamically update the comment section when a user leaves a new comment
     * @param $comments
     * @return array
     */
    private function renderComments($comments)
    {
        $content = $this->renderPartial('ueuser::comments.htm', ['comments' => $comments]);
        return ['#comment_section' => $content];
    }

    /**
     * Returns a collection of roles which the logged in user has
     * @return string
     */
    public function roles()
    {
        return UserRoleManager::currentUser()->allRoles()->getUsersRoles();
    }

    /**
     * Returns a collection of groups which the logged in user has
     * @return mixed
     */
    public function groups()
    {
        return UserGroupManager::currentUser()->allGroups()->getUsersGroups();
    }

    /**
     * Returns a collection of roles which a user has
     * @return mixed
     */
    public function usersRoles()
    {
        $code = $this->property('paramCode');

        if($code != '')
            $user = UserUtil::getUser($this->param($code));
        else
            $user = UserUtil::getLoggedInUser();

        return UserRoleManager::with($user)->allRoles()->getUsersRoles();
    }

    /**
     * Returns a collection of groups which a user has
     * @return mixed
     */
    public function usersGroups()
    {
        $code = $this->property('paramCode');

        if($code != '')
            $user = UserUtil::getUser($this->param($code));
        else
            $user = UserUtil::getLoggedInUser();

        return UserGroupManager::with($user)->allGroups()->getUsersGroups();
    }

    /**
     * AJAX handler to visit profiles
     * @return mixed
     */
    public function onVisitProfile($property = null, $userid = null)
    {
        if(!Settings::get('enable_profiles', true))
            return false;

        if(!isset($userid))
            $userid = post('id');

        if($userid != null)
        {
            $url = $property == null ? $this->property('profilePage') . "/" . $userid : $property . "/" . $userid;

            $user = RainLabUser::where('id', '=', $userid)->first();
            $user->increment('count_views');
            
            return Redirect::intended($url);
        }
    }

	/**
	 * Returns the user that is currently logged in.
	 * @return user
	 */
    public function loggedIn()
    {
        $account = new Account();
        return $account->user();
    }

	/**
	 * Returns if the passed in user id is a friend of the current user.
	 * @return bool
	 */
	public function isFriend($friendId)
    {
		//if there is not a current user logged in, exit.
        if(!UserUtil::getLoggedInUser())
            return null;

        return FriendsManager::isFriend($friendId);
	}
	
	/**
	 * Returns if the current user has a pending friend request from the current user.
	 * @return bool
	 */
	public function isRequested($friendId)
    {
		//if there is not a current user logged in, exit.
        if(!UserUtil::getLoggedInUser())
            return null;

        return FriendsManager::isRequested($friendId);
	}

	/**
	 * Gets the url of the current user's profile page
	 * @return string
	 */
	public function profilePage($param = '')
    {
        $url = url($this->property('profilePage'));
        if(!empty($param))
            $url .= '/' . $param;
        return $url;
    }

    public function isInRole($roleCode)
    {
        return UserRoleManager::currentUser()->isInRole($roleCode);
    }

    /**
     * Returns true if the user is in the role which is passed in
     * Useful to use in twig templates such as this:
     * {% if ueuser.hasRole('sr-dev') %}
     * @param $roleCode
     * @return bool
     */
    public function hasRole($roleCode)
    {
        return UserRoleManager::currentUser()->isInRole($roleCode);
    }

    /**
     * Returns true if the user is the group which is passed in
     * Useful to use in twig templates such as this:
     * {% if ueuser.hasGroup('admin') %}
     * @param $groupCode
     * @return bool
     */
    public function hasGroup($groupCode)
    {
        return UserGroupManager::currentUser()->isInGroup($groupCode);
    }

    public function onDownload(){

        $userid = $this->property('paramCode');
        
        if(!$user = UserUtil::getRainlabUser($userid))
            return;
        
        //$resume = $user->avatar->path;
        //$file = File::get($resume);
        //$response = Response::make($path, 200);
        //$response->header('Content-Type', 'application/docx');
        //$respone = File::get($resume);
        //return $response;

        $file = $user->avatar->path;
        $file_name = $user->avatar->file_name;

         $headers = array(
              'Content-Type: application/msword',
            );

        return response()->download($file, $file_name, $headers);
    }
}