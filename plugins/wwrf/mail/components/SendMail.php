<?php namespace Wwrf\Mail\Components;

use Cms\Classes\ComponentBase;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Redirect;
use Auth;

class SendMail extends ComponentBase
{
    /*
     * @var string
     *
     */
    public $user;

	public $token;

	public $reply;

	public $email;

    public function componentDetails()
    {
        return [
            'name'        => 'SendMail Component',
            'description' => 'No description provided yet...'
        ];
    }

    public function defineProperties()
    {
        return [            
            'id' => [
                'title'       => 'Message Id',
                'description' => 'Message Id parameter...',
                'default'     => '{{ :id }}',
                'type'        => 'string'
            ],
        ];
    }

	public function onRun()
    {
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}

        if ($this->property('id')){
			
			if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->property('id')))
			{
				$this->email = $this->after(':',$this->property('id'));
				return;
			}
			else {
			$id = $this->property('id');
			$getMessageUrl = "/me/messages('".$id."')";
			
			$token = $_SESSION['access_token'];
	
			$graph = new Graph();
			$graph->setAccessToken($_SESSION['access_token']);

			$message = new Model\Message();
	
			$message = $graph->createRequest('GET', $getMessageUrl)
								->setReturnType(Model\Message::class)
								->execute();
			
			$this->reply = $message->getReplyTo();
			}
		}

		$this->user = Auth::getUser();
    }

    /**
	* Send email from the current user to the POST 
	* form email provided
	*
	* @return view /email
	*/
	public function onSendEmail()
	{
		$this->user = Auth::getUser();

		if (session_status() == PHP_SESSION_NONE)
			session_start();
		$graph = new Graph();
		$graph->setAccessToken($_SESSION['access_token']);
		$me = $this->getMe();

		//Create a new sender object
		$sender = new Model\Recipient();
		$sEmail = new Model\EmailAddress();
		$sEmail->setName($me->getGivenName());
		$sEmail->setAddress($this->user->email);
		$sender->setEmailAddress($sEmail);
		
		//Create a new recipient object
		$recipient = new Model\Recipient();
		$rEmail = new Model\EmailAddress();
		$rEmail->setAddress($_POST['input_email']);
		$recipient->setEmailAddress($rEmail);

		//Set the body of the message
		$body = new Model\ItemBody();
		$body->setContent($_POST['input_body']);
		$body->setContentType(Model\BodyType::HTML);

		//Create a new message
		$mail = new Model\Message();
		$mail->setSubject($_POST['input_subject'])
			 ->setBody($body)
			 ->setSender($sender)
			 ->setFrom($sender)
			 ->setToRecipients(array($recipient));

		//Send the mail through Graph
		$request = $graph->createRequest("POST", "/me/sendMail")
						 ->attachBody(array ("message" => $mail));
		$request->execute();


		//Return to the email view
		return Redirect::to('/mail');
	}
	/**
	* Send reply from the current user to the POST 
	* form email provided
	*
	* @return view /email
	*/
	public function onSendReply()
	{

		$id = $this->property['id'];

		if (session_status() == PHP_SESSION_NONE)
			session_start();
		$graph = new Graph();
		$graph->setAccessToken($_SESSION['access_token']);
		$me = $this->getMe();

		//Create a new sender object
		$sender = new Model\Recipient();
		$sEmail = new Model\EmailAddress();
		$sEmail->setName($me->getGivenName());
		$sEmail->setAddress($this->user->email);
		$sender->setEmailAddress($sEmail);
		
		//Create a new recipient object
		$recipient = new Model\Recipient();
		$rEmail = new Model\EmailAddress();
		$rEmail->setAddress($this->reply);
		$recipient->setEmailAddress($rEmail);

		//Set the body of the message
		$body = new Model\ItemBody();
		$body->setContent($_POST['input_body']);
		$body->setContentType(Model\BodyType::HTML);

		//Create a new message
		$mail = new Model\Message();
		$mail->setSubject($_POST['input_subject'])
			 ->setBody($body)
			 ->setSender($sender)
			 ->setFrom($sender)
			 ->setToRecipients(array($recipient));

		//Send the mail through Graph
		$request = $graph->createRequest("POST", "/me/messages/".$id."reply")
						 ->attachBody(array ("message" => $mail));
		$request->execute();


		//Return to the email view
		return Redirect::to('/mail');
	}
	/**
	* Queries Microsoft Graph to get the current logged-in
	* user's info
	*
	* @return Microsoft\Graph\Model\User The current user
	*/
	public function getMe()
	{
		if (session_status() == PHP_SESSION_NONE)
			session_start();

		$graph = new Graph();
		$graph->setAccessToken($_SESSION['access_token']);

		$me = $graph->createRequest("get", "/me")
					->setReturnType(Model\User::class)
					->execute();
		return $me;
	}

	function after ($chr, $inthat)
    {
        if (!is_bool(strpos($inthat, $chr)))
        return substr($inthat, strpos($inthat,$chr)+strlen($chr));
    }
}
