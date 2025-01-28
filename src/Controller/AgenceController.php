<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/agence')]
class AgenceController extends AbstractController
{
    #[Route('/', name: 'app_agence_index', methods: ['GET'])]
    public function index(Request $request, AgenceRepository $agenceRepository, PaginatorInterface $paginator): Response
    {
        $searchForm = $this->createForm(AgenceType::class, null, [
            'method' => 'GET'
        ]);

        $searchForm->handleRequest($request);
        $queryBuilder = $agenceRepository->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC');

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            
            if ($data->getTelephone()) {
                $queryBuilder->andWhere('a.telephone LIKE :telephone')
                    ->setParameter('telephone', '%' . $data->getTelephone() . '%');
            }
            
            if ($data->getAdresse()) {
                $queryBuilder->andWhere('a.adresse LIKE :adresse')
                    ->setParameter('adresse', '%' . $data->getAdresse() . '%');
            }
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            2 // Limite à 2 éléments par page
        );

        return $this->render('agence/index.html.twig', [
            'agences' => $pagination,
            'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/new', name: 'app_agence_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $errors = [];
            
            if (empty($agence->getNumero())) {
                $errors['numero'] = 'Le numéro est obligatoire';
            } elseif ($entityManager->getRepository(Agence::class)->findOneBy(['numero' => $agence->getNumero()])) {
                $errors['numero'] = 'Ce numéro d\'agence existe déjà';
            }
            
            if (empty($agence->getAdresse())) {
                $errors['adresse'] = 'L\'adresse est obligatoire';
            }
            
            if (empty($agence->getTelephone())) {
                $errors['telephone'] = 'Le téléphone est obligatoire';
            } elseif (!preg_match('/^[0-9\s]*$/', $agence->getTelephone())) {
                $errors['telephone'] = 'Le numéro de téléphone doit contenir uniquement des chiffres';
            }

            if (empty($errors)) {
                $entityManager->persist($agence);
                $entityManager->flush();

                $this->addFlash('success', 'L\'agence a été créée avec succès');
                return $this->redirectToRoute('app_agence_index');
            }

            foreach ($errors as $field => $message) {
                $this->addFlash('error', $message);
            }
        }

        return $this->render('agence/new.html.twig', [
            'agence' => $agence,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'app_agence_show', methods: ['GET'])]
    #[ParamConverter('agence', class: Agence::class)]
    public function show(Agence $agence): Response
    {
        return $this->render('agence/show.html.twig', [
            'agence' => $agence
        ]);
    }

    #[Route('/{id}/delete', name: 'app_agence_delete_confirm', methods: ['GET'])]
    #[ParamConverter('agence', class: Agence::class)]
    public function deleteConfirm(Agence $agence): Response
    {
        return $this->render('agence/delete.html.twig', [
            'agence' => $agence
        ]);
    }

    #[Route('/{id}/delete', name: 'app_agence_delete', methods: ['POST'])]
    #[ParamConverter('agence', class: Agence::class)]
    public function delete(Request $request, Agence $agence, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$agence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($agence);
            $entityManager->flush();
            $this->addFlash('success', 'L\'agence a été supprimée avec succès');
        }

        return $this->redirectToRoute('app_agence_index');
    }
}
