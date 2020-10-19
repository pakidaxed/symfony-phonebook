<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\TempContacts;
use App\Repository\TempContactsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SharingController
 * @package App\Controller
 * @Route("/sharing")
 */
class SharingController extends AbstractController
{
    /**
     * Displaying results of shared and received contacts
     *
     * @Route("/", name="sharing")
     * @param TempContactsRepository $tempContactsRepository
     * @return Response
     */

    public function index(TempContactsRepository $tempContactsRepository)
    {
        return $this->render('sharing/index.html.twig', [
            'received_contacts' => $tempContactsRepository->findBy(['receiver' => $this->getUser()->getId()]),
            'sent_contacts' => $tempContactsRepository->findBy(['owner' => $this->getUser()->getId()]),
        ]);
    }

    /**
     * Deleting the contact from db when canceled
     *
     * @Route("/{id}", name="sharing_cancel", methods={"CANCEL"})
     * @param Request $request
     * @param TempContacts $tempContact
     * @return Response
     */
    public function delete(Request $request, TempContacts $tempContact): Response
    {
        if ($this->isCsrfTokenValid('cancel' . $tempContact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tempContact);
            $entityManager->flush();

            $this->addFlash('success', $tempContact->getName() . " successfully removed");
        }

        return $this->redirectToRoute('sharing');
    }

    /**
     * Creating new contact to the user from received TempContacts and deleting the source after
     *
     * @Route("/{id}", name="sharing_accept", methods={"GET"})
     * @param TempContacts $tempContacts
     * @return Response
     */
    public function show(TempContacts $tempContacts): Response
    {
        $contact = new Contacts();
        $contact->setOwner($this->getUser()->getId());
        $contact->setName($tempContacts->getName());
        $contact->setEmail($tempContacts->getEmail());
        $contact->setPhone($tempContacts->getPhone());
        $contact->setComment($tempContacts->getComment());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->remove($tempContacts);
        $entityManager->flush();

        $this->addFlash('success', $contact->getName() . ' contacts were successfully added');

        return $this->redirectToRoute('sharing');
    }
}
