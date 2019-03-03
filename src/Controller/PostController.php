<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {

        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('title', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('text', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Добавить пост'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedAt(new \DateTime());
            $post->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirect("/");
        }
        return $this->render("post/create.html.twig", [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="all_posts")
     */
    public function all()
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);

        $posts = $repository->findAll();

        return $this->render("post/all.html.twig", [
            "posts" => $posts,
        ]);
    }

    /**
     * @Route("/post/{id}", name="post", requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post($id)
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);

        $post = $repository->find($id);

        return $this->render("post/post.html.twig", [
            'post' => $post,
        ]);

    }
}
