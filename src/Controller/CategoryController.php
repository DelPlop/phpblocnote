<?php

namespace DelPlop\PbnBundle\Controller;

use DelPlop\PbnBundle\Entity\Category;
use DelPlop\PbnBundle\Form\CategoryFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DelPlop\PbnBundle\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class CategoryController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        TranslatorInterface $translator,
        Security $security,
        Environment $twig,
        CategoryRepository $categoryRepository
    ) {
        $this->translator = $translator;
        $this->twig = $twig;
        $this->security = $security;
        $this->categoryRepository = $categoryRepository;
    }

    // liste des catégories
    public function index(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/category/index.html.twig', [
            'categories' => $this->categoryRepository->findBy(['user' => $this->security->getUser()], ['position' => 'asc']),
            'activePage' => 'categories'
        ]));
    }

    // liste des catégories (menu de gauche)
    public function menuList(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/category/menu-list.html.twig', [
            'categories' => $this->categoryRepository->findBy(['user' => $this->security->getUser()], ['position' => 'asc'])
        ]));
    }

    // ordonner les catégories
    public function sort(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/category/sort.html.twig', [
            'categories' => $this->categoryRepository->findBy(['user' => $this->security->getUser()], ['position' => 'asc']),
            'activePage' => 'categories'
        ]));
    }

    // sauver l'ordre des catégories
    public function saveSort(Request $request): Response
    {
        $order = \json_decode($request->getContent());
        $i = 1;

        if (!empty($order)) {
            $entityManager = $this->getDoctrine()->getManager();

            foreach($order as $categoryId) {
                $position = $i * 10;

                $category = $this->categoryRepository->findOneBy(['id' => $categoryId])
                    ->setPosition($position);
                $entityManager->persist($category);

                $i ++;
            }

            $entityManager->flush();
        }

        return new Response();
    }

    // ajouter ou modifier une catégorie
    public function edit(Request $request, ?Category $category): Response
    {
        $label = $this->translator->trans('form.edit', [], 'messages');
        $type = 'edit';
        $page = 'categories';

        if (empty($category)) {
            $label = $this->translator->trans('form.add', [], 'messages');
            $type = 'add';
            $page = 'new-cat';

            $category = new Category();
            $category->setPosition(0);
        }

        $form = $this->createForm(CategoryFormType::class, $category, [
            'submitLabel' => $label
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->security->getUser() instanceof UserInterface) {

                if ($type == 'edit' && !$this->security->getUser()->getCategories()->contains($category)) {
                    return $this->redirectToRoute('index');
                }

                if ($type == 'add') {
                    $category->setUser($this->security->getUser());
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($category);
                $entityManager->flush();
            }

            return $this->redirectToRoute('categories');
        }

        return new Response($this->twig->render('@DelPlopPbn/category/edit.html.twig', [
            'form' => $form->createView(),
            'activePage' => $page,
            'type' => $type
        ]));
    }

    // modifier une catégorie en ajax
    public function save(Request $request, ?Category $category): Response
    {
        $content = json_decode($request->getContent());

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && $this->security->getUser() instanceof UserInterface
            && $this->security->getUser()->getCategories()->contains($category)
            && !empty($content->fieldValue)
        ) {
            $category->setName($content->fieldValue);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
        }

        return new Response();
    }

    // supprimer une catégorie
    public function delete(Category $category): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && $this->security->getUser() instanceof UserInterface
            && $this->security->getUser()->getCategories()->contains($category)
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return new Response();
    }
}
