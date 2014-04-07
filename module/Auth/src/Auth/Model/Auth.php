<?php
namespace Auth\Model;

use Zend\InputFilter\InputFilterAwareInterface,
	Zend\InputFilter\InputFilter,
	Zend\InputFilter\Factory as InputFactory;

class Auth implements InputFilterAwareInterface 
{
	protected $inputFilter = null;
	
	public function setInputFilter()
	{
		throw new \Exception('Not used');
	}	

	public function getInputFilter()
	{
		if (!$this->getInputFilter()) {
			$inputFilter = new InputFilter();
			$factory     = new InputFactory();
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'username',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' =>array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min'      => 1,
							'max'      => 100,
						),
					),
				),
			)));
			
			$inputFilter->add($factory->createInput(array(
				'name'     => 'password',
				'required' => true,
				'filters'  => array(
					array('name' => 'StripTags'),
					array('name' => 'StringTrim'),
				),
				'validators' =>array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'encoding' => 'UTF-8',
							'min'      => 1,
							'max'      => 100,
						),
					),
				),
			)));
			
			$this->inputFilter = $inputFilter;
		}
		return $this->inputFilter;
	}
	
}