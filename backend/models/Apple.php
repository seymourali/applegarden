<?php
	
	namespace backend\models;
	use Yii;
	
	class Apple
	{
		public $colors = ['#52c652', '#a1181f', '#e7e744'];
		
		public function on_tree($apple_ids = []) {
			if (empty($apple_ids)) {
				return Yii::$app->db->createCommand('SELECT * FROM apples WHERE status=0 ORDER BY id ASC')->queryAll();
			} else {
				$ids_str = implode(', ', $apple_ids);
				return Yii::$app->db->createCommand("SELECT * FROM apples WHERE status=0 AND id IN ({$ids_str}) ORDER BY id ASC")->queryAll();
			}
		}
		
		public function fell() {
			return Yii::$app->db->createCommand('SELECT * FROM apples WHERE status=1 ORDER BY id ASC')->queryAll();
		}
		
		public function generate() {
			$count = rand(1, 3);
			if (empty($count)) {return false;}
			$generated = [];
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
				$generated[] = Yii::$app->db->getLastInsertID();
			}
			return $generated;
		}
		
		public function fallToGround($apple_id) {
			if (empty($apple_id)) {return false;}
			return Yii::$app->db->createCommand('UPDATE apples SET status=1, fall_datetime=:datetime WHERE id=:id', [':id' => $apple_id, ':datetime' => date('Y-m-d H:i:s')])->execute();
		}
		
		public function eat($apple_id, $eaten_percent) {
			if (empty($apple_id) || empty($eaten_percent)) {return false;}
			$decimal_size = (1 - (float)$eaten_percent/100);
			if (empty($decimal_size)) {
				return $this->delete($apple_id);
			}
			return Yii::$app->db->createCommand('UPDATE apples SET size=:size WHERE id=:id', [':id' => $apple_id, ':size' => $decimal_size])->execute();
		}
		
		public function delete($apple_id) {
			if (empty($apple_id)) {return false;}
			return Yii::$app->db->createCommand('DELETE FROM apples WHERE id=:id', [':id' => (int)$apple_id])->execute();
		}
		
		public function get($apple_id) {
			if (empty($apple_id)) {return false;}
			return Yii::$app->db->createCommand('SELECT * FROM apples WHERE id=:id', [':id' => (int)$apple_id])->queryOne();
		}
	}