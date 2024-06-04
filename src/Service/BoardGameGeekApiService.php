<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class BoardGameGeekApiService
{
    private $client;
    private $params;
    private $logger;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->params = $params;
        $this->logger = $logger;

    }

    public function getGames(): array
    {
        $url = 'https://api.geekdo.com/xmlapi2/hot?type=boardgame';
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch data from BGG API');
        }

        $content = $response->getContent();
        $xml = simplexml_load_string($content);

        $games = [];
        foreach ($xml->item as $item) {
            $game = [];
            $game['id'] = (string) $item['id'];
            $game['name'] = (string) $item->name['value'];
            $game['thumbnail'] = (string) $item->thumbnail['value'];
            $game['yearpublished'] = (string) $item->yearpublished['value'];
            $games[] = $game;

        }

        return $games;
    }

    public function getGameDetailsById(string $gameId): array
    {
        $url = 'https://api.geekdo.com/xmlapi2/thing?id=' . $gameId;
        $this->logger->info('Fetching game details for ID ' . $gameId . ' from BGG API: ' . $url);

        try {
            $response = $this->client->request('GET', $url);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception('Failed to fetch data from BGG API. Status code: ' . $response->getStatusCode());
            }

            $content = $response->getContent();
            $xml = simplexml_load_string($content);

            $gameDetails = [
                'id' => (string) $xml->item['id'],
                'name' => (string) $xml->item->name['value'],
                'thumbnail' => (string) $xml->item->thumbnail,
                'yearPublished' => (string) $xml->item->yearpublished['value'],
                'description' => (string) $xml->item->description,
                'minPlayers' => (string) $xml->item->minplayers['value'],
                'maxPlayers' => (string) $xml->item->maxplayers['value'],
                'playingTime' => (string) $xml->item->playingtime['value'],
                'minPlayTime' => (string) $xml->item->minplaytime['value'],
                'maxPlayTime' => (string) $xml->item->maxplaytime['value'],
                'minAge' => (string) $xml->item->minage['value'],
            ];

            return $gameDetails;

        } catch (\Exception $e) {
            $this->logger->error('Error fetching game details for ID ' . $gameId . ' from BGG API: ' . $e->getMessage());
            throw new \Exception('Failed to fetch data from BGG API');
        }
    }

    // show multiple games by id
    public function getGamesByIds(array $gameIds): array
    {
        $games = [];
        foreach ($gameIds as $gameId) {
            $game = $this->getGameDetailsById($gameId);
            $games[] = $game;
        }

        return $games;
    }
}
