<?php
	
	namespace backend\models;
	use Yii;
	
	class Apple
	{
		public $colors = ['green', 'red', 'yellow'];
		
		public function on_tree() {
			return Yii::$app->db->createCommand('SELECT * FROM apples WHERE status=0')->queryAll();
		}
		
		public function fell() {
			return Yii::$app->db->createCommand('SELECT * FROM apples WHERE status=1')->queryAll();
		}
		
		public function generate() {
			$count = rand(1, 3);
			if (!empty($count)) {
				$i = 1;
				while($i <= $count) {
					$color_index = array_rand($this->colors);
					$color = $this->colors[$color_index];
					Yii::$app->db->createCommand()->insert('apples', [
						'color' => $color,
						'size' => 1,
						'create_datetime' => date('Y-m-d H:i:s')
					])->execute();
					$i++;
				}
			}
		}
		
		public function fallToGround($apple_id) {
			if (empty($apple_id)) {return false;}
			return Yii::$app->db->createCommand('UPDATE apples SET status=1 WHERE id=:id', [':id' => $apple_id])->execute();
		}
	}