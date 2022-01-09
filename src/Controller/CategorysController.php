<?php

namespace App\Controller;
//namespace App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use App\Entity\Category ;
use Doctrine\ORM\EntityManagerInterface ;

class CategorysController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
       ->getRepository(Category::class)
       ->findAll();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $categories,
        ]);
    }

    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirect('/' . $category->getId());

        }

        return $this->render(
            'category/create.html.twig',
            array('form' => $form->createView())
        );

    }

    public function viewCategory(Request $request, $id)
    {
        $categories = $this->getDoctrine()
            ->getRepository('App\Entity\Category')
            ->find($id);

        if (!$categories) {
            throw $this->createNotFoundException(
                'There are no  with the following id: ' . $id
            );
        }

        $form = $this->createForm(CategoryType::class, $categories);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $categories = $form->getData();
            $categories->flush();
            return $this->redirect('/' . $id);
        }

        return $this->render(
            'category/view.html.twig',
            array('form' => $form->createView())
        );
    }

    public function deleteCategory($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('App\Entity\Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'There are no articles with the following id: ' . $id
            );
        }

        $em->remove($category);
        $em->flush();

        return $this->redirect('/');
    }

}
