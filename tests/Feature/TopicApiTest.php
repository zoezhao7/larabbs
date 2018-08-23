<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Topic;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\ActingJWTUser;

class TopicApiTest extends TestCase
{
    use ActingJWTUser;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testStoreTopic()
    {
        $data = ['category_id' => 1, 'body' => 'test body', 'title' => 'test title'];

        $response = $this->JWTActingAs($this->user)
            ->json('POST', '/api/topics', $data);

        $assertData = [
            'category_id' => 1,
            'user_id' => $this->user->id,
            'title' => 'test title',
            'body' => clean('test body', 'user_topic_body'),
        ];

        $response->assertStatus(201)
            ->assertJsonFragment($assertData);
    }

    public function testUpdateTopic()
    {
        $topic = $this->makeTopic();

        $data = ['title' => 'update title', 'body' => 'update body', 'category_id' => '2'];

        $response = $this->JWTActingAs($this->user)
            ->json('PATCH', '/api/topics/' . $topic->id, $data);

        $assertData = [
            'id' => $topic->id,
            'user_id' => $this->user->id,
            'title' => 'update title',
            'body' => clean('update body', 'user_topic_body'),
            'category_id' => '2',
        ];

        $response->assertStatus(200)->assertJsonFragment($assertData);
    }

    public function testShowTopic()
    {
        $topic = $this->makeTopic();
        $response = $this->json('GET', '/api/topics/'.$topic->id);

        $assertData= [
            'category_id' => $topic->category_id,
            'user_id' => $topic->user_id,
            'title' => $topic->title,
            'body' => $topic->body,
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($assertData);
    }


    public function testIndexTopic()
    {
        $response = $this->json('GET', '/api/topics');

        $assertData = ['data', 'meta'];

        $response->assertStatus(200)->assertJsonStructure($assertData);
    }

    public function testDeleteTopic()
    {
        $topic = $this->makeTopic();

        $response = $this->JWTActingAs($this->user)->json('DELETE', '/api/topics/' . $topic->id);
        $response->assertStatus(204);

        $response = $this->JWTActingAs($this->user)->json('DELETE', '/api/topics/' . $topic->id);
        $response->assertStatus(404);
    }

    public function makeTopic()
    {
        return factory(Topic::class)->create([
            'category_id' => 1,
            'user_id' => $this->user->id,
        ]);
    }

}
