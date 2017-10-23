<?php

namespace Asylamba\Modules\Hephaistos\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class FeedbackGateway
{
    /** @var Client **/
    protected $client;
    
    /**
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->client = new Client(['base_uri' => $apiUrl]);
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param string $status
     * @param string $authorName
     * @param string $authorEmail
     * @return Response
     */
    public function createEvolution($title, $description, $status, $authorName, $authorEmail)
    {
        return $this->client->post('/evolutions', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
    
    /**
     * @param string $id
     * @return Response
     */
    public function getEvolution($id)
    {
        return $this->client->get("/evolutions/$id");
    }
    
    /**
     * @return Response
     */
    public function getEvolutions()
    {
        return $this->client->get('/evolutions');
    }
    
    /**
     * @param string $title
     * @param string $description
     * @param string $status
     * @param string $authorName
     * @param string $authorEmail
     * @return Response
     */
    public function createBug($title, $description, $status, $authorName, $authorEmail)
    {
        return $this->client->post('/bugs', [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
    
    /**
     * @param string $id
     * @return Response
     */
    public function getBug($id)
    {
        return $this->client->get("/bugs/$id");
    }
    
    /**
     * @return Response
     */
    public function getBugs()
    {
        return $this->client->get('/bugs');
    }
}