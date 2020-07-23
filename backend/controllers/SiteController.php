<?php
namespace backend\controllers;

use backend\models\Apple;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
	                [
		                'allow' => true,
		                'roles' => ['@'],
	                ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
	public function actionIndex() {
		$apple_model = new Apple();
		$apples_on_tree = $apple_model->on_tree();
		$apples_fell = $apple_model->fell();
		return $this->render('index', [
			'apples_on_tree' => $apples_on_tree,
			'apples_fell' => $apples_fell
		]);
	}
	
	public function actionGenerate() {
		if (!Yii::$app->request->isAjax) {
			Yii::$app->end();
		}
		Yii::$app->response->format = 'json';
		$response = ['message' => 'apples_not_generated', 'success' => false];
		$apple_model = new Apple();
		$generated = $apple_model->generate();
		if ($generated) {
			$apples_on_tree = $apple_model->on_tree($generated);
			if (!empty($apples_on_tree)) {
				$response = [
					'message' => 'apples_generated_successfully',
					'success' => true,
					'data' => $apples_on_tree
				];
			}
		}
		return $response;
	}
    
	public function actionFallAppleToGround() {
		if (!Yii::$app->request->isAjax) {
			Yii::$app->end();
		}
		Yii::$app->response->format = 'json';
		$response = ['message' => 'apple_not_fell', 'success' => false];
		$apple_id = strip_tags((int)Yii::$app->request->get('id'));
		if (empty($apple_id)) {return false;}
		$apple_model = new Apple();
		$fell = $apple_model->fallToGround($apple_id);
		if ($fell) {
			$apple = $apple_model->get($apple_id);
			$response['success'] = true;
			$response['message'] = 'apple_successfully_fell';
			$response['data'] = $apple;
		}
		return $response;
	}
	
	public function actionEatApple() {
		if (!Yii::$app->request->isAjax) {
			Yii::$app->end();
		}
		Yii::$app->response->format = 'json';
		$response = ['message' => 'apple_not_eaten', 'success' => false];
		$post_data = Yii::$app->request->post();
		$apple_id = strip_tags((int)$post_data['id']);
		$eaten_percent = strip_tags($post_data['percent']);
		if (empty($apple_id) || empty($eaten_percent)) {return $response;}
		if ((float)$eaten_percent<=0 || (float)$eaten_percent>100) {return $response;}
		$apple_model = new Apple();
		$eaten = $apple_model->eat($apple_id, $eaten_percent);
		if ($eaten) {
			$apple = $apple_model->get($apple_id);
			if (empty($apple)) {
				$response = [
					'success' => true,
					'message' => 'apple_was_eaten_completely_successfully',
				];
			} else {
				$response = [
					'success' => true,
					'message' => 'apple_was_eaten_partially_successfully',
					'data' => $apple
				];
			}
		}
		return $response;
	}

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
