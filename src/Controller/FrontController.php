<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Pass;
use App\Form\PassFormType;
use App\Repository\PassRepository;
use App\Entity\Rig;
use App\Form\RigFormType;
use App\Repository\RigRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Entity\PlanCommunication;
use App\Form\PlanCommunicationFormType;
use App\Repository\PlanCommunicationRepository;
use App\Entity\Satisfaction;
use App\Form\SatisfactionType;
use App\Repository\SatisfactionRepository;
use App\Entity\Sites;
use App\Form\SiteType;
use App\Repository\SitesRepository;
use App\Entity\Swot;
use App\Form\SwotType;
use App\Repository\SwotRepository;
use App\Entity\CartesCadeaux;
use App\Form\CartesCadeauxFormType;
use App\Repository\CartesCadeauxRepository;
use App\Form\SearchSwotType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\HttpFoundation\JsonResponse;

class FrontController extends AbstractController
{

    //
    // ******************* DASHBOARD PAGES **********************
    //

    /**
     * @Route("/DGdashboard", name="DGdashboard")
     */
    public function index(Request $request): Response
    {
        return $this->render('dg/index.html.twig', [
            'controller_name' => 'DgController',
        ]);
    }

    /**
     * @Route("/DGdashboard/swot", name="swot")
     */
    public function swot(Request $request, SwotRepository $swotRepo, UserRepository $userRepo): Response
    {   
        $form = $this->createForm(SearchSwotType::class);
        $search = $form->handleRequest($request);

        // Recherche des annonces correspondant aux mots clés
        $swot = $swotRepo->search($search->get('mots')->getData());
        
        $swotRepo = $this->getDoctrine()->getRepository(Swot::class);
        $swotList = $swotRepo -> findBy(
            ['user' => $this->getUser()],
        ); 

        //Données DR
        $swotRepo = $this->getDoctrine()->getRepository(Swot::class);
        $swotDR = $swotRepo -> findBy(
            ['user' => $this->getUser()],
        );

        //Données DS
        $swotRepo = $this->getDoctrine()->getRepository(Swot::class);
        $swotDS = $swotRepo -> findOneBy(
            ['user' => $this->getUser()],
        ); 

    return $this->render('dg/swot.html.twig', [
        'swot' => $swot,
        'form' => $form->createView(),
        'swotList' => $swotList,
        'swotDR' => $swotDR,
        'swotDS' => $swotDS,
    ]);
    }

    /**
     * @Route("/DGdashboard/satisfaction", name="satisfaction")
     */
    public function satisfaction(SatisfactionRepository $satisfactionRepo): Response
    {
    
        // Données DR
        $satisDR = $satisfactionRepo->findBy(
            ['user' => $this->getUser()],
        );

        $trimestre = [];
        $satis_proprete = [];
        foreach($satisDR as $satisDRS) {
            $trimestre[]= $satisDRS ->getTrimestre();
            $satis_proprete[] = $satisDRS ->getSatisProprete();
        }

            
        // Données DS
        $satisDS = $satisfactionRepo->findBy(
        ['user' => $this->getUser()],
        );

        // Données DG
        $satisDG = $satisfactionRepo->findAll();

    return $this->render('dg/satisfaction.html.twig', [
        'satisDR' => $satisDR,
        'satisDS' => $satisDS,
        'satisDG' => $satisDG,
        'trimestre' => json_encode($trimestre),
        'satis_proprete' => json_encode($satis_proprete),
    ]);
    }

