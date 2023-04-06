<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
Use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;

class IndexController extends AbstractController
{
    #[Route('/', name: 'article_list')]
    

    public function home(Request $request ,ManagerRegistry $doctrine): Response
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
        
        // Initially, the array of articles is empty, 
        // i.e., we display the articles only when the user clicks on the search button.
        $articles = [];
        
        if ($form->isSubmitted() && $form->isValid()) {
            // We retrieve the name of the article typed in the form
            $nom = $propertySearch->getNom();
            if ($nom !== '') {
                // If a name is provided, we display all the articles having that name
                $articles = $doctrine->getRepository(Article::class)->findBy(['Nom' => $nom]);
            } else {
                // If no name is provided, we display all the articles
                $articles = $doctrine->getRepository(Article::class)->findAll();
            }
        }
    
        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
        
#[Route('/article/save')]
public function save(ManagerRegistry $doctrine): Response {
$entityManager = $doctrine->getManager();
$article = new Article();
$article->setNom('Article 3');
$article->setPrix(3000);
$entityManager->persist($article);
$entityManager->flush();
return new Response('Article enregisté avec id '.$article->getId());
}
#[Route('/article/new', name:'new_article')]

public function new(Request $request, ManagerRegistry $doctrine) {
    $article = new Article();
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $article = $form->getData();
    $entityManager = $doctrine->getManager();
    $entityManager->persist($article);
    $entityManager->flush();
    return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
    }
    
    #[Route('/article/{id}', name:'article_show')]
    public function show($id,ManagerRegistry $doctrine): Response {
    $article = $doctrine->getRepository(Article::class)->find($id);
    return $this->render('articles/show.html.twig', array('article' =>
    $article));
    }
 #[Route('/article/edit/{id}', name: 'edit_article')]

public function edit(Request $request, $id, ManagerRegistry $doctrine)
{
$article = new Article();
$article = $doctrine->getRepository(Article::class)->find($id);
$form = $this->createForm(ArticleType::class,$article);
$form->handleRequest($request);
if($form->isSubmitted() && $form->isValid()) {
$entityManager = $doctrine->getManager();
$entityManager->flush();
return $this->redirectToRoute('article_list');
}
return $this->render('articles/edit.html.twig', ['form' =>
$form->createView()]);
}
 
#[Route('/article/delete/{id}', name: 'delete_article')]
public function delete(Request $request, $id, ManagerRegistry
$doctrine)
{
$article = $doctrine->getRepository(Article::class)->find($id);
$entityManager = $doctrine->getManager();
$entityManager->remove($article);
$entityManager->flush();
$response = new Response();
$response->send();
return $this->redirectToRoute('article_list');
}

#[Route('/category/newCat', name: 'new_category')]
public function newCategory(Request $request, ManagerRegistry $doctrine)
{
$category = new Category();
$form = $this->createForm(CategoryType::class, $category);
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
$article = $form->getData();
$entityManager = $doctrine->getManager();
$entityManager->persist($category);
$entityManager->flush();
}
return $this->render('articles/newCategory.html.twig', ['form' =>
$form->createView()]);
}

#[Route('/art_cat/', name: 'article_par_cat')]
public function articlesParCategorie(Request $request, ManagerRegistry $doctrine) {
$categorySearch = new CategorySearch();
$form = $this->createForm(CategorySearchType::class,$categorySearch);
$form->handleRequest($request);
$articles= [];
if ($form->isSubmitted() && $form->isValid()){
     $category = $categorySearch->getCategory();
    if ($category!="")
    $articles= $category->getArticles();
    else
    $articles= $doctrine->getRepository(Article::class)->findAll();
    }
    return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
    }

    #[Route('/art_prix/', name: 'article_par_prix')]
    public function articlesParPrix(Request $request, EntityManagerInterface $entityManager)
    {
    $priceSearch = new PriceSearch();
    $form = $this->createForm(PriceSearchType::class,$priceSearch);
    $form->handleRequest($request);
    $articles= [];
    if($form->isSubmitted() && $form->isValid()) {
    $minPrice = $priceSearch->getMinPrice();
    $maxPrice = $priceSearch->getMaxPrice();
    $articles = $entityManager->getRepository(Article::class)->findByPriceRange($minPrice, $maxPrice);
    }
    return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
    }
}
?>
