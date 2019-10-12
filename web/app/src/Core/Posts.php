<?php namespace Dopmn\Core;

use Dopmn\Model\PostsModel;
use Carbon\Carbon;

class Posts
{
  private $posts;
  private $users;

  public function __construct()
  {
    $this->posts = new PostsModel();
  }

  public function fromUser(string $userid)
  {
    return $this->posts->getAllFromUser($userid);
  }

  public function fromPage(int $num)
  {
    return $this->posts->getAllFromPage($num);
  }

  public function fromDate(string $mm, string $yyyy)
  {
    return $this->posts->getAllFromMonth($mm, $yyyy);
  }

  //Returns 0
  public function avgCharCountForMonth(string $mm, string $yyyy)
  {
    $posts = $this->fromDate($mm, $yyyy);
    $sum = 0;
    $counter = 0;

    foreach ($posts as $post) {
      $sum += $this->avgCharCount($post->message);
      $counter++;
    }

    return ($sum == 0 || $counter == 0) ? 0 : round($sum / $counter, 0, PHP_ROUND_HALF_UP);
  }


  public function longestCharCountForMonth(string $mm, string $yyyy)
  {
    $posts = $this->fromDate($mm, $yyyy);
    $max = 0;

    foreach ($posts as $post) {
      $len = $this->avgCharCount($post->message);

      if ($len > $max)
      { $max = $len;
      }
    }

    return $max;
  }

  // @return  Total number of posts by week
  public function weeklyTotal(string $mm, string $yyyy): array
  {
    //1. Get all posts for the month
    $posts = $this->posts->getAllFromMonth($mm, $yyyy);

    //2. Assign posts to their respective week
    $posts_for = [];

    $ctr1 = 0;
    $ctr2 = 0;
    $ctr3 = 0;
    $ctr4 = 0;
    $ctr5 = 0;

    foreach ($posts as $id=> $post)
    {
      $week = Carbon::parse($post->created_time)->weekOfMonth;
      if ($week == 1) $posts_for['week1'] = $ctr1++;
      if ($week == 2) $posts_for['week2'] = $ctr2++;
      if ($week == 3) $posts_for['week3'] = $ctr3++;
      if ($week == 4) $posts_for['week4'] = $ctr4++;
      if ($week == 5) $posts_for['week5'] = $ctr5++;
    }

    //3. Sum all posts contained in each week
    return $posts_for;
  }

  public function avgPerUser()
  {
    $this->users = $this->posts->extractAllUsers();

    // for each user id
    foreach ($this->users as $id)
    {
      // all posts from this user
      foreach ($this->posts->getAllFromUser($id) as $_posts)
      {
        $_users[$id] = $_posts;
      }
    }

    // Each user => posts
    $avg_for = [
      'month1'=> 0,
      'month2'=> 0,
      'month3'=> 0,
      'month4'=> 0,
      'month5'=> 0,
      'month6'=> 0
    ];


    return $_users;
  }

  private function avgCharCount($post): int
  {
    return \mb_strlen($post, 'UTF8');
  }

}
