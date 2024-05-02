<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Category; 
use App\Form\CategoryType;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     */
    public function home(EntityManagerInterface $entityManager)
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/save", methods={"POST"}, name="save_article")
     */
    public function save(EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $article->setNom('Article 2');
        $article->setPrix(1500);

        $entityManager->persist($article);
        $entityManager->flush();

        return $this->render('articles/save_success.html.twig', ['articleId' => $article->getId()]);
    }

    /**
     * @Route("/article/new", name="new_article", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();


            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
    }
    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id, EntityManagerInterface $entityManager)
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        return $this->render(
            'articles/show.html.twig',
            array('article' => $article)
        );
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $article = $entityManager->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, array(
                'label' => 'Modifier'
            ))->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/delete/{id}",name="article_delete")
     * @Method({"DELETE","GET"})
     * */
    public function delete(Request $request, $id,EntityManagerInterface $entityManager)
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
    

        $entityManager->remove($article);
        $entityManager->flush();

        $response = new Response();
        $response->send();
        return $this->redirectToRoute('homepage');
    }
    /** 
     * @Route("/category/newCat", name="new_category") 
     * Method({"GET", "POST"}) 
     */ 
     public function newCategory(Request $request, EntityManagerInterface $entityManager)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // No need to retrieve data from the form, $category already holds the submitted data
            $entityManager->persist($category);
            $entityManager->flush();
            
            // Redirect to a different route after successful form submission
            return $this->redirectToRoute('homepage');
        }

        return $this->render('articles/newCategory.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
