<?php

namespace DelPlop\PbnBundle\Controller;

use DelPlop\PbnBundle\Entity\Category;
use DelPlop\PbnBundle\Entity\Note;
use DelPlop\PbnBundle\Form\NoteFormType;
use DelPlop\PbnBundle\Form\NoteSearchFormType;
use DelPlop\PbnBundle\Repository\CategoryRepository;
use DelPlop\PbnBundle\Repository\NoteCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DelPlop\PbnBundle\Repository\NoteRepository;
use Twig\Environment;

class NoteController extends AbstractController
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
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(TranslatorInterface $translator,
                                Security $security,
                                Environment $twig,
                                NoteRepository $noteRepository
    ) {
        $this->translator = $translator;
        $this->twig = $twig;
        $this->security = $security;
        $this->noteRepository = $noteRepository;
    }

    // liste des notes d'une catégorie
    public function index(Category $category): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'categoryName' => $category->getName(),
            'categoryId' => $category->getId(),
            'notes' => $this->noteRepository->getNotesByCategoryAndUser($category, $this->security->getUser()),
            'activePage' => 'notes',
            'type' => 'all'
        ]));
    }

    // liste des notes importantes
    public function importantNotes(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'notes' => $this->noteRepository->findImportantNotes($this->security->getUser()),
            'activePage' => 'notes-important',
            'categoryName' => $this->translator->trans('notes.title_page', ['%state%' => $this->translator->trans('notes.important_lower', [], 'notes')], 'notes'),
            'type' => 'important'
        ]));
    }

    // liste des notes avec action
    public function actionNeededNotes(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'notes' => $this->noteRepository->findNotesWithActionNeeded($this->security->getUser()),
            'activePage' => 'notes-action-needed',
            'categoryName' => $this->translator->trans('notes.title_page', ['%state%' => $this->translator->trans('notes.action_needed_lower', [], 'notes')], 'notes'),
            'type' => 'action-needed'
        ]));
    }

    // liste des notes publiques
    public function publicNotes(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'notes' => $this->noteRepository->findBy(['visibility' => 'public']),
            'activePage' => 'notes-pub',
            'categoryName' => $this->translator->trans('notes.title_page', ['%state%' => $this->translator->trans('notes.public_lower', [], 'notes')], 'notes'),
            'type' => 'public'
        ]));
    }

    // liste des notes partagées
    public function sharedNotes(): Response
    {
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'notes' => $this->noteRepository->findBy(['visibility' => ['public', 'shared']]),
            'activePage' => 'notes-shared',
            'categoryName' => $this->translator->trans('notes.title_page', ['%state%' => $this->translator->trans('notes.shared_lower', [], 'notes')], 'notes'),
            'type' => 'shared'
        ]));
    }

    // formulaire de recherche du header
    public function searchForm(): Response
    {
        $form = $this->createForm(NoteSearchFormType::class, [], [
            'action' => $this->generateUrl('notes_search'),
            'attr' => [
                'id' => 'search_notes'
            ]
        ]);

        return new Response($this->twig->render('@DelPlopPbn/note/search-form.html.twig', [
            'form' => $form->createView()
        ]));
    }

    // résultats de recherche
    public function search(Request $request): Response
    {
        $notes = [];

        $form = $this->createForm(NoteSearchFormType::class, [], [
            'action' => $this->generateUrl('notes_search'),
            'attr' => [
                'id' => 'search_notes',
                'class' => 'w3-hide-small w3-hide-medium'
            ]
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $notes = $this->noteRepository->searchNotes($this->security->getUser(), $data['search']);
        }
        return new Response($this->twig->render('@DelPlopPbn/note/index.html.twig', [
            'notes' => $notes,
            'activePage' => 'home',
            'categoryName' => $this->translator->trans('notes.search', [], 'notes'),
            'type' => 'search'
        ]));
    }

    // ajouter ou modifier une note
    public function edit(Request $request, ?Note $note, ?Category $category): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->security->getUser() instanceof UserInterface) {
            $categoryId = 0;
            $label = $this->translator->trans('form.edit', [], 'messages');
            $type = 'edit';
            $page = 'home';

            if (empty($note)) {
                $label = $this->translator->trans('form.add', [], 'messages');
                $type = 'add';
                $page = 'notes-new';

                $note = new Note();
                $note->setIsImportant(false)
                    ->setNeedsAction(false);
            }

            $form = $this->createForm(NoteFormType::class, $note, [
                'submitLabel' => $label,
                'category' => $category
            ]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $note = $form->getData();

                if (empty($note->getContent())) {
                    $note->setContent('');
                }

                if ($type == 'edit' && !$this->security->getUser()->getNotes()->contains($note)) {
                    return $this->redirectToRoute('index');
                }

                if ($type == 'add') {
                    $note->setUser($this->security->getUser());
                }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($note);
                $entityManager->flush();

                // si catégorie spécifiée
                if (!empty($category)) {
                    $categoryId = $category->getId();
                } elseif ($type == 'add') {
                    $categoryId = $note->getCategories()[0]->getId();
                } else {
                    $categoryId = $note->getNoteCategories()[0]->getCategory()->getId();
                }

                return $this->redirectToRoute('category_notes', ['category' => $categoryId]);
            }

            return new Response($this->twig->render('@DelPlopPbn/note/edit.html.twig', [
                'form' => $form->createView(),
                'activePage' => $page,
                'type' => $type,
                'noteId' => $note->getId() ?: 0
            ]));
        } else {
            return $this->redirectToRoute('login');
        }
    }

    // ordonner les notes
    public function sort(Category $category)
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->security->getUser() instanceof UserInterface) {
            return new Response($this->twig->render('@DelPlopPbn/note/sort.html.twig', [
                'notes' => $this->noteRepository->getNotesByCategoryAndUser($category, $this->security->getUser()),
                'activePage' => 'home',
                'category' => $category
            ]));
        } else {
            return $this->redirectToRoute('login');
        }
    }

    // sauver l'ordre des notes
    public function saveSort(Request $request, NoteCategoryRepository $noteCategoryRepository, CategoryRepository $categoryRepository): Response
    {
        $data = \json_decode($request->getContent(), true);
        $i = 1;

        if (!empty($data['order']) && !empty($data['catId'])) {
            $category = $categoryRepository->find($data['catId']);

            $order = $data['order'];
            foreach($order as $noteId) {
                $position = $i * 10;
                $note = $this->noteRepository->find($noteId);

                $noteCategory = $noteCategoryRepository->updatePositionByNoteAndCategory($note, $category, $position);

                $i ++;
            }

        }

        return new Response();
    }

    // modifier une note en ajax
    public function save(Request $request, CategoryRepository $categoryRepository, ?Note $note): Response
    {
        $content = json_decode($request->getContent());

        if ($note instanceof Note && !empty($content->field) && isset($content->fieldValue)) {
            switch ($content->field) {
                case 'title':
                    $note->setTitle($content->fieldValue);
                    break;

                case 'content':
                    $note->setContent($content->fieldValue);
                    break;

                case 'visibility':
                    $note->setVisibility($content->fieldValue);
                    break;

                case 'important':
                    $note->setIsImportant($content->fieldValue);
                    break;

                case 'todo':
                    $note->setNeedsAction($content->fieldValue);
                    break;

                case 'category':
                    if ($content->action == 'delete') {
                        $note->removeCategory($categoryRepository->find($content->fieldValue));
                    } elseif ($content->action == 'add') {
                        $note->addCategory($categoryRepository->find($content->fieldValue));
                    }
                    break;
            }
        }

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && $this->security->getUser() instanceof UserInterface
            && $this->security->getUser()->getNotes()->contains($note)
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();
        }

        return new Response();
    }

    // supprimer une note
    public function delete(Note $note): Response
    {
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
            && $this->security->getUser() instanceof UserInterface
            && $this->security->getUser()->getNotes()->contains($note)
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return new Response();
    }
}