    /**
     * @Route("/DGdashboard/carteCadeau", name="carteCadeau")
     */
    public function carteCadeau(CartesCadeauxRepository $cartesRepo, SitesRepository $siteRepo): Response
    {
        // ************************* DS VIEW *******************************$
        // Données de la bdd pour la graphique 
        $cartescadeaux = $cartesRepo->findBy(
            ['user' => $this->getUser()],
            ['annee' => 'ASC'],
            12,
            0
        );

        $cartescadeauxvendues2017 = $cartesRepo->findByAnnee("2017");
        $cartescadeauxvendues2020 = $cartesRepo->findByAnnee("2020");
        $cartescadeauxvendues2021 = $cartesRepo->findByAnnee("2021");
        $cartescadeauxutilisées2017 = $cartesRepo->findByAnnee("2017");
        $cartescadeauxutilisées2020 = $cartesRepo->findByAnnee("2020");
        $cartescadeauxutilisées2021 = $cartesRepo->findByAnnee("2021");
        $cartescadeauxvalorisationvente2017 = $cartesRepo->findByAnnee("2017");
        $cartescadeauxvalorisationvente2020 = $cartesRepo->findByAnnee("2020");
        $cartescadeauxvalorisationvente2021 = $cartesRepo->findByAnnee("2021");
        $cartescadeauxvalorisationutilisation2017 = $cartesRepo->findByAnnee("2017");
        $cartescadeauxvalorisationutilisation2020 = $cartesRepo->findByAnnee("2020");
        $cartescadeauxvalorisationutilisation2021 = $cartesRepo->findByAnnee("2021");


        $mois = [];
        $cartesvendues = [];

        // FIELD nombre_cartes_vendues
        foreach($cartescadeauxvendues2020 as $cartecadeauvendues2020) {
            $mois[]= $cartecadeauvendues2020 ->getMois();
            $cartesvendues2020[] = $cartecadeauvendues2020 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvendues2017 as $cartecadeauvendues2017) {
            $mois[]= $cartecadeauvendues2017 ->getMois();
            $cartesvendues2017[] = $cartecadeauvendues2017 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvendues2021 as $cartecadeauvendues2021) {
            $mois[]= $cartecadeauvendues2021 ->getMois();
            $cartesvendues2021[] = $cartecadeauvendues2021 ->getNombreCartesVendues();
        }

        // FIELD nombre_cartes_utilisées
        foreach($cartescadeauxutilisées2020 as $cartecadeauutilisées2020) {
            $mois[]= $cartecadeauutilisées2020 ->getMois();
            $cartesvendues2020[] = $cartecadeauutilisées2020 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxutilisées2017 as $cartecadeauutilisées2017) {
            $mois[]= $cartecadeauutilisées2017 ->getMois();
            $cartesvendues2017[] = $cartecadeauutilisées2017 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxutilisées2021 as $cartecadeauutilisées2021) {
            $mois[]= $cartecadeauutilisées2021 ->getMois();
            $cartesvendues2021[] = $cartecadeauutilisées2021 ->getNombreCartesVendues();
        }

        // FIELD valorisation_vente
        foreach($cartescadeauxvalorisationvente2017 as $cartecadeauvalorisationvente2017) {
            $mois[]= $cartecadeauvalorisationvente2017 ->getMois();
            $cartesvendues2020[] = $cartecadeauvalorisationvente2017 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvalorisationvente2020 as $cartecadeauvalorisationvente2020) {
            $mois[]= $cartecadeauvalorisationvente2020 ->getMois();
            $cartesvendues2017[] = $cartecadeauvalorisationvente2020 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvalorisationvente2021 as $cartecadeauvalorisationvente2021) {
            $mois[]= $cartecadeauvalorisationvente2021 ->getMois();
            $cartesvendues2021[] = $cartecadeauvalorisationvente2021 ->getNombreCartesVendues();
        }

        // FIELD valorisation_utilisation
        foreach($cartescadeauxvalorisationutilisation2017 as $cartecadeauvalorisationutilisation2017) {
            $mois[]= $cartecadeauvalorisationutilisation2017 ->getMois();
            $cartes2020[] = $cartecadeauvalorisationutilisation2017 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvalorisationutilisation2020 as $cartecadeauvalorisationutilisation2020) {
            $mois[]= $cartecadeauvalorisationutilisation2020 ->getMois();
            $cartesvendues2017[] = $cartecadeauvalorisationutilisation2020 ->getNombreCartesVendues();
        }

        foreach($cartescadeauxvalorisationutilisation2021 as $cartecadeauvalorisationutilisation2021) {
            $mois[]= $cartecadeauvalorisationutilisation2021 ->getMois();
            $cartesvendues2021[] = $cartecadeauvalorisationutilisation2021 ->getNombreCartesVendues();
        }
        

        //données de la bdd pour le tableau - RAPPEL - tri par site et par dates des cartes cadeaux
        $sitesTableau = $siteRepo -> findBy (
            ['user' => $this->getUser()],
        );
        $cartesCadeauxTableau = $cartesRepo->findBy(
            ['site' => $this->getUser(),
            ],
            ['mois' => 'ASC']
        );  
        
    return $this->render('dg/carteCadeau.html.twig', [
        'mois' => json_encode($mois),
        'cartesvendues' => json_encode($cartesvendues),
        'cartesvendues2020' => json_encode($cartesvendues2020),
        'cartesvendues2021' => json_encode($cartesvendues2021),
        'cartesvendues2017' => json_encode($cartesvendues2017),
        'cartesvendues2017' => json_encode($cartesvendues2017),
        'cartesvendues2017' => json_encode($cartesvendues2017),
        'cartesvendues2017' => json_encode($cartesvendues2017),
        'cartesCadeauxTableau' => $cartesCadeauxTableau,
        'sitesTableau' => $sitesTableau,

    ]);
    }

