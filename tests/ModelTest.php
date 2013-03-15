<?php
require_once 'lib/ORM.php';

require_once 'models/Example.php';
require_once 'models/Post.php';

use ORM\Example;
use ORM\Post;

class TestModel extends PHPUnit_Framework_TestCase
{
	public function testInserting() {
		$example = new Example;
		$this->assertTrue($example->save());
	}

	public function testNewRecord() {
		$model = new Example();
		$this->assertTrue($model->isNewRecord());

		$model->save();
		$this->assertFalse($model->isNewRecord());
	}

	public function testTable() {
		$model = new Example();
		$this->assertInstanceOf('ORM\Table', $model->table());
	}

	public function testCreate() {
		$post = Post::create(array('title' => 'Example'));
		$this->assertInstanceOf('ORM\Post', $post);
		$this->assertEquals('Example', $post->title);
	}
}
