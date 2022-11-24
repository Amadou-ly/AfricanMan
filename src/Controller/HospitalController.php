<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Prestation;
use App\Form\PrestationType;
use App\Repository\UserRepository;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HospitalController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $users): Response
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('hospital\index.html.twig', [
            'controller_name' => 'HospitalController',
            'users' => $users->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/patients", name="patients")
     */
    public function patients(): Response
    {
        
        $repo = $this->getDoctrine() ->getRepository(Patient::class);
        $patients = $repo ->findAll();
        return $this->render('hospital\patients.html.twig', [
            'controller_name' => 'HospitalController',
            'patients' => $patients,
        ]);
    }

     /**
     * @IsGranted("ROLE_USER")
     * @Route("/patients/search", name="search_patient")
     */
    public function search_patient(PatientRepository $patientrepo, Request $request): Response
    {
        // $results = $patientrepo -> searchResult(
        //     $request ->query ->get('name')
        // );
        $searchName = $request ->query ->get('name');
     
        $results = $this->getDoctrine() ->getRepository(Patient::class)->createQueryBuilder('p')
            ->where('p.Nom = :searchName')
            ->setParameter('searchName', $searchName)
            ->getQuery()
            ->getResult();

        print_r($results);
        return $this->render('hospital\patient_search.html.twig', [
            'results' => $results,
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/prestations", name="prestations")
     */
    public function prestations(): Response
    {
        return $this->render('hospital\prestations.html.twig', [
            'controller_name' => 'HospitalController',
        ]);
    }
    
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/pathologies", name="pathologies")
     */
    public function pathologies(): Response
    {
        return $this->render('hospital\pathologies.html.twig', [
            'controller_name' => 'HospitalController',
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/patients/add", name="add_patient")
     * @Route("/patients/{id}/edit", name="patient_edit")
     */
    public function add_edit_patient(Patient $patient = null, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$patient){
            $patient = new Patient();
       }
       
       if($request->request->count() > 0 ){
           $patient->setNom($request->request->get('Nom'))
           ->setContact($request->request->get('Contact'));
           if(!$patient -> getId()){
            $patient->setDateDeCreation(new \Datetime());
         }
           $manager->persist($patient);
           $manager->flush();
           return $this->redirectToRoute('patient_details',['id' => $patient->getId()]);
       }

        return $this->render('hospital\add-patient.html.twig',
            ['editMode'=> $patient->getId() !== null]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/patients/{id}", name="patient_details")
     */
    public function patient_details($id): Response
    {
        $repo = $this->getDoctrine() ->getRepository(Patient::class);
        $patient = $repo-> find($id);
                
        return $this->render('hospital\patient_details.html.twig', [
            'patient' => $patient,
        ]);
    }
    
    

    /**
     * @Route("/users", name="users")
     */
    public function users(UserRepository $users): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,'Vous n\'avez pas accès à cette page!');
        return $this->render('hospital\users.html.twig', [
            'controller_name' => 'HospitalController',
            'users' => $users->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/patients/{id}/add_prestation", name="add_prestation")
     */
    public function add_prestation(PatientRepository $patients, $id, Request $request, EntityManagerInterface $manager): Response
    {
        $prestation = new Prestation();
        $form = $this->createForm(PrestationType::class, $prestation);
        $form->handleRequest($request);
        $prestation->setDateDeCreation(new \Datetime());

        if($form->isSubmitted() && $form ->isValid()){
            $patient = $patients->find($id);
            $prestation->setpatient($patient);
            $manager->persist($prestation);
            $manager->flush(); 
        }
        return $this->render('hospital\add-prestation.html.twig',['form'=> $form->createView()]);
    }

}