    /**
     * @Route("/DGdashboard/planCommunication", name="planCommunication")
     */
    public function planCommunication(): Response
    {
    return $this->render('dg/planCommunication.html.twig', [
        'controller_name' => 'DgController',
    ]);
    }

    /**
     * @Route("/DGdashboard/pass", name="pass")
     */
    public function pass(): Response
    {
        $sitesRepo = $this->getDoctrine()->getRepository(CartesCadeaux::class);
        $sites = $sitesRepo ->findby(
            ['site' => $this->getUser()],
            ['site' => 'ASC'],
        );  
        
    return $this->render('dg/pass.html.twig', [
        'sites' => $sites,
    ]);
    }

    /**
     * @Route("/DGdashboard/frequentation&CA", name="frequentation")
     */
    public function freq(): Response
    {
    return $this->render('dg/frequentation.html.twig', [
        'controller_name' => 'DgController',
    ]);
    }

    /**
     * @Route("/DGdashboard/listeDesSites/{id}", name="singleSite", requirements={"id"="\d+"})
     */
    public function singleSite(int $id, SitesRepository $sitesRepo): Response
    {
        $sitesRepo = $this->getDoctrine()->getRepository(Sites::class);
        $sites = $sitesRepo->find($id);

        return $this->render('dg/singleSite.html.twig', [
            'sites' => $sites,
        ]);
    }

    /**
     * @Route("/DGdashboard/listeDesSites", name="listeDesSites")
     */
    public function sites(Request $request, SitesRepository $sitesRepo, UserRepository $userRepo): Response
    {

        //ROLE_DR
        $sitesRepo = $this->getDoctrine()->getRepository(Sites::class);
        $sites = $sitesRepo -> findby(
            ['user' => $this->getUser()],
            ['ville' => 'ASC'],
        );  

        //ROLE_DG
        $sitesRepo = $this->getDoctrine()->getRepository(Sites::class);
        $sites = $sitesRepo -> findby(
            ['user' => $this->getUser()],
        );  
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $sitesDG = $userRepo -> findby(
            ['prenom_nom' => $this->getUser()],
        );  

        //ROLE_DS
        $sitesRepo = $this->getDoctrine()->getRepository(Sites::class);
        $sitesDS = $sitesRepo -> findOneBy(
            ['user' => $this->getUser()]);


        // (console.log) sur $filters à faire
        $filters = $request->get("sites");
        // On vérifie si on a une requête AJAX
            if ($request->get('ajax')){
        // render des données en Json
                return new JsonResponse([
                    $this->renderView('dg/listeDesSites.html.twig', [
                    'sites' => $sites,
                    ])
                ]);
            }

    return $this->render('dg/listeDesSites.html.twig', [
        'sites' => $sites,
        'sitesDG' => $sitesDG,
        'sitesDS' => $sitesDS,
    ]);
    }

    /**
     * @Route("/DGdashboard/coutCommunication", name="coutCommunication")
     */
    public function coutCommunication(): Response
    {
    return $this->render('dg/coutCommunication.html.twig', [
        'controller_name' => 'DgController',
    ]);
    }

    //
    // ********************** FORMULAIRE : ADD ELEMENTS **********************
    //

    /**
     * @Route("/DGdashboard/nouveauSite", name="nouveauSite")
     */
    public function newSite(Request $request): Response
    {
        $site = new Sites();
        $form = $this->createForm(SiteType::class, $site);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($site);
            $entityManager->flush();
            $this->addFlash(
                "success",
                "Votre projet a bien été ouvert, vous n'avez plus qu'à mettre vos premières tâches afin d'atteindre vos objectifs ! :)"
            );
            return $this->redirectToRoute('nouveauSite');
        }

