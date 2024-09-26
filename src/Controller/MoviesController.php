<?php

namespace App\Controller;

use App\Service\MovieApiService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class MoviesController extends AbstractController
{
    public function __construct(
        private MovieApiService $movieApiService,
    ){}

    /**
    * @Route("/", name="movie_list")
    */
    public function index(Request $request): Response
    {
        $genres = $this->movieApiService->getMovieGenres();
        $bestMovieId = $this->movieApiService->getBestMovieId();
        $bestMovie = $this->movieApiService->getVideoMovie($bestMovieId);

        $genreId = $request->query->get('with_genres');
        $searchQuery = $request->query->get('search');

        // Récupérer les films selon le filtre ou la recherche
        if ($genreId) {
            $listMovies = $this->movieApiService->getListMovies($genreId);
        } elseif ($searchQuery) {
            $listMovies = $this->movieApiService->getListMoviesByKeyWord($searchQuery);
        } else {
            $listMovies = $this->movieApiService->getListMovies();
        }

        return $this->render('movie/index.html.twig', compact('genres', 'bestMovie', 'listMovies'));
    }

    /**
     * @Route("/movies/{id}/details", name="movie_details")
     */
    public function getMovieDetails($id): JsonResponse
    {
        $videoMovie = $this->movieApiService->getVideoMovie($id);
        $detailsMovie = $this->movieApiService->getDetailMovie($id);

        return new JsonResponse(compact('videoMovie', 'detailsMovie'));
    }

    /**
     * @Route("/movies", name="api_movies", methods={"GET"})
     */
    public function getMoviesBySearch(Request $request): JsonResponse
    {
        $searchQuery = $request->query->get('search', '');
        
        $movies = $this->movieApiService->getListMoviesByKeyWord($searchQuery);

        $movieTitles = array_map(function($movie) {
            return $movie['title'];
        }, $movies);

        return new JsonResponse(['movies' => $movieTitles]);
    }
}
