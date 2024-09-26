<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class MovieApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private ParameterBagInterface $params,
        private LoggerInterface $logger // Pour logger les erreurs
    ) {
    }

    public function getMovieGenres(): array
    {
        $params = [
            'language' => 'fr'
        ];

        try {
            $response = $this->client->request('GET', $this->params->get('API_URL') . '/genre/movie/list', $this->getHeaders($params));
            $content = $response->toArray();
            return $content['genres'] ?? [];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la récupération des genres de films : ' . $e->getMessage());
            return [];
        }
    }

    public function getBestMovieId(): int|array
    {
        $params = [
            'language' => 'en-US',
            'page' => 1
        ];

        try {
            $response = $this->client->request('GET', $this->params->get('API_URL') . '/movie/top_rated', $this->getHeaders($params));
            $content = $response->toArray();
            return $content['results'][0]['id'] ?? [];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la récupération du film top : ' . $e->getMessage());
            return [];
        }
    }

    public function getVideoMovie(int $movieId): array
    {
        $url = sprintf('%s/movie/%d/videos', $this->params->get('API_URL'), $movieId);

        try {
            $response = $this->client->request('GET', $url, $this->getHeaders());
            $content = $response->toArray();
            return $content['results'][0] ?? [];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la récupération des vidéos du film : ' . $e->getMessage());
            return [];
        }
    }

    public function getListMovies(int|string $query = null): array
    {
        $params = [
            'include_adult' => false,
            'language' => 'en-US',
            'sort_by' => 'vote_count.desc',
            'include_video' => true,
            'page' => 1
        ];

        if ($query) {
            $params['with_genres'] = $query;
        }

        try {
            $response = $this->client->request('GET', $this->params->get('API_URL') . '/discover/movie', $this->getHeaders($params));
            $content = $response->toArray();
            return $content['results'] ?? [];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la récupération des films : ' . $e->getMessage());
            return [];
        }
    }

    public function getListMoviesByKeyWord(string $query): array
    {
        $params = [
            'include_adult' => false,
            'language' => 'en-US',
            'page' => 1,
            'query' => $query
        ];

        try {
            $response = $this->client->request('GET', $this->params->get('API_URL') . '/search/movie', $this->getHeaders($params));
            $content = $response->toArray();
            return $content['results'] ?? [];
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la recherche de films : ' . $e->getMessage());
            return [];
        }
    }

    public function getDetailMovie(int $movieId): array
    {
        $url = sprintf('%s/movie/%d', $this->params->get('API_URL'), $movieId);

        try {
            $response = $this->client->request('GET', $url, $this->getHeaders());
            $content = $response->toArray();

            $detailsMovie = [
                'vote_average' => $content['vote_average'] ?? null,
                'vote_count' => $content['vote_count'] ?? null,
                'tagline' => $content['tagline'] ?? null,
            ];

            return $detailsMovie;
        } catch (TransportExceptionInterface | ClientExceptionInterface | ServerExceptionInterface $e) {
            $this->logger->error('Erreur lors de la récupération des détails du film : ' . $e->getMessage());
            return [];
        }
    }

    private function getHeaders(array $params = []): array
    {
        return [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->params->get('API_TOKEN'),
                'accept' => 'application/json',
            ],
            'query' => $params
        ];
    }
}