        return $this->render('registration/newSite.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
     * @Route("/DGdashboard/nouveauSwot", name="nouveauSwot")
     */
    public function newSwot(Request $request): Response
    {
        $swot = new Swot();
        $form = $this->createForm(SwotType::class, $swot);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $swot->setUser($this->getUser());
            $swot->setActive(1);
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($swot);
                $entityManager->flush();
                $this->addFlash(
                    "success",
                    "Le SWOT de votre site a bien été créée, vous n'avez plus qu'à rentrer les forces et faiblesses de ce dernier ! :)"
                );
                return $this->redirectToRoute('nouveauSwot');
                }
            catch (\Exception $php_errormsg) {
                $this->addFlash(
                    'danger',
                    "Une erreur est survenue, votre Swot n'a pas été enregistré"
                  );
            }
        }
        return $this->render('registration/newSwot.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
     * @Route("/DGdashboard/listeDesSites/{id}/newCartesCadeaux", name="newCartesCadeaux", requirements={"id"="\d+"})
     */
    public function newCartesCadeaux(Request $request, SitesRepository $sitesRepo, int $id): Response
    {
        $cadeaux = new CartesCadeaux();
        $form = $this->createForm(CartesCadeauxFormType::class, $cadeaux);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sites = $sitesRepo->find($id);
            $cadeaux->setSite($sites);
            $cadeaux->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cadeaux);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Vos chiffres concernant vos Cartes Cadeaux ont été bien enregistrés sur le site correspondant :)"
            );

            return $this->redirectToRoute('newCartesCadeaux', ['id' => $cadeaux->getSite()->getId()]);
        }

        return $this->render('registration/newCartesCadeaux.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
    * @Route("/DGdashboard/listeDesSites/{id}/newPlanCommunication", name="newPlanCommunication", requirements={"id"="\d+"})
    */
    public function newPlanCommunication(Request $request, SitesRepository $sitesRepo, int $id): Response
    {
        $planCom = new PlanCommunication();
        $form = $this->createForm(PlanCommunicationFormType::class, $planCom);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sites = $sitesRepo->find($id);
            $planCom->setSite($sites);
            $planCom->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($planCom);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Votre Plan de communication a bien été associé à votre site ! :)"
            );

            return $this->redirectToRoute('newPlanCommunication', ['id' => $planCom->getSite()->getId()]);
        }

        return $this->render('registration/newPlanCommunication.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
    * @Route("/DGdashboard/listeDesSites/{id}/newSatisfaction", name="newSatisfaction", requirements={"id"="\d+"})
    */
    public function newSatisfaction(Request $request, SitesRepository $sitesRepo, int $id): Response
    {
        $satisfaction = new Satisfaction();
        $form = $this->createForm(SatisfactionType::class, $satisfaction);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sites = $sitesRepo->find($id);
            $satisfaction->setSite($sites);
            $satisfaction->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($satisfaction);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Votre enquête de satisfaction pour le mois a bien été ajoutée votre site, félicitations ! :)"
            );

            return $this->redirectToRoute('newSatisfaction', ['id' => $satisfaction->getSite()->getId()]);
        }

        return $this->render('registration/newSatisfaction.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
    * @Route("/DGdashboard/listeDesSites/{id}/newRig", name="newRig", requirements={"id"="\d+"})
    */
    public function newRig(Request $request, SitesRepository $sitesRepo, int $id): Response
    {
        $rig = new Rig();
        $form = $this->createForm(RigFormType::class, $rig);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sites = $sitesRepo->find($id);
            $rig->setSite($sites);
            $rig->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rig);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Votre C.A/Fréquentation a bien été enregistrée pour votre site ! :)"
            );

            return $this->redirectToRoute('newRig', ['id' => $rig->getSite()->getId()]);
        }

        return $this->render('registration/newSatisfaction.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
    * @Route("/DGdashboard/listeDesSites/{id}/newPass", name="newPass", requirements={"id"="\d+"})
    */
    public function newPass(Request $request, PassRepository $passRepo, int $id): Response
    {
        $pass = new Pass();
        $form = $this->createForm(PassFormType::class, $pass);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rig = $passRepo->find($id);
            $rig->setSite($rig);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rig);
            $entityManager->flush();

            $this->addFlash(
                "success",
                "Votre projet a bien été ajoutée votre site, vous n'avez plus qu'à mettre vos premières tâches afin d'atteindre vos objectifs ! :)"
            );

            return $this->redirectToRoute('Rig');
        }

        return $this->render('registration/newSatisfaction.html.twig', [
            'form' => $form->createView()
         ]);
    }


    //
    // ********************** FORMULAIRE : EDIT ELEMENTS **********************
    //

    /**
     * @Route("/DGdashboard/swot/{id}/editSwot", name="editSwot")
     */
    public function editSwot(Swot $swot, Request $request) : Response {
        $form = $this->createForm(SwotType::class, $swot);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($swot);
        $entityManager->flush();

        return $this->render('edit/editSwot.html.twig', [
            'form' => $form->createView()
         ]);

    }

    /**
     * @Route("/DGdashboard/listeDesSites/{id}/editSites", name="editSites")
     */
    public function editSites(Sites $sites, Request $request) : Response {
        $form = $this->createForm(SiteType::class, $sites);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($sites);
        $entityManager->flush();

        return $this->render('edit/editSites.html.twig', [
            'form' => $form->createView()
         ]);

    }

    /**
     * @Route("/DGdashboard/Satisfaction/{id}/editSastisfaction", name="editSatisfaction")
     */
    public function editSatisfaction(Satisfaction $satisfaction, Request $request) : Response {
        $form = $this->createForm(SatisfactionType::class, $satisfaction);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($satisfaction);
        $entityManager->flush();

        return $this->render('edit/editSatisfaction.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
     * @Route("/DGdashboard/CartesCadeaux/{id}/editCartesCadeaux", name="editCartesCadeaux")
     */
    public function editCartesCadeaux(CartesCadeaux $cartes, Request $request) : Response {
        $form = $this->createForm(CartesCadeauxFormType::class, $cartes);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cartes);
        $entityManager->flush();

        return $this->render('edit/editCartesCadeaux.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
     * @Route("/DGdashboard/Pass/{id}/editPass", name="editPass")
     */
    public function editPlanCommunication(PlanCommunication $plancom, Request $request) : Response {
        $form = $this->createForm(PlanCommunicationFormType::class, $plancom);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($plancom);
        $entityManager->flush();

        return $this->render('edit/editPlanCommunication.html.twig', [
            'form' => $form->createView()
         ]);
    }

        /**
     * @Route("/DGdashboard/Pass/{id}/editPass", name="editPass")
     */
    public function editPass(Pass $pass, Request $request) : Response {
        $form = $this->createForm(PlanCommunicationFormType::class, $pass);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($pass);
        $entityManager->flush();

        return $this->render('edit/editPlanCommunication.html.twig', [
            'form' => $form->createView()
         ]);
    }

    /**
     * @Route("/DGdashboard/Rig/{id}/editRig", name="editRig")
     */
    public function editRig(Rig $rig, Request $request) : Response {
        $form = $this->createForm(RigFormType::class, $rig);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($rig);
        $entityManager->flush();

        return $this->render('edit/editRig.html.twig', [
            'form' => $form->createView()
         ]);
    }


    //
    // ********************** FORMULAIRE : DELETE ELEMENTS **********************
    //

    /** 
    * @Route("/DGdashboard/swot/{id}/supprimerSwot", name="supprimerSwot")
    */
    function deleteSwot(Swot $swot): Response {
       $entityManager = $this->getDoctrine()->getManager();
       $entityManager -> remove($swot);
        $entityManager->flush();

        return $this->redirectToRoute('swot');
    }

    /** 
    * @Route("/DGdashboard//{id}/supprimerCarteCadeau", name="supprimerCarteCadeau")
    */
    function deleteCartesCadeaux(Swot $swot): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($swot);
         $entityManager->flush();
 
         return $this->redirectToRoute('DGdashboard');
     }

    /** 
    * @Route("/DGdashboard//{id}/deleteSites", name="deleteSites")
    */
    function deleteSites(Sites $sites): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($sites);
        $entityManager->flush();
 
        return $this->redirectToRoute('listeDesSites');
     }

    /** 
    * @Route("/DGdashboard//{id}/deleteSatisfaction", name="deleteSatisfaction")
    */
    function deleteSatisfaction(Satisfaction $satisfaction): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($satisfaction);
        $entityManager->flush();
 
        return $this->redirectToRoute('listeDesSites');
     }

    /** 
    * @Route("/DGdashboard//{id}/deletePass", name="deletePass")
    */
    function deletePass(Pass $pass): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($pass);
        $entityManager->flush();
 
        return $this->redirectToRoute('listeDesSites');
     }

    /** 
    * @Route("/DGdashboard//{id}/deleteRig", name="deleteRig")
    */
    function deleteRig(Rig $rig): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($rig);
        $entityManager->flush();
 
        return $this->redirectToRoute('listeDesSites');
     }

    /** 
    * @Route("/DGdashboard//{id}/deletePlanCommunication", name="deletePlanCommunication")
    */
    function deletePlanCommunication(PlanCommunication $plancom): Response {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager -> remove($plancom);
        $entityManager->flush();
 
        return $this->redirectToRoute('listeDesSites');
     }

}



