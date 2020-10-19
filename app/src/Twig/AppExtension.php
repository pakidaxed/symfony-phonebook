<?php

namespace App\Twig;

use App\Repository\TempContactsRepository;
use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private TempContactsRepository $tempContactsRepository;
    private UserRepository $userRepository;

    public function __construct(TempContactsRepository $tempContactsRepository, UserRepository $userRepository)
    {
        $this->tempContactsRepository = $tempContactsRepository;
        $this->userRepository = $userRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('idToName', [$this, 'convertName']),
            new TwigFunction('newReceived', [$this, 'newReceived']),
        ];
    }

    /**
     * Converting to full name from id, for display in twig
     *
     * @param int $id
     * @return string
     */
    public function convertName(int $id)
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        return $user->getFullName();
    }

    /**
     * Making visable on all pages, when the new contacts are received
     *
     * @param $id
     * @return mixed
     */
    public function newReceived(int $id)
    {
        return count($this->tempContactsRepository->findBy(['receiver' => $id]));
    }
}