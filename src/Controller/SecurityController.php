<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PrestationType;
use App\Form\RegistrationType;
use App\Form\PrestationTypeType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PrestationTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/users/add", name="security_add_user")
     */
    public function add_user(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('security/signup.html.twig',['form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){}

    /**
     * @Route("/users/{id}/edit", name="security_edit_user")
     */
    public function edit_user(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('message', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('home');
        }

        return $this->render('security/signup.html.twig',['form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/profile", name="user_profile")
     */
    public function user_profile(User $user, Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $this->denyAccessUnlessGranted('ROLE_USER',null,'Vous n\'avez pas accès à cette page!');
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('hospital/user_details.html.twig',['form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/users/{id}/delete", name="security_delete_user")
     */
    public function delete_user(User $user, EntityManagerInterface $manager,){
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        
            $manager->remove($user);
            $manager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/prestation_type/add", name="add_prestation_type")
     * @Route("/prestation_type/{id}/edit", name="edit_prestation_type")
     */
    public function add_edit_prestation_type(PrestationType $prestation_type = null, Request $request, EntityManagerInterface $manager){
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        if(!$prestation_type){
            $prestation_type = new PrestationType();
        }
        $form = $this->createForm(PrestationTypeType::class, $prestation_type);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form ->isValid()){
            $manager->persist($prestation_type);
            $manager->flush();
            return $this->redirectToRoute('prestation_type');
        }

        return $this->render('security/add-prestation-type.html.twig',['form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/prestation_type/{id}/delete", name="delete_prestation_type")
     */
    public function delete_prestation_type(PrestationType $prestation_type, EntityManagerInterface $manager,){
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        
            $manager->remove($prestation_type);
            $manager->flush();

        return $this->redirectToRoute('prestation_type');
    }

    /**
     * @Route("/prestation_type", name="prestation_type")
     */
    public function prestation_type(PrestationTypeRepository $prestation_types): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        return $this->render('security/prestation_type.html.twig', [
            'prestation_types' => $prestation_types->findAll(),
        ]);
    }
}
