<?php

namespace KnpU\CodeBattle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Programmer
{

    /* All public properties are persisted */
    public $id;

    /**
     * @Assert\NotBlank(message="Please enter a nickname")
     */
    public $nickname;

    /**
     * Number of an avatar, from 1-6
     *
     * @var integer
     */
    public $avatarNumber;

    /**
     * @Assert\NotBlank(message="Don't leave tagLine empty")
     */
    public $tagLine;

    public $userId;

    public $powerLevel = 0;

    public function __construct($nickname = null, $avatarNumber = null)
    {
        $this->nickname = $nickname;
        $this->avatarNumber = $avatarNumber;
    }


}
