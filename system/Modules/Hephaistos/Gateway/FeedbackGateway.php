<?php

namespace Asylamba\Modules\Hephaistos\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

use Asylamba\Modules\Hephaistos\Model\Feedback;
use Asylamba\Modules\Hephaistos\Model\Evolution;
use Asylamba\Modules\Hephaistos\Model\Bug;

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
     * @param Evolution $evolution
     * @return Response
     */
    public function updateEvolution(Evolution $evolution)
    {
        return $this->client->put('/evolutions/' . $evolution->getId(), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $evolution->getTitle(),
                'description' => $evolution->getDescription(),
                'status' => $evolution->getStatus(),
                'author' => [
                    'name' => $evolution->getAuthor()->getName(),
                    'email' => $evolution->getAuthor()->getBind()
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
     * @param Bug $bug
     * @return Response
     */
    public function updateBug(Bug $bug)
    {
        return $this->client->put('/bugs/' . $bug->getId(), [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'title' => $bug->getTitle(),
                'description' => $bug->getDescription(),
                'status' => $bug->getStatus(),
                'author' => [
                    'name' => $bug->getAuthor()->getName(),
                    'email' => $bug->getAuthor()->getBind()
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
    
    public function createCommentary($feedbackId, $feedbackType, $content, $authorName, $authorEmail)
    {
        $endpoint = ($feedbackType === Feedback::TYPE_BUG) ? 'bugs' : 'evolutions';
        return $this->client->post("/$endpoint/$feedbackId/comments", [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'content' => $content,
                'author' => [
                    'name' => $authorName,
                    'email' => $authorEmail
                ],
            ])
        ]);
    }
}