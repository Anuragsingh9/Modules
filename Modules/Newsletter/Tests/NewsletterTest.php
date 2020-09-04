<?php

namespace Modules\Newsletter\Tests;

use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Newsletter\Entities\News;
use Tests\TestCase;


class NewsletterTest extends TestCase
{

    public function getUser() {
        $user = User::find(1); // find specific user
        $this->actingAs($user);
    }

    public function test_create(){
        $this->getUser();
        $data =  [
            'title'              => 'News Creating',
            'header'             => 'Heading for',
            'description'        => 'description',
            'status'             => 'validated',
            'created_by'         => 1,
            'media_type' => 2,
            'media_url'  => 'https://www.google.com/url?sa=i&url=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FImage&psig=AOvVaw0Iv14_BE1V_qGIuDOGzZrZ&ust=1599308172643000&source=images&cd=vfe&ved=0CAIQjRxqFwoTCLCYyba9z-sCFQAAAAAdAAAAABAD',
            'media_blob' => '',
        ];
        $this->post(route('news.create'),$data)
            ->assertJson(['status' => TRUE]);
    }

    public function test_applyTransition() {

        $this->getUser();
        $data = [
            'news_id' => 160,
            'transition_name' => 'to_editorial',
        ];

        $this->post(route('news.transition'),$data)
            ->assertJson(['status' => true]);
    }

    public function test_getNews(){
        $this->getUser();
        $data = [
            'status' => 'pre_validated',
        ];
        $this->get(route('news.byStatus'),$data)
            ->assertJson(['status' => true]);
    }

    public function test_groupByStatus(){
        $this->getUser();
        $this->get(route('news.groupByStatus'))
            ->assertStatus(200)
            ->assertJson(['status' => TRUE]);
    }

    public function test_newsDelete(){
        $this->getUser();
        $data = ['news_id' => 160];
        $this->post(route('news.delete'),$data)
            ->assertStatus(200)
            ->assertJson(['status'=> TRUE]);
    }

    public function test_createNewsletter(){
        $this->getUser();
        $data = [
            'news_id' => 161,
            'newsletter_id' =>215,
        ];
        $this->post(route('news.createNewsletter'),$data)
            ->assertJson(['status' => TRUE]);
    }

            /////////Review Unit Test\\\\\\\\\\\
    public function test_createReview(){
        $this->getUser();
        $data = [
            'review_reaction' => 2,
            'review_text'    =>'This Review Create',
            'is_visible'      => 0, //  as requirement says send when click on send button
            'reviewed_by'     => 1,
            'news_id'          =>309,
            'reviewable_id'   => 309,
            'reviewable_type' => News::class,
        ];
        $this->post(route('review.createReview'),$data)
            ->assertJson(['status' => TRUE]);
    }

    public function test_reviewGetReview(){
        $this->getUser();
        $this->get(route('review.getReview', 309))
           ->assertJson(['status'=> TRUE]);
    }

    public function test_showReview(){
        $this->getUser();
        $data = [
            'is_visible' => 1,
            'news_id' => 309,
        ];
        $this->get(route('review.showReview'),$data)
            ->assertJson(['status' => TRUE]);
    }

}
