<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MovieApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private ParameterBagInterface $params
    ){}

    public function getMovieGenres(): array
    {
        $params = [
            'language' => 'fr'
        ];

       $response = $this->client->request('GET', $this->params->get('API_URL') . '/genre/movie/list', $this->getHeaders($params));

        $content = $response->toArray();
        return $content['genres'] ?? [];
    }

    public function getBestMovieId(): int|array
    {
        $params = [
            'language' => 'en-US',
            'page'  => 1
        ];

        $response = $this->client->request('GET', $this->params->get('API_URL') . '/movie/top_rated', $this->getHeaders($params));

        $content = $response->toArray();
        return $content['results'][0]['id'] ?? [];
    }

    public function getVideoMovie(int $movieId): array
    {
        $url = sprintf('%s/movie/%d/videos', $this->params->get('API_URL'), $movieId);

        $response = $this->client->request('GET', $url , $this->getHeaders());
        $content = $response->toArray();

        return $content['results'][0] ?? [];
    }

    public function getListMovies(int|string $query = null): array
    {
        $params = [
            'include_adult' => false,
            'language' => 'en-US',
            'sort_by' => 'vote_count.desc',
            'include_video' => true,
            'page'  => 1
        ];

        if ($query) {
            $params['with_genres'] = $query;
        }
        
        $response = $this->client->request('GET', $this->params->get('API_URL') . '/discover/movie', $this->getHeaders($params));
        $content = $response->toArray();
        
        return $content['results'] ?? [];
    }

        public function getListMoviesByKeyWord(string $query): array
        {
            $params = [
                'include_adult' => false,
                'language' => 'en-US',
                'page'  => 1,
                'query' => $query
            ];
         
            $response = $this->client->request('GET', $this->params->get('API_URL') . '/search/movie', $this->getHeaders($params));
            $content = $response->toArray();
            
            return $content['results'] ?? [];
        }

    public function getDetailMovie(int $movieId): array
    {
        $url = sprintf('%s/movie/%d', $this->params->get('API_URL'), $movieId);
        $response = $this->client->request('GET', $url, $this->getHeaders());
        $content = $response->toArray();
        $detailsMovie = [
            'vote_average' => $content['vote_average'],
            'vote_count' =>  $content['vote_count'],
            'tagline' => $content['tagline'],
        ];
        return $detailsMovie ?? [];
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
