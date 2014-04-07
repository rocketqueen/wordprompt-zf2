<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController,
	Zend\Authentication\AuthenticationService,
	Zend\Authentication\Adapter\DbTable as AuthAdapter,
	Zend\Authentication\Result as Result,
	Zend\View\Model\ViewModel;
use	Auth\Form\AuthForm;

class IndexController extends AbstractActionController
{
	public function indexAction()
	{
	}

	public function loginAction()
	{
		$form = new AuthForm();
		$messages = null;
		
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$authFormFilters = new Auth();
			$form->setInputFilter($authFormFilters->getInputFilters());
			$form->setData($request->getPost());
			if ($form->isValid()) {
				$data = $form->getData();
				$sm = $this->getServiceLocator();
				$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
				
				$config = $this->serviceLocator()->get('Config');
				$staticSalt = $config['static_salt'];
				
				$authAdapter = new AuthAdapter($dbAdapter,
					'users', // setTableName
					'username', // Identity Column
					'password', // USer password
					"MD5(CONCAT('$staticSalt', ?, user_password_salt')) AND status = 1"
				);
				
				$authAdapter
					->setIdentity($data['username'])
					->setCredential($data['password']);
				
				$auth = new AuthenticationService();
				$result = $auth->authenticate($authAdapter);
				
				switch($result->getCode()) {
					case Result::FAILURE_IDENTITY_NOT_FOUND:
						// do something for non existant identity
						break;
					case Result::FAILURE_CREDENTIAL_INVALID:
						// do something for invalid credential
						break;
					case Result::SUCCESS:
						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(null, 'password'));
						$time = 1209600; // 14 days
						if ($data['rememberme']) {
							$sessionManager = new \Zend\Session\SessionManager();
							$sessionManager->rememberMe($time);
						}
						break;
					default:
						// do stuff for other failures
						break;
				}
			}
		}
		return new ViewModel(array('form' => $form, 'messages' => $messages));
	}
			
}