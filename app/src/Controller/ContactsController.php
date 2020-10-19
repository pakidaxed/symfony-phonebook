<?php

namespace App\Controller;

use App\Entity\Contacts;
use App\Entity\TempContacts;
use App\Form\ContactsType;
use App\Form\TempContactsType;
use App\Repository\ContactsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contacts")
 */
class ContactsController extends AbstractController
{
    public array $error = [];
    public string $receiver;

    /**
     * @Route("/", name="contacts_index", methods={"GET"})
     * @param ContactsRepository $contactsRepository
     * @return Response
     */
    public function index(ContactsRepository $contactsRepository): Response
    {
        return $this->render('contacts/index.html.twig', [
            'contacts' => $contactsRepository->findBy(['owner' => $this->getUser()->getId()]),
        ]);
    }

    /**
     * Creating new contact to the database
     *
     * @Route("/new", name="contacts_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $contact = new Contacts();
        $contact->setOwner($this->getUser()->getId());
        $form = $this->createForm(ContactsType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', $contact->getName() . ' added successfully');

            return $this->redirectToRoute('contacts_index');
        }

        return $this->render('contacts/new.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Editing chosen contact from the list
     *
     * @Route("/{id}/edit", name="contacts_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Contacts $contact
     * @return Response
     */
    public function edit(Request $request, Contacts $contact): Response
    {
        $form = $this->createForm(ContactsType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $contact->getName() . ' contacts were successfully updated');

            return $this->redirectToRoute('contacts_index');
        }

        return $this->render('contacts/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deleting existing contact from database with confirmation
     *
     * @Route("/{id}", name="contacts_delete", methods={"DELETE"})
     * @param Request $request
     * @param Contacts $contact
     * @return Response
     */
    public function delete(Request $request, Contacts $contact): Response
    {
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();

            $this->addFlash('success', $contact->getName() . " successfully deleted");
        }

        return $this->redirectToRoute('contacts_index');
    }

    /**
     * Sharing chosen contact with other APP user (who exists on this DB)
     * Adding existing contact to other TempContact entity letting to change only comment before sending
     *
     * @Route("/{id}/share", name="contacts_share", methods={"GET","POST"})
     * @param Request $request
     * @param Contacts $contact
     * @param UserRepository $userRepository
     * @return Response
     */
    public function share(Request $request, Contacts $contact, UserRepository $userRepository): Response
    {
        $tempContact = new TempContacts();
        $tempContact->setName($contact->getName());
        $tempContact->setEmail($contact->getEmail());
        $tempContact->setPhone($contact->getPhone());
        $tempContact->setComment($contact->getComment());
        $tempContact->setOwner($this->getUser()->getId());
        $form = $this->createForm(TempContactsType::class, $tempContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->receiver = $form->getData()->getReceiver();

            if ($this->receiver == $this->getUser()->getEmail()) {
                $this->error[] = 'You can not send to yourself';

            } else if ($this->receiver == null) {
                $this->error[] = 'Enter the receivers email address';

            } else {
                $search = $userRepository->findOneBy(['email' => $this->receiver]);

                if (!$search) {
                    $this->error[] = 'No such user in our database';

                } else {
                    $tempContact->setReceiver($search->getId());
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($tempContact);
                    $entityManager->flush();

                    $this->addFlash('success', $contact->getName() . ' contacts were successfully sent to ' . $this->receiver);

                    return $this->redirectToRoute('contacts_index');
                }
            }
        }

        return $this->render('contacts/share.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'error' => $this->error,
        ]);
    }
}
