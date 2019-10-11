<?php namespace Dopmn\Model;

use Exception;
use Dopmn\Core\Post;
use Dopmn\Core\DataFetcher;

class PostModel
{
  use Iterator;

  private $posts = [];
  private $store;

  public function __construct()
  {
    $this->store = DataFetcher::getInstance()->getStore();
  }

  public function getAllFromPage(int $num)
  {
    if ($num > 0 || $num < 11)
    { return $this->store[$num - 1];;
    }

    // TODO: Flash the message: 'Only between 1 and 10'
  }


  public function getAllFromUser(string $id)
  {
    // 👀
    foreach ($this->iterate($this->store) as $data)
    {
      // 👀
      foreach ($data as $posts)
      {
        if ($posts->from_id === $id) { $this->posts[] = $posts; }
      }
    }

    return $this->posts;
  }


  public function getAllFromMonth(string $created_time)
  {
    // '2019-09-21T23:05:58+00:00'...
    $dx = function($date) {
      return \substr($date, 0, 7); //-> '2019-09'
    };

    foreach ($this->iterate($this->store) as $data)
    { // 👀
      foreach ($data as $posts)
      {
        if ($dx($posts->created_time) === $created_time)
        { $this->posts[] = $posts;
        }
      }
    }
    return $this->posts;
  }

}

trait Iterator
{
  public function iterate(array $data)
  {
    foreach ($data as $obj)
    { yield $obj->data->posts;
    }
  }
}