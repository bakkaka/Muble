<?php
namespace App\Form\Model;
use App\Validator\UniqueUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{
    /**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;
	
	 /**
     * @Assert\NotBlank(message="Please enter an email")
     * @Assert\Phone()
     * @UniqueUser()
     */
    public $phone;
	
	
    /**
     * @Assert\NotBlank(message="Choose a password!")
     * @Assert\Length(min=5, minMessage="Come on, you can think of a password longer than that!")
     */
    public $plainPassword;
    /**
     * @Assert\IsTrue(message="I know, it's silly, but you must agree to our terms.")
     */

    public $agreeTerms;

    /**
     * @Assert\NotBlank(message="Choose a username!")
     * @Assert\Length(min=5, minMessage="Come on, you can think of a username longer than that!")
     */
    public $username;

    /**
     * @Assert\NotBlank(message="Choose a fullName!")
     * @Assert\Length(min=5, minMessage="Come on, you can think of a fullName longer than that!")
     */
    public $fullName;

}